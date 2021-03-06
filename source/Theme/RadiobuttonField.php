<?php
namespace SeanMorris\Form\Theme;
/**
 * Template for Radiobutton Fields.
 */
class RadiobuttonField extends OptionField
{}
__halt_compiler();
?>
<label><?=$title;?> <span class = "required"><?=$indicator;?></span>
<?php foreach($options as $label => $optionValue): ?>
	<label>
		<input name = "<?=$fullname; ?>" type = "radio" value = "<?=$optionValue; ?>" <?php if($optionValue === $value):?>checked="checked"<?php endif;?> />
		<?=$label; ?>
	</label>
<?php endforeach; ?>
<?php if(isset($caption)): ?>
	<span class = "caption"><?=$caption; ?></span>
<?php endif;?>
</label>