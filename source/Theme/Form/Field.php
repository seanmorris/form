<?php
namespace SeanMorris\Form\Theme\Form;
class Field extends \SeanMorris\Theme\View
{
}
__halt_Compiler();
?>
<label for = "<?=$fullname;?>"><?=$title?></label>
<input <?php
	foreach($attrs as $k => $v): ?> <?=$k?> = "<?=$v?>"<?php endforeach;
 ?>/>
 <?php /*
<span style="font-size:smaller"><?=$fullname;?></span>
*/ ?>