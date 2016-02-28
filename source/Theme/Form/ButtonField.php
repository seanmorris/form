<?php
namespace SeanMorris\Form\Theme\Form;
class ButtonField extends Field
{
	public function preprocess(&$vars)
	{
		parent::preprocess($vars);
		
		$vars['value'] = $vars['fieldDef']['value'];
	}
}
__halt_Compiler();
?>
<input <?php if(isset($value)): ?> value = "<?=$value;?>"<?php endif;
	foreach($attrs as $k => $v): ?> <?=$k?> = "<?=$v?>"<?php endforeach;
 ?>/>
 <?php if(isset($caption)): ?>
 	<span class = "caption">
 	</span>
 <?php endif;?>