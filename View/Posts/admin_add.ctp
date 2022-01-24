<?php echo $this->Html->script(array('ckeditor/ckeditor')); ?>
<div id="main">
<?php echo $this->Session->flash(); ?>
   <div class="panel panel-primary">
      <div class="panel-heading">
	  
         <h3 class="panel-title">
            Create New Blog
            <?php echo $this->Html->link('Back',array('controller' => 'posts', 'action' => 'index', 'admin'=>true),array('class' => 'btn btn-warning btn-xs pull-right'));?>
         </h3>
		 
      </div>
	  
      <div class="panel-body form-horizontal">
         <div class="row rownot">
            <div class="col-sm-12">
			   <?php echo $this->Form->create(array('url'=>array('controller' => 'posts', 'action' => 'add', 'admin'=>true),'type' => 'file','class'=>'addEdit')); ?>
               <div class="form-content">
                  <div class="page-header">
                     <h3> Create  Blog</h3>
                  </div>
                  <div class="row">
                     <div class="col-sm-9">
                        <div class="form-group">
                           <label for="" class="control-label col-sm-3">Blog Title<span class="star"> * </span></label>
                           <div class="col-sm-9">
							  <?php  echo $this->Form->input('Post.title',array('type'=>'text','label'=>false, 'class'=>"field form-control" ));   ?>
							</div>
                        </div>
                     </div>						 
                  </div>
				  
				  <div class="row">
				  	 <div class="col-sm-9">
                        <div class="form-group">
                           <label for="" class="control-label  col-sm-3">Blog Description<span class="star">*</span></label>
                           <div class="col-sm-9">
						   	  <?php echo $this->Form->input('Post.description', array('type' => 'textarea','label' => false,'div' => false,'class'=>'field form-control ckeditor page_calss'));?>
		  		  			</div>
                        </div>
                     </div> 
                  </div>
				  
				  <div class="row">
				  	  <div class="col-sm-9">
							<div class="form-group">
								<label for="" class="control-label  col-sm-3">Image:</label>
								 <div class="col-sm-9">
									<?php echo $this->Form->input('Post.blog_img', array('type' => 'file', 'accept'=>"image/x-png,image/gif,image/jpeg,image/jpg" , 'label' => false, 'div' => false, 'class' => 'form-control', 'style'=>'height:auto')); 
									echo "<span style='font-size:10px;'>Please upload image with minimum dimension (570px * 150px)</span>";
									?>
								</div>								
							</div>                        
                     </div>
                  </div>
				  
				  <div class="row">
				  	  <div class="col-sm-9">
					 	<div class="form-group">
                           <label for="" class="control-label  col-sm-3">Status</label>
						   <div class="col-sm-9">
						   	  <?php $arr_list = array( '1' => 'Active','0' =>'Inactive', ); ?>
		  		  			  <?php  echo $this->Form->input('Post.status', array('label'=>false, 'options' =>$arr_list,'class'=>"field form-control")); ?>
                           </div>	                           
                        </div>                        
                     </div>
                  </div>
				  
				 <?php $url =  SITEURL."sitepanel/posts/"; ?>
					<div class="modal-footer clearfix">
						<button type="submit" class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Save</button>
						<a class="btn btn-danger" href="<?php echo $url; ?>">Cancel</a>
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