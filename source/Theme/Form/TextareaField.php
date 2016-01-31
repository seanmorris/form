<?php
namespace SeanMorris\Form\Theme\Form;
class TextareaField extends Field
{
	public function preprocess(&$vars)
	{
		unset($vars['attrs']['value']);
		unset($vars['attrs']['type']);
	}
}
__halt_Compiler();
?>
<label for = "<?=$fullname;?>"><?=$title?></label>
<textarea name = "<?=$fullname;?>" <?php
	foreach($attrs as $k => $v): ?> <?=$k?> = "<?=$v?>"<?php endforeach;
?>><?=$value;?></textarea>
