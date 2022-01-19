<?php
$is_full_permission_to_current_login = $this->Wiki->check_permission($project_id,$this->Session->read("Auth.User.id"));
$project_wiki = $this->Wiki->getProjectWiki($project_id, $this->Session->read("Auth.User.id"));
$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read("Auth.User.id"));
$user_project = $this->Common->userproject($project_id, $this->Session->read("Auth.User.id"));
$gp_exists = $this->Group->GroupIDbyUserID($project_id, $this->Session->read("Auth.User.id"));
if (isset($gp_exists) && !empty($gp_exists)) {
    $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
}
?>
<?php
	echo $this->Html->script(array('ckeditor/ckeditor'));
?>
<?php echo $this->Form->create('WikiPage', array('url' => array('controller' => 'wikies', 'action' => 'create_wiki_page_save'), 'class' => 'form-bordered')); ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="myModalLabel">Create Wiki Page</h3>
</div>
<!-- POPUP MODAL BODY -->
<div class="modal-body">
    <div class="form-group">
        <label class=" " for="txa_title">Title:</label>
        <?php echo $this->Form->input('WikiPage.title', array('label' => false, 'type' => 'text', 'class' => 'form-control title_limit title', 'placeholder' => 'max chars allowed 50', 'id' => 'title'));
        ?>
        <span class="error-message text-danger" ></span>
        <!--<span class="title_error error-message text-danger" ></span>-->
    </div>

    <div class="form-group">
        <label class=" " for="txa_title">Description:</label>
        <?php echo $this->Form->textarea('WikiPage.descriptions', [ 'class' => 'form-control description_limit description', 'id' => 'description_create_wiki', 'escape' => true, 'rows' => 10, 'placeholder' => 'max chars allowed 500']); ?>
        <span class="error-message text-danger descriptions" ></span>
        <textarea id="txts_dec" name="data[WikiPage][description]" style="display:none;" class="description_limit"></textarea>
        <!--<span class="title_error error-message text-danger" ></span>-->
    </div>
    <?php
    echo $this->Form->input('WikiPage.wiki_id', array('label' => false, 'type' => 'hidden', 'value' => $wiki_id));
    echo $this->Form->input('WikiPage.user_id', array('label' => false, 'type' => 'hidden', 'value' => $this->Session->read('Auth.User.id')));
    echo $this->Form->input('WikiPage.project_id', array('label' => false, 'type' => 'hidden', 'value' => $project_id));
    echo $this->Form->input('WikiPage.updated_user_id', array('label' => false, 'type' => 'hidden', 'value' => $this->Session->read('Auth.User.id')));
    ?>
</div>
<div class="modal-footer">
    <button type="submit" id="submitSaves" class="btn btn-success">Save</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>

<?php echo $this->Form->end(); ?>
<script type="text/javascript" >

    $(document).ready(function () {
        CKEDITOR.replace('description_create_wiki');


        $('[data-toggle="popover"]').popover({container: 'body', html: true, placement: "left"});

// Submit Add Form


        $("#submitSaves").click(function (e) {
			
            e.preventDefault();
			$that = $(this);
			$that.attr('disabled','disabled');
            //var postData = $(this).serializeArray();
            var $form = $('#modal_create_wiki_page form');
            var postData = $('#modal_create_wiki_page form').serialize();
            //alert(postData);
            var formURL = $('#modal_create_wiki_page form').attr("action");
 
			
            $form.find(".error-message").text('')
            $.ajax({
                url: formURL,
                type: "POST",
                data: postData,
                dataType: 'JSON',
                success: function (response) {

                    console.log('response', response)
                    if (response.success) {
						console.log("success");
                        if (response.socket_content) {
                            console.log('response.socket_content', response.socket_content)
                            // send web notification
                            $.socket.emit('socket:notification', response.socket_content, function(userdata) {});
                        }
                        setTimeout(function(){
                            location.reload();
                        }, 1000)
                        // $('#modal_create_blogpost').html(response);
                    } else {
						console.log("error");
						$that.removeAttr('disabled');
                        $.each(response.content, function (i, val) {

                            $form.find("." + i).parents('.form-group:first').find('.error-message').text(val)
                        })
                    }
                }
            });

            return;
        });


		/*$('body').delegate("#title", 'keyup focus', function(event){
			var characters = 50;
			event.preventDefault();
			var $error_el = $(this).parent().next('.error-message');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})*/

    });


    timer = setInterval(updateDiv, 100);
    function updateDiv() {
        var editorText = CKEDITOR.instances.description_create_wiki.getData();
        $('#txts_dec').html(editorText);
    }

</script>