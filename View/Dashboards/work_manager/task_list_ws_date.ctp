
<style>
.date_box {
	display: block;
	border: 1px solid #ccc;
	padding: 20px 5px 10px 5px;
	display: none;
}
</style>

<?php if( isset($response) && !empty($response) )  { ?>


<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Workspace Schedule</h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<?php
			echo $this->Form->create('Workspace', array('url' => array('controller' => 'dashboards', 'action' => 'task_list_save_workspace', $response['project_id']), 'class' => 'form-bordered', 'id' => 'modelFormUpdateWorkspace', 'data-async' => "")); 
		?>
		
		<span id="date-error-message" style="" class="error-message text-danger"> </span>
		
 

			<div class="form-group clearfix">
				<label class="" for="start_date" style="padding-top: 6px; padding-left: 0px;">Title:</label>
				<div class="wsel_title wsp-<?php echo workspace_status($this->data['Workspace']['id']); ?> "  >
					<?php echo $this->data['Workspace']['title']; ?> 
				</div> 
			</div>
			
		<div class="date_constraints_wrappers" style="">
			<div class="form-group clearfix">
				<label class="" for="start_date" style="padding-top: 6px; padding-left: 0px;">Start Date:</label>
				<div class="input-group">
					<?php echo $this->Form->input('Workspace.start_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date', 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small']); 
					
					echo $this->Form->input('Workspace.id', [ 'type' => 'hidden', ] );
					echo $this->Form->input('Workspace.color_code', [ 'type' => 'hidden', 'value' => $this->data['Workspace']['color_code'] ] );
					?>
                                    
					<div class="input-group-addon  open-start-date-picker calendar-trigger">
						<i class="fa fa-calendar"></i>
					</div>
				</div>
				<span id="start_date_err" class="error-message text-danger"> </span>
				<span class="error chars_left" ></span>
			</div>

			<div class="form-group clearfix">
				<label class="" for="end_date" style="padding-top: 6px; padding-left: 0px;">End Date:</label>
				<div class="input-group">
					<?php echo $this->Form->input('Workspace.end_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'end_date', 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small']); ?>
                                    
					<div class="input-group-addon  open-end-date-picker calendar-trigger">
						<i class="fa fa-calendar"></i>
					</div>
				</div>
                <span id="end_date_err" class="error-message text-danger"> </span>
				<span class="error chars_left" ></span>
			</div>
		</div> 
		<?php echo $this->Form->end(); ?>
	</div>
	
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="submit"  class="btn btn-success submit_wsp submitted">Save</button>
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
	
	

	<?php
	$date = $this->Common->getDateStartOrEnd($project_id);
	
	$mindate =  date("d-m-Y");
	$maxdate = isset($date['end_date']) && !empty($date['end_date']) ? date("d-m-Y",strtotime($date['end_date'])) : '';
	
	?>
	
	<script type="text/javascript" >
	$(function() {
		
		 
		$('#myModal').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
		});
		
		var start = '<?php echo date("d-m-Y");?>';
		var end = '<?php echo $maxdate;?>';
		$(".open-start-date-picker").click(function(){
			$("#start_date").datepicker('show').focus();
		})
		
		$(".open-end-date-picker").click(function(){
			$("#end_date").datepicker('show').focus();
		})
		
		$( "#start_date" ).datepicker({
			minDate: '<?php echo $mindate;?>',
			maxDate: '<?php echo $maxdate;?>',
			//defaultDate: "+1w",
			dateFormat: 'dd-mm-yy',
			changeMonth: true,
			beforeShow: function( input, inst ) { 
				setTimeout(function(){
					inst.dpDiv.zIndex(9999999)
				}, 2)
			},
			onClose: function( selectedDate ) {
				
				if(selectedDate != ''){
					$( "#end_date" ).datepicker( "option", "minDate", selectedDate );
				}
			  
			},
			onSelect: function(selectedDate) { 
			   if(start == ''){
					this.value='';
					$("#dateAlertBox").modal("show");
				} else{
				$("#end_date").datepicker("setDate", selectedDate);
				  $( "#end_date" ).datepicker( "option", "minDate", selectedDate );
			  }
			}
		});
		$( "#end_date" ).datepicker({
			minDate: '<?php echo $mindate;?>',
			maxDate: '<?php echo $maxdate;?>',
		   // defaultDate: "+1w",
			dateFormat: 'dd-mm-yy',
			changeMonth: true,
			beforeShow: function( input, inst ) { 
				setTimeout(function(){
					inst.dpDiv.zIndex(9999999)
				}, 2)
			},
			onClose: function( selectedDate ) {
			},
			onSelect: function(selectedDate) {
				
				if(end == ''){
					this.value='';
					$("#dateAlertBox").modal("show");
				} 
			}
		});
		
		
		$('.submit_wsp').on( "click", function(e){

			e.preventDefault();

			var $this = $(this),
				$form = $('form#modelFormUpdateWorkspace'),
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
						$this.html('<i class="fa fa-spinner fa-pulse"></i>')
					}, 
					success: function( response, status, jxhr ) {
						
						$this.html('Save')
						// REMOVE ALL ERROR SPAN
						$form.find('span.error-message.text-danger').text("")

						if( response.success ) {

							$.save_clicked = true;
							$("#modal_small").modal('hide');
							
						}
						else {
							$this.html('Save')
							if( ! $.isEmptyObject( response.content ) ) {
									
								$.each( response.content, function( ele, msg) { 
										
										var $element = $form.find('[name="data[Workspace]['+ele+']"]')
										var $parent = $element.parent();

										if( $parent.find('span.error-message.text-danger').length  ) {
											$parent.find('span.error-message.text-danger').text(msg)
										}
										if(ele == 'start_date'){
											$("#start_date_err").text(msg);
										}
										if(ele == 'end_date'){
											$("#end_date_err").text(msg);
										}

									}
								) 
							}
							if( ! $.isEmptyObject(response.date_error ) ) {
								$("#date-error-message").html('<div id="successFlashMsg" class="box box-solid bg-red text-white" style="overflow: hidden;  "><div class="box-body"><p>'+response.date_error+'</p></div></div>').show()
							   setTimeout(function(){
									$("#date-error-message").fadeOut("1000"); 
								},3000)
							}
						}
					}
				}); 
			}
		}) 
	})
	</script> 
<?php } ?> 