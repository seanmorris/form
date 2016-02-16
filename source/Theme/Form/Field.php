<?php
namespace SeanMorris\Form\Theme\Form;
class Field extends \SeanMorris\Theme\View
{
}
__halt_Compiler();
?>
<label for = "<?=$fullname;?>"><?=$title?>
<input <?php
	foreach($attrs as $k => $v): ?> <?=$k?> = "<?=$v?>"<?php endforeach;
 ?>/>
 </label>
 