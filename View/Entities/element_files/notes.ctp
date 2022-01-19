
<?php
$onemsg = null;
$datatitle = '';


if( isset( $message ) && !empty($message) && empty($overdue) ){
	$onemsg = 'You cannot add a Note because this Task has been signed off.';
	$datatitle = 'Signed Off';
}

?>
<!-- Indivisual Form -->
<div class="note_form">

	<div data-msg="<?php echo htmlentities($onemsg);?>" class="list-form border bg-warning nopadding <?php echo $class_d;?>">
	   <?php if((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level==1)  || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){ ?>
		<a href="" class="list-group-item clearfix open_form noborder-radius" >
			<span class="pull-left"><i class="asset-all-icon re-NoteBlack"></i>&nbsp; New Note</span>
			<!--<span class="pull-right"><i class="fa fa-plus"></i></span>-->
		</a>
		<?php } else { ?>
		<a   class="list-group-item disabled clearfix   noborder-radius" style="background:#dddddd; border:solid 1px #dddddd ">
			<span class="pull-left"><i class="asset-all-icon re-NoteBlack"></i>&nbsp; New Note</span>
			<!--<span class="pull-right"><i class="fa fa-plus"></i></span>-->
		</a>
		<?php }  ?>

<?php

$pointer_event = '';
if( $ele_signoff == true ){
	$pointer_event = 'signoffpointer';
}

echo $this->Form->create('Notes', array('url' => array('controller' => 'entities', 'action' => 'add_note', $element_id), 'class' => "padding formAddElementNote $pointer_event", 'style' => 'overflow:hidden;', 'enctype' => 'multipart/form-data'));

echo $this->Form->input('Notes.create_activity', [ 'type' => 'hidden','value'=>true]);
?>
<?php
$allow = 'pointer-events:none;';
if((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level==1)  || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){
$allow = 'pointer-events:default;';
}
?>
		<input type="hidden" name="data[Notes][element_id]" class="form-control" value="<?php echo $this->data['Element']['id']; ?>" />


		 <input type="hidden" name="data[Notes][project_id]" class="form-control" value="<?php echo $project_id; ?>" />

		<div class="form-group">
			<label class=" " for=" ">Title:</label>
			<input type="text" name="data[Notes][title]" placeholder="Note title" class="form-control " value="" />
			<span class="error-message text-danger" style=""></span>
		</div>

		<div class="form-group desc-note-block" >
			<label class=" " for=" ">Description:</label>

	<?php
	if($ele_signoff == false ){
			if((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level==1)  || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){ ?>
			<a id="" href="#" class="btn btn-sm btn-success save_note_b save_note submit">
					  Save
			</a>
			<?php }else{ ?>
			<a id="" href="#" class="btn btn-sm btn-success save_note_b  disabled submit">
					  Save
			</a>
	<?php }
	} else {?>
		<a id="" href="#" class="btn btn-sm btn-success save_note_b disabled ">
				  Save
		</a>
	<?php } ?>



			<!--<a class="btn btn-sm btn-danger cancel_update" style=""   id="cancel_update_note">
					 Cancel
			</a>-->

			<textarea rows="12" class="form-control" placeholder="Note description" name="data[Notes][description]" id="note_desc_ck"></textarea>
			<span class="error-message text-danger" style=""> </span>
		</div>



<?php echo $this->Form->end(); ?>
	</div>
</div>



 <div class="table_wrapper clearfix" id="notes_table" data-model="note" data-limit="1">
	<div class="table_head">
		<div class="row">
			<div class="col-sm-3 resp">
				<h5> Title</h5>
			</div>
			<div class="col-sm-3 resp">
				<h5> Creator</h5>
			</div>
			<div class="col-sm-2 resp">
				<h5> Added</h5>
			</div>
			<div class="col-sm-2 resp">
				<h5> Updated</h5>
			</div>
			<div class="col-sm-2 text-center resp">
				<h5> Action</h5>
			</div>
		</div>
	</div>
	<div class="table-rows data_catcher" >
				<?php
				// if( isset($notePage) && !empty($notePage) ) {
				if (isset($this->data['Notes']) && !empty($this->data['Notes'])) {

					foreach ($this->data['Notes'] as $detail) {
						// $data = $detail['ElementNote'];
						$data = $detail;
						//pr($data);
						?>
				<div class="row">
					<div class="col-sm-3 resp">
						<?php echo htmlentities($data['title'], ENT_QUOTES, "UTF-8") ; ?>
					</div>
					<div class="col-sm-3 resp">
					<?php if($data['creater_id'] > 0){ ?>
							<a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $data['creater_id']; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
							<i class="fa fa-user"></i>
							</a>                                                                         
							<?php //echo $data['creator']; ?>
						<?php
							$element_creator = $this->Common->elementNote_creator($data['id'],$project_id,$this->Session->read('Auth.User.id'));

							echo $element_creator;
							}else{
							echo "N/A";
							}
							//$project_id;
							//pr($data);
							  ?>
					</div>

					<div class="col-sm-2 resp">
                    <span class="deta-time-i">
<?php //echo dateFormat($data['created'] );   ?>
<?php //echo date('d M, Y g:iA', strtotime($data['created']));
echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['created'])),$format = 'd M, Y g:iA');
?>
		</span>		</div>

					<div class="col-sm-2 resp"><span class="deta-time-i">
<?php //echo dateFormat($data['modified'] );   ?>
<?php //echo date('d M, Y g:iA', strtotime($data['modified']));
echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['modified'])),$format = 'd M, Y g:iA');
?></span>
					</div>

					<div class="col-sm-2 text-center resp">
						<div class="btn-group ">
						<?php if((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level==1)  || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0) || (isset($is_read_shares) && $is_read_shares >0)){

						//if($ele_signoff == false ){
						?>
							<a href="#" class="update_note tipText" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'update_note', $data['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['id']; ?>" title="Open" data-action="update">
								<i class="showlessblack icon_up"></i>
								<i class="showmoreblack icon_down"></i>
							</a>
						<?php /* } else {?>
						<a href="#" class="btn btn-sm bg-blakish disabled tipText" >
								<i class="fa fa-arrow-down icon_down"></i>
							</a>
						<?php } */ ?>


<a href="javascript:void(0);" class="history_note tipText history" itemid="historynote_<?php echo $data['id']; ?>" itemtype="element_notes" data-id="<?php echo $data['id']; ?>" title="History"  >
								<i class="historyblack"></i>
							</a>

						<?php }else{ ?>
					<a href="#" class="tipText disabled"  title="Open" data-action="update">

								<i class="showmoreblack icon_down"></i>
							</a>

<a href="javascript:void(0);" class="disabled history_note tipText history" itemtype="notes" itemid="historynote_<?php echo $data['id']; ?>"  data-id="<?php echo $data['id']; ?>" data-action="remove"  title="History"  >
								<i class="historyblack"></i>
						</a>

						<?php } ?>

					<?php
					if($ele_signoff == false ){
						if((isset($is_owner) && !empty($is_owner))|| (isset($project_level) && $project_level==1)   || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){ ?>
							<!-- <a href="#" class="btn btn-sm btn-danger remove_note tipText" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'remove_note', $this->data['Element']['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['id']; ?>" title="Remove Note" data-action="remove">
								<i class="fa fa-trash"></i>
							</a> -->
							<a href="#" class="tipText delete_resource" title="Remove Note" data-id="<?php echo $data['id']; ?>" data-msg="Are you sure you want to delete this Note?" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'remove_note', $this->data['Element']['id'], 'admin' => FALSE), TRUE); ?>" data-parent="#notes_table" data-type="note">
                                        <i class="deleteblack"></i>
                                    </a>
							<?php  } else { ?>
								<!--<i class="fa fa-trash"></i>-->
							</a>
							<?php }
					} else {?>
					<a href="#" class="tipText disabled" title="Remove Note" >
                                        <i class="deleteblack"></i>
                                    </a>
					<?php } ?>
						</div>
					</div>

					<div class="col-sm-12 col-sm-12 resp" style="<?php echo $allow; ?>">
						<div class="list-form padding" >
<?php echo $this->Form->create('Notes', array('url' => array('controller' => 'entities', 'action' => 'add_note', $element_id), 'class' => "padding-top formAddElementNote $pointer_event", 'id' => '', 'enctype' => 'multipart/form-data'));



echo $this->Form->input('Notes.create_activity', [ 'type' => 'hidden','value'=>true]);
?>

							<input type="hidden" name="data[Notes][id]" class="form-control" value="<?php echo $data['id']; ?>" />

							<input type="hidden" name="notetitle" class="form-control" value="<?php echo htmlentities($data['title']); ?>" />

							<input type="hidden" name="data[Notes][element_id]" class="form-control" value="<?php echo $data['element_id']; ?>" />

							 <input type="hidden" name="data[Notes][project_id]" class="form-control" value="<?php echo $project_id; ?>" />

							<div class="form-group">
								<label class=" " for=" ">Title:</label>
								<input type="text" name="data[Notes][title]" class="form-control" placeholder="Note title" value="<?php echo htmlentities($data['title']); ?>" />
								<span class="error-message text-danger" style=""></span>
							</div>

							<div class="form-group  desc-note-block">
								<label style="margin-top:8px;" class="" for="note_description_<?php echo $data['id'] ?>">Description:</label>

						<?php
						if($ele_signoff == false ){
								if((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level==1)  || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){ ?>
									<a id="" href="#" class="btn btn-sm btn-success text-right save_note save_note_b submit"> Save </a>

								<?php }else{ ?>
								<!-- <a id="" href="#" class="btn btn-sm btn-success disabled  submit"> Save </a>
									<a class="btn btn-sm btn-danger cancel_update" style=""  id="cancel_update_note"> Close </a>-->
						<?php  }
						} else {
						?>
							<a id="" href="#" class="btn btn-sm btn-success text-right  save_note_b disabled"> Save </a>
						<?php } ?>
								<textarea rows="12" class="form-control note_description" placeholder="Note description" name="data[Notes][description]" id="note_description_<?php echo $data['id'] ?>"><?php echo htmlentities($data['description']); ?></textarea>
								<span class="error-message text-danger" style=""> </span>
							</div>


			<?php echo $this->Form->end(); ?>
						</div>
					</div>

				</div>
<div id="historynote_<?php echo isset($data['id']) && !empty($data['id']) ? $data['id'] :'';; ?>" class="history_update" style="display: none;">
<?php  //include 'activity/update_history.ctp';?>
</div>
		<?php
		}
	}else{
		echo '<span class="nodatashow note">No Notes</span>';
	}
	?>


	</div>
<?php /* if( isset($notePage) && !empty($notePage) ) {   ?>
<div class="ajax-pagination clearfix">

<?php echo $this->element('pagination', array( 'model'=>'ElementNote', 'limit' => 2, 'pageCount' => $notePageCount ));  ?>
</div>

<?php } */ ?>
</div>


<style>

.Editor-editor {
    background-color: #ffffff;
    border: 1px solid #eee;
    border-radius: 0;
    height: 250px;
    overflow-wrap: break-word;
    padding: 1%;
	float: left;
	overflow: hidden;
	width: 100%;
}
 .save_note_b{ float: right; margin-bottom:5px;}
 .desc-note-block .btn-group {
 	display: unset !important;
 }
</style>
<script type="text/javascript">
$('body').delegate("input[name='data[Notes][title]']", "keyup focus", function(event){
	var characters = 50;
	event.preventDefault();
	var $error_el = $(this).parents().find('.error-message:first');
	if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
		$.input_char_count(this, characters, $error_el);
	}
})

</script>