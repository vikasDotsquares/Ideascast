<?php 
$onemsg = null;
$datatitle = '';


if( isset( $message ) && !empty($message) && empty($overdue) ){
	$onemsg = 'You cannot add a Link because this Task has been signed off.';
	$datatitle = 'Signed Off';
}
 
?>
                                                    <div  class="link_form"   >
                                                        <div class="list-form border bg-warning nopadding <?php echo $class_d;?>" data-msg="<?php echo htmlentities($onemsg);?>" >
<?php 
if((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level==1)  || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){ ?>
                                                            <a href="" class="list-group-item clearfix open_form noborder-radius" >
                                                                <span class="pull-left"><i class="asset-all-icon re-LinkBlack "></i>&nbsp; New Link</span>
                                                                <!--<span class="pull-right"><i class="fa fa-plus"></i></span>-->
                                                            </a>
															 <?php echo $this->Form->create('Links', array('url' => array('controller' => 'entities', 'action' => 'add_links', $element_id), 'class' => 'padding formAddElementLinks ', 'id' => 'formAddElementLinks' ,'style'=>"display:none")); ?>
<?php }else{ ?>
															<a class="list-group-item clearfix  disabled noborder-radius"  style="background:#dddddd; border:solid 1px #dddddd ">
                                                                <span class="pull-left"><i class="asset-all-icon re-LinkBlack"></i>&nbsp; New Link</span>
                                                                <!--<span class="pull-right"><i class="fa fa-plus"></i></span>-->
                                                        </a>
														<?php echo $this->Form->create('Links', array('url' => array('controller' => 'entities', 'action' => 'add_links', $element_id), 'class' => 'padding formAddElementLinks ', 'id' => 'formAddElementLinks','style'=>"display:none")); ?>
<?php }  ?>
<?php echo $this->Form->input('perform_action', [ 'type' => 'hidden', 'value' => 'create']); ?>
<?php echo $this->Form->input('Links.id', [ 'type' => 'hidden']);

echo $this->Form->input('Links.create_activity', [ 'type' => 'hidden','value'=>true]);
?>

<?php echo $this->Form->input('Links.element_id', [ 'type' => 'hidden', 'value' => $this->data['Element']['id']]); ?>
<?php echo $this->Form->input('Links.project_id', [ 'type' => 'hidden', 'value' => $project_id]); ?>

                                                            <div  class="row" >
                                                                <div  class="col-md-5" >
                                                                    <div class="input-group">
                                                                        <div class="input-group-addon">
                                                                            <i class="fa fa-font"></i>
                                                                        </div>
<?php echo $this->Form->input('Links.title', [ 'type' => 'text', 'label' => false, 'div' => false, 'required' => false, 'class' => 'form-control', 'id' => 'link_title', 'placeholder' => 'Link title']); ?>
                                                                    </div>
                                                                    <span style="" class="error-message text-danger"> </span>

                                                                   <?php /* ?> <div class="mg_top sm_top lg_top" id="link_options" >
                                                                        <!--<label for="" class="text-normal disp-block">Data Options:</label>-->
                                                                        <div class="radio-family plain" >
                                                                            <div class="radio radio-danger">
                                                                                <input type="radio" name="data[Links][link_type]" id="refer_external" value="1" checked="checked" data-collapse="refer_external_content">
                                                                                <label for="refer_external"> External URL </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="radio-family plain">
                                                                            <div class="radio radio-danger">
                                                                                <input type="radio" name="data[Links][link_type]" id="refer_embeded" value="2" data-collapse="refer_embeded_content">
                                                                                <label for="refer_embeded"> Embeded Code </label>
                                                                            </div>
                                                                        </div>
                                                                    </div> <?php */ ?>
                                                                </div>

                                                                <div  class="col-lg-5 col-md-5 mg_top" >

                                                                    <div class="refer_options" style="display: block;" id="refer_external_content" >
                                                                        <div class="input-group">
                                                                            <div class="input-group-addon">
                                                                                <i class="fas fa-external-link-alt"></i>
                                                                            </div>
                                                                            <?php echo $this->Form->input('Links.references', [ 'type' => 'text', 'label' => false, 'div' => false, 'required' => false, 'class' => 'form-control ', 'id' => 'link_reference', 'placeholder' => 'Link URL']); ?>
                                                                        </div>
                                                                        <span style="" class="error-message text-danger"> </span>
                                                                    </div>

                                                                    <div class="refer_options" style="display: none;" id="refer_media_content" >
                                                                        <div class="input-group">
                                                                            <div class="input-group-addon">
                                                                                <i class="fa fa-play-circle"></i>
                                                                            </div>
<?php echo $this->Form->input('Links.media_link', [ 'type' => 'text', 'label' => false, 'div' => false, 'required' => false, 'class' => 'form-control ', 'id' => 'media_link', 'placeholder' => 'Media URL']); ?>
                                                                        </div>
                                                                        <span style="" class="error-message text-danger"> </span>
                                                                    </div>

                                                                    <div class="refer_options" style="display: none;" id="refer_embeded_content" >
                                                                        <textarea rows="5" class="form-control link_embeded" placeholder="Embeded Code" name="data[Links][embed_code]" id="embed_code" style="width: 100% !important; resize: vertical;" ></textarea>
                                                                        <span class="error-message text-danger"> </span>
                                                                    </div>

                                                                </div>

                                                                <div  class="col-lg-2 col-sm-12 mg_top link-but-md" >
														<?php 
															if($ele_signoff == false ){
																if((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level==1)  || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){  ?>
                                                                    <a class="btn btn-sm btn-success save_link" href="#" id="save_link">
                                                                     Save
                                                                    </a>

																<?php }else{ ?>
																 <a class="btn btn-sm btn-success disabled" href="#" id="save_link">
                                                                     Save
                                                                    </a>
																<?php }
																} else { 	
																?>
																<a class="btn btn-sm btn-success disabled" href="#">
                                                                     Save
                                                                    </a>
																<?php } ?>

                                                                    <a class="btn btn-sm btn-danger cancel_update" style="" href="#" id="cancel_update_link">
                                                                     Clear
                                                                    </a>
                                                                </div>
                                                            </div>
<?php echo $this->Form->end(); ?>
                                                        </div>
                                                    </div>

<div class="table_wrapper" id="links_table" data-model="link" data-limit="3">

                                                        <div class="table_head">
                                                            <div class="row">
                                                                <div class="col-sm-2 resp">
                                                                    <h5> Title</h5>
                                                                </div>
                                                                <div class="col-sm-2 resp">
                                                                    <h5> Link </h5>
                                                                </div>
																<div class="col-sm-2 resp">
                                                                    <h5> Creator </h5>
                                                                </div>
                                                                <div class="col-sm-2 resp"><h5> Added</h5></div>
                                                                <div class="col-sm-2 resp"><h5> Updated</h5></div>
                                                                <div class="col-sm-2 text-center resp">
                                                                    <h5> Action</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="table-rows data_catcher" style="max-height:734px; overflow-y:auto;">
<?php
if (isset($this->data['Links']) && !empty($this->data['Links'])) {

    foreach ($this->data['Links'] as $detail) {
        $data = $detail;
        ?>
                                                                    <div class="row" data-id="<?php echo $data['id']; ?>">


                                                                        <div class="col-sm-2 resp" data-value="<?php echo htmlentities($data['title']); ?>">
                                                                            <?php
                                                                            echo (strlen($data['title']) > 32) ? substr($data['title'], 0, 32) . '...' : $data['title'];
                                                                            ?>
                                                                        </div>

                                                                        <div class="col-sm-2 resp link_reff">

        <?php
        if ($data['link_type'] == 1) {
            echo (strlen($data['references']) > 70) ? substr($data['references'], 0, 70) . '...' : htmlentities($data['references']);
        } else {
            $html = htmlentities($data['embed_code'], ENT_QUOTES);
            echo (strlen($html) > 90) ? substr($html, 0, 90) . '...' : $html;
        }
        ?>
                                                                        </div>
																		<div class="col-sm-2 resp">
																		<?php
																		if($data['creater_id'] > 0){
																		?>
<a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $data['creater_id']; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                    <i class="fa fa-user"></i>
                </a> 																				<?php
																				$element_creator = $this->Common->elementLink_creator($data['id'],$project_id,$this->Session->read('Auth.User.id'));

																				echo $element_creator;
																				}else{
																				echo "N/A";
																				}
                                                                                ?>
                                                                        </div>
                                                                        <div class="col-sm-2 resp"><span class="deta-time-i">
        <?php
			 echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['created'])),$format = 'd M, Y g:iA');
		?></span>
                                                                        </div>

                                                                        <div class="col-sm-2 resp">
                                                                             <span class="deta-time-i">   <?php //echo date('d M, Y g:iA', strtotime($data['modified']));
																				echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['modified'])),$format = 'd M, Y g:iA');
																				?></span>
                                                                        </div>

                                                                        <div class="col-sm-2 text-center resp">
                                                                            <div class="btn-group">
																			<?php 
																			if($ele_signoff == false ){
																				if((isset($is_owner) && !empty($is_owner))  || (isset($project_level) && $project_level==1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){ ?>
																					<a href="#" class=" update_link tipText" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'update_link', $data['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['id']; ?>" title="Update"  data-action="update">
                                                                                    <i class="edit-icon"></i>
																					</a>
																			<?php }else{ ?>
																				<a href="#" class=" disabled  tipText"   title="Update"  data-action="update">
                                                                                    <i class="edit-icon"></i>
                                                                                </a>
                                                                             <?php }
																				} else {?>
																				<a href="#" class=" disabled  tipText"   title="Update"  data-action="update">
                                                                                    <i class="edit-icon"></i>
                                                                                </a>	
																			<?php }
                                                                                if ($data['link_type'] == 1) {
                                                                                    $hreff = '';
                                                                                    $chkLinks = explode("//", $data['references']);
                                                                                    if (isset($chkLinks['1']) && !empty($chkLinks['1'])) {
                                                                                        $hreff = $data['references'];
                                                                                    } else {
                                                                                        $hreff = "http://" . $data['references'];
                                                                                    }
                                                                                    ?>
                                                                                    <a class="visit_link tipText " title="Visit Link" target="_blank" href="<?php echo $hreff; ?>"  >
                                                                                        <i class="openlinkicon"></i>
                                                                                    </a>
            <?php
        } else if ($data['link_type'] == 2) {
			
			
            ?>
                                                                                    <a class="btn btn-danger btn-sm play_embeded tipText" title="Open Embeded Video" data-toggle="modal" data-target="#modal_medium"  data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'play_media', $data['id'], 'admin' => FALSE), TRUE); ?>">
                                                                                        <i class="fa fa-play" ><span class=" " style="position: absolute; top: -0px;">E</span></i>
                                                                                    </a>
			 
        <?php } ?>

				<?php if((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level==1)  || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){
							

                                    ?>															<a href="javascript:void(0);" class="history_link tipText history" itemtype="element_links" itemid="historylink_<?php echo $data['id']; ?>"  data-id="<?php echo $data['id']; ?>" data-action="remove"  title="History"  >
                                                                                    <i class="historyblack"></i>
                                                                            </a>
                                                                                <!-- <a href="#" class="btn btn-sm btn-danger remove_link tipText"  data-id="<?php echo $data['id']; ?>" data-action="remove"  title="Remove" data-msg="Are you sure you want to delete this Link?" data-toggle="confirmation" data-header="Authentication"  data-auth-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'auth_check', 'admin' => FALSE), TRUE); ?>"  data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'remove_link', $data['id'], 'admin' => FALSE), TRUE); ?>" >
                                                                                    <i class="fa fa-trash"></i>
                                                                                </a> -->
								<?php if($ele_signoff == false ){ ?>																
																				
                                    <a href="#" class="tipText delete_resource" title="Remove" data-id="<?php echo $data['id']; ?>" data-msg="Are you sure you want to delete this Link?" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'remove_link', $data['id'], 'admin' => FALSE), TRUE); ?>" data-parent="#links_table" data-type="link">
                                        <i class="deleteblack"></i>
                                    </a>
								<?php } else { ?>
								 <a href="#" class="disabled"  data-id="<?php echo $data['id']; ?>"    title="Remove" data-msg="Are you sure you want to delete this Link?" data-toggle="Delete Link" data-header="Authentication"   >
									<i class="deleteblack"></i>
								</a>
								<?php } ?>

															<?php  }else { ?>
																			<a href="javascript:void(0);" class="disabled history_link tipText history" itemtype="element_links" itemid="historylink_<?php echo $data['id']; ?>"  data-id="<?php echo $data['id']; ?>" data-action="remove"  title="History"  >
                                                                                    <i class="historyblack"></i>
                                                                            </a>

                                                                            <a href="#" class="disabled tipText"  data-id="<?php echo $data['id']; ?>"    title="Remove" data-msg="Are you sure you want to delete this Link?" data-toggle="Delete Link" data-header="Authentication"   >
                                                                                    <i class="deleteblack"></i>
                                                                                </a>
															<?php } ?>
                                                                            </div>
                                                                        </div>

                                                                        <input type="hidden" class="l_type" value="<?php echo $data['link_type'] ?>">
                                                                    <?php if ($data['link_type'] == 1) { ?>
                                                                            <textarea class="hide" name="" id="hdata_<?php echo $data['id'] ?>" ><?php echo $data['references'] ?></textarea>
                                                                    <?php } else if ($data['link_type'] == 2) {
                                                                        ?>
                                                                            <textarea class="hide" name="" id="hdata_<?php echo $data['id'] ?>" ><?php echo $data['embed_code'] ?></textarea>
                                                                <?php } ?>

                                                                    </div>
                <div id="historylink_<?php echo isset($data['id']) && !empty($data['id']) ? $data['id'] :'';; ?>" class="history_update" style="display: none;">
                    <?php  //include 'activity/update_history.ctp';?>
                </div>

                                                            <?php
                                                            }
                                                        }else{
															echo '<span class="nodatashow link"> No links <div>';
														}
                                                        ?>
                                                        </div>

</div>
<script type="text/javascript">
$('body').delegate('#link_title', 'keyup focus', function(event){
	var characters = 50;
	event.preventDefault();
	var $error_el = $(this).parents(".formAddElementLinks").find('.error-message:first');
	if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
		$.input_char_count(this, characters, $error_el);
	}
})
</script>



