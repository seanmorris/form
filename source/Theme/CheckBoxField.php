<?php
namespace SeanMorris\Form\Theme;
class CheckBoxField extends Field
{
	public function preprocess(&$vars)
	{
		parent::preprocess($vars);
		
		if(!isset($vars['value']))
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
?> />
<?=$title;?> <span class = "required"><?=$indicator;?></span>
 <?php if(isset($caption)): ?>
 	<span class = "caption"><?=$caption; ?></span>
 <?php endif;?>
</label>