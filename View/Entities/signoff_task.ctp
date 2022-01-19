<div class="modal-header" >
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
	<h3 id="modalTitle" class="modal-title" >Gated Sign Off</h3>
</div>
<?php $ele_status = '';
$task_msg = '';
if( isset($elementid) && !empty($elementid) ){
	$ele_status = $this->ViewModel->checkEleDepndancy($elementid);
	$task_msg = "This task cannot be signed off untill all gated $ele_status tasks are signed off";
}
?>
<div class="modal-body clearfix people" style="max-height:424px; overflow:auto;">
	<div class="row top-row">
		<div class="col-sm-12"><?php echo $task_msg; ?></div>
	</div>
    <div class="signoff-middle-sec">
	<div class="row signoff-h">
		<div class="col-sm-9">
			Title
		</div>
		<div class="col-sm-3 text-center">
			Signed Off
		</div>
	</div>
	<?php

	if( isset($elements) && !empty($elements) ){
		foreach($elements as $ele_val){
	?>
		<div class="row signoff-cont">
			<div class="col-sm-9">
				<a href="<?php echo SITEURL.'entities/update_element/'.$ele_val['Element']['id'];?>"><?php echo htmlentities($ele_val['Element']['title'],ENT_QUOTES);?></a>
			</div>
			<div class="col-sm-3 text-center" >
				<div class="bs-checkbox">
					<label>
						<input type="checkbox" disabled="disabled" <?php if($ele_val['Element']['sign_off'] == 1 ){ ?> checked="checked"<?php } ?>>
						<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					</label>
				</div>
			</div>
		</div>

	<?php }
	} ?>
        </div>
</div>
<div class="modal-footer">
	<button class="btn btn-danger" data-dismiss="modal">Close</button>
</div>
<style>
.people .top-row {
    margin-top: 0px;
    padding-top: 0px;
}
.people .row {
   border-top: 1px solid #f4f4f4;
}
</style>