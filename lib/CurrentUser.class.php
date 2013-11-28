<?php
class CurrentUser {

	private static $userRolesWeight;
	
	/**
	 * Prevents the CurrentUser class of being instantiated.
	 */
	private function __construct() {
	}
	
	/**
	 * Initialises the CurrentUser class.
	 */
	public static function init() {
		self::$userRolesWeight = array('reader' => 0, 'subscriber' => 2, 'writer' => 4, 'editor' => 6, 'publisher' => 8);
	}

	public static function getUser() {
		return $_SESSION['user'];
	}
	public static function hasSubscriberAccess(){
		return self::hasAccess('subscriber');
	}
	public static function hasWriterAccess() {
		return self::hasAccess('writer');
	}

	public static function hasEditorAccess() {
		return self::hasAccess('editor');
	}

	public static function hasPublisherAccess() {
		return self::hasAccess('publisher');
	}

	private static function hasAccess($roleToVerify) {
		if (self::$userRolesWeight[self::getUser() -> role] < self::$userRolesWeight[$roleToVerify]) {
			return false;
		} else {
			return true;
		}
	}

}