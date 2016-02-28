<?php
namespace SeanMorris\Form\Test\Extension;
class ProfileForm extends \SeanMorris\Form\Form
{
	protected static
		$typesToClasses = [
			'name' => 'SeanMorris\Form\Test\Extension\NameField'
			, 'height' => 'SeanMorris\Form\Test\Extension\HeightField'
			, 'avatar' => 'SeanMorris\Form\Test\Extension\AvatarField'
		];
		
	public function __construct($skeleton = [])
	{
		$skeleton['_method'] = 'POST';
		$skeleton['_theme'] = 'SeanMorris\Form\Test\Extension\Theme\ExtendedFormTheme';
		$skeleton['_classes'] = ['testClass'];
		
		$skeleton['firstName'] = [
			'_title'      => 'First Name'
			, 'type'      => 'name'
			, '_required' => '%s is required'
		];

		$skeleton['lastName'] = [
			'_title'      => 'Last Name'
			, 'type'      => 'name'
			, '_required' => '%s is required'
		];

		$skeleton['avatar'] = [
			'_title'      => 'Avatar'
			, 'type'      => 'avatar'
			, '_required' => '%s is required'
		];

		$skeleton['bio'] = [
			'_title' => 'Bio'
			, 'type' => 'textarea'
		];

		$skeleton['height'] = [
			'_title' => 'Height'
			, 'type' => 'height'
		];

		$skeleton['submit'] = [
			'type' => 'Submit'
		];

		parent::__construct($skeleton);
	}
}