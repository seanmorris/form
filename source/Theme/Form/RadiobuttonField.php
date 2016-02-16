<?php
namespace SeanMorris\Form\Theme\Form;
class RadiobuttonField extends OptionField
{

}
__halt_compiler();
?>
<label><?=$title;?>
<?php foreach($options as $label => $optionValue): ?>
	<label>
		<input name = "<?=$fullname; ?>" type = "radio" value = "<?=$optionValue; ?>" <?php if($optionValue === $value):?>checked="checked"<?php endif;?> />
		<?=$label; ?>
	</label>
<?php endforeach; ?>	
</label>