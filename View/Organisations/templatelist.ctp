<?php $html = '';
if( isset($templatelist) && !empty($templatelist) && count($templatelist) > 0 ){
	
}
echo $this->Form->input('User.managedomain_id ', array('options' =>$templatelist,  'empty' => 'Select ', 'label' => false, 'div' => false, 'class' => 'form-control','id'=>'userTemplateid','multiple'=>'multiple','required')); ?>
