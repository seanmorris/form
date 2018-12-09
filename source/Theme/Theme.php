<?php
namespace SeanMorris\Form\Theme;
/**
 * Default theme for Forms.
 */
class Theme extends \SeanMorris\Theme\Theme
{
	/**
	 * Map of form classes to view classes.
	 */
	protected static
		$view = [
			'SeanMorris\Form\Form' => 'SeanMorris\Form\Theme\Form'
			, 'SeanMorris\Form\RadiobuttonField' => 'SeanMorris\Form\Theme\RadiobuttonField'
			, 'SeanMorris\Form\SelectField' => 'SeanMorris\Form\Theme\SelectField'
			, 'SeanMorris\Form\Html' => 'SeanMorris\Form\Theme\Html'
			, 'SeanMorris\Form\HiddenField' => 'SeanMorris\Form\Theme\HiddenField'
			, 'SeanMorris\Form\SubmitField' => 'SeanMorris\Form\Theme\SubmitField'
			, 'SeanMorris\Form\ButtonField' => 'SeanMorris\Form\Theme\ButtonField'
			, 'SeanMorris\Form\CheckBoxField' => 'SeanMorris\Form\Theme\CheckBoxField'
			, 'SeanMorris\Form\TextareaField' => 'SeanMorris\Form\Theme\TextareaField'
			, 'SeanMorris\Form\PasswordField' => 'SeanMorris\Form\Theme\PasswordField'
			, 'SeanMorris\Form\FileField' => 'SeanMorris\Form\Theme\FileField'
			, 'SeanMorris\Form\Fieldset' => 'SeanMorris\Form\Theme\Fieldset'
			, 'SeanMorris\Form\Field' => 'SeanMorris\Form\Theme\Field'
		]
	;
}