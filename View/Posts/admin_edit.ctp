<?php echo $this->Html->script(array('ckeditor/ckeditor'));?>
<?php echo $this->Session->flash(); ?>
<div id="main">
<?php echo $this->Session->flash(); ?>
   <div class="panel panel-primary">
      <div class="panel-heading">
         <h3 class="panel-title">
            Edit <?php echo $this->data['Post']['title'];?>
            <?php //echo $this->Html->link('Back',array('controller' => 'posts', 'action' => 'index', 'admin'=>true),array('class' => 'btn btn-warning btn-xs pull-right'));?>
         </h3>
      </div>
      <div class="panel-body form-horizontal">
         <div class="row rownot">
            <div class="col-sm-12">
			   <?php echo $this->Form->create(array('url'=>array('controller' => 'posts', 'action' => 'edit',$this->data['Post']['id'], 'admin'=>true),'type' => 'file','class'=>'addEdit')); ?>
               <?php  echo $this->Form->hidden('Post.id',array('label'=>false));   ?>
               <div class="form-content">
                  <div class="page-header">
                     <h3>Edit Blog</h3>
                  </div>
                  <div class="row">
                     <div class="col-sm-9">
                        <div class="form-group">
                           <label for="" class="control-label col-sm-3">Blog Title<span class="star"> * </span></label>
                           <div class="col-sm-9">
							  <?php  echo $this->Form->input('Post.title',array('type'=>'text','label'=>false,  'class'=>"field form-control" ));

							  ?>
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
									<?php echo $this->Form->input('Post.blog_img', array('type' => 'file', 'label' => false, 'div' => false, 'class' => 'form-control','required'=>false, 'accept'=>"image/x-png,image/gif,image/jpeg,image/jpg", 'style'=>'height:auto'));
									echo "<span style='font-size:10px;'>Please upload image with minimum dimension (570px * 150px)</span>";
									?>
								</div>
							</div>
                     </div>
                  </div>
				  <?php
				  if( isset($this->request->data['Post']['blog_img']) && !empty($this->request->data['Post']['blog_img']) && empty($this->request->data['Post']['blog_img']['name']) && file_exists(WWW_ROOT.POST_RESIZE_PIC_PATH.$this->request->data['Post']['blog_img'] ) ){
				  ?>
				  <div class="row">
				  	  <div class="col-sm-9">
							<div class="form-group">
								<label for="" class="control-label  col-sm-3"></label>
								 <div class="col-sm-9">
									<div class="post-thumbnail">
										<img id="PostImage" data-postimage="<?php echo $this->request->data['Post']['blog_img']; ?>" data-blogid="<?php echo $this->request->data['Post']['id'];?>" src="<?php echo SITEURL.POST_PIC_PATH.$this->request->data['Post']['blog_img']; ?>" data-deleteimageurl="<?php echo SITEURL.'posts/deletepostimage'?>" >
										<a href="javascript:" data-tooltip="tooltip" title="Delete Image" id="closeimg"><i class="fa fa-close"></i></i></a>
										<a data-toggle="modal" id="closeimg" title="Delete Image"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-close"></i></a>


									</div>
								</div>
							</div>
                     </div>
                  </div>
				  <?php } ?>
				  <div class="row">
                     <div class="col-sm-9">
					 	<div class="form-group">
                           <label for="" class="control-label  col-sm-3">Status</label>
						   <div class="col-sm-9">
						   	  <?php $arr_list = array( '0' =>'Inactive', '1' => 'Active'); ?>
		  		  			  <?php  echo $this->Form->input('Post.status', array('label'=>false, 'options' =>$arr_list,'class'=>"field form-control")); ?>
                           </div>
                        </div>
                     </div>
                  </div>
				  	<?php $url =  SITEURL."sitepanel/posts/"; ?>
					<div class="modal-footer clearfix">
					<button type="submit" class="btn btn-success">Save</button>
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

$(function(){
	$('body').delegate('#closeimg','click',function(){

		var blogid = $('#PostImage').data('blogid');
		var blogImg = $('#PostImage').data('postimage');
		var delurl = $('#PostImage').data('deleteimageurl');


		BootstrapDialog.show({
			title: 'Confirmation',
			message: 'Are you sure, you would like to delete this image?',
			type: BootstrapDialog.TYPE_DANGER,
			draggable: true,
			buttons: [
				{
					//icon: '',
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: true,
					action: function (dialogRef) {
						$.when(
						//if(blogid != '' && blogImg != ''){
							$.ajax({
									url: delurl,
									type: "POST",
									data: $.param({blogid:blogid,blogImg:blogImg}),
									global: true,
									success: function (response) {
										console.log(response);
										if(response == 'success'){
											location.reload();
										}
									}
							})
						//}
						).then(function( data, textStatus, jqXHR ) {
							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							dialogRef.getModalBody().html('<div class="loader"></div>');

						})
					}
				},
				{
					label: ' No',
					//icon: '',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}

				}
			]
		})

		//console.log(delurl);


   })
});


//config.extraPlugins = 'image,dialog';
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
<style>
.post-thumbnail {
    width:100px;
    height:auto;
    position:relative;
}

.post-thumbnail img {
    max-width:100%;
    max-height:100%;
}

.post-thumbnail a .fa.fa-close {
	font-size: 14px;
	color: #f00;
	border: solid 1px #f00;
	border-radius: 50%;
	padding: 2px;
	background: #fff;
	cursor: pointer;
}

.post-thumbnail a {
	display: block;
	width: 10px;
	height: 10px;
	position: absolute;
	top: -8px;
	right: -5px;

}

</style>