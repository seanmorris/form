<?php
namespace SeanMorris\Form;
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

	protected function fullname()
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
}