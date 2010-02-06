<?php
/**
 * Simple Spam Protector that implements spam protection.
 * 
 * Simple spam protection provides a honeypot captcha field that
 * should appear blank, and a timestamp field that checks that the
 * page was requested within a reasonable interval from when the
 * comment is submitted. Both fields are hidden and the user
 * should not see any changes in the way comment fields work.
 * 
 * @package spamprotection
 * @subpackage simplespamprotector
 * @author Hamish Campbell <hn.campbell@gmail.com>
 * @copyright copyright (c) 2010, Hamish Campbell 
 */
class SimpleSpamProtector implements SpamProtector {
	
	static $timeout = 120;
	
	static $page_max_age = 14;
	
	public function getFieldName() {
		return 'SimpleSpamProtectorField';
	}
	
	/**
	 * Update a form with the Simple Spam Field Protection
	 */
	function updateForm($form, $before=null, $fieldsToSpamServiceMapping=null) {
		if ($before && $form->Fields()->fieldByName($before)) {
			$form->Fields()->insertBefore($this->getFormField("Captcha", null, null, $form, null), $before);
		} else {
			$form->Fields()->push($this->getFormField("Captcha", null, null, $form, null));
		}
		return $form->Fields();
	}
	
	/**
	 * Set which fields need to be mapped for protection
	 */
	function setFieldMapping($fieldToPostTitle, $fieldsToPostBody=null, $fieldToAuthorName=null, $fieldToAuthorUrl=null, $fieldToAuthorEmail=null, $fieldToAuthorOpenId=null) {
		
	}
	
	/**
	 * Return the Field Associated with this protector
	 */
	public function getFormField($name = null, $title = null, $value = null, $form = null, $rightTitle = null) {
		return new SimpleSpamProtectorField($name, $title, $value, $form, $rightTitle);
	}
	
	/**
	 * Function required to handle dynamic feedback of the system.
	 * if unneeded just return true
	 */
	public function sendFeedback($object = null, $feedback = "") {
		return true;
	}
	
	/**
	 * Checks if page comments are still allowed for the page
	 * 
	 * @param integer $id Page ID
	 * @return boolean
	 */
	static function PageCommentsExpired($id) {
		if(!self::$page_max_age) return false;
		$page = DataObject::get_by_id('SiteTree', $id);
		if(!$page) return false;
		$expiry = strtotime("+ " . self::$page_max_age . " days", strtotime($page->LastEdited));
		return ($expiry <= strtotime('now'));
	}
	
}
?>