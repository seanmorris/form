<?php
namespace SeanMorris\Form\Test\Extension\Theme;
class AvatarField extends \SeanMorris\Form\Theme\FileField
{	
	public function preprocess(&$vars)
	{

		$vars['attrs']['type'] = 'file';
		$vars['src'] = NULL;
		if(isset($vars['value'], $vars['value']->tmp_name)
			&& file_exists($vars['value']->tmp_name)
		){
			$vars['src'] = 'data:image/png;base64,' . base64_encode(
				file_get_contents($vars['value']->tmp_name)
			);
		}
	}
}
__halt_compiler(); ?>
<?php print static::render($vars, 1); ?>
<?php if($src):?>
<img src = "<?=$src;?>" />
<?php endif; ?>