<?php
namespace SeanMorris\Form\Validator;
class EmailValidator extends Validator
{
	protected $rules;

	public function __construct($errorMessage = NULL)
	{
		$this->errorMessage = $errorMessage
			? $errorMessage
			: '%s must be a valid email address.';
	}

	public function validate($field, $form)
	{
		$value = $field->value($form);

		if(!filter_var($value, FILTER_VALIDATE_EMAIL))
		{
			$this->errors[] = $this->errorMessage;
		}

		return !$this->errors;
	}
}