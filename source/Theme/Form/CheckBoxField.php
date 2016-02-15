<?php
namespace SeanMorris\Form\Theme\Form;
class CheckBoxField extends Field
{

}
__halt_Compiler();
?>
<input <?php
	foreach($attrs as $k => $v): ?> <?=$k?> = "<?=$v?>"<?php endforeach;
 ?> value = "1" />