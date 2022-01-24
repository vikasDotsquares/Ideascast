<?php 
// echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true)); 
// echo $this->Html->css('projects/bootstrap-input');

// pr($data);
 ?>
<style>
#selected_user {
	list-style: inside none number;
}
#selected_user > li {
	background: #f8f8f8 none repeat scroll 0 0;
	border-radius: 2px;
	float: left;
	margin: 0 20px 5px 0;
	min-width: 46%;
	max-width: 46%;
	padding: 5px;
	overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
<!-- POPUP MODEL BOX CONTENT HEADER -->
		<?php
			echo $this->Form->create('SearchList', array('url' => array('controller' => 'searches', 'action' => 'save_people_list' ), 'class' => 'form-bordered', 'id' => 'modelFormAddSearchList' ));
		?>
		 
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Create People List</h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<div class="form-group">
			<label class=" " for="title">Title:</label>
			<?php echo $this->Form->input('SearchList.title', [ 'type' => 'text', 'class' => 'form-control', 'required'=>false, 'div' => false, 'id' => 'title', 'escape' => true, 'placeholder' => 'max chars allowed 255', 'label' => false, 'autocomplete' => 'off' ] );   ?>
			<span style="" class="error-message text-danger"> </span>
		</div>

		<?php if( isset($data['selection']) && !empty($data['selection'])  && $data['selection'] == 1 ) { ?>
		<div class="form-group">
			<label class=" " for="add_selection">Add Selection:</label>
			<input type="checkbox" value="1" class="checkbox_on_off tipText" name="add_selection" id="add_selection">
			<span style="" class="error-message text-danger"> </span>
		</div>
		
		
		<div class="form-group clearfix">
			<label for="add_selection" class=" ">Selected Users:</label>
			<?php if( isset($data['users']) && !empty($data['users']) ) { ?>
			
			<?php 
				$user_ids = Set::extract($data['users'], '{n}.id');
				$user_ids = implode(',', $user_ids);
				
			?>
			<?php echo $this->Form->input('SearchListUser.user_id', [ 'type' => 'hidden', 'value' => $user_ids, 'required'=>false, 'div' => false, 'id' => 'user_id', 'label' => false,  ] );   ?>
			
			<ul style="" id="selected_user">
			
				<?php foreach($data['users'] as $key => $val) { ?>
				
					<li><?php echo $val['name']; ?></li>
					
				<?php } ?>
				
			</ul>
			
			<?php }
			else {
				echo "No user selected.";
			}
			?>
			
		</div>
		
		<?php } ?>
	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="submit"  class="btn btn-success submit_list">Save</button>
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
		<?php echo $this->Form->end(); ?>

<script type="text/javascript" >
$(function() {

	$('#modal_box').on('hidden.bs.modal', function () {
		$(this).removeData('bs.modal');
	});
	
	$(".checkbox_on_off").checkboxpicker({
		style: true,
		defaultClass: 'tick_default',
		disabledCursor: 'not-allowed',
		offClass: 'tick_off',
		onClass: 'tick_on',
		offTitle: "Off",
		onTitle: "On",
		offLabel: 'Off',
		onLabel: 'On',
	})
	
	$('body').delegate('#title', 'keyup', function(event){
		event.preventDefault();
		
		var val = $(this).val(),
			length = val.length;
			
		var characters = 255;
		if($(this).val().length > characters){
			$(this).val($(this).val().substr(0, characters));
		} 
		$(this).parent().find('.error-message:first').text('Chars: '+characters +", "+$(this).val().length + ' characters entered.')
	})
	
	
	
})
</script>
