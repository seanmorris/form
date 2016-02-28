<?php
namespace SeanMorris\Form\Theme\Form;
/**
 * Template for Forms.
 */
class Form extends \SeanMorris\Theme\View
{}
__halt_compiler();
?>
<form method = "<?=$method;?>" action = "<?=$action;?>"<?php if($enctype):?> enctype = "<?=$enctype;?>"<?php endif;?>>
<?php if(isset($caption)): ?>
 	<span class = "caption"><?=$caption; ?></span>
 <?php endif;?>
<?php foreach($fields as $field): ?>
	<?=$field;?>
<?php endforeach; ?>
</form>