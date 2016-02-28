<?php
namespace SeanMorris\Form\Validator;
/**
 * Validates field is one of the provided options.
 */
class OptionFilter extends Validator
{
	protected $rules;

	public function __construct($errorMessage)
	{
		$this->errorMessage = $errorMessage;
	}

	public function validate($form, $field = NULL)
	{
		parent::validate($form, $field);

		$fieldDef = $field->fieldDef();
		$value = $field->value();
		$found = FALSE;

		if(isset($value) && isset($fieldDef['_options']) && is_array($fieldDef['_options']))
		{
			foreach($fieldDef['_options'] as $label => $optionValue)
			{
				if($value == $optionValue)
				{
					$found = TRUE;
					break;
				}
			}
		}

		if(isset($value) && !$found)
		{
			$this->errors[] = $this->errorMessage;
		}

		return !$this->errors;
	}
}