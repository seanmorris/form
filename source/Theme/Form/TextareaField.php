<?php
namespace SeanMorris\Form\Theme\Form;
class TextareaField extends Field
{
	public function preprocess()
	{
		unset($this->vars['attrs']['value']);
		unset($this->vars['attrs']['type']);
	}
}
__halt_Compiler();
?>
<label for = "<?=$fullname;?>"><?=$title?></label>
<textarea name = "<?=$fullname;?>" <?php
	foreach($attrs as $k => $v): ?> <?=$k?> = "<?=$v?>"<?php endforeach;
?>><?=$value;?></textarea>
