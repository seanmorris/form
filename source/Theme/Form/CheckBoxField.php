<?php
namespace SeanMorris\Form\Theme\Form;
class CheckBoxField extends Field
{
	public function preprocess(&$vars)
	{
		parent::preprocess($vars);
		
		if(!isset($vars['value']))
		{
			if(isset($vars['fieldDef']['_value']))
			{
				$vars['value'] = $vars['fieldDef']['_value'];
			}
			else
			{
				$vars['value'] = 1;
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
<label for = "<?=$fullname;?>">
<input <?php
	foreach($attrs as $k => $v): ?> <?=$k?> = "<?=$v?>"<?php endforeach;
?> />
<?=$title;?> <span class = "required"><?=$indicator;?></span>
 <?php if(isset($caption)): ?>
 	<span class = "caption"><?=$caption; ?></span>
 <?php endif;?>
</label>