<?php
namespace SeanMorris\Form\Theme;
/**
 * Template for Select Fields.
 */
class SelectField extends OptionField
{
	public function preprocess(&$vars)
	{
		parent::preprocess($vars);

		$vars['selected'] = [];

		if(isset($vars['attrs']['multiple'])
			&& $vars['attrs']['multiple']
			&& isset($vars['value'])
			&& is_array($vars['value'])
		){
			foreach($vars['value'] as $selected)
			{
				$vars['selected'][$selected] = TRUE;
			}
		}
		elseif(isset($vars['value']) && !is_array($vars['value']))
		{
			$vars['selected'][$vars['value']] = TRUE;
		}

		$vars['select_attrs'] = NULL;

		$tag_attributes = [];

		foreach($vars['attrs'] as $attr => $val)
		{
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
<label><?=$title;?> <?php if(isset($indicator)): ?><span class = "required"><?=$indicator;?></span><?php endif; ?>
<select <?=$select_attrs;?>>
<?php foreach($options as $label => $optionValue): ?>
		<option value = "<?=$optionValue; ?>" <?php 
			if (isset($selected[$optionValue])):?>selected="selected"<?php
				endif; ?>><?=$label; ?></option>
<?php endforeach; ?>	
</select>
<span class = "caption"><?=$caption; ?></span>
</label>