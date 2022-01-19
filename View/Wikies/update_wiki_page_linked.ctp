<?php
$is_full_permission_to_current_login = $this->Wiki->check_permission($project_id,$this->Session->read("Auth.User.id"));
$project_wiki = $this->Wiki->getProjectWiki($project_id, $this->Session->read("Auth.User.id"));
$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read("Auth.User.id"));
$user_project = $this->Common->userproject($project_id, $this->Session->read("Auth.User.id"));
$gp_exists = $this->Group->GroupIDbyUserID($project_id, $this->Session->read("Auth.User.id"));
if (isset($gp_exists) && !empty($gp_exists)) {
    $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
}
?>
<?php
	echo $this->Html->script(array('ckeditor/ckeditor'));
?>
<?php
echo $this->Form->create('WikiPage', array('url' => array('controller' => 'wikies', 'action' => 'update_wiki_page_linked_save',$project_id,$user_id,$wiki_id,$wiki_page_id), 'class' => 'form-bordered','id'=>'WikiPageUpdateWikiPageLinkedForm'));

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="myModalLabel">Update Wiki Page</h3>
</div>
<!-- POPUP MODAL BODY -->
<div class="modal-body">
    <div class="form-group">
        <label class=" " for="txa_title">Title:</label>
        <?php echo $this->Form->input('WikiPage.title', array('label' => false, 'type' => 'text', 'class' => 'form-control title_limit title', 'placeholder' => 'max chars allowed 50', 'id' => 'title'));
        ?>
        <span class="error-message text-danger" ></span>
        <!--<span class="title_error error-message text-danger" ></span>-->
    </div>

    <div class="form-group">
        <label class=" " for="txa_title">Description:</label>
        <?php echo $this->Form->textarea('WikiPage.descriptions', [ 'value'=>$this->request->data['WikiPage']['description'],'class' => 'form-control description_limit description', 'id' => 'description_update_linked', 'escape' => true, 'rows' => 10, 'placeholder' => 'max chars allowed 500']); ?>
        <span class="error-message text-danger" ></span>
        <textarea id="txts_update_link" name="data[WikiPage][description]" style="display:none;" class="description_limit"><?php echo $this->request->data['WikiPage']['description'];?></textarea>
        <!--<span class="title_error error-message text-danger" ></span>-->
    </div>
    <?php
    echo $this->Form->input('WikiPage.id', array('label' => false, 'type' => 'hidden', 'value' => $wiki_page_id));
    echo $this->Form->input('WikiPage.wiki_id', array('label' => false, 'type' => 'hidden', 'value' => $wiki_id));
    echo $this->Form->input('WikiPage.user_id', array('label' => false, 'type' => 'hidden'));
    echo $this->Form->input('WikiPage.project_id', array('label' => false, 'type' => 'hidden', 'value' => $project_id));
    echo $this->Form->input('WikiPage.updated_user_id', array('label' => false, 'type' => 'hidden', 'value' => $this->Session->read('Auth.User.id')));
    ?>
</div>
<div class="modal-footer">
    <button type="submit" id="submitwikipage_linkedSaves" data-id="<?php echo $wiki_page_id; ?>" class="btn btn-success">Save</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>

<?php echo $this->Form->end(); ?>
<script type="text/javascript" >

    $(document).ready(function () {
        CKEDITOR.replace('description_update_linked' );


        $('[data-toggle="popover"]').popover({container: 'body', html: true, placement: "left"});


		$('body').delegate("#title", 'keyup focus', function(event){
			var characters = 50;
			event.preventDefault();
			var $error_el = $(this).parent().next('.error-message');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})

    });


    timer = setInterval(updateDiv, 200);
    function updateDiv() {
        var editorText = CKEDITOR.instances.description_update_linked.getData();
        $('#txts_update_link').html(editorText);
    }

</script>