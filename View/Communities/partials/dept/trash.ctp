
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="createModelLabel">Delete Department</h3>
</div>
<div class="modal-body popup-select-icon clearfix">
	<?php if (isset($total_dept) && $total_dept > 1 ) { ?>
		<?php if (isset($totalpeople) && !empty($totalpeople)) {
			echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
			echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
		?>
		<div class="message-text ">The Department you are deleting has <?php echo $totalpeople ?> <?php echo ($totalpeople == 1) ? 'Person' : 'People'; ?> in it.</div>
		<div class="form-group" >

			<label for="UserUser" class="control-label dep-label">Select a Department to move them to:</label>
			<div class="form-control-skill">
			<div class="form-control-select">
				<?php
				$depart_data = array();
				if( isset($all_dept) && !empty($all_dept) ){
					$depart_data = array_map(function ($v) {
							return trim(htmlentities(html_entity_decode($v,ENT_QUOTES, "UTF-8")));
						}, $all_dept);
				}

				echo $this->Form->input('Department.name', array('type' => 'select', 'options' => $depart_data, 'label' => false, 'div' => false, 'class' => 'form-control', 'id'=> 'dept_name')); ?>
			</div>
			<label class="error text-red"></label>
				</div>
		</div>
		<?php
		} else{ ?>
			<div class="message-text">Are you sure you want to delete this Department?</div>
		<?php } ?>
	<?php }else{ ?>
		<div class="message-text">You must have at least one Department.</div>
	<?php } ?>
</div>
<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
	<?php if (isset($total_dept) && $total_dept > 1 ) { ?>
		<button type="button" class="btn btn-success btn-delete"> Delete</button>
    	<button type="button" class="btn btn-danger" data-dismiss="modal"> Cancel</button>
	<?php }else{ ?>
		<button type="button" class="btn btn-success" data-dismiss="modal"> Close</button>
	<?php } ?>
</div>

<?php if ( (isset($total_dept) && $total_dept > 1 ) && (isset($totalpeople) && !empty($totalpeople)) ) { ?>
<script type="text/javascript">
	$(function(){
		$dept_name = $('#dept_name').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Department',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Department',
            onSelectAll:function(){
			},
			onDeselectAll:function(){
			},
            onChange: function(element, checked) {
            }
        });

		$('.btn-delete').click(function(event) {
			event.preventDefault();

			var id = '<?php echo $id; ?>',
				dept_id = $dept_name.val(),
				data = { id: id,  dept_id: dept_id, type: 'dept' };

				if( id > 0 ) {

					$.ajax({
						url: $.module_url + 'trash',
						data: data,
						type: 'post',
						dataType: 'json',
						success: function (response) {
							if(response.success) {
								$.get_departments()
								$('#modal_delete').modal('hide');
							}
						}
					})
				}

		});
	})
</script>
<?php }else{ ?>
<script type="text/javascript">
	$(function(){


		$('.btn-delete').click(function(event) {
			event.preventDefault();

			var $btn = $.current_delete,
				$panel = $btn.parents('.ssd-data-row:first'),
	            data = $panel.data();

			var	id = '<?php echo $id; ?>';
				if( id > 0 ) {

					$.ajax({
						url: $.module_url + 'trash',
						data: { id: id, type: 'dept' },
						type: 'post',
						dataType: 'json',
						success: function (response) {
							if(response.success) {
								$($panel).animate({
									opacity: 0,
									height: 0
								}, 100, function () {
									$(this).remove()
								})
								var parent = $('.dept-list-wrapper').parents('.tab-pane:first');
								$.countRows('dept', parent)

								$('#modal_delete').modal('hide');
							}
						}
					})
				}

		});

	})
</script>
<?php } ?>