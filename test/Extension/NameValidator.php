<?php
namespace SeanMorris\Form\Test\Extension;
class NameValidator extends \SeanMorris\Form\Validator\Validator
{
	public function __construct($errorMessage)
	{
		$this->errorMessage = $errorMessage;
	}

	public function validate($form, $field = NULL)
	{
		parent::validate($form, $field);
		
		$value = $field->value();

		if(isset($value) && strlen($value) && !preg_match('/^[A-Za-z]+$/', $value))
		{
			$this->errors[] = $this->errorMessage;
		}

		return !$this->errors;
	}
}