<?php
namespace SeanMorris\Form\Validator;
/**
 * Validates field matches one or more other fields.
 */
class Confirm extends Validator
{
	protected $rules, $message;

	public function __construct($rules = [])
	{
		$this->rules = $rules;
	}

	public function validate($form, $field = NULL)
	{
		parent::validate($form, $field);

		$value = $field->value();
		$fields = $form->fields();
		if($field->superior())
		{
			$fields = $field->superior()->fields();
		}

		foreach($this->rules as $fieldName => $errorMessage)
		{
			if(!isset($fields[$fieldName]))
			{
				$this->errors[] = $errorMessage;
				continue;
			}

			$otherValue = $fields[$fieldName]->value();

			if($value != $otherValue)
			{
				$this->errors[] = sprintf($errorMessage, $field->title(), $fields[$fieldName]->title());
			}
		}
	}
}