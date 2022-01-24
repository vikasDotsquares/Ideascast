<?php

echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));

?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3 class="modal-title">Bulk Delete</h3>
</div>
<div class="modal-body popup-select-icon">
	<div class="content" style="min-height:0;">
		 <div class="row">
			<div class="form-group item-selection">
				  <label for="UserUser" class="control-label">Subjects:</label>
				  <?php $subjects = htmlentity($subjects);
				  if( isset($subjects) && !empty($subjects) ){

						echo $this->Form->input('subjects', array(
							'options' => $subjects,
							'class' => 'form-control select',
							'id' => 'bulk_update',
							'multiple' => 'multiple',
							'label' => false,
							'div' => false,
							"size" => 1
						));

				  }
				  ?>
				  <span class="error-text"></span>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer clearfix">

	<span class="del_confirmation">
		<span class="error-text" style="margin-right: 10px;">Are you sure?</span>
		<a href="" class="btn btn-xs btn-success del_success tipText" title="Confirm"><i class="fa fa-check"></i></a>
		<a href="" class="btn btn-xs btn-danger del_cancel tipText" title="Cancel"><i class="fa fa-times"></i></a>
	</span>
	<button type="button" class="btn btn-default bulkupdate" >Delete</button>
	<button type="button" id="discard" class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>

<style>
.del_confirmation {
	display: none;
}
.multiselect-container.dropdown-menu li:not(.multiselect-group) a label.checkbox {
    padding: 5px 20px 5px 40px;
}
.multiselect-container.dropdown-menu > li:not(.multiselect-group) {
    vertical-align: top;
}

</style>
<script>
	$(function(){
		$bulk_update = $('#bulk_update').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'skills[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Subject',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Subject',
            onSelectAll:function(){

				var selected = $('#bulk_update').val();
            	if(selected){
					$(".bulkupdate").removeClass('btn-default').addClass('btn-success');
            		$('.item-selection .error-text').text('');
            	}

				if(selected == null){
					$(".bulkupdate").addClass('btn-default').removeClass('btn-success');
            		$('.item-selection .error-text').text('');
            	}

			},
			onDeselectAll:function(){
				$(".bulkupdate").addClass('btn-default').removeClass('btn-success');
			},
            onChange: function(element, checked) {
            	var selected = $('#bulk_update').val();
            	if(selected){
					$(".bulkupdate").removeClass('btn-default').addClass('btn-success');
            		$('.item-selection .error-text').text('');
            	}

				if(selected == null){
					$(".bulkupdate").addClass('btn-default').removeClass('btn-success');
            		$('.item-selection .error-text').text('');
            	}
            }

        });


		$('body').delegate('.bulkupdate',  'click', function(e){
			e.preventDefault()
			$('.item-selection .error-text').text('');
			var  $this = $(this);
			var selected_ids = $('#bulk_update').val();
			if(selected_ids){
				$('#bulk_update').multiselect("disable");
				$("#modal_bulk_update").modal('hide')
				$("#modal_confirm").find('input[name="selected_ids"]').val(selected_ids);
				$("#modal_confirm").find('input[name="selected_type"]').val('Subject');
				setTimeout(function(){
					$("#modal_confirm").find('.selected-type-text').text('Subjects');
				}, 1000)
				$("#modal_confirm").modal('show');
			}
			else{
				$('.item-selection .error-text').text('Please select at least one subject');
			}
		})
	})

</script>