<!-- <script  type="text/javascript" src="<?php echo SITEURL.'ckeditor/ckeditor.js';?>"></script> -->
<?php echo $this->Html->script(array('ckeditor/ckeditor'));?>
<?php //pr($blogData);
echo $this->Form->create('TeamTalks',array('url' => array('controller' =>'team_talks','action'=>'edit_blog',$this->request->data['Blog']['id']),'class' => 'form-bordered')); ?>
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h3 class="modal-title" id="myModalLabel">Update Blog</h3>
		</div>
		<!-- POPUP MODAL BODY -->
		<div class="modal-body">
			<div class="form-group"><?php //pr($blogData); ?>
				<label class=" " for="txa_title">Title:</label>
				<?php echo $this->Form->input('Blog.title',array('label' => false, 'type'=>'text','class'=>'form-control','placeholder' => 'max chars allowed 50', 'id' => 'title'   ));
				?>
				<span class="error-message text-danger" ></span>
				<!--<span class="title_error error-message text-danger" ></span>-->
			</div>

			<div class="form-group">
				<label class=" " for="txa_title">Description:</label>
				<?php echo $this->Form->textarea('Blog.descriptions', [ 'class'	=> 'form-control', 'id' => 'description', 'escape' => true, 'rows' => 10, 'placeholder' => 'max chars allowed 500', 'value'=>$this->request->data['Blog']['description'] ] );   ?>
				<span class="error-message text-danger" ></span>
				<textarea id="txts" name="data[Blog][description]" style="display:none;"><?php echo $this->request->data['Blog']['description'];  ?></textarea>
				<!--<span class="title_error error-message text-danger" ></span>-->
			</div>

			<?php
				echo $this->Form->input('Blog.user_id',array('label' => false, 'type'=>'hidden', 'value'=> $this->Session->read('Auth.User.id')));
				echo $this->Form->input('Blog.project_id',array('label' => false, 'type'=>'hidden', 'value'=> $this->request->data['Blog']['project_id'] ));
				echo $this->Form->input('Blog.updated_id',array('label' => false, 'type'=>'hidden', 'value'=> $this->Session->read('Auth.User.id') ));
				echo $this->Form->input('Blog.id',array('label' => false, 'type'=>'hidden', 'value'=> $blog_id ));

			?>
		</div>
		<div class="modal-footer">
			<button type="submit" id="submitSaves" class="btn btn-success">Save</button>
			<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
		</div>

<?php echo $this->Form->end(); ?>
<script type="text/javascript" >
$(document).ready(function(){
 	CKEDITOR.replace( 'description' );


	$('[data-toggle="popover"]').popover({container: 'body',html: true,placement: "left"});

// Submit Add Form


$("#submitSaves").click(function(e){
    e.preventDefault();

	var $form = $('#TeamTalksEditBlogForm') ;
	var postData = $('#TeamTalksEditBlogForm').serialize();
	var postArray = $('#TeamTalksEditBlogForm').serializeArray();

	var formURL =  $('#TeamTalksEditBlogForm').attr("action");
	$form.find(".error-message").text('')
	$.ajax({
		url : formURL,
		type: "POST",
		data : postData,
		dataType : 'JSON',
		success:function(response){

			if( $("#BlogProjectId").val() ){
				var projects_id = $("#BlogProjectId").val();
			} else {
				var projects_id = postArray[5]["value"];
			}

			if( response.success == true ){
				if(response.content){
					// send web notification
					$.socket.emit('socket:notification', response.content.socket, function(userdata){});
				}
				//location.href = $js_config.base_url+'/team_talks/index/project:'+projects_id;
				location.href = '<?php echo SITEURL;?>team_talks/index/project:'+projects_id;
				// $('#modal_create_blogpost').html(response);
				//console.log( JSON.parse(response) );
			}else{
				$.each(response.content, function(i, val){
					$form.find("#"+i).parents('.form-group:first').find('.error-message').text(val)

				})
				ad();
			}
		}
	});

	return;
});


});

 $(function(){
    $('#modal_edit_blogpost').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });

$('body').delegate("input[name='data[Blog][title]']", 'keyup focus', function(event){
	var characters = 50;
	event.preventDefault();
	var $error_el = $(this).parent().next('.error-message');
	if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
		$.input_char_count(this, characters, $error_el);
	}
})

});

    timer = setInterval(updateDiv,100);
    function updateDiv(){
        var editorText = CKEDITOR.instances.description.getData();
        $('#txts').html(editorText);
    }

</script>