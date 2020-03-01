<?php
namespace SeanMorris\Form\Test\Extension\Theme;
class ExtendedFormTheme extends \SeanMorris\Form\Theme\Theme
{
	protected static $view = [
		'SeanMorris\Form\Test\Extension\ProfileForm' => 'SeanMorris\Form\Test\Extension\Theme\ExtendedForm'
		, 'SeanMorris\Form\Test\Extension\AvatarField' => 'SeanMorris\Form\Test\Extension\Theme\AvatarField'
	];
}