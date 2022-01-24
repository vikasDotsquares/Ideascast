<?php
$onemsg = null;
$datatitle = '';


if( isset( $message ) && !empty($message) && empty($overdue) ){
	$onemsg = 'You cannot add a Document because this Task has been signed off.';
	$datatitle = 'Signed Off';
}

?>
<div class="doc_form"  >
                                                        <div data-msg="<?php echo htmlentities($onemsg);?>" class="list-form border bg-warning nopadding <?php echo $class_d;?>" style="display: block;">
 <?php if((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level==1)  || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){ ?>

														   <a class="list-group-item clearfix open_form noborder-radius" href="">
                                                                <span class="pull-left update_docc"><i class="asset-all-icon re-DocumentBlack"></i>&nbsp; New Document</span>
                                                                <span class="pull-right"></span>
                                                            </a>
<?php }else{ ?>
															<a class="list-group-item clearfix  disabled noborder-radius"  style="background:#dddddd; border:solid 1px #dddddd " >
                                                                <span class="pull-left update_docc"><i class="asset-all-icon re-DocumentBlack"></i>&nbsp; New Document</span>
                                                                <span class="pull-right"><!--<i class="fa fa-plus"></i>--></span>
                                                            </a>
<?php } ?>

                                                            <div  class="list-group-item clearfix nopadding" >



 <?php if((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level==1)  || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){ ?>

                                                                            <?php echo $this->Form->create('Documents', array('url' => array('controller' => 'entities', 'action' => 'add_document', $element_id), 'class' => 'padding', 'id' => 'formAddElementDoc', 'enctype' => 'multipart/form-data','style'=>'display:none')); ?>
<?php }else{ ?>
                                                                            <?php echo $this->Form->create('Documents', array('url' => array('controller' => 'entities', 'action' => 'add_document', $element_id), 'class' => 'padding', 'id' => 'formAddElementDoc', 'enctype' => 'multipart/form-data' ,'style'=>'display:none')); ?>
<?php } ?>

<?php echo $this->Form->input('Documents.id', [ 'type' => 'hidden']);

echo $this->Form->input('Documents.create_activity', [ 'type' => 'hidden','value'=>true]);

?>
<?php echo $this->Form->input('Documents.element_id', [ 'type' => 'hidden', 'value' => $this->data['Element']['id']]); ?>
<?php echo $this->Form->input('Documents.project_id', [ 'type' => 'hidden', 'value' => $project_id]) ;?>
                                                                 <?php /* <div class="progress-wrapper" id="progress_wrapper">
                                                                    <div class="progress progress-xs" id="progress" data-width="" style="margin: 0px; height: 13px;">
                                                                        <div style="width:0;" id="progress_bar" class="progress-bar progress-bar-danger">
                                                                            <div class="percent-text percent" id="percent_text"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>*/ ?>

																<div class="row documents-create">

                                                                    <div  class="col-md-5 col-lg-5">
                                                                        <div class="input-group">
                                                                            <div class="input-group-addon">
                                                                                <i class="fa fa-font"></i>
                                                                            </div>
                                                                                <?php echo $this->Form->input('Documents.title', [ 'type' => 'text', 'label' => false, 'div' => false, 'required' => false, 'class' => 'form-control documents-title-h', 'id' => 'doc_title', 'placeholder' => 'Document title']); ?>
                                                                        </div>
                                                                        <div id="error-messages" class="error-messages text-dangers showing">Title is required.</div>
																		<div style="display:none;" id="error-messages-char" class="error-messages text-dangers showing">Title is required.</div>
                                                                    </div>

                                                                    <div  class="col-md-7 col-lg-5 mg_top" >
                                                                        <div class="input-group">
                                                                            <div class="input-group-addon">
                                                                                <i class="uploadblack"></i>
                                                                            </div>
                                                                            <span title="" class="docUpload icon_btn bg-white border-radius-right tipText" data-original-title="Click to Upload a file">
<?php echo $this->Form->input('Documents.file_name', [ 'type' => 'file', 'label' => false, 'div' => false, 'required' => false, 'class' => 'form-control upload', 'id' => 'doc_file' , 'placeholder' => 'Upload File']); ?> <span class="text-blue" id="upText"  style="display: block; min-height: 26px;">Upload Document</span>
                                                                            </span>
                                                                            <div class="input-group-addon" style="border: transparent;">
                                                                                <i class="loader-icon fa fa-spinner fa-pulse" style="display: none;"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div id="test_for_error"  class="error-messages text-dangers showing">Document is required.</div>
                                                                        <div id="doc_type_error"  class="error-messages text-dangers showing" >Invalid file format.</div>
                                                                        <!--
                                                                        <div class="progress"><div id="docUploadProgressBar" class="progress-bar"></div></div>
                                                                        -->
                                                                    </div>

                                                                    <div  class="col-lg-2 col-sm-12 mg_top link-but-md">
																	<?php
																	if($ele_signoff == false ){
																		if((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level==1)  || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){ ?>
																			<a class="btn btn-success btn_progress btn_progress_wrapper btn-sm save_document" id="save_document">
																				<div class="btn_progressbar"></div>
																				<!--<i class="fa fa-fw fa-save"></i>-->
																				<span class="text">Save</span>
																			</a>
																		<?php }else { ?>
																			<a class="btn btn-success btn_progress btn_progress_wrapper btn-sm disabled" id="save_document">
																				<div class="btn_progressbar"></div>
																				<!--<i class="fa fa-fw fa-save"></i>-->
																				<span class="text">Save</span>
																			</a>
																	<?php }
																	} else { ?>
																		<a class="btn btn-success btn_progress btn-sm disabled" >
																				<div class="btn_progressbar"></div>
																				<span class="text">Save</span>
																			</a>
																	<?php } ?>
                                                                        <a class="btn btn-sm btn-danger cancel_update" style=" " href="#" id="cancel_update_doc">
                                                                                <!--<i class="fa fa-fw fa-times"></i>--> Clear
                                                                        </a>
                                                                        <!--
                                                                        <a class="btn btn-sm btn-success" href="#" id="save_document">
                                                                                <i class="fa fa-fw fa-save"></i> Save
                                                                        </a>

                                                                        <div id="progressbarNewWrapper" style="height: 20px; display: block; width: 100%;" >
                                                                                <div id="progressbar_new" style="height: 20px; display: block; width: 0;" class="error-message bg-red">
                                                                                        <div id="percent" style=" " class="text-danger"> </div>
                                                                                </div>
                                                                        </div>
                                                                        -->
                                                                    </div>
                                                                </div>

                                                                <div class="row image_preview" >
                                                                    <div class="col-sm-9 text-right image_preview_inner" >
                                                                        <img src="" class="img-circle" id="img_circle" />
                                                                        <img src="" class="img-rounded" id="img_rounded" />
                                                                        <img src="" class="img-thumbnail" id="img_thumbnail" />
                                                                    </div>
                                                                    <div class="col-sm-3" ></div>
                                                                </div>
<?php echo $this->Form->end(); ?>
                                                            </div>
                                                        </div>
                                                    </div>









 <div class="table_wrapper" id="documents_table" >

                                                        <div class="table_head">
                                                            <div class="row">
                                                                <div class="col-sm-2 resp">
                                                                    <h5> Title</h5>
                                                                </div>
                                                                <div class="col-sm-2 resp">
                                                                    <h5> Document</h5>
                                                                </div>
																<div class="col-sm-2 resp">
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
                                                        <div class="table-rows data_catcher document-wrap-col" style="max-height:692px; overflow-y:auto;">
	<?php
	if (isset($this->data['Documents']) && !empty($this->data['Documents'])) {
		$docs = $this->data['Documents'];
		$trClass = 'even';
		foreach ($docs as $data) {
			$trClass = ($trClass == 'even') ? 'odd' : 'even';
			?>
	<div class="row">
		<div class="col-sm-2 resp"><span class="col_doc_title" ><?php echo  htmlentities($data['title'], ENT_QUOTES, "UTF-8") ;  ?></span></div>
                <div class="downloads col-sm-2 resp " id="downloads_<?php echo $data['id'];?>">
			<?php
			$upload_path = WWW_ROOT . ELEMENT_DOCUMENT_PATH . DS . $this->data['Element']['id'] . DS;

			$upload_file = $upload_path . $data['file_name'];

			$ftype = pathinfo($upload_file);
			if (isset($ftype) && !empty($ftype)) {
				//
				$dirname = ( isset($ftype['dirname']) && !empty($ftype['dirname'])) ? $ftype['dirname'] : '';
				$basename = ( isset($ftype['basename']) && !empty($ftype['basename'])) ? $ftype['basename'] : '';
				$filename = ( isset($ftype['filename']) && !empty($ftype['filename'])) ? $ftype['filename'] : '';
				$extension = ( isset($ftype['extension']) && !empty($ftype['extension'])) ? $ftype['extension'] : '';
				$basename1 = $basename;
				$base_name = explode('.', $basename);

				if( is_array($base_name) && !empty($base_name) ) {
					unset($base_name[count($base_name)-1]);
					$basename1 = implode('', $base_name);
				}


			}
			?>
			<span style="display: table-cell; width: 20%;">
				<span class="download_asset icon_btn icon_btn_sm icon_btn_teal">
					<span class="icon_text"><?php echo $extension; ?></span>
				</span>
			</span>
			<span style="display: table-cell; width: 80%;">
			<?php
				echo $this->Html->link($basename1, [
						'controller' => 'entities', 'action' => 'download_asset', $data['id']
					],
					[
						'class' => 'col_doc_filename',
						'title' => strtolower($data['file_name']),
						'style' => 'margin-left: 4px;'
					]
				);
				//$this->Html->tag('span', $basename1, array('class' => 'col_doc_filename'))
			?>
			<?php /* $downloadURL = Router::Url(array('controller' => 'entities', 'action' => 'download_asset', $data['id'], 'admin' => FALSE), TRUE); ?>
			<a href="<?php echo $downloadURL ?>" class="btn_file_link tipText" title="<?php echo $data['file_name']; ?>" data-remote="<?php echo $downloadURL ?>" data-id="<?php echo $data['id']; ?>" >
				<span class="col_doc_filename" >
					<?php echo $basename1; ?>
				</span>
			</a>
			<?php  */ ?>
			</span>
		</div>
		<div class="col-sm-2 resp">
            <div class="text-doc-elps">
			<?php if($data['creater_id'] > 0){ ?>
                    <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $data['creater_id']; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                    <i class="fa fa-user"></i>
                </a>
                    <?php $doc_creator = $this->Common->elementDoc_creator($data['id'],$project_id,$this->Session->read('Auth.User.id'));
		echo $doc_creator;
		}else{
		echo "N/A";
		}
		?></div>
                </div>
		<div class="col-sm-2 resp">
		<span class="deta-time-i">
		<?php
			//echo date('d M, Y g:iA', strtotime($data['created']));
			echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['created'])),$format = 'd M, Y g:iA');
			?> </span></div>
                <div class="col-sm-2 resp" id="document_modified_<?php echo $data['id']; ?>"> <span class="deta-time-i"><?php
				//echo date('d M, Y g:iA', strtotime($data['modified']));
				echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['modified'])),$format = 'd M, Y g:iA');
				?></span> </div>
		<div class="text-center col-sm-2 resp">
			<div class="btn-group ">
			 <?php
			  if($ele_signoff == false ){
				if((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level==1)  || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){ ?>
				<a href="#" class=" update_doc tipText" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'update_doc', $data['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['id']; ?>" title="Update Document" data-action="update">
					<i class="edit-icon"></i>
				</a>

<a href="javascript:void(0);" class=" history_doc tipText history"  itemtype="element_documents" itemid="historydocument_<?php echo $data['id']; ?>"   data-id="<?php echo $data['id']; ?>" data-action="remove"  title="History">
                           <i class="historyblack"></i>
						</a>

				<a href="#" class=" delete_resource tipText" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'remove_doc', $data['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['id']; ?>" title="Remove Document" data-parent="#documents_table" data-type="doc" data-msg="Are you sure you want to delete this Document?">
					<i class="deleteblack"></i>
				</a>
				<?php } else {  ?>
				<a href="#" class=" disabled  tipText"  data-id="<?php echo $data['id']; ?>" title="Update Document" data-action="update">
					<i class="edit-icon"></i>
				</a>
<a href="javascript:void(0);" class="history_doc disabled tipText"  itemtype="element_documents" itemid="historydocument_<?php echo $data['id']; ?>"   data-id="<?php echo $data['id']; ?>" data-action="remove"  title="History"  >
                           <i class="historyblack"></i>
						</a>
				<a href="#" class=" disabled  tipText"   data-id="<?php echo $data['id']; ?>" title="Remove Document"  data-action="delete">
					<i class="deleteblack"></i>
				</a>
				<?php }
				} else { ?>
				<a href="#" class=" disabled  "  title="Update Document" data-action="update">
					<i class="edit-icon"></i>
				</a>
<a href="javascript:void(0);" class="history_doc tipText history"  itemtype="element_documents" itemid="historydocument_<?php echo $data['id']; ?>"   data-id="<?php echo $data['id']; ?>" data-action="remove"  title="History"  >
                           <i class="historyblack"></i>
						</a>
				<a href="#" class=" disabled  "   title="Remove Document"  data-action="delete">
					<i class="deleteblack"></i>
				</a>
				<?php } ?>
			</div>
		</div>
	</div>
                                                            <div id="historydocument_<?php echo isset($data['id']) && !empty($data['id']) ? $data['id'] :'';; ?>" class="history_update" style="display: none;">
                    <?php  //include 'activity/update_history.ctp';?>
                </div>
        <?php
    }
}else{
		echo '<span class="nodatashow document">No Documents</span>';
	} 
?>
                                                        </div>
                                                    </div>


<script type="text/javascript">
	$(function(){
		//$('.col_doc_filename').ellipsis_word();
		setTimeout(function(){
		//$('.col_doc_filename').ellipsis_word();

		},300)

		$(window).on('resize', function(){
		//	$('.col_doc_filename').ellipsis_word();
		})

        $('body').delegate('.col_doc_filename', 'mouseenter', function(event) {
            $(this).tooltip({ container: 'body', placement: 'auto', 'template': '<div class="tooltip tips" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>' })
            $(this).tooltip('show')
        })
        $('body').delegate('.col_doc_filename', 'mouseleave', function(event) {
            $(this).tooltip('hide')
        })

		$('body').delegate("#doc_title", "keyup focus", function(event){
			var characters = 50;
			event.preventDefault();
			var $error_el = $(this).parents("#formAddElementDoc").find('#error-messages-char').show();
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})
	})
</script>

<style>

#col_doc_filename {
  position: relative;
  width: 180px;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

#col_doc_filename:after {
  content: attr(data-filetype);
  position: absolute;
  left: 100%;
  top: 0;
}

/*
#upText {
  position: relative;
  width: 352px;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

#upText:after {
  content: attr(data-filetype);
  position: absolute;
  left: 100%;
  top: 0;
}  */

.tooltip.tips {text-transform: none !important; }
.doc_form{ margin : 0;}
.doc_form .list-form {
    display: block;
    margin: 0 0 15px 0;
    border: none;
    padding: 0;
}
</style>