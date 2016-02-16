<?php
namespace SeanMorris\Form\Theme\Form;
class CheckBoxField extends Field
{
	public function preprocess(&$vars)
	{
		if(!isset($vars['attrs']['value']))
		{
			if(isset($vars['fieldDef']['_value']))
			{
				$vars['attrs']['value'] = $vars['fieldDef']['_value'];
			}
			else
			{
				$vars['attrs']['value'] = 1;
			}
		}
		else
		{
			$vars['attrs']['checked'] = 'checked';
		}
	}
}
__halt_Compiler();
?>
<label>
<input <?php
	foreach($attrs as $k => $v): ?> <?=$k?> = "<?=$v?>"<?php endforeach;
 ?> value = "1" />
 <?=$title;?></label>