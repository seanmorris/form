<?php
namespace SeanMorris\Form\Validator;
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

		$value = $field->value($form);

		if(!strlen($value))
		{
			$this->errors[] = $this->errorMessage;
		}

		return !$this->errors;
	}
}