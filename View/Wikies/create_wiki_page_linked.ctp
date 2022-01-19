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
<?php echo $this->Form->create('WikiPage', array('url' => array('controller' => 'wikies', 'action' => 'create_wiki_page_linked_save'), 'class' => 'form-bordered','id'=>'WikiPageCreateWikiPageLinkedForm')); ?>
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
        <?php echo $this->Form->textarea('WikiPage.descriptions', [ 'class' => 'form-control description_limit description', 'id' => 'description_linked_create', 'escape' => true, 'rows' => 10, 'placeholder' => 'max chars allowed 500']); ?>
        <span class="error-message text-danger" ></span>
        <textarea id="txts_linked" name="data[WikiPage][description]" style="display:none;" class="description_limit"></textarea>
        <!--<span class="title_error error-message text-danger" ></span>-->
    </div>
    <?php
    echo $this->Form->input('WikiPage.is_linked', array('label' => false, 'type' => 'hidden', 'value' => 1));
    echo $this->Form->input('WikiPage.wiki_id', array('label' => false, 'type' => 'hidden', 'value' => $wiki_id));
    echo $this->Form->input('WikiPage.user_id', array('label' => false, 'type' => 'hidden', 'value' => $this->Session->read('Auth.User.id')));
    echo $this->Form->input('WikiPage.project_id', array('label' => false, 'type' => 'hidden', 'value' => $project_id));
    echo $this->Form->input('WikiPage.updated_user_id', array('label' => false, 'type' => 'hidden', 'value' => $this->Session->read('Auth.User.id')));
    ?>
</div>
<div class="modal-footer">
    <button type="submit" id="submitLinkedPageSaves" class="btn btn-success">Save</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>

<?php echo $this->Form->end(); ?>
<script type="text/javascript" >

    $(document).ready(function () {
        CKEDITOR.replace('description_linked_create');


        $('[data-toggle="popover"]').popover({container: 'body', html: true, placement: "left"});

// Submit Add Form

    //$('body').delegate('#WikiPageCreateWikiPageLinkedForm', 'click', function (e) {
    $("#submitLinkedPageSaves").click(function (e) {
            e.preventDefault();
            var $form = $('#WikiPageCreateWikiPageLinkedForm');
            var postData = $('#WikiPageCreateWikiPageLinkedForm').serialize();
            var formURL = $('#WikiPageCreateWikiPageLinkedForm').attr("action");
            $form.find(".error-message").text('')
            $.ajax({
                url: formURL,
                type: "POST",
                data: postData,
                dataType: 'JSON',
                success: function (response) {
                    if (response.success == true) {
                       var $row = $(".contant_selection").find(".selected-now");

                       $row.attr("data-id",response.wiki_page_id);

                        //$row.attr("href",$js_config.base_url+"wikies/linkedpage/"+response.wiki_page_id);
                        $row.removeClass("selected-now");

                        var urlupdate = $(".contant_selection").data("project-id")+'/'+$(".contant_selection").data("user-id")+"/"+$(".contant_selection").data("wiki-id")+"/"+$(".contant_selection").data("page-id");

                         $.ajax({
                            url: $js_config.base_url+"wikies/update_description/"+urlupdate,
                            type: "POST",
                            data: {description:$(".contant_selection").html()},
                            dataType: 'JSON',
                            success: function (response) {
                                $("#modal_create_wiki .modal-content .modal-header .close").trigger("click")
                            }
                        })

                    } else {
                        $.each(response.content, function (i, val) {
                            $form.find("." + i).parents('.form-group:first').find('.error-message').text(val)
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


    timer = setInterval(updateDiv, 100);
    function updateDiv() {
        var editorText = CKEDITOR.instances.description_linked_create.getData();
        $('#txts_linked').html(editorText);
    }

</script>