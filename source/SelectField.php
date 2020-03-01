<?php
namespace SeanMorris\Form;
/**
 * Logic for Select fields.
 */
class SelectField extends Field
{
	protected $multi = FALSE;

	public function __construct($fieldDef, $form)
	{
		if(isset($fieldDef['multiple']) && $fieldDef['multiple'])
		{
			$this->multi = TRUE;
		}

		parent::__construct($fieldDef, $form);
	}

	public function fullname()
	{
		$name = parent::fullname();

		if($this->multi)
		{
			$name .= '[]';
		}

		return $name;
	}

	public function isArray()
	{
		return $this->multi;
	}

	public function set($value, $override = false)
	{
		if($value === '')
		{
			$value = NULL;
		}

		parent::set($value, $override);
	}
}