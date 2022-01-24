<?php 
if( isset($response['do_list_id']) && !empty($response['do_list_id']) ) { 
?>

<?php
	echo $this->Form->create('DoListComment', array('url' => array('controller' => 'todos', 'action' => 'save_comment' ), 'class' => 'form-bordered', 'id' => 'modelFormManageComment', 'data-async' => ""));
?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h3 class="modal-title" id="createModelLabel">
		<?php if(isset($response['comment_id']) && !empty($response['comment_id']) ){
			echo 'Edit Comment';
			}else {
			echo 'Add Comment';
		} ?>
	</h3>
	
</div>

<!-- POPUP MODAL BODY -->
<div class="modal-body">
	
	<?php echo $this->Form->input('DoListComment.do_list_id', [ 'type' => 'hidden', 'class' => 'form-control', 'required'=>true, 'div' => false, 'label' => false, 'value' => $response['do_list_id'] ] ); ?>
	<?php echo $this->Form->input('DoListComment.id', [ 'type' => 'hidden', 'class' => 'form-control', 'required'=>true, 'div' => false, 'label' => false ] ); ?>
	<?php echo $this->Form->input('DoListComment.user_id', [ 'type' => 'hidden', 'class' => 'form-control', 'required'=>true, 'div' => false, 'label' => false, 'value' => $this->Session->read('Auth.User.id') ] ); ?>
	
	<div class="form-group"> 
		<label for="comments" >Comments:</label>
		<?php echo $this->Form->textarea('DoListComment.comments', [ 'type' => 'text', 'class' => 'form-control', 'required'=>false, 'div' => false, 'id' => 'comments', 'escape' => true, 'placeholder' => 'max chars allowed 1000', 'label' => false, 'rows' => 5 ] );   ?>
		<span class="error-message text-danger" ></span>
	</div>
	
	
    <div class="form-group ">
        <label for="doc_file_sub">Attachment :</label>
        <div class="input-group">
            <div class="input-group-addon">
                <i class="uploadblackicon"></i>
            </div>
			
            <span title="" class="docUpload icon_btn bg-white border-radius-right tipText" data-original-title="Click to upload multiple files">
                <?php
				echo $this->Form->input('DoListCommentUpload.file_name.', ['value' => '', 'type' => 'file', 'label' => false, 'div' => false, 'required' => false, 'class' => 'form-control upload comment-uploads', 'id' => 'file_name', 'placeholder' => 'Upload Multiple Files', "multiple" => "multiple"]);
                ?>
				
                <span class="text-blue" id="upText">Upload Multiple Documents</span>
            </span>
        </div>
		
        <ul class="list-group" id="comment_uploads_list">
        
            <?php
            //pr($this->request->data);
            if (isset($this->request->data["DoListCommentUpload"]) && !empty($this->request->data["DoListCommentUpload"])) {
                foreach ($this->request->data["DoListCommentUpload"] as $val_doc) {
                    echo '<li class="todoimg list-group-item"><a href="' . SITEURL . TODOCOMMENT . $val_doc['file_name'] . '" class="todoimglink tipText" title="' . $val_doc['file_name_original'] . '" download  >';
                    echo $val_doc['file_name_original'];
                    echo '</a><span class="del-img-todo pull-right"><a id="' . $val_doc["id"] . '" title="Click here to delete" class="text-red tipText confirm_doc_delete"  data-file="' . $val_doc['file_name'] . '"   href="javascript:void(0);"> <i class="fa fa-times"></i> </a></span></li>';
                }
            }
            ?>    
            
        </ul>
		
		
    </div>
	
</div>

<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
	<button type="submit"  class="btn btn-success submit_comment">Save</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>
<?php } ?>
<script type="text/javascript" >
    $(document).ready(function()  {

		$('a[href="#"][data-toggle="modal"]').attr('href', 'javascript:;');

        $('body').delegate('#comments', 'keyup focus', function(event){
            var characters = 1000;

            event.preventDefault();
            var $error_el = $(this).parent().find('.error-message');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })
		setTimeout(function(){
			$('#comments').trigger('focus');
		},500)
		//$('#comments').trigger('click');
    });

</script>
