<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="modal-title" style="font-size:24px;">Decline Opportunity Request</h3>
</div>
	<div class="modal-body">
		<div style="font-weight: 650; padding-bottom: 5px;">Reason for declining:</div>
		<ul style="padding-left: 15px; line-height: 24px;" class="declinelist" >
		<?php 
			$reasons = $this->Common->declineReason();
			if( isset($reasons) && !empty($reasons) ){
				foreach($reasons as $lists){
				$checked = '';
				if( isset($data['id']) && !empty($data['id']) && $data['id'] == $lists['DeclineReason']['id'] ){
					$checked = 'checked="checked"';
				}	
					
		?>
			<li style="list-style:none;"><input style="margin-top: 6px; vertical-align: top;" type="radio" name="responsereason" <?php echo $checked;?> value="<?php echo $lists['DeclineReason']['id'];?>" >&nbsp;<span ><?php echo $lists['DeclineReason']['reasons'];?></span></li> 
		<?php }
			} 
			if( isset( $data['board_id'] ) && !empty(( $data['board_id'] )) ){
				$boarddetail = $this->Common->board_data($data['board_id']);
				//pr($boarddetail);
		?>
			<input type="hidden" id="project_id" name="data[BoardResponse][project_id]" value="<?php echo $data['project_id'];?>">			
			<input type="hidden" id="sender" name="data[BoardResponse][sender]" value="<?php echo $boarddetail['ProjectBoard']['sender'];?>">
			<input type="hidden" id="receiver" name="data[BoardResponse][receiver]" value="<?php echo $boarddetail['ProjectBoard']['receiver'];?>">
			<input type="hidden" id="project_status" name="data[ProjectBoard][project_status]" value="<?php echo $data['project_status'];?>">
			<input type="hidden" id="board_id" name="data[ProjectBoard][id]" value="<?php echo $data['board_id'];?>">
			 
			
		<?php } ?>	
		
		</ul>
	</div>	 
<div class="text-white modal-footer">
    <button type="button" id="savedecline" class="btn btn-success">Decline</button>
    <button type="button" data-dismiss="modal" class="btn btn-danger">Cancel</button>
</div>

<script type="text/javascript" >

	$("#savedecline").on('click', function (event) {
		
		event.preventDefault();
		var $that = $(this);
		var parents = $that.parents();
		var board_id = $('#board_id').val();
		var project_id = $('#project_id').val();
		var project_status = $('#project_status').val();
		var sender = $('#sender').val();
		var receiver = $('#receiver').val();
		var reason = $('input[name=responsereason]:checked').val();	 
		
		if( board_id != undefined && project_id != undefined && project_status != undefined && sender != undefined && receiver != undefined && reason != undefined ){
		
			$.when(
				$.ajax({
					type: 'POST',
					data: $.param({ 'board_id': board_id, 'project_id': project_id, 'project_status': project_status, 'sender': sender, 'receiver': receiver, 'declinereason': reason }),
					url: $js_config.base_url + 'boards/board_opp_decline_save',
					global: false,
					success: function(response) {
						
						var project_id = $.modal_data.data('project');
						var sender_id = $.modal_data.data('sender');
						$.get_request_user(project_id,sender_id, $.update_row );
						setTimeout(function(){
							$("#popup_model_box_decline").modal('hide');
						},100);	
					}
				})

			).then(function(rdata, textStatus, jqXHR) {

			}) 
		
		}
						
	});
	
</script>
 