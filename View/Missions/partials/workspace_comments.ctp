<?php
if( isset($response['workspace_id']) && !empty($response['workspace_id']) ) {
?>

<?php
	echo $this->Form->create('WorkspaceComment', array('url' => array('controller' => 'todos', 'action' => 'save_comment' ), 'class' => 'form-bordered', 'id' => 'modelFormWorkspaceComment', 'data-async' => ""));
?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h3 class="modal-title" id="createModelLabel">
		<?php if(isset($response['comment_id']) && !empty($response['comment_id']) ){
			echo 'Edit Comment';
			}else {
			echo 'Add Comment';
		} ?>
	</h3>

</div>

<!-- POPUP MODAL BODY -->
<div class="modal-body">

	<?php echo $this->Form->input('WorkspaceComment.workspace_id', [ 'type' => 'hidden', 'class' => 'form-control', 'required'=>true, 'div' => false, 'label' => false, 'value' => $response['workspace_id'] ] ); ?>
	<?php echo $this->Form->input('WorkspaceComment.id', [ 'type' => 'hidden', 'class' => 'form-control', 'required'=>true, 'div' => false, 'label' => false ] ); ?>
	<?php echo $this->Form->input('WorkspaceComment.user_id', [ 'type' => 'hidden', 'class' => 'form-control', 'required'=>true, 'div' => false, 'label' => false, 'value' => $this->Session->read('Auth.User.id') ] ); ?>

	<div class="form-group">
		<label for="comments" >Comment:</label>
		<?php echo $this->Form->textarea('WorkspaceComment.comments', [ 'type' => 'text', 'class' => 'form-control', 'style' => 'resize: vertical;', 'required'=>false, 'div' => false, 'id' => 'comments', 'escape' => true, 'placeholder' => 'max chars allowed 1000', 'label' => false, 'rows' => 8 ] );   ?>
		<span class="error-message text-danger" ></span>
	</div>

</div>

<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
	<button type="button"  class="btn btn-success submit_comment">Save</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>
<?php echo $this->Form->end(); ?>
<?php } ?>
<script type="text/javascript" >
    $(document).ready(function()  {

		$('body').delegate('#comments', 'keyup focus', function(event){
			var characters = 1000;
			event.preventDefault();
			var $error_el = $(this).parent().find('.error-message');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})

    });

</script>
