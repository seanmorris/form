<?php
namespace SeanMorris\Form\Theme\Form;
class HiddenField extends \SeanMorris\Form\Theme\Form\Field
{

}
__halt_compiler();
?>
<input <?php
	foreach($attrs as $k => $v): ?> <?=$k?> = "<?=$v?>"<?php endforeach;
?>/ type = "hidden" name = "<?=$fullname;?>" value = "<?=$value;?>" />