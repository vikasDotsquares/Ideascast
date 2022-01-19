
	<div id="dateAlertBox" class="modal fade">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <!-- dialog body -->
		  <div class="modal-body">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			Please set project dates before setting workspace dates.
		  </div>
		  <!-- dialog buttons -->
		  <div class="modal-footer"><button type="button" class="btn btn-primary" data-dismiss="modal">OK</button></div>
		</div>
	  </div>
	</div>

<?php
$btn_text = 'Add';
if( isset($response) && !empty($response) )  { ?>


<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header comm-head">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel">
			<?php if(isset($response['workspace_id']) && !empty($response['workspace_id']) ){
				echo 'Edit Workspace';
			}else {
				echo 'Add Workspace';
			} ?>
		</h4>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<?php
			echo $this->Form->create('Workspace', array('url' => array('controller' => 'studios', 'action' => 'save_workspace', $response['project_id']), 'class' => 'form-bordered', 'id' => 'modelFormAddWorkspace', 'data-async' => ""));
		?>
		<span id="date-error-message" style="" class="error-message text-danger"> </span>


			<?php
			// echo $this->Form->input('Workspace.studio_status', [ 'type' => 'hidden' ] );

			if(isset($response['workspace_id']) && !empty($response['workspace_id']) ){
				echo $this->Form->input('Workspace.color_code', [ 'type' => 'hidden' ] );
				echo $this->Form->input('ProjectWorkspace.workspace_id', [ 'type' => 'hidden', 'value' => $response['workspace_id'] ] );
				echo $this->Form->input('Workspace.id', [ 'type' => 'hidden', 'value' => $response['workspace_id'] ] );
				$btn_text = 'Save';
			}
			?>

			<?php
			$temlate_id = isset($this->request->data['Workspace']['template_id']) ? $this->request->data['Workspace']['template_id'] : 0;
			echo $this->Form->input('ProjectWorkspace.project_id', [ 'type' => 'hidden',  'value' => $response['project_id'] ] );

			?>
			<?php echo $this->Form->input('Workspace.template_id', [ 'type' => 'hidden', 'value' => $temlate_id ] ); ?>


		<div class="form-group parent-wrap">
			<label class="  control-label" for="title">Title:<sup>*</sup></label>
			<?php
			echo $this->Form->input('Workspace.title', [ 'type' => 'text', 'class' => 'form-control ws_title', 'required'=>false, 'div' => false, 'id' => 'title', 'escape' => true, 'placeholder' => '100 chars', 'label' => false, 'autocomplete' => 'off' ] ); ?>
			<span style="" class="error-message text-danger"> </span>
			<span class="error chars_left " ></span>
		</div>

		<div class="form-group parent-wrap">
			<label class="control-label" for="txa_description">Description: <sup>*</sup></label>
			<?php echo $this->Form->textarea('Workspace.description', [ 'class'	=> 'form-control task-desp', 'id' => 'txa_description', 'escape' => true, 'rows' => 3, 'placeholder' => '500 chars' ] ); ?>
			<span style="" class="error-message text-danger"> </span>
			<span class="error chars_left " ></span>
		</div>
		<div class="form-group parent-wrap">
			<label class="control-label" for="txa_outcome">Outcome:</label>
			<?php echo $this->Form->textarea('Workspace.outcome', [ 'class'	=> 'form-control outcome', 'id' => 'txa_outcome', 'escape' => true, 'rows' => 3, 'placeholder' => '500 chars' ] ); ?>
			<span style="" class="error-message text-danger"> </span>
			<span class="error chars_left " ></span>
		</div>
		<div class="row taskschedule">
			<div class="form-group">
				<label class="control-label col-lg-2" for="date_constraint">Schedule:</label>
				<div class="col-lg-4 " style="pointer-events: none;">
					<div class="switch-task">
						<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="date_constraint" checked="checked" value="1">
						<label for="date_constraint"></label>
					</div>
				</div>
			</div>
		</div>
		<div class="row task-start-end-date" >
			<label class="control-label col-lg-2" for="start_date">Start Date:</label>
				<div class="col-lg-4  parent-wrap">
	            <div class="input-group ">
	                <?php
					if( isset($this->request->data['Workspace']['start_date']) && !empty($this->request->data['Workspace']['start_date']) ){
						$wspStratDate = date('d M Y', strtotime($this->request->data['Workspace']['start_date']));
					} else {
						$wspStratDate = '';
					}

					echo $this->Form->input('Workspace.start_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date', 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small', 'value'=>$wspStratDate ]); ?>
	                <div class="input-group-addon data-new open-start-date-picker calendar-trigger">
	                    <i class="fa fa-calendar"></i>
	                </div>
				</div>
                <span class="sd_error error-message text-danger" ></span>
            </div>
			<label class="control-label col-lg-2" for="end_date">End Date:</label>
			<div class="col-lg-4  parent-wrap">
	            <div class="input-group">
	                <?php
					if( isset($this->request->data['Workspace']['end_date']) && !empty($this->request->data['Workspace']['end_date']) ){
						$wspEndDate = date('d M Y', strtotime($this->request->data['Workspace']['end_date']));
					} else {
						$wspEndDate = '';
					}
					echo $this->Form->input('Workspace.end_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'end_date', 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small', 'value'=>$wspEndDate ]); ?>
	                <div class="input-group-addon data-new open-end-date-picker calendar-trigger">
	                    <i class="fa fa-calendar"></i>
	                </div>
	 			</div>
                <span class="ed_error error-message text-danger" ></span>
            </div>

		</div>
		<?php echo $this->Form->end(); ?>
	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="submit"  class="btn btn-primary submit_wsp submitted"><?php echo $btn_text; ?></button>
		 <button type="button" class="btn btn-primary outline-btn-t" data-dismiss="modal">Cancel</button>
	</div>



	<?php
	$date = $this->Common->getDateStartOrEnd($project_id);

	if( FUTURE_DATE == 'on' ){
		$mindate = isset($date['start_date']) && !empty($date['start_date']) ? date("d M Y",strtotime($date['start_date'])) : '';
	} else {
		//$mindate =  date("d M Y");
		$mindate = isset($date['start_date']) && !empty($date['start_date']) ? date("d M Y",strtotime($date['start_date'])) : '';
	}

	$maxdate = isset($date['end_date']) && !empty($date['end_date']) ? date("d M Y",strtotime($date['end_date'])) : '';

	?>

	<script type="text/javascript" >
	$(function() {

		$('.ws_title').focus();
		$('body').delegate(".ws_title", 'keyup focus', function(event){
			var characters = 100;
			event.preventDefault();
			var $error_el = $(this).parent().find('.error-message');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})

		$('body').delegate("#txa_description", 'keyup focus', function(event){
			var characters = 500;
			event.preventDefault();
			var $error_el = $(this).parent().find('.error-message');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})

		$('body').delegate("#txa_outcome", 'keyup focus', function(event){
			var characters = 500;
			event.preventDefault();
			var $error_el = $(this).parent().find('.error-message');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})

		$('#create_model').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
		});

		var start = '<?php echo date("d M Y");?>';
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
			//dateFormat: 'dd-mm-yy',
			dateFormat: 'dd M yy',
			changeMonth: true,
			changeYear: true,
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
					//$("#end_date").datepicker("setDate", selectedDate);
				  $( "#end_date" ).datepicker( "option", "minDate", selectedDate );
			  }
			}
		});
		$( "#end_date" ).datepicker({
			minDate: '<?php echo $mindate;?>',
			maxDate: '<?php echo $maxdate;?>',
		   // defaultDate: "+1w",
			//dateFormat: 'dd-mm-yy',
			dateFormat: 'dd M yy',
			changeMonth: true,
			changeYear: true,
			beforeShow: function( input, inst ) {
				setTimeout(function(){
						inst.dpDiv.zIndex(9999999)
				}, 2)
			},
			onClose: function( selectedDate ) {

				if(selectedDate != ''){
					$( "#start_date" ).datepicker( "option", "maxDate", selectedDate );
				}

			},
			onSelect: function(selectedDate) {

				if(end == ''){
					this.value='';
					$("#dateAlertBox").modal("show");
				}
			}
		});

		$('.submit_wsp').on( "click", function(e){
			$.save_triggered = true;
			e.preventDefault();

			var $this = $(this),
				$form = $('form#modelFormAddWorkspace'),
				add_ws_url = $form.attr('action'),
				runAjax = true;

			var project_id = '<?php echo $response['project_id']; ?>';

			if( runAjax ) {
				runAjax = false;
				$.ajax({
					url: add_ws_url,
					type:'POST',
					data: $form.serialize(),
					dataType: 'json',
					success: function( response, status, jxhr ) {
						// REMOVE ALL ERROR SPAN
						$form.find('span.error-message.text-danger').text("")

						if( response.success ) {

							if( !$.isEmptyObject(response.content) ) {
								if(response.content.insert){
									$.wsp_insert = true;
								}
								var insert_ws_id = response.content.id;
								if( insert_ws_id ) {
									$('#create_model').modal('hide')
								}
							}
						}
						else {
							if( ! $.isEmptyObject( response.content ) ) {
								$.each( response.content, function( ele, msg) {
									var $element = $form.find('[name="data[Workspace]['+ele+']"]')
									var $parent = $element.parents(".parent-wrap:first");

									if( $parent.find('span.error-message.text-danger').length ) {
										$parent.find('span.error-message.text-danger').text(msg)
									}
								})

							}
							if( ! $.isEmptyObject(response.date_error ) ) {
								$("#date-error-message").html('<div id="successFlashMsg" class="box box-solid bg-red" style="overflow: hidden;  "><div class="box-body"><p>'+response.date_error+'</p></div></div>').show();
							   setTimeout(function(){
									$("#date-error-message").fadeOut("500");
								},2000)
							}
						}
					}
				});
				// end ajax

			}
		})



	})
	</script>
<?php } ?>