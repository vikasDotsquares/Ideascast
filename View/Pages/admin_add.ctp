<?php 	
	echo $this->Html->script(array('ckeditor/ckeditor'));
	//echo $this->Html->script(array('jquery-ui')); 
	//echo $this->Html->css(array('jquery-ui-1.10.0.custom.min'));
?>
<div id="main">
   <div class="panel panel-primary">
      <div class="panel-heading">
         <h3 class="panel-title">
            Create New Page
            <?php echo $this->Html->link('Back',array('controller' => 'pages', 'action' => 'index', 'admin'=>true),array('class' => 'btn btn-warning btn-xs pull-right'));?>
         </h3>
      </div>
      <div class="panel-body form-horizontal">
         <div class="row rownot">
            <div class="col-sm-12">
               <?php //echo $this->Form->create(array('enctype' => 'multipart/form-data', 'class'=>'addEdit'));	?>
			   <?php echo $this->Form->create(array('url'=>array('controller' => 'pages', 'action' => 'add', 'admin'=>true),'type' => 'file','class'=>'addEdit')); ?>
               
               <div class="form-content">
                  <div class="page-header">
                     <h3> Create  Page</h3>
                  </div>
                  <div class="row">
                     <div class="col-sm-6">
                        <div class="form-group">
                           <label for="" class="control-label col-sm-3">Page Name<span class="star"> * </span></label>
                           <div class="col-sm-9">
							  <?php  echo $this->Form->input('name',array('type'=>'text','label'=>false, 'class'=>"field form-control" ));   ?>
							</div>
                        </div>
                     </div>
                     <div class="col-sm-6">
                        <div class="form-group">
                           <label for="" class="control-label  col-sm-3">Meta Title</label>
                           <div class="col-sm-9">
							  <?php  echo $this->Form->input('meta_title',array('type'=>'text','label'=>false, 'maxlength'=>'65','class'=>"field form-control"));   ?>
							</div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-6">
                        <div class="form-group">
                           <label for="" class="control-label  col-sm-3">Meta Keywords</label>
                           <div class="col-sm-9">
						   	  <?php  echo $this->Form->input('meta_keywords',array('label'=>false,'type'=>'text','maxlength'=>'160', 'class'=>"field form-control" ));   ?>	
                           </div>
                        </div>
                     </div>
                     <div class="col-sm-6">
					 	<div class="form-group">
                           <label for="" class="control-label  col-sm-3">Status</label>
						   <div class="col-sm-9">
						   	  <?php $arr_list = array( '1' => 'Active','0' =>'Inactive', ); ?>
		  		  			  <?php  echo $this->Form->input('status', array('label'=>false, 'options' =>$arr_list,'class'=>"field form-control")); ?>
                           </div>	
                           
                        </div>
                        
                     </div>
                  </div>
                   
                  <div class="row">
				  	 <div class="col-sm-6">
                        <div class="form-group">
                           <label for="" class="control-label  col-sm-3">Meta Description</label>
                           <div class="col-sm-9">	
							    
								<?php echo $this->Form->input('meta_description', array('type' => 'textarea','label' => false,'maxlength'=>256,'class'=>"field form-control"));?>
                           </div>
                        </div>
                     </div> 
                  </div>
				  
				  
				  <div class="row">
				  	 <div class="col-sm-6">
                        <div class="form-group">
                           <label for="" class="control-label  col-sm-3">Page Content<span class="star">*</span></label>
                           <div class="col-sm-9">
						   	  <?php echo $this->Form->input('content', array('type' => 'textarea','label' => false,'div' => false,'class'=>'field form-control ckeditor page_calss'));?>
		  		  			</div>
                        </div>
                     </div> 
                  </div>
				  
				 <?php $url =  SITEURL."sitepanel/pages/";
					?>
					<div class="modal-footer clearfix">

					<button type="submit" class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Save</button>
					
					
					<?php 
					
					?><a class="btn btn-danger" href="<?php echo $url; ?>">Cancel</a>
					
					<!-- <button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button> -->
				</div>
				  

                  <?php echo $this->Form->end(); ?> 
               </div>
            </div>
         </div>
      </div>
      <div id="dialog"></div>
   </div>
</div>
<div id="dialog"></div>
<!----------------------------->
<script type="text/javascript" >

CKEDITOR.replace( 'PageContent', {
	toolbar: [
	{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
	{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
	{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
	'/',
	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
	{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
	{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
	'/',
	{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
	{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
	{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
	{ name: 'others', items: [ '-' ] },
	{ name: 'about', items: [ 'About' ] }
],	height: 400,
	width: 885,
	

	// NOTE: Remember to leave 'toolbar' property with the default value (null).
});

</script>
<?php /*?><?php 	echo $this->Html->script(array('ckeditor/ckeditor'));?>
<!-- Main -->
<div id="main">
  <!-- Content -->
  <div class="box">
    <!-- Box Head -->
    <div class="box-head">
      <h2 class="left">Add Page</h2>
      <div class="right"><?php echo $this->Html->link('Back',array('controller' => 'pages', 'action' => 'index', 'admin'=>true),array('class' => 'button'));?></div>
    </div>
    <p class="red_star">* Required Field</p>
    <!-- End Box Head -->
    <?php echo $this->Form->create(array('type' => 'file','class'=>'addEdit')); ?>
   
    <!-- Form -->
    <div class="form">
      <div class="form-content">
        <div class="row">
          <label>Page Name<span class="star"> * </span></label>
          <?php  echo $this->Form->input('name',array('label'=>false,'type'=>'text', 'class'=>"field size4" ));   ?>
		  <?php  echo $this->Form->input('redirect',array('label'=>false,'type'=>'hidden', 'class'=>"field size4" ));   ?>
        </div>
        <div class="row">
          <label>Meta Title</label>
								<?php echo $this->Form->input('meta_title', array('label' => false,'maxlength'=>65, 'class'=>"field size4" ));?>
		  
        </div>
        <div class="row">
          <label>Meta Keywords</label>
								<?php echo $this->Form->input('meta_keywords', array('label' => false,'maxlength'=>160, 'class'=>"field size4" ));?>
		 
        </div>
        <div class="row">
          <label>Meta Description</label>
								<?php echo $this->Form->input('meta_description', array('type' => 'textarea','label' => false,'maxlength'=>256));?>
		 
        </div>
        <div class="row">
          <label>Page Content<span class="star"> * </span></label>
			 </div>
             <div class="row">
             &nbsp;
			 </div>    
        <div class="row">
			<?php echo $this->Form->input('content', array('type' => 'textarea','label' => false,'div' => false,'class'=>'ckeditor page_calss'));?>
		 
        </div>   
		<div class="row" style="margin-top:15px;">
        &nbsp;
        </div> 
		<div class="row">
          <label>Status<span class="star"></span></label>
          <?php  echo $this->Form->input('status', array('label'=>false, 'options' => array('0' =>'Inactive', '1' => 'Active'), array('class'=>"field size4")));  ?>
        </div>		
      </div>
    </div>
    <!-- End Form -->
    <!-- Form Buttons -->
    <div class="buttons"> <?php echo $this->Form->submit('Save', array('class' => 'button')); ?> </div>
    <!-- End Form Buttons -->
    </form>
    <?php echo $this->Form->end(); ?> </div>
  <!-- End Box -->
</div>
<!-- End Content -->
<div class="cl">&nbsp;</div>
</div>
<!-- Main -->
<script type="text/javascript" >
CKEDITOR.replace( 'PageContent', {
	toolbar: [
				// Line break - next group will be placed in new line.
		{ name: 'basicstyles', items: [ 'Source', 'Bold', 'Italic', 'Underline' ] },
		{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
		{ name: 'links', items : [ 'Link' ] },
		{ name: 'basicstyles', items: [ 'Font','FontSize'] },
	],
	height: 400,
	width: 885,
});

</script><?php */?>