<?php
namespace SeanMorris\Form\Validator;
abstract class Validator
{
	protected $errors = [];

	public function errors()
	{
		return $this->errors;
	}

	public function validate($field, $form)
	{
		$this->errors = [];
		return true;
	}
}