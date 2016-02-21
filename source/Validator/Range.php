<?php
namespace SeanMorris\Form\Validator;
class Range extends Validator
{
	protected $rules, $messages;

	public function __construct($rules = [])
	{
		$min = 0;
		$max = 0;

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

		ksort($this->rules);
	}

	public function validate($field, $form)
	{
		reset($this->rules);
		
		$nan = key($this->rules);
		next($this->rules);
		
		$min = key($this->rules);
		next($this->rules);

		$max = key($this->rules);
		next($this->rules);

		$val = $field->value($form);

		if(!is_numeric($val))
		{
			$this->errors[] = $this->rules[$nan];

			return FALSE;
		}

		if($val < $min)
		{
			$this->errors[] = $this->rules[$min];

			return FALSE;
		}

		if($val > $max)
		{
			$this->errors[] = $this->rules[$max];

			return FALSE;
		}

		return TRUE;
	}
}