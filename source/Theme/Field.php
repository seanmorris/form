<?php
namespace SeanMorris\Form\Theme;
/**
 * Template for Fields.
 */
class Field extends \SeanMorris\Theme\View
{
	public function preprocess(&$vars)
	{
		$vars['indicator'] = NULL;
		$vars['caption'] = NULL;

		if(isset($vars['fieldDef']['_required']) && $vars['fieldDef']['_required'])
		{
			$vars['indicator'] ='*';
		}

		if(isset($vars['fieldDef']['_caption']) && $vars['fieldDef']['_caption'])
		{
			$vars['caption'] = $vars['fieldDef']['_caption'];
		}
	}
}
__halt_Compiler();
?>
<label><?=$title;?>
	<?php if(isset($indicator)): ?>
		<span class = "required"><?=$indicator;?></span>
	<?php endif; ?>
	<input <?php
		foreach($attrs as $k => $v): ?> <?=$k?> = "<?=$v?>"<?php endforeach;
			if(isset($value)): ?> value = "<?=$value;?>"<?php endif; ?>/>
 	<?php if(isset($caption)): ?>
 	<span class = "caption"><?=$caption; ?></span>
 	<?php endif;?>
</label>
 