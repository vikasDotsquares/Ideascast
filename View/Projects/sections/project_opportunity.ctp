<?php

$all_org = (!empty($ssd_data[0][0]['all_org'])) ? json_decode($ssd_data[0][0]['all_org'], true) : [];
$selected_org = (!empty($ssd_data[0][0]['selected_org'])) ? json_decode($ssd_data[0][0]['selected_org'], true) : [];
$selected_org = (!empty($selected_org)) ? Set::extract($selected_org, '{n}.id') : [];
$all_org = htmlentity($all_org, 'title');
function asrt($a, $b) {
		$t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['title']);
		$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['title']);
	    return strcasecmp($t1, $t2);
	}
if(isset($all_org) && !empty($all_org)) usort($all_org, 'asrt');
$all_org = Set::combine($all_org, '{n}.id', '{n}.title');

?>
<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>
<?php echo $this->Form->create('ProjectOrgOpportunity'); ?>
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin: 3px 0 0;"><span aria-hidden="true">&times;</span></button>
			<h3 class="modal-title" id="myModalLabel">Project Opportunity</h3>
		</div>

		<!-- POPUP MODAL BODY -->
		<div class="modal-body">
			<input type="hidden" value="<?php echo $project_id; ?>" name="data[Project][id]" id="project_id" />

		 <div class="content" style="min-height:0;">
			<div class="row" >
				<div class="form-group item-selection">
				  <label for="UserUser" class="control-label">Post Opportunity For:</label>
					<?php
					echo $this->Form->input('ProjectOrg.organization_id', array(
						'options' => $all_org,
						'class' => 'form-control select',
						'id' => 'all_org',
						'multiple' => 'multiple',
						'label' => false,
						'div' => false,
						"size" => 1,
						'default' => $selected_org
					));
					?>
					<span class="error-text"></span>
				</div>
			</div>
			</div>

		</div>
		<!-- POPUP MODAL FOOTER -->
		<div class="modal-footer">

			 <button type="button" id="post_data" class="btn btn-default disabled">Post</button>

			 <?php if( isset($selected_org) && !empty($selected_org) ){ ?>
				<button type="button" class="btn btn-success " id="remove_org" >Unpost</button>
			 <?php } else {?>
				<button type="button" class="btn btn-default disabled" id="remove_org">Unpost</button>
			 <?php } ?>
			 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
		</div>
	<?php echo $this->Form->end(); ?>
<style>
.multiselect-container.dropdown-menu li:not(.multiselect-group) a label.checkbox {
    padding: 5px 20px 5px 40px;
}
.multiselect-container.dropdown-menu > li:not(.multiselect-group) {
    vertical-align: top;
}
</style>
<?php $all = (count($all_org) == count($selected_org)) ? 1 : 0; ?>
<script type="text/javascript" >
$(function(){
	$.selected_all = <?php echo $all; ?>;

	var $all_org =  $('#all_org').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
			numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'opportunities[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Organizations',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Target Organizations',
			onSelectAll:function(){
				var selected = $('#all_org').val();
				if( !$.selected_all ){
					if(selected ){
						$("#post_data").removeClass('btn-default disabled').addClass('btn-success');
						$('.item-selection .error-text').text('');
					}

					if(selected == null){
						$("#post_data").addClass('btn-default disabled').removeClass('btn-success');
						$('.item-selection .error-text').text('');
					}
				}
				$.selected_all = false;

			},
			onDeselectAll:function(){
				$("#post_data").addClass('btn-default disabled').removeClass('btn-success');
			},
            onChange: function(element, checked) {
            	var selected = $('#all_org').val();
            	if(selected){
					$("#post_data").removeClass('btn-default disabled').addClass('btn-success');
            		$('.item-selection .error-text').text('');
            	}

				if(selected == null){
					$("#post_data").addClass('btn-default disabled').removeClass('btn-success');
            		$('.item-selection .error-text').text('');
            	}
            }
        });





})
</script>