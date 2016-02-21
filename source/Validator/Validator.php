<?php
namespace SeanMorris\Form\Validator;
abstract class Validator
{
	protected $errors = [];

	public function errors()
	{
		return $this->errors;
	}

	public function validate($form, $field = NULL)
	{
		$this->errors = [];
		return true;
	}
}