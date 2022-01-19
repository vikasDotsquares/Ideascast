<?php 
echo $this->Html->script(array('ckeditor/ckeditor'));
 ?>
<?php echo $this->Form->create('TeamTalks',array('url' => array('controller' =>'wikies','action'=>'save_wiki',$project_id),'class' => 'form-bordered','id'=>'TeamTalksCreateWikiForm' )); ?>		
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h3 class="modal-title" id="myModalLabel">Create Wiki</h3>
		</div>
		<!-- POPUP MODAL BODY -->
		<div class="modal-body">
			<div class="form-group">
				<label class=" " for="txa_title">Title:</label>
				<?php echo $this->Form->input('Wiki.title',array('label' => false, 'type'=>'text','class'=>'form-control','placeholder' => 'max chars allowed 50', 'id' => 'title'));
				  ?>
				<span class="error-message text-danger" ></span>				
			</div>
			<div class="form-group">
				<label class=" " for="txa_title">Description:</label>				
				<?php echo $this->Form->textarea('Wiki.descriptions', [ 'class'	=> 'form-control', 'id' => 'description', 'escape' => true, 'rows' => 10, 'placeholder' => 'max chars allowed 500' ] );   ?>
				<span class="error-message text-danger" ></span>
				<textarea id="txts" name="data[Wiki][description]" style="display:none;"></textarea>
			</div>
			<div class="form-group">
				<label class=" " for="txa_title">Type:</label>
				<div class="radio radio-warning">
                                        <?php 
                                        $wiki_type_open = false;$wiki_type_limited = false;$wiki_status_publish = false;$wiki_status_draft = false;
                                        if(isset($this->request->data['WikiPage']['wtype']) && $this->request->data['WikiPage']['wtype'] == 0){
                                            $wiki_type_open = 'checked="checked"';
                                        }else{
                                            $wiki_type_limited = 'checked="checked"';
                                        }
                                        if(isset($this->request->data['WikiPage']['status']) && $this->request->data['WikiPage']['status'] == 0){
                                            $wiki_status_draft = 'checked="checked"';
                                        }else{
                                            $wiki_status_publish = 'checked="checked"';
                                        }
                                        
                                        ?>
					<input type="radio"  id="wiki_type_open" <?php echo $wiki_type_open; ?> name="data[Wiki][wtype]" class="fancy_input" value="1"   />
					<label class="fancy_labels" for="wiki_type_open" style="width:72px;">Open</label>
					
					<input type="radio" <?php echo $wiki_type_limited; ?> id="wiki_type_limited" name="data[Wiki][wtype]" class="fancy_input" value="0"   />
					<label class="fancy_labels" for="wiki_type_limited" style="width:72px;">Limited</label>
				</div>
				<span class="error-message text-danger" ></span>
			</div>
			<div class="form-group">
				<label class=" " for="txa_title">Page Status:</label>
				
				<div class="radio radio-warning">
					<input type="radio" <?php echo $wiki_status_publish; ?> id="wiki_status_publish" name="data[Wiki][status]" class="fancy_input" value="1"   />
					<label class="fancy_labels" for="wiki_status_publish" style="width:82px;">Publish</label>
					
                                        <input type="radio" <?php echo $wiki_status_draft; ?> id="wiki_status_draft" name="data[Wiki][status]" class="fancy_input" value="0"   />
					<label class="fancy_labels" for="wiki_status_draft" style="width:82px;">Draft</label>
				</div>
				<span class="error-message text-danger" ></span>
			</div>
			<?php 
				echo $this->Form->input('Wiki.user_id',array('label' => false, 'type'=>'hidden', 'value'=> $this->Session->read('Auth.User.id')));
				echo $this->Form->input('Wiki.project_id',array('label' => false, 'type'=>'hidden', 'value'=> $project_id ));
			?>
		</div>	
		<div class="modal-footer">
			<button type="submit" id="submitSave" class="btn btn-success">Save</button>
			<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
		</div>	
		
<?php echo $this->Form->end(); ?>

<script type="text/javascript" >
$(document).ready(function(){
	CKEDITOR.replace( 'description');
	
	
	
	$("#submitSave").click(function(e){
		e.preventDefault(); 
		$that = $(this);
		$(this).attr('disabled','disabled');
		//var postData = $(this).serializeArray();
		var $form = $('#TeamTalksCreateWikiForm') ;
		var postData = $('#TeamTalksCreateWikiForm').serialize();
		 
		var formURL =  $('#TeamTalksCreateWikiForm').attr("action");	
		$form.find(".error-message").text('')
			$.ajax({
				url : formURL,
				type: "POST",
				data : postData,
				dataType : 'JSON',
				success:function(response){				 
					if( response.success == true ){					 
					  location.href='<?php echo SITEURL."wikies/index/project_id:" ?>'+response.project_id;
					}else{ 
						$that.removeAttr('disabled');						 
						$.each(response.content, function(i, val){    
							$form.find("#"+i).parents('.form-group:first').find('.error-message').text(val)
						})
					}
				} 
			});
			return;
		});
		
		
		$('body').delegate("#title", 'keyup focus', function(event){
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