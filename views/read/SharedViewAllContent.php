<section class="column fullSized">
	<h1><?php echo $this -> viewBag['sort']; ?> </h1>
			<?php foreach($this -> viewBag['all'] as $item) { ?>
			<article class="article-box imageOverlay">
				<div>
					<h1><?php echo $item -> title; ?></h1>
					<img class="previewImage" src="<?php echo $item -> coverUrl?>" alt="Cover Image" title="<?php echo $item -> title; ?>">
				</div>
				<div class="sideText"><span><?php echo $item -> getSummary(); ?> 
					<a href="/read/content/<?php echo $item -> id ?>">Read more.</a></span>
				</div>
			</article>
			<?php } ?>
</section>