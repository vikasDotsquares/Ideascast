
<!-- EDIT Industry User -->

			<?php echo $this->Form->create('Subject', array('class' => 'form-horizontal form-bordered', 'id' => 'RecordFormedit')); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php if( isset($this->request->data['Subject']['id']) && !empty($this->request->data['Subject']['id']) ){ ?>Edit<?php } else {?>Add<?php }?> Subject</h4>
			</div>

				<div class="modal-body clearfix">
				<?php
					echo $this->Form->input('Subject.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control'));

					if( isset($this->request->data['Subject']['id']) && !empty($this->request->data['Subject']['id']) ){
						echo $this->Form->input('actionType', array('type' => 'hidden','value' => 'editAction', 'label' => false, 'div' => false, 'class' => 'form-control'));
					} else {
						echo $this->Form->input('actionType', array('type' => 'hidden', 'value' => 'addAction', 'label' => false, 'div' => false, 'class' => 'form-control'));
					}
				?>
				<?php echo $this->Form->input('Subject.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>

					<div class=" ">
					    <div class="col-md-12">
							<div class="form-group">
							  <label for="SubjectTitle" class=" ">Subject:</label>
								<?php echo $this->Form->input('Subject.title', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'autocomplete' => 'off')); ?>
								<label id="add_edit_msg" class="text-red normal" style="font-weight:normal; font-size: 11px;"></label>

							</div>
						</div>
					</div>
				</div><!-- /.modal-content -->
				<div class="modal-footer clearfix">
					<button type="submit" id="subject_submit"  class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Save</button>
					<button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><!--<i class="fa fa-times"></i>--> Cancel</button>
				</div>
			<?php echo $this->Form->end(); ?>
<script type="text/javascript" >


// Submit Edit Form
$("#RecordFormedit").submit(function(e){

		$("#subject_submit").addClass('disabled');
		var $form = $(this);
		var postData = new FormData($(this)[0]);
		var formURL = $(this).attr("action");
		$.ajax({
			url : formURL,
			type: "POST",
			dataType: "JSON",
			data : postData,
			beforeSend:function(response){
				$("#subject_submit").addClass('disabled');
			},
			success:function(response){
			$("#subject_submit").removeClass('disabled');


			if( response.success ){
				location.reload();
			}else {
				if( ! $.isEmptyObject( response.content ) ) {

					$.each( response.content, function( ele, msg) {
							var $element = $form.find('[name="data[Subject]['+ele+']"]')
							var $parent = $element.parent();
							console.log($element)
							console.log($parent)

							if( $parent.find('.text-red').length  ) {
								$parent.find('.text-red').text(msg)
							}
						}
					)

				}
			}

			console.log(response);
				if( $.trim(response) != 'success'){
					//$('#add_edit_msg').show();
					$("#subject_submit").removeClass('disabled');
				}else{
					$("#subject_submit").removeClass('disabled');
					//location.reload(); // Saved successfully
				}
			},
			cache: false,
			contentType: false,
			processData: false,
			error: function(jqXHR, textStatus, errorThrown){
				// error
			}
		});
		e.preventDefault(); //STOP default action
		//e.unbind(); //unbind. to stop multiple form submit.
});


$(document).ready(function(){

	setTimeout(function(){
		$('#SubjectTitle').focus();
	},250)



	$('body').delegate('#SubjectTitle', 'keyup focus', function(event){
		var characters = 50;

		event.preventDefault();
		var $error_el = $(this).parent().find('#add_edit_msg');
		if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
			$.input_char_count(this, characters, $error_el);
		}
	})

	$('body').delegate('#SubjectTitle', 'keypress', function(event){

		var englishAlphabetAndWhiteSpace = new RegExp('^[a-zA-Z0-9 \-]$');
		var key = String.fromCharCode(event.which);

		if(event.keyCode == '9' || event.keyCode == '16' || event.keyCode == '13'){
			return;
	    }

		if(event.shiftKey){
			if(event.keyCode == 37){
				//$("#add_edit_msg").removeClass('text-green').addClass('text-red').html("Special Characters and white spaces are not allowed");
				return false;
			}

		}
		if (event.keyCode == 8 || event.keyCode == 37 || event.keyCode == 39 || englishAlphabetAndWhiteSpace.test(key)) {
			//$("#add_edit_msg").html("");
			return true;
		}else {
			//$("#add_edit_msg").removeClass('text-green').addClass('text-red').html("Special Characters and white spaces are not allowed");
		}
		return false;

	});


});
</script>