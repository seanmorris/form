<?php
namespace SeanMorris\Form\Theme\Form;
class RadiobuttonField extends \SeanMorris\Theme\View
{
	public function preprocess(&$vars)
	{
		$options = $this->vars['options'];
		$_options = [];

		foreach($options as $k => $v)
		{
			if(is_numeric($k))
			{
				$_options[$v] = $k;
			}
			else
			{
				$_options[$k] = $v;
			}
		}

		$this->vars['options'] = $_options;
	}
}
__halt_compiler();
?>
<label><?=$title;?></label>
<?php foreach($options as $label => $optionValue): ?>
	<label>
		<input name = "<?=$fullname; ?>" type = "radio" value = "<?=$optionValue; ?>" <?php if($optionValue === $value):?>checked="checked"<?php endif;?> />
		<?=$label; ?>
	</label>
<?php endforeach; ?>	
