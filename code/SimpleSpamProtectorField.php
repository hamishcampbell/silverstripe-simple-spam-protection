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
		if($form) {
			$data = $form->getData();
			if(SimpleSpamProtector::PageCommentsExpired($data['ParentID'])) { 
				$form->unsetAllActions();
				$form->Fields = new FieldSet();
				$form->setMessage(_t('SimpleSpamProtector.DISABLED',"Comments for this page have been disabled"), 'warning');
				$form->makeReadonly();
			}
		}		
		$title .= _t('SimpleSpamProtector.WHATIS',"What is") . " " . rand(1, 10) . " + " . rand(1, 10);
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
		Requirements::customCSS("\n.simplespamprotector { display: none; }\n");
		return parent::FieldHolder();
	}
	
	/**
	 * Checks the field values for potential spam
	 * Fail validation if the captcha field is filled out or the timestamp (time in minutes from the
	 * first page load is greater than SimpleSpamProtector::$timeout
	 * @return 	boolean
	 */
	function validate($validator) {
		if(Permission::check('ADMIN'))
			return true;

		if(isset($_REQUEST['ParentID']) && SimpleSpamProtector::PageCommentsExpired($_REQUEST['ParentID'])) {
			$validator->validationError(
				$this->name, 
				_t(
					'SimpleSpamProtector.DISABLED', 
					"Comments for this page have been disabled"
				), 
				"validation", 
				false
			);
			return false;	
		}
			
		$timestamp_field = $this->name()."_timestamp";
		if(!isset($_REQUEST[$timestamp_field]) || !is_numeric($_REQUEST[$timestamp_field]) || ((time() - (int)$_REQUEST[$timestamp_field]) > (60 * SimpleSpamProtector::$timeout))) {
			$validator->validationError(
				$this->name, 
				_t(
					'SimpleSpamProtector.INCORRECTTIMESTAMP', 
					"This form has timed out. Please refresh your browser and try again."
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
					'SimpleSpamProtector.INCORRECTCAPTCHA', 
					"You didn't type in the correct captcha text. Please try again.",
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