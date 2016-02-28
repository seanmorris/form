<?php
namespace SeanMorris\Form\Validator;
/**
 * Validates that field value matches one or more regex.
 */
class Regex extends Validator
{
	protected $rules;

	public function __construct($rules)
	{
		$this->rules = $rules;
	}

	public function validate($form, $field = NULL)
	{
		parent::validate($form, $field);
		
		$value = $field->value();

		foreach($this->rules as $rule => $error)
		{
			if(isset($value) && !preg_match($rule, $value))
			{
				$this->errors[] = $error;
			}
		}

		return !$this->errors;
	}
}