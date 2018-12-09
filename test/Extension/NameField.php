<?php
namespace SeanMorris\Form\Test\Extension;
class NameField extends \SeanMorris\Form\Field
{
	protected static
		$validatorShorthand = [
			'_name' => 'SeanMorris\Form\Test\Extension\NameValidator'
		];

	public function __construct($fieldDef, $form)
	{
		$fieldDef += [
			'type'  => 'text'
			, '_name' => '%s must consist of only letters.' 
		];

		parent::__construct($fieldDef, $form);
	}
}