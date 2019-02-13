<?php
namespace SeanMorris\Form\Validator;
/**
 * Validates that field value is present.
 */
class Required extends Validator
{
	protected $errorMessage;

	public function __construct($errorMessage)
	{
		$this->errorMessage = $errorMessage;
	}

	public function validate($form, $field = NULL)
	{
		parent::validate($form, $field);

		$value = $field->value();

		if(is_null($value) || (is_scalar($value) && !strlen($value)))
		{
			$this->errors[] = $this->errorMessage;
		}
		else if(!is_scalar($value) && !$value)
		{
			$this->errors[] = $this->errorMessage;
		}

		return !$this->errors;
	}
}