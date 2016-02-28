<?php
namespace SeanMorris\Form\Theme\Form;
/**
 * Preprocessing logic for option-list type tempplates.
 */
class OptionField extends Field
{
	/**
	 * Preprocesing logic for option-lists.
	 * 
	 * @param $vars list of references to tenplate vars.
 	 */
	public function preprocess(&$vars)
	{
		parent::preprocess($vars);

		$options = $this->vars['options'];
		$_options = [];

		foreach($options as $k => $v)
		{
			if(is_numeric($k))
			{
				$_options[$v] = $k;
			}
			else
			{
				$_options[$k] = $v;
			}
		}

		$this->vars['options'] = $_options;
	}
}	
