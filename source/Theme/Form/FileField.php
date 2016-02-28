<?php
namespace SeanMorris\Form\Theme\Form;
class FileField extends Field
{

}
__halt_Compiler();
?>
<label for = "<?=$fullname;?>"><?=$title;?>
	<?php if(isset($indicator)): ?>
		<span class = "required"><?=$indicator;?></span>
	<?php endif; ?>
	<input <?php
		foreach($attrs as $k => $v): ?> <?=$k?> = "<?=$v?>"<?php endforeach; ?>/>
 	<?php if(isset($caption)): ?>
 	<span class = "caption"><?=$caption; ?></span>
 	<?php endif;?>
</label>
