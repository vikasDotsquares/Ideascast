<style>
.elements-list {
	display: block;
	max-height: 180px;
	overflow-x: hidden;
	overflow-y: auto;
}
.elements-list .element-item {
	display: block;
	border-bottom: 1px solid #ccc;
	padding: 10px 0;
	margin: 5px 0;
}
.elements-list .element-item:last-child {
	border-bottom: none;
}
.item-label {
	font-size: 13px;
}
.item-label label {
	font-weight: 600;
}
.item-label span {
	display: block;
}
.item-label span.head {
	font-weight: 600;
}

.task-label {
	font-size: 13px;
	font-weight: 600;
	display: block;
}
.task-data {
	font-size: 13px;
	display: block;
	border: 1px solid #ccc;
	padding: 5px;
}
.el-panel {
	cursor: pointer;
	font-size: 13px;
}
.el-panel .collapsible:hover {
	color: #ffffff;
}
.el-panel[aria-expanded="true"] i.collapse-icon::before {
	content: "\f077";
}
.el-panel[aria-expanded="false"] i.collapse-icon::before {
	content: "\f078";
}
.panel-group .panel {
	border-radius: 4px !important;
}
.panel .panel-collapse.panel-body {
	padding: 0 !important;
}
.panel .panel-collapse.panel-body .body-inner {
	padding: 15px;
}
.image-box{ margin : 5px 0 0 0;}
</style>
<div class="modal-header"> 
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Tasks in Knowledge Template</h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body workspace-task-popup">
		
		<?php $elements = template_elements($template_id, false);
			
			if(isset($elements) && !empty($elements)) {
		?>
			<div class="panel-group" id="accordion">
				<?php foreach($elements as $key => $val ) {
				$item = $val['ElementRelation'];
				$ev = $val; 
				?>
				<div class="panel <?php echo $item['color_code']; ?>">
					<div class="panel-heading">
						<h4 class="panel-title el-panel" data-toggle="collapse" data-parent="#accordion" href="#collapsible<?php echo $item['id']; ?>">
							<a class="collapsible"><?php echo $item['title']; ?></a>
							<span class="pull-right ">
							<?php 
							/*
							$verify_doc =  element_documents($item['id']);
								 if($verify_doc > 0){
							?>
							
							<i class="fa fa-folder-o  "></i>
							
							<?php } */ ?>
							<?php
 
							if(isset($ev['ElementRelationDocument']) && !empty($ev['ElementRelationDocument'])){ 
							    $cd = count($ev['ElementRelationDocument']);
								$countD = ( $cd ==1) ? $cd.' Document' : $cd.' Documents'; 
							?>
							<i class="noteblack tipText" title="<?php echo $countD; ?>"></i>
							<?php } ?>
							<i class="fa fa-chevron-down collapse-icon"></i>
							</span>
						</h4>
					</div>
					
					<div id="collapsible<?php echo $item['id']; ?>" class="panel-collapse collapse panel-body">
						<div class="body-inner" style="margin-bottom:0;">
							<span class="task-label">Task Description:</span>
							<span class="task-data">
								<?php echo nl2br($item['description']); ?>
							</span>
						</div>
						

							<?php
 
							if(isset($ev['ElementRelationDocument']) && !empty($ev['ElementRelationDocument'])){ ?>
							
						<div class="body-inner" style="margin: 0px; padding-top: 0px;">
							<span class="task-label">Documents:</span>
							<span class="task-datas" style="border:none">
						
						<div class="comment  feedback-comment">
						<div class=" clearfix  "  style="margin: 0px; padding-top: 0px;">
							
								<div class=" clearfix col-sm-12  nopadding">
								 
								<div class="col-md-12 nopadding">
								<?php 
									foreach($ev['ElementRelationDocument'] as $Attachment){ 
										$id = $Attachment['id'];
										 
										//$id = $FeedbackAttachment['id'];
										$upload_path = WWW_ROOT . TEMPLATE_DOCUMENTS . DS ;
										$upload_file = $upload_path . $Attachment['file_name'];

										$ftype = pathinfo($upload_file);
										if (isset($ftype) && !empty($ftype)) {
											// pr($ftype);
											$dirname = ( isset($ftype['dirname']) && !empty($ftype['dirname'])) ? $ftype['dirname'] : '';
											$basename = ( isset($ftype['basename']) && !empty($ftype['basename'])) ? $ftype['basename'] : '';
											$filename = ( isset($ftype['filename']) && !empty($ftype['filename'])) ? $ftype['filename'] : '';
											$extension = ( isset($ftype['extension']) && !empty($ftype['extension'])) ? $ftype['extension'] : '';
										}
								?>
								<div class="image-box template_elem_rel">
									<span class="download_asset icon_btn icon_btn_sm icon_btn_teal">
										<span class="icon_text"><?php echo $extension; ?></span> 
									</span>
									<?php $downloadURL = Router::Url(array('controller' => 'templates', 'action' => 'download_template_doc', $id, 'admin' => FALSE), TRUE); ?>
									<a href="<?php echo $downloadURL ?>"  class="imagename" href="javascript:void(0);">
										<span class="elem_rel_f_filename"><?php echo $filename.'.'.$extension; ?></span>
										<?php /* <span class="elem_rel_f_ext"><?php echo $extension; ?></span> */ ?>
									</a>
									<a class="tipText confirm_doc_delete btn_file_link" data-remote="<?php echo $downloadURL ?>" data-id="<?php echo $id; ?>" > </a>
								</div>
								<?php } ?>
								</div>
								</div>
								</div>
						</div>	
						
						</span>
						</div>
							<?php } ?>
					
						
					</div>
				</div>
				<?php } ?>
				
			</div>
		<?php } ?>
		
	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		<button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
	</div>

