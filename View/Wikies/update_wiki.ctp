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
<?php echo $this->Form->create('Wiki',array('url' => array('controller' =>'wikies','action'=>'update_wiki_save',$project_id,$user_id,$wiki_id),'class' => 'form-bordered' )); ?>
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h3 class="modal-title" id="myModalLabel">Update Wiki</h3>
		</div>
		<!-- POPUP MODAL BODY -->
		<div class="modal-body">
			<div class="form-group">
				<label class=" " for="txa_title">Title:</label>
				<?php echo $this->Form->input('Wiki.title',array('label' => false, 'type'=>'text','class'=>'form-control title_limit title','placeholder' => 'max chars allowed 50', 'id' => 'title'));
				  ?>
				<span class="error-message text-danger" ></span>
			</div>
			<div class="form-group">
				<label class=" " for="txa_title">Description:</label>
				<?php echo $this->Form->textarea('Wiki.descriptions', ['value'=>strip_tags($this->request->data['Wiki']['description']) ,'class'	=> 'form-control description_limit description', 'id' => 'description_wiki_update', 'escape' => true, 'rows' => 10, 'placeholder' => 'max chars allowed 500' ] );   ?>
				<span class="error-message text-danger" ></span>
                                <textarea id="txts_uwiki" name="data[Wiki][description]" style="display:none;" class="description_limit"><?php echo $this->request->data['Wiki']['description'];?></textarea>
			</div>
			<div class="form-group">
				<label class=" " for="txa_title">Type:</label>
				<div class="radio radio-warning">
                                        <?php
                                        $wiki_type_open = false;$wiki_type_limited = false;$wiki_status_publish = false;$wiki_status_draft = false;

                                        if(isset($this->request->data['Wiki']['wtype']) && $this->request->data['Wiki']['wtype'] == 0){
                                            $wiki_type_limited = 'checked="checked"';
                                        }else{
                                            $wiki_type_open = 'checked="checked"';
                                        }
                                        if(isset($this->request->data['Wiki']['status']) && $this->request->data['Wiki']['status'] == 0){
                                            $wiki_status_draft = 'checked="checked"';
                                        }else{
                                            $wiki_status_publish = 'checked="checked"';
                                        }


                                        ?>
					<input type="radio"  id="wiki_type_open" <?php echo $wiki_type_open; ?> name="data[Wiki][wtype]" class="fancy_input" value="1"   />
					<label class="fancy_labels" for="wiki_type_open" style="width:72px;">Open</label>

					<input type="radio" <?php echo $wiki_type_limited; ?> id="wiki_type_limited" name="data[Wiki][wtype]" class="fancy_input" value="0"   />
					<label class="fancy_labels" for="wiki_type_limited" style="width:122px;">Limited (Owners)</label>
				</div>
				<span class="error-message text-danger" ></span>
			</div>
			<div class="form-group">
				<label class=" " for="txa_title">Page Status:</label>

				<div class="radio radio-warning">
					<input type="radio" <?php echo $wiki_status_publish; ?> id="wiki_status_publish" name="data[Wiki][status]" class="fancy_input" value="1"   />
					<label class="fancy_labels" for="wiki_status_publish" style="width:82px;">Publish</label>

                                        <input type="radio" <?php echo $wiki_status_draft; ?> id="wiki_status_draft" name="data[Wiki][status]" class="fancy_input" value="0"   />
					<label class="fancy_labels" for="wiki_status_draft" style="width:82px;">Draft</label>
				</div>
				<span class="error-message text-danger" ></span>
			</div>
			<?php

				echo $this->Form->input('Wiki.project_id',array('label' => false, 'type'=>'hidden', 'value'=> $project_id ));
                                echo $this->Form->input('Wiki.id',array('label' => false, 'type'=>'hidden', 'value'=> $wiki_id ));
                                echo $this->Form->input('Wiki.updated_user_id',array('label' => false, 'type'=>'hidden', 'value'=> $this->Session->read('Auth.User.id') ));
			?>
		</div>
		<div class="modal-footer">
			<button type="submit" id="submitSave" class="btn btn-success">Update</button>
			<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
		</div>

<?php echo $this->Form->end(); ?>

<script type="text/javascript" >

    $(document).ready(function(){
		var des_characters = 500;
		CKEDITOR.replace( 'description_wiki_update' )

		/*$('body').delegate("#title", 'keyup focus', function(event){
			var characters = 50;
			event.preventDefault();
			var $error_el = $(this).parent().next('.error-message');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})*/

    });

	timer = setInterval(updateDiv,100);
    function updateDiv(){
        var editorText = CKEDITOR.instances.description_wiki_update.getData();
        $('#txts_uwiki').html(editorText);
    }

</script>