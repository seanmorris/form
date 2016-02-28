<?php
namespace SeanMorris\Form\Theme\Form;
/**
 * Template for Hidden Fields.
 */
class HiddenField extends \SeanMorris\Form\Theme\Form\Field
{}
__halt_compiler();
?>
<input <?php if(isset($value)): ?>value = "<?=$value;?>"<?php endif;
	foreach($attrs as $k => $v): ?> <?=$k?> = "<?=$v?>"<?php endforeach;
?>/>