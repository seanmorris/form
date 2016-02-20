<?php
namespace SeanMorris\Form\Theme\Form;
class Fieldset extends Field
{

}
__halt_Compiler();
?>
<fieldset<?php
	if($disabled): ?> disabled = "disabled"<?php endif;
 ?><?php
	foreach($attrs as $k => $v): ?> <?=$k?> = "<?=$v?>"<?php endforeach;
 ?>>
 <?php if(isset($caption)): ?>
 	<span class = "caption"><?=$caption; ?></span>
 <?php endif;?>
<?php if($title): ?><legend for = "<?=$fullname?>"><?=$title?></legend><?php endif; ?>
<?php foreach($fields as $field): ?>
	<?=$field;?>
<?php endforeach; ?>
</fieldset>