
<style>
#comment_uploads_list {
    margin-top: 5px;
}
</style>
<?php
	echo $this->Form->create('BlogComment', array('url' => array('controller' => 'team_talks', 'action' => 'edit_blog_comments_list' ), 'class' => 'form-bordered', 'id' => 'BlogComment', 'data-async' => "", 'enctype'=>'multipart/form-data'));
?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h3 class="modal-title" id="createModelLabel">Edit Blog Comments</h3>
</div>

<!-- POPUP MODAL BODY -->
<div class="modal-body">

<?php  echo $this->Form->input('BlogComment.blog_id', [ 'type' => 'hidden', 'class' => 'form-control', 'required'=>true, 'div' => false, 'label' => false ] );
	echo $this->Form->input('BlogComment.user_id', [ 'type' => 'hidden', 'class' => 'form-control', 'required'=>true, 'div' => false, 'label' => false, 'value' => $this->Session->read('Auth.User.id') ] );
	/*echo $this->Form->input('Blog.project_id', [ 'type' => 'hidden', 'class' => 'form-control', 'required'=>true, 'div' => false, 'label' => false] ); */
	echo $this->Form->input('BlogComment.id', [ 'type' => 'hidden', 'class' => 'form-control', 'required'=>true, 'div' => false, 'label' => false ] );
?>
	<div class="form-group">
		<label for="comments" >Description:</label>
		<?php echo $this->Form->textarea('BlogComment.description', [ 'type' => 'text', 'class' => 'form-control', 'required'=>true, 'div' => false, 'id' => 'title', 'escape' => true, 'placeholder' => 'max chars allowed 1000','rows'=>5, 'label' => false ] );   ?>
		<span class="error-message text-danger" ></span>
	</div>
    <div class="form-group ">
        <label for="doc_file_sub">Attachment:</label>
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-upload"></i>
            </div>

            <span title="" class="docUpload icon_btn bg-white border-radius-right tipText" data-original-title="Click to upload multiple files">
                <?php
					echo $this->Form->input('BlogDocument.document_name.', ['value' => '', 'type' => 'file', 'label' => false, 'div' => false, 'required' => false, 'class' => 'form-control upload comments_doc_uploads', 'id' => 'file_name', 'placeholder' => 'Upload Multiple Files', "multiple" => "multiple"]);
                ?>

                <span class="text-blue" id="upText">Upload Multiple Documents</span>
            </span>
        </div>

        <ul class="list-group" id="comment_uploads_list">

            <?php
            //pr($this->request->data);
             if (isset($this->request->data["BlogDocument"]) && !empty($this->request->data["BlogDocument"])) {
                foreach ($this->request->data["BlogDocument"] as $val_doc) {
                    echo '<li class="todoimg list-group-item"><a href="' . SITEURL . TODOCOMMENT . $val_doc['document_name'] . '" class="todoimglink tipText" title="' . $val_doc['document_name'] . '" download="download" >';
                    echo $val_doc['document_name'];
                    echo '</a><span class="del-img-todo pull-right"><a id="' . $val_doc["id"] . '" title="Click here to delete" class="text-red tipText confirm_blogdoc_delete"  data-file="' . $val_doc['document_name'] . '"   href="javascript:void(0);"> <i class="fa fa-times"></i> </a></span></li>';
                }
            }
            ?>
        </ul>
    </div>
</div>
<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
	<button type="button"  class="btn btn-success submit_blog_edit_comments_list">Update</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>

<script type="text/javascript" >
    $(document).ready(function()  {
        var characters = 1000;
        $("#title").keyup(function(){
            var $ts = $(this)
            if($(this).val().length > characters){
                $(this).val($(this).val().substr(0, characters));
            }
            var remaining = characters -  $(this).val().length;
            $(this).next().html("Char 1000 , <strong>" +$(this).val().length+ "</strong> characters entered.");
            if(remaining <= 10)
            {
                $(this).next().css("color","red");
            }
            else
            {
                $(this).next().css("color","red");
            }
        });


    $('#modal_edit_blogpost').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });



});
</script>
