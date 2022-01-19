<style>
.templates_list li {
	list-style: outside none none;
}
.templates_list li .box {
	border-bottom: 1px solid #67a028;
	border-left: 1px solid #67a028;
	border-right: 1px solid #67a028;
	max-height: 155px;
	min-height: 155px;
}
.templates_list ul {
	padding: 0;
}
.templates_list ul > li > div.box {
	transition: background-color 0.5s ease 0s;
}
.templates_list ul > li > div.box:hover {
	background: rgba(105, 165, 142, 0.2) none repeat scroll 0 0;
}
.btn_select_workspace {
	margin: 25px 0 0 !important;
}
i.exceeded {
	background: #f39c12 none repeat scroll 0 0;
	border-radius: 50%;
	color: #fff;
	font-size: 13px;
	padding: 3px 7px;
	margin-right: 5px;
}
.headline_icon {
	float: none;
	font-size: 50px;
	font-weight: 300;
	text-align: center;
}
.stitle {
	font-size: 15px;
	margin: 10px 0;
	padding: 0 15px;
}
.wstitle {
	display: block;
	font-size: 20px;
	padding: 0 15px;
}
</style>

<?php if(isset($data) && !empty($data)){ ?>

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Generate Workspace</h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		
		<?php 
		if( isset($workspace_id) && !empty($workspace_id) ) {
		?>
		<div class="wstitle list-title">Workspace: 	
		<?php  
			$ws = $this->Viewmodel->getWorkspaceDetail($workspace_id);
			
			echo ucfirst(strip_tags($ws['Workspace']['title']));
		?>
		</div>
		<?php 
		}
		$templates = get_template_by_size($data); 
		if(isset($templates) && !empty($templates)){
		?>
		<div class="stitle">Select a Workspace template:</div>
		
		<div class="templates_list" >
			<ul class="clearfix">
			
			<?php foreach($templates as $key => $val){ 
				$item = $val['Template'];
				//pr($item['id']);
				//pr($ws['Workspace']['template_id']);
			?>
			
				<li class="col-sm-6">
					<div class="box <?php if( $ws['Workspace']['template_id'] == $item['id'] ) { ?>box-primary<?php }else{ ?>box-success<?php } ?>">
					
						<div class="box-header"> <h3 class="box-title "><?php echo $item['title'] ?> </h3> </div>
						<div class="box-body clearfix"> 
							<a title="Select" href="#" class="btn <?php if( $ws['Workspace']['template_id'] == $item['id'] ) { ?>btn-primary<?php }else{ ?>btn-jeera<?php } ?> btn-sm select-btn btn_select_workspace" id="" data-id="<?php echo $item['id']; ?>"  > <i class="fa fa-check"></i> Select </a>
								<div class="pull-right"> <?php echo $this->Html->image('layouts/'.$item['layout_preview'], ['class' => 'thumb']); ?></div>	
						</div>	
						
					</div>
				 
				</li>
			<?php } ?>
			
			</ul>
		</div>
		<?php }
			else {
		?>
		<div style="text-align:center">
			<h2 class="headline_icon text-yellow"> <span class="glyphicon glyphicon-th-large"></span> </h2>
			<!-- <i class="fa fa-exclamation exceeded"></i> -->
				Zones limit has exceeded. No template available for the available Zones.
		</div>
		<?php 
			}?>
	
	</div>
	
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer"> 
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
	
<?php  } ?>

<script type="text/javascript" >
$(function(){
	
})
</script>