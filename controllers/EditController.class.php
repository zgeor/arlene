<?php
class EditController extends Controller {
	private $articleManager;
	private $columnManager;
	private $reviewManager;

	public function __construct($action, $uriParams) {
		parent::__construct($action, $uriParams);
		$this -> articleManager = new ArticleManager();
		$this -> columnManager = new ColumnManager();
		$this -> reviewManager = new ReviewManager();
		$this -> authorizationMapping = array('comment' => 'editor', 'columns' => 'editor', 'articles' => 'editor', 'reviews' => 'editor', 'content' => 'writer');
	}

	public function articles() {
		$this -> viewBag['awaitingChanges'] = $this -> articleManager -> getAllArticles('awaiting_changes');
		$this -> viewBag['underReview'] = $this -> articleManager -> getAllArticles('underReview');
		$this -> viewBag['submitted'] = $this -> articleManager -> getAllArticles('submitted');

		$this -> renderView();
	}

	public function content() {
		if (isset($_POST['status']) || isset($_POST['recommended'])) {
			if (CurrentUser::hasEditorAccess()) {
				$this -> updateStatus();
				$this -> updateRecommendation();
			} else {
				$this -> addNotification('error', "You cant change article status, or recommend it!");
			}
		}

		if ($this -> isArticle()) {
			if ($this -> articleManager -> updateArticle($_POST['id'], $_POST['title'], $_POST['contents'], $_POST['imgUrl'])) {
				$this -> addNotification('info', 'Successfully updated the article!');
			} else {
				$this -> addNotification('error', "We couldn't update the article. Try again.");
			}
			$this -> viewBag['content'] = $this -> articleManager -> getArticleById($_POST['id'], null, true);
			$this -> renderView(true);
			return;
		}
		if ($this -> isColumn()) {
			if ($this -> columnManager -> update($_POST['id'], $_POST['title'], $_POST['contents'], $_POST['imgUrl'], $_POST['topic'])) {
				$this -> addNotification('info', 'Successfully updated the column!');
			} else {
				$this -> addNotification('error', "We couldn't update the column. Try again.");
			}
			$this -> viewBag['content'] = $this -> columnManager -> getColumnById($_POST['id'], null, true);
			$this -> renderView(true);
			return;
		}
		if ($this -> isReview()) {
			if ($this -> reviewManager -> update($_POST['id'], $_POST['title'], $_POST['contents'], $_POST['imgUrl'], $_POST['topic'], $_POST['rating'])) {
				$this -> addNotification('info', 'Successfully updated the review!');
			} else {
				$this -> addNotification('error', "We couldn't update the review. Try again.");
			}
			$this -> viewBag['content'] = $this -> reviewManager -> getReviewById($_POST['id'], null, true);
			$this -> renderView(true);
			return;
		}
		if (isset($this -> uriParams[2])) {
			$this -> viewBag['content'] = $this -> articleManager -> getArticleById($this -> uriParams[2], null, true);
			if (!empty($this -> viewBag['content'])) {
				$this -> renderView();
				return;
			}
			$this -> viewBag['content'] = $this -> columnManager -> getColumnById($this -> uriParams[2], null, true);
			if (!empty($this -> viewBag['content'])) {
				$this -> renderView();
				return;
			}
			$this -> viewBag['content'] = $this -> reviewManager -> getReviewById($this -> uriParams[2], null, true);
			if (!empty($this -> viewBag['content'])) {
				$this -> renderView();
				return;
			}
		}
	}

	public function reviews() {
		$this -> viewBag['awaitingChanges'] = $this -> reviewManager -> getAllReviews('awaiting_changes');
		$this -> viewBag['underReview'] = $this -> reviewManager -> getAllReviews('underReview');
		$this -> viewBag['submitted'] = $this -> reviewManager -> getAllReviews('submitted');
		$this -> renderView();
	}

	public function columns() {
		$this -> viewBag['awaitingChanges'] = $this -> columnManager -> getAllColumns('awaitingChanges');
		$this -> viewBag['underReview'] = $this -> columnManager -> getAllColumns('underReview');
		$this -> viewBag['submitted'] = $this -> columnManager -> getAllColumns('submitted');
		$this -> renderView();
	}

	public function comment() {
		if (!array_key_exists('comment', $_POST) && !array_key_exists('article_id', $_POST)) {
			$this -> addNotification('warn', 'Upsi.. Daisy.. Something went wrong.');
			$this -> renderView($this -> viewBag, true);
			return;
		}
		if (!$this -> articleManager -> addEditorCommentToId($_POST['article_id'], CurrentUser::getUser() -> userId, $_POST['comment'])) {
			$this -> addNotification('warn', "Something went wrong we couldn't add your comment.");
		}
		$this -> renderView(true);
	}

	private function isArticle() {
		return isset($_POST['id']) && isset($_POST['title']) && isset($_POST['contents']) && isset($_POST['imgUrl']) && !isset($_POST['topic']) && !isset($_POST['rating']);
	}

	private function isReview() {
		return isset($_POST['id']) && isset($_POST['title']) && isset($_POST['contents']) && isset($_POST['imgUrl']) && isset($_POST['topic']) && isset($_POST['rating']);
	}

	private function isColumn() {
		return isset($_POST['id']) && isset($_POST['title']) && isset($_POST['contents']) && isset($_POST['imgUrl']) && isset($_POST['topic']) && !isset($_POST['rating']);
	}

	private function updateStatus() {
		if (isset($_POST['status'])) {
			if ($this -> articleManager -> changeStatus($_POST['id'], CurrentUser::getUser() -> userId, $_POST['status'])) {
				$this -> addNotification('info', 'Successfully updated status of the article!');
			} else {
				$this -> addNotification('error', "Couldn't update the article status. Try again!");
			}
		}
	}

	private function updateRecommendation() {
		if (isset($_POST['recommended'])) {
			if ($this -> articleManager -> changeRecommendedStatus($_POST['id'], $_POST['recommended'] == "true")) {
				if($_POST['recommended'] == "true"){
					$this -> addNotification('info', 'Successfully recommended the content!');
				}else{
					$this -> addNotification('info', 'Successfully removed the content recommendation!');
				}
			} else {
				$this -> addNotification('error', "Couldn't recommend the article. Try again!");
			}
		}
	}
}
