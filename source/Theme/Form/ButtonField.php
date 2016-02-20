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
 <?php if(isset($caption)): ?>
 	<span class = "caption">
 	</span>
 <?php endif;?>