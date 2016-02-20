<?php
namespace SeanMorris\Form\Theme\Form;
class SubmitField extends Field
{

}
__halt_Compiler();
?>
<input type = "submit" name = "submit" value = "<?=$title;?>" />
<?php if(isset($caption)): ?>
	<span class = "caption"><?=$caption; ?></span>
<?php endif;?>
