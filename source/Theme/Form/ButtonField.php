<?php
namespace SeanMorris\Form\Theme\Form;
class ButtonField extends Field
{

}
__halt_Compiler();
?>
<input <?php
	foreach($attrs as $k => $v): ?> <?=$k?> = "<?=$v?>"<?php endforeach;
 ?>/>