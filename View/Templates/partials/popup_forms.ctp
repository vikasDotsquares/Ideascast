
<?php

if( isset($form_name) && !empty($form_name) ) {

		switch( $form_name ) {
			case 'workspace' :
?>

<?php
	echo $this->Form->create('Workspace', array('url' => array('controller' => 'templates', 'action' => 'create_workspace', $response['project_id']), 'class' => 'form-bordered', 'id' => 'modelFormAddWorkspace')); ?>

<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header comm-head">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel">
			<?php
			$btn_text = 'Add';
			if(isset($response['workspace_id']) && !empty($response['workspace_id']) ){
				$btn_text = 'Save';
				echo 'Edit Workspace';
			}else {
				echo 'Add Workspace';
			} ?>
		</h4>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
            <span id="date-error-message" style="" class="error-message text-danger">

            </span>
			<?php if(isset($response['workspace_id']) && !empty($response['workspace_id']) ){
					echo $this->Form->input('ProjectWorkspace.workspace_id', [ 'type' => 'hidden', 'value' => $response['workspace_id'] ] );
			} ?>

			<?php  echo $this->Form->input('ProjectWorkspace.project_id', [ 'type' => 'hidden',  'value' => $response['project_id'] ] ); ?>
			<?php echo $this->Form->input('Workspace.template_id', [ 'type' => 'hidden', 'value' => $response['template_id'] ] ); ?>

		<div class="form-group parent-wrap">
			<label class=" control-label" for="title">Title:</label>
			<?php echo $this->Form->text('Workspace.title', [ 'class'	=> 'form-control', 'required'=>false, 'id' => 'ws_title', 'escape' => true, 'placeholder' => '100 chars', 'autocomplete' => 'off' ] );   ?>
			<span style="" class="error-message text-danger"> </span>
			<span class="error chars_left" ></span>
		</div>



		<div class="form-group parent-wrap">
			<label class="control-label" for="txa_description">Description: <sup>*</sup></label>
			<?php echo $this->Form->textarea('Workspace.description', [ 'class'	=> 'form-control task-desp', 'required'=>false, 'id' => 'txa_description', 'escape' => true, 'rows' => 3, 'placeholder' => '500 chars' ] ); ?>
			<span style="" class="error-message text-danger"> </span>
			<span class="error chars_left " ></span>
		</div>
		<div class="form-group parent-wrap">
			<label class="control-label" for="txa_outcome">Outcome:</label>
			<?php echo $this->Form->textarea('Workspace.outcome', [ 'class'	=> 'form-control outcome', 'required'=>false, 'id' => 'txa_outcome', 'escape' => true, 'rows' => 3, 'placeholder' => '500 chars' ] ); ?>
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
			<div class="col-lg-4 parent-wrap">
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
                <span class="error-message text-danger" ></span>
            </div>
			<label class="control-label col-lg-2" for="end_date">End Date:</label>
			<div class="col-lg-4 parent-wrap">
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
                <span class="error-message text-danger " ></span>
            </div>

		</div>


	</div>


	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="submit"  class="btn btn-primary submit_wsp"><?php echo $btn_text; ?></button>
		 <button type="button" class="btn btn-primary outline-btn-t" data-dismiss="modal">Cancel</button>
	</div>

		<?php echo $this->Form->end(); ?>
<!-- set up the modal to start hidden and fade in and out -->
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
$date = $this->Common->getDateStartOrEnd($project_id);
if( FUTURE_DATE == 'on' ){
	$mindate = isset($date['start_date']) && !empty($date['start_date']) ? date("d M Y",strtotime($date['start_date'])) : '';
} else {
	$mindate =  date("d M Y");
}
$maxdate = isset($date['end_date']) && !empty($date['end_date']) ? date("d M Y",strtotime($date['end_date'])) : '';
?>

<script type="text/javascript" >
	$(function() {
		$('#ws_title').focus();

		$('body').delegate("#ws_title", 'keyup focus', function(event){
			var characters = 100;
			event.preventDefault();
			var $error_el = $(this).parent().find('.error-message');
			console.log($error_el)
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
                $("#end_date").datepicker("setDate", selectedDate);
                  $( "#end_date" ).datepicker( "option", "minDate", selectedDate );
              }
            }
        });
        $( "#end_date" ).datepicker({
            minDate: '<?php echo $mindate;?>',
            maxDate: '<?php echo $maxdate;?>',
           // defaultDate: "+1w",
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


	})
</script>
<?php break; // end workspace form ?>

<?php  case 'element' : ?>

<?php
	echo $this->Form->create('Element', array('url' => array('controller' => 'templates', 'action' => 'create_workspace', $response['project_id']), 'class' => 'form-bordered', 'id' => 'modelFormAddWorkspace')); ?>

<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Add Workspace</h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<!-- <h5 class="project-name"> popup box heading </h5>	-->

		<div class="form-group">
			<label class=" " for="title">Title:</label>
			<?php  echo $this->Form->input('Workspace.project_id', [ 'type' => 'hidden', 'value' => $response['project_id'] ] ); ?>
			<?php echo $this->Form->input('Workspace.template_id', [ 'type' => 'hidden', 'value' => $response['template_id'] ] ); ?>
			<?php echo $this->Form->textarea('Workspace.title', [ 'class'	=> 'form-control', 'id' => 'title', 'escape' => true, 'rows' => 1, 'placeholder' => 'max chars allowed 50' ] );   ?>
		</div>
		<div class="form-group">
			<label class=" " for="description">Description:</label>
			<?php echo $this->Form->textarea('Workspace.description', [ 'class'	=> 'form-control', 'id' => 'description', 'escape' => true, 'rows' => 3, 'placeholder' => 'max chars allowed 250' ] ); ?>
		</div>

		<div class="form-group">
			<input type="radio"  id="ws_status_yes" name="data[Workspace][status]" class="fancy_input" value="1"  />
			<label class="fancy_label" for="ws_status_yes">
			On</label>

			<input  type="radio" id="ws_status_no" name="data[Workspace][status]" class="fancy_input" value="0" />
			<label class="fancy_label" for="ws_status_no">
			Off</label>
		</div>
	</div>


	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="submit"  class="btn btn-warning">Save changes</button>
		 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	</div>

		<?php echo $this->Form->end(); ?>

<?php break; // end workspace form ?>



<?php
		default :
			echo "No form available for the choice.";
		break;
	}
} ?>
