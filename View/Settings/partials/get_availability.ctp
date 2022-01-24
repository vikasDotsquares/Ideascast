<?php
	//$current_user_id = $this->Session->read('Auth.User.id');
//$data = getAvailability();	
//pr($data);
if( isset($data) && !empty($data) ) { ?>
	<?php foreach($data as $row) { ?>
		<?php		

			//$current_org_other = $this->Permission->current_org($row['UserAvailability']['user_id']);
			// pr($current_org_other);
		?>

				<div class="available-data-row">
					 <div class="avbl-col avbl-col-1">
						<?php echo date('d M, Y',strtotime($row['UserAvailability']['effective'])); ?>
					 </div>
					 <div class="avbl-col avbl-col-2">
						<?php echo $row['UserAvailability']['monday']; ?>
					 </div>
					<div class="avbl-col avbl-col-3">
						<?php echo $row['UserAvailability']['tuesday']; ?>
					</div>
					<div class="avbl-col avbl-col-4">
						<?php echo $row['UserAvailability']['wednesday']; ?>
					</div>
					<div class="avbl-col avbl-col-5">
						<?php echo $row['UserAvailability']['thursday']; ?>
					</div>
					<div class="avbl-col avbl-col-6">
						<?php echo $row['UserAvailability']['friday']; ?>
					</div>
					<div class="avbl-col avbl-col-7">
						<?php echo $row['UserAvailability']['saturday']; ?>
					</div>
					<div class="avbl-col avbl-col-8">
						<?php echo $row['UserAvailability']['sunday']; ?>
					</div>
					<div class="avbl-col avbl-col-9">
						<?php echo $row['UserAvailability']['monday']+$row['UserAvailability']['tuesday']+$row['UserAvailability']['wednesday']+$row['UserAvailability']['thursday']+$row['UserAvailability']['friday']+$row['UserAvailability']['saturday']+$row['UserAvailability']['sunday']; ?>
					</div>
					
					<div class="avbl-col avbl-col-10 avbl-actions">
						<a href="#" class="tipText" title="Edit" id="edit-data" data-id="<?php echo $row['UserAvailability']['id']; ?>"><i class="edit-icon"></i></a>
						<a href="#" class="tipText" title="Delete" id="delete-data" data-id="<?php echo $row['UserAvailability']['id']; ?>"><i class="clearblackicon"></i></a>
					</div>
				</div>


	<?php } ?>
<?php }
else { ?>
<div class="availability-data-found" >No Availability</div>
<?php } ?>



<style>
	.popover p {
    margin-bottom: 2px !important;
	}
	.popover p:nth-child(2) {
		font-size: 11px;
	}
	.style-people-name { white-space : inherit;}
</style>
<script type="text/javascript" >
$(function() {

	$('.style-popple-icons').off('click').on('click', function(event) {
			
		$('#model_bx').modal('hide');
	})	
})
</script>

 <script>
$(function(){
	
 $('.pophover').popover({
            placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400},
            template: '<div class="popover abcd" role="tooltip"><div class="arrow"></div><div class="popover-content user-menus-popoverss"></div></div>'
        })
		
})
</script>	
