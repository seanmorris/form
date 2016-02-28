<?php
namespace SeanMorris\Form\Test\Extension;
class HeightField extends \SeanMorris\Form\Fieldset
{
	public function __construct($fieldDef, $form)
	{
		$children['units'] = [
			'type'     => 'text'
			, '_title' => 'Units'
			, '_regex' => [
				'/^\d+$/' => '%s must consist of only numbers.'
			]
		];

		$children['measure'] = [
			'type'       => 'select'
			, '_title'   => 'Measure'
			, '_options' => [
				'Inches'        => 'in'
				, 'Centimeters' => 'cm'
			]
		];

		$fieldDef += [
			'_title'      => 'Height'
			, 'type'      => 'fieldset'
			, '_children' => $children
			, '_array'    => TRUE
		];

		parent::__construct($fieldDef, $form);
	}
}