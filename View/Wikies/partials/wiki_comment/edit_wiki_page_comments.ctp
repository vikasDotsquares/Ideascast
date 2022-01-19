
<style>
    #comment_uploads_list {
        margin-top: 5px;
    }
</style>
<?php
echo $this->Form->create('WikiPageComment', array('url' => array('controller' => 'wikies', 'action' => 'wiki_page_comment_save',$comment_id), 'class' => 'form-bordered',  'data-async' => "","id"=>"wikipagecommentform", 'enctype' => 'multipart/form-data'));
?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="createModelLabel">Add Blog Comments</h3>
</div>

<!-- POPUP MODAL BODY -->
<div class="modal-body">

<?php
echo $this->Form->input('WikiPageComment.id', [ 'type' => 'hidden','value' => $comment_id]);
echo $this->Form->input('WikiPageComment.project_id', [ 'type' => 'hidden','value' => $project_id]);
echo $this->Form->input('WikiPageComment.wiki_id', [ 'type' => 'hidden','value' => $wiki_id]);
echo $this->Form->input('WikiPageComment.wiki_page_id', [ 'type' => 'hidden','value' => $wiki_page_id]);
if(isset($this->request->data['WikiPageComment']['user_id']) && !empty($this->request->data['WikiPageComment']['user_id'])){
    echo $this->Form->input('WikiPageComment.user_id', [ 'type' => 'hidden','value' =>  $this->request->data['WikiPageComment']['user_id']]);
}else{
    echo $this->Form->input('WikiPageComment.user_id', [ 'type' => 'hidden','value' =>$this->Session->read("Auth.User.id")]);
}
?>
    <div class="form-group">
        <label for="comments" >Description:</label>
            <?php echo $this->Form->textarea('WikiPageComment.description', [ 'type' => 'text', 'class' => 'form-control', 'required' => true, 'div' => false, 'id' => 'title', 'escape' => true, 'placeholder' => 'max chars allowed 1000', 'rows' => 5, 'label' => false]); ?>
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
                echo $this->Form->input('WikiPageCommentDocument.document_name.', ['value' => '', 'type' => 'file', 'label' => false, 'div' => false, 'required' => false, 'class' => 'form-control upload wiki_page_comments_doc_uploads', 'id' => 'file_name', 'placeholder' => 'Upload Multiple Files', "multiple" => "multiple"]);
                ?>

                <span class="text-blue" id="upText">Upload Multiple Documents</span>
            </span>
        </div>

        <ul class="list-group" id="comment_uploads_list">

            <?php
            if (isset($this->request->data["WikiPageCommentDocument"]) && !empty($this->request->data["WikiPageCommentDocument"])) {
                foreach ($this->request->data["WikiPageCommentDocument"] as $val_doc) {
                    echo '<li class="todoimg list-group-item"><a href="' . SITEURL . WIKI_PAGE_DOCUMENT . $val_doc['document_name'] . '" class="todoimglink tipText" title="' . $val_doc['document_name'] . '" download="download" >';
                    echo $val_doc['document_name'];
                    echo '</a><span class="del-img-todo pull-right"><a id="' . $val_doc["id"] . '" title="Click here to delete" class="text-red tipText confirm_wiki_page_doc_delete"  data-file="' . $val_doc['document_name'] . '"   href="javascript:void(0);"> <i class="fa fa-times"></i> </a></span></li>';
                }
            }
            ?>
        </ul>
    </div>
</div>
<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
    <button type="button" id="<?php echo $comment_id;?>" class="btn btn-success submit_wiki_page_comments">Save</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>

<script type="text/javascript" >
    $(document).ready(function () {
         $('body').delegate("textarea[name='data[WikiPageComment][description]']", 'keyup focus', function(event){
			var characters = 1000;
			event.preventDefault();
			var $error_el = $(this).next('.error-message');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})
    });
    $(document).ready(function () {

    })
</script>

