<?php
/**
 * Simple Spam Protector Form Field.
 *
 * @package spamprotection
 * @subpackage simplespamprotector
 * @author Hamish Campbell <hn.campbell@gmail.com>
 * @copyright copyright (c) 2010, Hamish Campbell 
 */
class SimpleSpamProtectorField extends SpamProtectorField {
	
	function __construct($name = null, $title = null, $value = null, $form = null, $rightTitle = null) {
		$title .= "What is " . rand(1, 10) . " + " . rand(1, 10);
		parent::__construct($name, $title, $value, $form, $rightTitle);
	}
	
	function Field() {
		// Honeypot Captcha
		$attributes = array(
			'type' => 'text',
			'class' => 'text' . ($this->extraClass() ? $this->extraClass() : ''),
			'id' => $this->id(),
			'name' => $this->Name(),
			'value' => $this->Value(),
			'title' => $this->Title(),
			'tabindex' => $this->getTabIndex(),
			'maxlength' => ($this->maxLength) ? $this->maxLength : null,
			'size' => ($this->maxLength) ? min( $this->maxLength, 30 ) : null 
		);

		$html = $this->createTag('input', $attributes);

		// Timestamp Check
		$attributes = array(
			'type' => 'text',
			'class' => 'text' . ($this->extraClass() ? $this->extraClass() : ''),
			'id' => $this->id()."_timestamp",
			'name' => $this->Name()."_timestamp",
			'value' => time(),
			'title' => $this->Title(),
			'tabindex' => $this->getTabIndex(),
			'maxlength' => ($this->maxLength) ? $this->maxLength : null,
			'size' => ($this->maxLength) ? min( $this->maxLength, 30 ) : null
		);		

		$html .= $this->createTag('input', $attributes);
		
		return $html;
	}
	
	function FieldHolder() {
		Requirements::customCSS("\n.simplespamprotector {	display: none; }\n");
		return parent::FieldHolder();
	}
	
	/**
	 * Checks the field values for potential spam
	 * Fail validation if the captcha field is filled out or the timestamp (time in minutes from the
	 * first page load is greater than SimpleSpamProtector::$timeout
	 * Note that the validation messages are not really important - a human reader shouldn't see them.
	 * @return 	boolean
	 */
	function validate($validator) {
		if(Permission::check('ADMIN'))
			return true;

		$timestamp_field = $this->name()."_timestamp";
		if(!isset($_REQUEST[$timestamp_field]) || !is_numeric($_REQUEST[$timestamp_field]) || ((time() - (int)$_REQUEST[$timestamp_field]) > (60 * SimpleSpamProtector::$timeout))) {
			$validator->validationError(
				$this->name, 
				_t(
					'SimpleSpamProtectorField.INCORRECTTIMESTAMP', 
					"Comment timed out. Please reload the page to submit your comment.",
					PR_MEDIUM
				), 
				"validation", 
				false
			);
			return false;	
		}
			
		if($this->value) {
			$validator->validationError(
				$this->name, 
				_t(
					'SimpleSpamProtectorField.INCORRECTCAPTURE', 
					"You didn't type in the correct captcha text. Please type it in again.",
					PR_MEDIUM
				), 
				"validation", 
				false
			);
			return false;
		}
		return true;
		
	}
}
?>