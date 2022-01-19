<?php
$onemsg = null;
$datatitle = '';


if( isset( $message ) && !empty($message) && empty($overdue) ){
	$onemsg = 'You cannot add a Mind Map because this Task has been signed off.';
	$datatitle = 'Signed Off';
}

?>
 <!-- Indivisual Form -->
                                                    <div class="mindmap_form">

                                                        <div data-msg="<?php echo htmlentities($onemsg);?>"  class="list-form border bg-warning nopadding <?php echo $class_d;?>">

<?php if((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level==1)  || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){ ?>
															<a href="" class="list-group-item clearfix open_form noborder-radius" >
                                                                <span class="pull-left"><i class="asset-all-icon re-MindMapBlack"></i>&nbsp; New Mind Map</span>
                                                                <span class="pull-right"><!--<i class="fa fa-plus"></i>--></span>
                                                            </a>
<?php }else { ?>
															<a  style="background:#dddddd; border:solid 1px #dddddd "  class="list-group-item clearfix  disabled noborder-radius" >
                                                                <span class="pull-left"><i class="asset-all-icon re-MindMapBlack"></i>&nbsp; New Mind Map</span>
                                                                <span class="pull-right"><!--<i class="fa fa-plus"></i>--></span>
                                                            </a>

<?php } ?>

<?php
$pointer_event = '';
if( $ele_signoff == true ){
	$pointer_event = 'signoffpointer';
}

echo $this->Form->create('Mindmaps', array('url' => array('controller' => 'entities', 'action' => 'add_mindmap', $element_id), 'class' => "padding formAddElementMindmap $pointer_event", 'style' => '', 'enctype' => 'multipart/form-data'));


echo $this->Form->input('ElementMindmap.create_activity', [ 'type' => 'hidden','value'=>true, 'id'=> 'emca']);


?>
                                                            <input type="hidden" name="data[ElementMindmap][element_id]" class="form-control" value="<?php echo $this->data['Element']['id']; ?>" />
															                                                            <input type="hidden" name="data[ElementMindmap][project_id]" class="form-control" value="<?php echo $project_id; ?>" />

                                                            <div class="form-group">
                                                                <label class=" " for=" ">Title:</label>
                                                                <input type="text" name="data[ElementMindmap][title]" placeholder="Mind Map title" class="form-control" value="" />
                                                                <span class="error-message text-danger" style=""></span>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class=" " for=" ">Description:</label>
                                                                <textarea rows="10" class="form-control" placeholder="Mind Map description" name="data[ElementMindmap][description]" id="mindmap_desc"></textarea>
                                                                <span class="error-message error text-danger" style=""> </span>
                                                            </div>
                                                            <div class="form-group text-center">
														<?php
														if($ele_signoff == false ){
															if((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level==1)  || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){ ?>
                                                                <a  target="_blank" id="" href="#" class="btn btn-sm btn-success save_mindmap submit">
                                                                        <!--<i class="fa fa-fw fa-save"></i>--> Save
                                                                </a>
															<?php }else { ?>
                                                                <a  target="_blank" id="" href="#" class="btn btn-sm btn-success disabled submit">
                                                                        <!--<i class="fa fa-fw fa-save"></i>--> Save
                                                                </a>
															<?php }
																} else{ ?>
																	<a  target="_blank" id="" href="#" class="btn btn-sm btn-success disabled submit">
                                                                        <!--<i class="fa fa-fw fa-save"></i>--> Save
																	</a>
															<?php } ?>
                                                                <a  href="#" class="btn btn-sm btn-danger cancel_mindmap  " id="cancel_mindmap">
                                                                        <!--<i class="fa fa-trash"></i>--> Clear
                                                                </a>
                                                            </div>
<?php echo $this->Form->end(); ?>
                                                        </div>
                                                    </div>



 <div class="table_wrapper clearfix" id="mindmap_table" data-model="mindmap" data-limit="5">
                                                        <div class="table_head">
                                                            <div class="row">
                                                                <div class="col-sm-3 resp">
                                                                    <h5> Title</h5>
                                                                </div>
																<div class="col-sm-3 resp">
                                                                    <h5> Creator</h5>
                                                                </div>
                                                                <!--<div class="col-sm-2 resp">
                                                                        <h5> Description</h5>
                                                                </div>-->
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
                                                        <div class="table-rows data_catcher" style="max-height:734px; overflow-y:auto;">
<?php

if (isset($mMPage) && !empty($mMPage)) {
    //if( isset($this->data['Mindmaps']) && !empty($this->data['Mindmaps']) ) {
    ?>
                                                                        <?php
                                                                        foreach ($mMPage as $detail) {
                                                                            //pr($detail,1);
                                                                            //$mindmaps = $this->data['Mindmaps'];
                                                                            $data = $detail['ElementMindmap'];
                                                                            ?>
                                                                    <div class="row">
                                                                        <div class="col-sm-3 resp" > <?php echo htmlentities($data['title'], ENT_QUOTES, "UTF-8") ;  ?></div>
                                                                        <!--<div class="col-sm-2 text-justify resp">
        <?php echo _substr_text($data['description'], 150); ?>
                                                                        </div>-->
																		<div class="col-sm-3 resp">
       <?php if($data['creater_id'] > 0){ ?>                                                                                                                                             <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $data['creater_id']; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                    <i class="fa fa-user"></i>
                </a>
        <?php

$elementmm_creator = $this->Common->elementMM_creator($data['id'],$project_id,$this->Session->read('Auth.User.id'));
		echo $elementmm_creator;
		}else{
		echo "N/A";
		}
		?>
                                                                        </div>
                                                                        <div class="col-sm-2 resp"><span class="deta-time-i">
        <?php //echo date('d M, Y g:iA', strtotime($data['created']));
		echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['created'])),$format = 'd M, Y g:iA');
		?>
                                                                      </span>  </div>
                                                                        <div class="col-sm-2 resp"><span class="deta-time-i">
        <?php //echo date('d M, Y g:iA', strtotime($data['modified']));
			echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['modified'])),$format = 'd M, Y g:iA');
		?>
                                                                  </span>      </div>
                                                                        <div class="col-sm-2 text-center resp">
                                                                            <div class="btn-group" >
																			<?php if((isset($is_owner) && !empty($is_owner))  || (isset($project_level) && $project_level==1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){


																			?>
                                                                                <a href="javascript:;" class="view_mindmap tipText" data-remote="	<?php echo Router::Url(array('controller' => 'entities', 'action' => 'view_mindmap', $this->data['Element']['id'], $data['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['id']; ?>"  data-eid="<?php echo $this->data['Element']['id']; ?>" data-uid="<?php echo $this->Session->read('Auth.User.id'); ?>" data-sessionid="<?php echo $session_id; ?>" data-mmtime="<?php  echo $this->Session->read('Auth.User.mm_time'); ?>" title="Open Mind Map" >
                                                                                    <i class="viewblack"></i>
                                                                                </a>
																				<?php //if($ele_signoff == false ){ ?>
                                                                                <a href="#" class=" update_mindmap tipText" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'update_mindmap', $data['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['id']; ?>" data-action="update" title="Update Mind Map">
                                                                                    <i class="showlessblack icon_up"></i>
																					<i class="showmoreblack icon_down"></i>
                                                                                </a>
																				<?php /* } else { ?>
																					<a href="#" class="btn btn-sm bg-blakish  disabled tipText" >
                                                                                    <i class="fa fa-arrow-down"></i>
                                                                                </a>
																				<?php } */ ?>

																				<a href="javascript:void(0);" class="history_mindmap tipText history" itemtype="element_mindmaps" itemid="historymindmap_<?php echo $data['id']; ?>"   data-id="<?php echo $data['id']; ?>" data-action="remove"  title="History"  >
																					<i class="historyblack"></i>
																				</a>
																				<?php if($ele_signoff == false ){ ?>
					<a href="#" class=" delete_resource tipText" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'remove_mindmap', $this->data['Element']['id'], $data['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['id']; ?>" title="Remove Mind Map" data-parent="#mindmap_table" data-type="mindmap" data-msg="Are you sure you want to delete this Mind Map?">
                                                                                    <i class="deleteblack"></i>
                                                                                </a>
																				<?php } else {?>
					<a href="#" class="disabled tipText" >
                                                                                    <i class="deleteblack"></i>
                                                                                </a>
																				<?php } ?>

																			<?php } else {?>
																			<a href="javascript:;" class=" disabled tipText"  data-id="<?php echo $data['id']; ?>"  data-eid="<?php echo $this->data['Element']['id']; ?>" data-uid="<?php echo $this->Session->read('Auth.User.id'); ?>" data-sessionid="<?php echo $session_id; ?>" title="Open Mind Map" >
                                                                                    <i class="viewblack"></i>
                                                                                </a>

                                                                                <a href="#" class="btn btn-sm bg-blakish  disabled tipText"   data-action="update" title="Update Mind Map">
                                                                                    <i class="fa fa-arrow-down"></i>
                                                                                </a>
<a href="javascript:void(0);" class=" history_mindmap tipText history disabled" itemtype="element_mindmaps" itemid="historymindmap_<?php echo $data['id']; ?>"   data-id="<?php echo $data['id']; ?>" data-action="remove"  title="History"  >
																					<i class="historyblack"></i>
																				</a>
                                                                                <a href="#" class="disabled tipText"   data-id="<?php echo $data['id']; ?>" data-action="remove" title="Remove Mind Map">
                                                                                    <i class="deleteblack"></i>
                                                                                </a>
																			<?php } ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="list-form col-sm-12 resp" style="display: none;">
        <?php echo $this->Form->create('Mindmaps', array('url' => array('controller' => 'entities', 'action' => 'add_mindmap', $element_id), 'class' => "padding-top formAddElementMindmap $pointer_event", 'id' => '', 'enctype' => 'multipart/form-data'));

        echo $this->Form->input('ElementMindmap.create_activity', [ 'type' => 'hidden','value'=>true, 'id'=> 'emca1']);
        ?>
		<input type="hidden" name="data[ElementMindmap][project_id]" class="form-control" value="<?php echo $project_id; ?>" />
                                                                            <input type="hidden" name="data[ElementMindmap][id]" class="form-control" value="<?php echo $data['id']; ?>" />
                                                                            <input type="hidden" name="data[ElementMindmap][element_id]" class="form-control" value="<?php echo $data['element_id']; ?>" />
                                                                            <div class="form-group">
                                                                                <label class=" " for=" ">Title:</label>
                                                                                <input type="text" name="data[ElementMindmap][title]" class="form-control" placeholder="Mind Map title" value="<?php echo htmlentities($data['title']); ?>" />
                                                                                <span class="error-message  text-danger" style=""></span>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class=" " for="mindmap_desc_<?php echo $data['id'] ?>">Description:</label>
                                                                                <textarea rows="3" class="form-control mindmap_desc" placeholder="Mind Map description" name="data[ElementMindmap][description]" id="mindmap_desc_<?php echo $data['id'] ?>"><?php echo htmlentities($data['description']); ?></textarea>
																				 <span class="error-message error text-danger" style=""> </span>
                                                                                <script>
		/* $(function () {
			$('#mindmap_desc_<?php //echo $data['id'] ?>').wysihtml5({
				"font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
				"emphasis": true, //Italics, bold, etc. Default true
				"lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
				"html": false, //Button which allows you to edit the generated HTML. Default false
				"link": true, //Button to insert a link. Default true
				"image": true, //Button to insert an image. Default true,
				"image_url": $js_config.base_url + "entities/upload_note_image/" + $js_config.currentElementId, //Button to insert an image. Default true,
				"color": false, //Button to change color of font
				"size": 'sm', //Button size like sm, xs etc.
				"tags": {
					"img": {
						"check_attributes": {
							"width": 200,
							"alt": "alt-text",
						}
					}
				},
				'parserRules': {'tags': {'br': {'remove': 0}, 'ul': {'remove': 0}, 'li': {'remove': 0}, 'b': {'remove': 0}, 'u': {'remove': 0}, 'i': {'remove': 0}, 'blockquote': {'remove': 0}, 'ol': {'remove': 0}}}

			});
		}) */
                                                                                </script>

                                                                            </div>

                                                                            <div class="form-group text-center">

																			<?php
																			if($ele_signoff == false ){
																				if((isset($is_owner) && !empty($is_owner))  || (isset($project_level) && $project_level==1)  || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){ ?>

                                                                                <a id="" href="#" class="btn btn-sm btn-success save_mindmap submit">
                                                                                        <!--<i class="fa fa-fw fa-save"></i>--> Save
                                                                                </a>
																			<?php }else {?>
																				<a id="" href="#" class="btn btn-sm btn-success disabled submit">
                                                                                        <!--<i class="fa fa-fw fa-save"></i>--> Save
                                                                                </a>
																			<?php }
																			} else {
																			?>
																				<a id="" href="#" class="btn btn-sm btn-success disabled submit">
                                                                                        <!--<i class="fa fa-fw fa-save"></i>--> Save
                                                                                </a>
																			<?php } ?>
                                                                            </div>
                                                                <?php echo $this->Form->end(); ?>
                                                                        </div>
                                                                    </div>
                <div id="historymindmap_<?php echo isset($data['id']) && !empty($data['id']) ? $data['id'] :'';; ?>" class="history_update" style="display: none;">
                    <?php  //include 'activity/update_history.ctp';?>
                </div>
    <?php } ?>
<?php }else{
		echo '<span class="nodatashow mindmap">No Mind Maps</span>';
	} ?>
                                                        </div>
<?php if (isset($mindmapPageCount) && !empty($mindmapPageCount)) { ?>
                                                            <div class="ajax-pagination clearfix">
    <?php //echo $this->element('pagination', array('model' => 'ElementMindmap', 'limit' => 5, 'pageCount' => $mindmapPageCount)); ?>
                                                            </div>

                                                            <?php } ?>
                                                    </div>
<script type="text/javascript">
	$(function(){
		$('body').delegate("input[name='data[ElementMindmap][title]']", "keyup focus", function(event){
			var characters = 50;
			event.preventDefault();
			// var $error_el = $(this).parents("#MindmapsUpdateElementForm").find('.error-message:first');
			var $error_el = $(this).next('.error-message');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})
	})
</script>
