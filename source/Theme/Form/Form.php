<?php
namespace SeanMorris\Form\Theme\Form;
class Form extends \SeanMorris\Theme\View
{
}
__halt_compiler();
?>
<form method = "<?=$method;?>" action = "<?=$action;?>" <?php if($enctype):?>enctype = "<?=$enctype;?>"<?php endif;?>>
<?php foreach($fields as $field): ?>
	<?=$field;?>
<?php endforeach; ?>
</form>