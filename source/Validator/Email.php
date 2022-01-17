<?php
namespace SeanMorris\Form\Validator;
/**
 * Validates that field value is an email address.
 */
class Email extends Validator
{
	protected $rules;

	public function __construct($errorMessage)
	{
		$this->errorMessage = $errorMessage;
	}

	public function validate($form, $field = NULL)
	{
		parent::validate($form, $field);

		$value = $field->value();

		if(!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL))
		{
			$this->errors[] = $this->errorMessage;
		}

		return !$this->errors;
	}
}
