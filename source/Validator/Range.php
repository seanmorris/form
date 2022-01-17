<?php
namespace SeanMorris\Form\Validator;
/**
 * Validates that field value is numerical and withing a boundary.
 */
class Range extends Validator
{
	protected $rules, $messages;

	public function __construct($rules = [])
	{
		$min = 0;
		$max = 0;
		
		ksort($rules);

		foreach($rules as $rule => $error)
		{
			if($min < $rule)
			{
				$min = $rule;

				$this->rules[$min] = $error;
			}

			if($max < $rule)
			{
				$max = $rule;

				$this->rules[$max] = $error;
			}

			if($rule === 'nan')
			{
				$min = $rule;

				$this->rules['nan'] = $error;
			}
		}
	}

	public function validate($form, $field = NULL)
	{
		parent::validate($form, $field);

		$keys = array_keys($this->rules);

		$nan = $keys[0];
		$min = $keys[1];
		$max = $keys[2];
		$val = $field->value();		

		if(isset($val) && !is_numeric($val))
		{
			$this->errors[] = $this->rules[$nan];

			return FALSE;
		}

		if(isset($val) && $val < $min)
		{
			$this->errors[] = $this->rules[$min];

			return FALSE;
		}

		if(isset($val) && $val > $max)
		{
			$this->errors[] = $this->rules[$max];

			return FALSE;
		}

		return TRUE;
	}
}