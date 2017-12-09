<?php
namespace SeanMorris\Form\Theme;
/**
 * Template for Textarea Fields.
 */
class TextareaField extends Field
{
	public function preprocess(&$vars)
	{
		parent::preprocess($vars);
		unset($vars['attrs']['value']);
		unset($vars['attrs']['type']);
	}
}
__halt_Compiler();
?>
<label><?=$title?> <span class = "required"><?=$indicator;?></span>
<textarea<?php
	foreach($attrs as $k => $v): ?> <?=$k?> = "<?=$v?>"<?php endforeach;
?>><?=$value;?></textarea>
<?php if(isset($caption)): ?>
	<span class = "caption"><?=$caption; ?></span>
<?php endif;?>
</label>
