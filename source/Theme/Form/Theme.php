<?php
namespace SeanMorris\Form\Theme\Form;
class Theme extends \SeanMorris\Theme\Theme
{
	protected static
		$view = [
			'SeanMorris\Form\Form' => 'SeanMorris\Form\Theme\Form\Form'
			, 'SeanMorris\Form\RadiobuttonField' => 'SeanMorris\Form\Theme\Form\RadiobuttonField'
			, 'SeanMorris\Form\SelectField' => 'SeanMorris\Form\Theme\Form\SelectField'
			, 'SeanMorris\Form\Html' => 'SeanMorris\Form\Theme\Form\Html'
			, 'SeanMorris\Form\HiddenField' => 'SeanMorris\Form\Theme\Form\HiddenField'
			, 'SeanMorris\Form\SubmitField' => 'SeanMorris\Form\Theme\Form\SubmitField'
			, 'SeanMorris\Form\ButtonField' => 'SeanMorris\Form\Theme\Form\ButtonField'
			, 'SeanMorris\Form\CheckBoxField' => 'SeanMorris\Form\Theme\Form\CheckBoxField'
			, 'SeanMorris\Form\TextareaField' => 'SeanMorris\Form\Theme\Form\TextareaField'
			, 'SeanMorris\Form\PasswordField' => 'SeanMorris\Form\Theme\Form\PasswordField'
			, 'SeanMorris\Form\FileField' => 'SeanMorris\Form\Theme\Form\FileField'
			, 'SeanMorris\Form\Fieldset' => 'SeanMorris\Form\Theme\Form\Fieldset'
			, 'SeanMorris\Form\Field' => 'SeanMorris\Form\Theme\Form\Field'
		]
	;
}