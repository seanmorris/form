<?php
namespace SeanMorris\Form\Test\Extension\Theme;
class ExtendedForm extends \SeanMorris\Form\Theme\Form
{
	public function preprocess(&$vars)
	{
		parent::preprocess($vars);
		$vars['classes'] = [];
		if(isset($vars['skeleton']['_classes']) && is_array($vars['skeleton']['_classes']))
		{
			$vars['classes'] = $vars['skeleton']['_classes'];
		}
	}
}
__halt_compiler(); ?>
<div class = "extendedForm<?php
	foreach($classes as $class):?> <?=$class;?><?php endforeach;?>">
	<?php print static::render($vars, 1);?>
</div>