<style>
	label {
	    width: 100%;
	}
	.multiselect-container{
		max-height: 300px;
		overflow-x: hidden;
		overflow-y: auto;
	}

	.elements-list .list-group{ margin-top:0;}


	.btn-select .btn-select-value {
	    padding: 6px 12px;
	    display: block;
	    position: absolute;
	    left: 0;
	    right: 34px;
	    text-align: left;
	    text-overflow: ellipsis;
	    overflow: hidden;
	    border-top: none !important;
	    border-bottom: none !important;
	    border-left: none !important;
	}

	.program-manager .elements-list .elements-tabs li a {
	    padding: 5px 10px !important;
	    background-color: transparent;
	    color: #5e9224;
	    border: none;
	}

	.program-manager .elements-list .nav.nav-tabs li:last-child {
	    border-right: none;
	}
	.deleteprogram{
		margin-left:3px;
	}

	span.del-options {
	    margin-left: 2px;
		display:none;
		float:right;
	}
	span.del-options i.fa.accept {
	    border: 1px solid;
	    padding: 3px 5px;
	    border-radius: 3px;
	}
	span.del-options i.fa.reject{
	    border: 1px solid;
	    padding: 3px 5px;
	    border-radius: 3px;
	}

	.edit_program{
		border-color: #67a028 !important;
	}

	.exitprogram{
		border-color: #dd4b39 !important;
	}

	/* .no-data-found {
	    color: #bbbbbb;
	    font-size: 20px;
	    left: 4px;
	    text-align: center;
	    text-transform: uppercase;
	    top: 35%;
	    width: 98%;
	} */

	.no-data-found {
	    color: #bbbbbb;
	    font-size: 20px;
	    text-align: center;
	    text-transform: uppercase;
	    display: block;
	}
	#myModalLabel{
		font-size:24px;
	}

</style>
<?php
	$current_user_id = $this->Session->read('Auth.User.id');

	echo $this->Form->create('ProjectProgram', array('url' => array('controller' => 'projects', 'action' => 'save_program'), 'class' => 'form-bordered', 'id' => 'modelFormProjectProgram'));

	// <select name="type" id="multiple_element_type" style="display:none; width:  147px;" multiple="multiple">
	?>
<script type="text/javascript" src="<?php echo SITEURL; ?>js/projects/plugins/selectbox/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="<?php echo SITEURL; ?>css/projects/bs-selectbox/bootstrap-multiselect.css" type="text/css"/>
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel">Program Manager</h4>

	</div>

	<!-- POPUP MODAL BODY -->
		<div class="modal-body program-manager allpopuptabs" style="overflow: visible;">
				<ul class="nav nav-tabs">
					<li class="active">
						<a id="create_program" class="active" href="#program_create" data-toggle="tab">Create</a>
					</li>
					<li class="">
						<a id="update_program" href="#program_update" data-toggle="tab">Update</a>
					</li>
				</ul>

			<div id="elementTabContent" class="tab-content">
				<div class="tab-pane fade active in" id="program_create">
					<div class="form-group"  >
						<div class="row">
							<div class="col-md-12">
								<label class="">Program Name:</label>
								<?php
									echo $this->Form->input('Program.program_name', array(
										'type' => 'text',
										'label' => false,
										'div' => false,
										'class' => 'form-control program_name',
										'placeholder' => '50 Chars Max'
									));
								?>
								<span class="error-message-pname text-danger error chars_left" ></span>
							</div>
							<div class="col-md-12" style="padding-top: 10px;">
								<label class="">Add Projects To Program:<br></label>
							</div>
							<div class="col-md-12" style="display:none;" id="creatreProgramProject">
								<?php
									echo $this->Form->input('ProjectProgram.project_id', array(
										'options' => $projects,
										// 'empty' => 'All Projects',
										'type' => 'select',
										'multiple' => 'multiple',
										'label' => false,
										'div' => false,
										'class' => 'form-control aqua clear',
										'id'=>'multiple_program_project'
									));
								?>
								<span class="error-message-pid text-danger" ></span>
							</div>
						</div>
					</div>
				</div>

				<div class="tab-pane fade" id="program_update" >
				</div>
		</div>
	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		<button type="button" id="update_submit_program" class="btn btn-success hide">Update</button>
		<button type="button" id="submit_program" class="btn btn-success">Save</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	</div>

<?php echo $this->Form->end(); ?>
<script>
$(function() {
$("#update_submit_program").hide();
$("#submit_program").show();

$("#create_program").click(function(){
	$("#update_submit_program").removeClass('show').hide();
	$("#submit_program").show();
});

setTimeout(function(){
	$("#creatreProgramProject").show();
},1)

$('#multiple_program_project').hide()
 setTimeout(function () {

 	//

    $('body').delegate('.program_name', 'keyup focus', function(event){
        event.preventDefault();
        var characters = 50;
        var $error_el = $(this).parents(".col-md-12:first").find('.error');
        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
            $.input_char_count(this, characters, $error_el);
        }
    })


	$('#multiple_program_project').multiselect({
		enableFiltering: true,
		buttonClass	: 'btn btn-default',
		includeSelectAllOption: true,
		buttonWidth: '100%',
		checkboxName: 'data[ProjectProgram][project_id]',
	});

	$('.span_profile').hide()
 }, 1);

 $('#multiple_program_project_update').hide()
	setTimeout(function () {

		$('#multiple_program_project_update').multiselect({
			enableFiltering: true,
			buttonClass	: 'btn btn-default',
			includeSelectAllOption: true,
			buttonWidth: '100%',
			checkboxName: 'data[ProjectProgram][project_id][]',
		});

		$('.span_profile').hide()
	}, 1);

});
</script>
