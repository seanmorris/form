<?php
namespace SeanMorris\Form\Theme\Form;
class OptionField extends \SeanMorris\Theme\View
{
	public function preprocess(&$vars)
	{
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
