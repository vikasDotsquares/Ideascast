<style>

.btn_select_template {
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
.wsp_template{
	background:rgba(103, 160, 40, 0.2);
}
</style>

<?php if(isset($data) && !empty($data)){ ?>

		<?php  $cid = (isset($this->params['pass']['1']) && !empty($this->params['pass']['1'])) ? $this->params['pass']['1'] : 0;
		$templates = get_template_by_size($data);
		 
		if(isset($templates) && !empty($templates)){
		?>

			<?php foreach($templates as $key => $val){
				$item = $val['Template'];
				
			
			?>

				<li class="col-lg-3 col-md-4 col-sm-6 temp-list" data-zones="<?php echo $data; ?>" data-cid="<?php echo $cid; ?>"  data-template="<?php echo $item['id']; ?>">
					<div class="box box-success">

						<div class="box-header"> <h3 class="box-title "><?php echo $item['title'] ?> </h3> </div>
						<div class="box-body clearfix<?php if( isset($item['wsp_imported']) && $item['wsp_imported'] == 1 ){?> wsp_template<?php } ?>">
							<a title="Select" href="#" class="btn btn-success btn-sm btn_select_template" id="btn_select_template"  data-id="<?php echo $item['id']; ?>"    > <i class="fa fa-check"></i> Select </a>
							
								<div class="pull-right"> <?php echo $this->Html->image('layouts/'.$item['layout_preview'], ['class' => 'thumb']); ?></div>
						</div>

					</div>

				</li>
			<?php } ?>


		<?php }
			else {
		?>
		<div style="text-align:center">
			<h2 class="headline_icon text-yellow"> <span class="glyphicon glyphicon-th-large"></span> </h2>
			<!-- <i class="fa fa-exclamation exceeded"></i> -->
				No template available from the selection.
		</div>
		<?php
		}?>


<?php  } ?>
