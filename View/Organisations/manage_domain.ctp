<style>
.custom-dropdown {
		position: absolute !important;
		display: inline-block;
		margin: 0 auto;
		z-index: +1;
		width:100%;
		margin-bottom:20px;	
}
.custom-dropdown select.aqua {
    border-color: #d2d6de !important;
}
</style>		
		<!-- Add Manage Domain Email -->
			<?php 
			$domainnamee = explode(".",$this->request->data['ManageDomain']['domain_name']);
			$this->request->data['ManageDomain']['domain_name'] = $domainnamee[0];
			echo $this->Form->create('ManageDomain', array('class' => 'form-horizontal form-bordered', 'id' => 'RecordFormedit')); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php if( isset($this->request->data['ManageDomain']['id']) && !empty($this->request->data['ManageDomain']['id']) ){ ?>Edit<?php } else {?>Add<?php }?> Email Domain</h4>
			</div>

				<div class="modal-body clearfix">
				<?php
					echo $this->Form->input('ManageDomain.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control'));

					if( isset($this->request->data['ManageDomain']['id']) && !empty($this->request->data['ManageDomain']['id']) ){
						echo $this->Form->input('actionType', array('type' => 'hidden','value' => 'editAction', 'label' => false, 'div' => false, 'class' => 'form-control'));
						
						$usercount = $this->Common->getEmailDomainUser($this->request->data['ManageDomain']['id']);
						
					} else {
						echo $this->Form->input('actionType', array('type' => 'hidden', 'value' => 'addAction', 'label' => false, 'div' => false, 'class' => 'form-control'));
						$usercount = 0;
					}
					
					$disabled11 = '';
					if( isset($usercount) && $usercount > 0 ){
						$disabled11 = "disabled"; 
					}
					
					//pr($this->request->data['ManageDomain']);
					
				?>
				<?php echo $this->Form->input('ManageDomain.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>

					<div class=" ">
					    <div class="col-md-12">
							<div class="form-group">
							  <label for="DomainName" class="">Email Domain:</label>
								<?php 
								echo $this->Form->input('ManageDomain.domain_name', array('type' => 'text', 'label' => false, 'div' => false, 'required' => false, 'class' => 'form-control','autocomplete'=>'off')); 								
								?>
								<span style="font-size:12px;" >Please make sure email domain name is in lowercase.</span>
								<div class="text-red normal text-red" style="font-weight:normal;font-size:11px;"></div>
							</div>
							<div class="form-group" style="margin-bottom:50px;">
							  <label for="DomainName" class="">Extension type:</label>	
							  <?php 
								if(isset($domaintype) && !empty($domaintype)){ 
									$domaintype = $domaintype; 
								}else{
									$domaintype = array();									
								}
								/*
								echo $this->Form->input('ManageDomain.domain_id', array('type' => 'select','empty'=>'Select','options'=>$domaintype, 'label' => false, 'required' => false, 'div' => false, 'class' => 'form-control')); 
								*/ 
								?>								
								<div class="domianmanage">
								
                                    <select id="ManageDomainDomainId" class="aqua form-control " onfocus='this.size=11;' onblur='this.size=1;' 
onchange='this.size=1; this.blur();' name="data[ManageDomain][domain_id]" style="width:100%;">
                                        <option value="">Select</option>
                                        <?php if( isset($domaintype) && !empty($domaintype) ){
										foreach($domaintype as $key => $dvalue){
											$selectedDoamin = '';
											if( isset($this->request->data['ManageDomain']['domain_id']) && $this->request->data['ManageDomain']['domain_id'] == $key ){
												$selectedDoamin = 'selected="selected"';
											}
										?>
                                           <option <?php echo $selectedDoamin; ?>  value="<?php echo $key;?>"><?php echo strtolower($dvalue);?></option>
                                    <?php }
									} ?>
                                    </select>
									<div id="domainidError" class="text-red normal" style="font-weight:normal;font-size:11px;"></div>
                                
								</div>
							</div>
						</div>
					</div>
				</div><!-- /.modal-content -->
				<div class="modal-footer clearfix">
				<?php if( !isset($usercount) || $usercount <= 0 ){	?>
					<button type="submit" id="domain_submit"  class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Save</button>
				<?php } ?>	
					<button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><!--<i class="fa fa-times"></i>--> Close</button>
				</div>
			<?php echo $this->Form->end(); ?>
<script type="text/javascript" >
// Submit Edit Form
$('#add_edit_msg').hide();
$("#RecordFormedit").submit(function(e){

		$("#domain_submit").addClass('disabled'); 
		var $form = $(this);
		var postData = new FormData($(this)[0]);
		var formURL = $(this).attr("action");
		$.ajax({
			url : formURL,
			type: "POST",
			dataType: "JSON",
			data : postData,
			beforeSend:function(response){
				$("#domain_submit").addClass('disabled');
			},
			success:function(response){
				$("#domain_submit").removeClass('disabled');
				
			  	if( $.trim(response.success) == 'false'){
					//console.log("vikas");	
					//console.log(response);	
					$('#add_edit_msg').show();					
					$("#add_edit_msg").removeClass('text-green').addClass('text-red').html(response.content);
						//console.log(response.content);
						
						$.each( response.content, function( ele, msg) {
							
							if(ele == 'domain'){
								var $element = $form.find('[name="data[ManageDomain][domain_name]"]');
								var $parent = $element.parent();
								if( $parent.find('.text-red').length  ) {
									$parent.find('.text-red').text(msg);
								}
							} else {
								var $element = $form.find('[name="data[ManageDomain][domain_id]"]');
								var $parent = $element.parent();								
								if( $parent.find('.text-red').length  ) {
									$parent.find('.text-red').text(msg);
								}								
							}
							
								
						})

					  
					$("#skill_submit").removeClass('disabled');
				}else{
					$("#skill_submit").removeClass('disabled');					 
					location.reload();
				}






				
			},
			 cache: false,
			 contentType: false,
			 processData: false,
			 
		});
  	e.preventDefault(); //STOP default action
		//e.unbind(); //unbind. to stop multiple form submit.
});

$('body').delegate('#ManageDomainDomainId', 'click', function(event){

	$("#domainidError").text('');
	
})
$('body').delegate('#ManageDomainDomainName', 'keyup focus', function(event){
	var characters = 50;
	
	event.preventDefault();
	var $error_el = $(this).parent().find('.text-red');
	if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
		$.input_char_count(this, characters, $error_el);
	}
})

$(document).ready(function(){	
	// initilize popover tooltip message
	$('[data-toggle="popover"]').popover({container: 'body',html: true,placement: "left"});
	
});
</script>
