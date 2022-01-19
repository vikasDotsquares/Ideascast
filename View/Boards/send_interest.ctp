<?php
if( !empty($this->request->params['pass'][0]) && !empty($this->request->params['named']['project']) ){

	$projectID = $this->request->params['named']['project'];
	$projectOwnerID = $this->request->params['pass'][0];

	$projectFullDetails = $this->Common->get_project($projectID);
	$projectName = $projectFullDetails['Project']['title'];
}
?>
<?php //
/* echo $this->Html->script('projects/plugins/wysi-b3-editor/lib/js/wysihtml5-0.3.0', array('inline' => true));
echo $this->Html->script('projects/plugins/wysi-b3-editor/bootstrap3-wysihtml5', array('inline' => true));
echo $this->Html->script('projects/plugins/wysihtml5.editor' , array('inline' => true)); */
?>

<script type="text/javascript" >
$(function() {

 $(document).on('keyup', "#txa_title", function(){
		var characters = 250
		if($(this).val().length > characters){
			$(this).val($(this).val().substr(0, characters));
		}
		console.log($(this).val())
		$(this).parent().find('.error-message.text-danger:first').text('Chars: '+characters +", "+$(this).val().length + ' characters entered.')
	})

})
</script>

<div class="container">
	<div class="row">
			<!-- MAIN CONTENT -->
			<div class="box-content">
				<section class="contact_page  innercontent clearfix">
						<div class="container">
							<div class="row contact_form">
								<?php echo $this->Form->create('send_interest', array('url'=> array('controller'=>'boards','action'=>'send_interest') )); ?>
								<div class="col-md-12 ">
										<div class="row"><div class="col-sm-12" id="showMsg">&nbsp;</div></div>
										<div class="row">
											<div class="col-sm-12">
												<div class="form-group has-feedback">
													<p>Let <strong><?php echo $this->common->userFullname($projectOwnerID); ?></strong> know you are interested in this Project:</p>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-12">
												<div class="form-group has-feedback">
													<p><strong><?php echo $projectName;?></strong></p>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-sm-12">
												<div class="form-group has-feedback border">

													<?php
													$loggedUser = $this->common->userFullname($this->Session->read('Auth.User.id'));
													$defaultMsg = 'I would like to take part in your project.';

													echo $this->Form->textarea('message',array('type'=>'text','rows'=>4,'id' => 'txa_title','label'=>false, "required",'class' =>'form-control' , 'placeholder' => 'max chars allowed 50','value'=>$defaultMsg, 'style' => 'resize: vertical;border:none'));

													echo $this->Form->input('UserDetail.user_id', array('label'=>false,'type'=>'hidden','value'=>$projectOwnerID));
													echo $this->Form->input('Project.id', array('label'=>false,'type'=>'hidden','value'=>$projectID));

													//echo "  ".$loggedUser;
													?>

												</div>
													<span class="error-message error text-danger" ></span>
													<?php echo $this->Form->error('message'); ?>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-12">
												<div class="form-group has-feedback">
													<p><?php echo $loggedUser;?></p>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<div class="pull-right">
													<?php
														echo $this->Form->submit(
														'Send Request',
														array('class' => 'btn btn-success btn-sm pull-left tipText', 'div' => false,'id'=>'sendinterestsbt','type'=>'button')
														);
													?>
													&nbsp;&nbsp;
													<?php
														echo $this->Form->submit(
														'Cancel',
														array('class' => 'btn btn-danger btn-sm pull-right', 'data-dismiss'=>'modal', 'div' => false, 'type'=>'button')
														);
													?>
												</div>
											</div>
										</div>
										<div class="row">&nbsp;</div>
								</div>
								<?php echo $this->Form->end(); ?>
							</div>
						</div>
			    </section>
			</div>
		<!-- END MAIN CONTENT -->
	</div>
	<!-- END OUTER WRAPPER -->
   </div>
<script type="text/javascript" >
$(document).ready(function(){

	$('#sendinterestsbt').click(function(e){
		var postData = $("#send_interestSendInterestForm").serializeArray();
		var formURL = $("#send_interestSendInterestForm").attr("action");
		//alert(formURL);
			$.ajax({
				url : formURL,
				type: "POST",
				data : postData,
				dataType : 'json',
				success:function(response){
					console.log('response', response)
					if(response.success) {
						if(response.content){
							// send web notification
							$.socket.emit('socket:notification', response.content, function(userdata){
								$.create_notification(userdata);
							});
						}
						$( '.modal' ).modal( 'hide' ).data( 'bs.modal', null );
						location.reload();
					}
					//$("#showMsg").html(response);
				}
			});
			e.preventDefault(); //STOP default action
		});

});
</script>