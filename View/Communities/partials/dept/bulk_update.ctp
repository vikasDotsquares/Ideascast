<?php

echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));

?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3 class="modal-title">Bulk Update</h3>
</div>
<div class="modal-body popup-select-icon">
	<div class="content" style="min-height:0;">
		<div class="row">
			<?php if( isset($all_data) && !empty($all_data) ){ ?>
			<?php if(count($all_data) == 1){ ?>
				<div class="message-text" style="margin-bottom: -15px;">You must have at least one Department</div>
			<?php }else{ ?>
				<div class="form-group item-selection">
					  <label for="UserUser" class="control-label">Departments:</label>
					  <?php
							echo $this->Form->input('all_data', array(
								'options' => $all_data,
								'class' => 'form-control select',
								'id' => 'bulk_update',
								'multiple' => 'multiple',
								'label' => false,
								'div' => false,
							));
					  ?>
					  <span class="error-text"></span>
				</div>
			<?php } ?>
			<?php } ?>
		</div>
	</div>
</div>
<div class="modal-footer clearfix">
	<?php if( isset($all_data) && !empty($all_data) && count($all_data) > 1){ ?>
		<button type="button" class="btn btn-default bulkupdate">Delete</button>
	<?php } ?>
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
            checkboxName: 'departments[]',
            includeSelectAllOption: false,
            enableFiltering: true,
            filterPlaceholder: 'Search Departments',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Departments',
            onChange: function(element, checked) {
            	var selected = $('#bulk_update').val();

            	if( selected && selected.length == ($('#bulk_update option').length - 1)){
                    // Disable all other checkboxes.
                    var nonSelectedOptions = $('#bulk_update option').filter(function() {
                        return !$(this).is(':selected');
                    });

                    nonSelectedOptions.each(function() {
                        var input = $('input[value="' + $(this).val() + '"]');
                        input.prop('disabled', true);
                        input.parent('li').addClass('disabled');
                    });
                }
                else {
                    // Enable all checkboxes.
                    $('#bulk_update option').each(function() {
                        var input = $('input[value="' + $(this).val() + '"]');
                        input.prop('disabled', false);
                        input.parent('li').addClass('disabled');
                    });
                }

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


		$('.bulkupdate').off('click').on('click', function(e){

			e.preventDefault()

			$('.item-selection .error-text').text('');
			var  $this = $(this);
			var selected_ids = $('#bulk_update').val();
			if(selected_ids){
				$('#bulk_update').multiselect("disable");
				$("#modal_bulk_update").modal('hide')
				$("#modal_confirm").find('input[name="selected_ids"]').val(selected_ids);
				$("#modal_confirm").find('input[name="selected_type"]').val('dept');
					$("#modal_confirm").find('.selected-type-text').text('Departments');
				$("#modal_confirm").modal('show');
			}
			else{
				$('.item-selection .error-text').text('Please select at least one department');
			}
		})

	})

</script>