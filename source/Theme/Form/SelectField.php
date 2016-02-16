<?php
namespace SeanMorris\Form\Theme\Form;
class SelectField extends OptionField
{
	public function preprocess(&$vars)
	{
		parent::preprocess($vars);
		
		$tag_attributes = [];

		$vars['selected'] = [];
		$vars['select_attrs'] = NULL;

		if(!$vars['attrs'])
		{
			return;
		}

		foreach($vars['attrs'] as $attr => $val)
		{
			if(isset($vars['attrs']['multiple'])
				&& $vars['attrs']['multiple']
				&& $attr == 'value'
			){
				if(!is_array($val))
				{
					break;
				}
				
				foreach($val as $selected)
				{
					$vars['selected'][$selected] = TRUE;
				}
				continue;
			}

			$tag_attributes[] = sprintf(
				'%s = "%s"'
				, $attr
				, $val
			);
		}

		$vars['select_attrs'] = implode(' ', $tag_attributes);
	}
}
__halt_compiler();
?>
<label><?=$title;?>
<select name = "<?=$fullname; ?>" <?=$select_attrs;?> />
<?php foreach($options as $label => $optionValue): ?>
		<option value = "<?=$optionValue; ?>" <?php 
			if (isset($selected[$optionValue])):?>selected="selected"<?php
				endif; ?>>
		<?=$label; ?>
		</option>
<?php endforeach; ?>	
</select>
</label>