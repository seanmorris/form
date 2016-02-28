<?php
namespace SeanMorris\Form\Theme;
/**
 * Template for Hidden Fields.
 */
class HiddenField extends \SeanMorris\Form\Theme\Field
{}
__halt_compiler();
?>
<input <?php if(isset($value)): ?>value = "<?=$value;?>"<?php endif;
	foreach($attrs as $k => $v): ?> <?=$k?> = "<?=$v?>"<?php endforeach;
?>/>