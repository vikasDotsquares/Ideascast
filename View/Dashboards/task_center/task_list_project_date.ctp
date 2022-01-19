
<style>
.date_box {
	display: block;
	border: 1px solid #ccc;
	padding: 20px 5px 10px 5px;
	display: none;
}
.start-date-errors, .end-date-errors, .start-end-date-errors {
	display: block;
}
.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
    cursor: default;
    background-color: #eee;
    opacity: 1;
}
</style>



		<?php
			echo $this->Form->create('Project', array('url' => array('controller' => 'dashboards', 'action' => 'task_list_project_save', $response['project_id']), 'class' => 'form-bordered', 'id' => 'modelFormUpdateProject', 'data-async' => "")); 
		?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="createModelLabel">Project Schedules</h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body elements-list">

		<div class="panel panel-default panel-els">
			
			<div class="panel-body" >
				
				<div class="form-outer">
				<div class="">
						<div style="display: none;" class="response-msg padding alert-success"> </div>
					</div>
				<?php  
				echo $this->Form->input('Project.id', [  'type' => 'hidden'] );
				?>
			
				<div class="form-group">
					<label class=" " for="title">Title:</label>
					<div class="title-group">
						<div class="title-group-input bg-<?php echo str_replace('panel-', '', $this->request->data['Project']['color_code']) ; ?>"  >
							<?php echo strip_tags($this->request->data['Project']['title']); ?>
						</div>
						<div class="title-group-addon"  data-toggle="collapse" href="#collapseDates_<?php echo $this->request->data['Project']['id'] ?>" aria-expanded="false" aria-controls="collapseDates_<?php echo $this->request->data['Project']['id'] ?>">
							<i class="fa"></i>
						</div>
					</div>
				</div>
			
				<div class="date_constraints_wrappers collapse" id="collapseDates_<?php echo $this->request->data['Project']['id'] ?>" data-section="<?php echo $this->request->data['Project']['id'] ?>" >
					
					<span id="" style="" class="error-message text-danger start-date-errors err"></span>
					<span id="" style="" class="error-message text-danger end-date-errors err"></span>
					<span id="" style="" class="error-message text-danger start-end-date-errors err"></span>
					
					
					
					<div class="form-group clearfix">
						<label class="" for="end_date" style="padding-top: 6px; padding-left: 0px;">End Date:</label>
						<div class="input-group">
							<?php 
							$startdate = isset($this->request->data['Project']['start_date']) && !empty($this->request->data['Project']['start_date']) ? date("d M Y", strtotime($this->request->data['Project']['start_date'])) : date('d M Y');
							$endate = isset($this->request->data['Project']['end_date']) && !empty($this->request->data['Project']['end_date']) ? date("d M Y", strtotime($this->request->data['Project']['end_date'])) : '';

							echo $this->Form->input('Project.end_date', [ 'type' => 'text', 'label' => false, 'div' => false,  'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small end_date', 'value' => $endate]); ?>
							
							<div class="input-group-addon  open-end-date-picker calendar-trigger">
								<i class="fa fa-calendar"></i>
							</div>
						</div>
						<span id="end_date_err" class="error-message text-danger"> </span> 
					</div>
					<div class="form-group">
						<label class=" " for="title">Annotate:</label>
						<?php 
						echo $this->Form->input('Project.comments',['type'=>'textarea','label'=>false,'rows'=>"3",'style'=>"resize: vertical;",'class'=>"form-control"]);
						?> 
							 
					</div>

					
				</div>
				</div>
					
				<div class="" id="annotate-list">
				<?php 
					echo $this->element('../Dashboards/task_center/project_annotate', $history);
				?>
				</div>

			</div>
		</div>
	</div>
	
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="button"  class="btn btn-success submit_project submitted">Save</button>
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
	<?php echo $this->Form->end(); ?>
	<script type="text/javascript" >
	$(function() {
		$('.submit_project').on( "click", function(e){

			e.preventDefault();

			var $this = $(this),
				$form = $('form#modelFormUpdateProject'),
				add_ws_url = $form.attr('action'),
				runAjax = true;
				
			if( runAjax ) {
				runAjax = false;
				$.ajax({
					url: add_ws_url,
					type:'POST',
					data: $form.serialize(),
					dataType: 'json',
					beforeSend: function( response, status, jxhr ) {
						// Add a spinner in button html just after ajax starts
						$("#end_date_err").text('');
						$(".response-msg").css("display","none").text('');
						$this.html('<i class="fa fa-spinner fa-pulse"></i>')
					}, 
					success: function( response, status, jxhr ) {
						
						$this.html('Save')
						$(".annotate-list").html( '' );
						if( response.success ) {
							$.save_clicked = true;
							$(".response-msg").show().text("You have successfully updated end date.").delay(2500).fadeOut(600);
							$.post( 
								$js_config.base_url + 'dashboards/project_annotate/'+ response.project_id, 
								function( response ) {
							  		$("#annotate-list").html( response );
								}
							);
							$('#ProjectComments').val('');
							$('#modal_small').modal('hide');
						}
						else { 
							$("#end_date_err").text(response.date_error); 
						}
					}
				});
				// end ajax
				
			}
		})
		
		
		
	})	

	$(".open-end-date-picker").click(function () {
        $(this).parents('.input-group:first').find(".end_date").datepicker('show').focus();
    })

	$(".end_date").datepicker({
            minDate: '<?php echo $startdate; ?>',
            //maxDate: end,
            dateFormat: 'dd M yy',
            changeMonth: true,
			changeYear: true,
			beforeShow: function( input, inst ) {
				setTimeout(function(){
					inst.dpDiv.zIndex(9999999)
				}, 2)
			},
			onClose: function (selectedDate) { 
                if (selectedDate != '') {
                    start = selectedDate;
                    $(this).datepicker("setDate", selectedDate);
                }


            },
        });
	</script>