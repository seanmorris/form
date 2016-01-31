<?php
namespace SeanMorris\Form\Validator;
class RegexValidator extends Validator
{
	protected $rules;

	public function __construct($rules)
	{
		$this->rules = $rules;
	}

	public function validate($field, $form)
	{
		$value = $field->value($form);

		foreach($this->rules as $rule => $error)
		{
			if(!preg_match($rule, $value))
			{
				$this->errors[] = $error;
			}
		}

		return !$this->errors;
	}
}