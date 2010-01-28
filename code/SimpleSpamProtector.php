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
	
}
?>