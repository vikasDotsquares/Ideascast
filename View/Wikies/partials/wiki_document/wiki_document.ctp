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


<div class="col-sm-8 col-md-8 col-lg-9 wiki-right-section">
    <div id="comments_list" class="tast-list-left-main">
        <?php
        $wikipagedocuments = $this->Wiki->get_wiki_page_public_document($project_id, $user_id, $wiki_id);
        ?>
        <div class="left-main-header clear-box">
            <h5 class="pull-left">Wiki Documents</h5>
            <?php
            echo $this->Form->create('WikiPageCommentDocument', array('url' => array('controller' => 'wikies', 'action' => 'wiki_page_public_document_save'), 'class' => 'form-bordered',  'data-async' => "","id"=>"wikipagedocumentform", 'enctype' => 'multipart/form-data'));
            ?>
            <?php
            echo $this->Form->input('WikiPageCommentDocument.project_id', [ 'type' => 'hidden','value' => $project_id]);
            echo $this->Form->input('WikiPageCommentDocument.user_id', [ 'type' => 'hidden','value' => $this->Session->read("Auth.User.id")]);
            echo $this->Form->input('WikiPageCommentDocument.wiki_id', [ 'type' => 'hidden','value' => $wiki_id]);
            ?>
            <div class="input-group pull-right" style="max-width:100px;">
                <div class="input-group-addon">
                    <i class="fa fa-upload"></i>
                </div>

                <span class="docUpload icon_btn bg-white border-radius-right tipText" data-original-title="Click to upload multiple files">
                    <?php
                    echo $this->Form->input('WikiPageCommentDocument.document_name.', ['value' => '', 'type' => 'file', 'label' => false, 'div' => false, 'required' => false, 'class' => 'form-control upload wiki_page_public_document_uploads', 'id' => 'file_name', 'placeholder' => 'Upload Multiple Files', "multiple" => "multiple"]);
                    ?>

                    <span class="text-blue" id="upText">Upload Multiple Documents</span>
                </span>
            </div>
            <?php
            echo $this->Form->end();
            ?>
        </div>

        <div class="task-list-left-tabs">
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="all">
                    <ul class="people-list document-list" id="wiki-update-comment-list">
                        <?php
                        
                        if (isset($wikipagedocuments) && !empty($wikipagedocuments)) {
                            foreach ($wikipagedocuments as $wikipagedocument) {
                                
                                ?>
                                <li id="wiki-document-<?php echo $wikipagedocument['WikiPageCommentDocument']['id']; ?>">
                                    <div class="comment-people-pic">
                                <?php
                                $user_data = $this->ViewModel->get_user_data($wikipagedocument['WikiPageCommentDocument']['user_id']);
                                $pic = $user_data['UserDetail']['profile_pic'];
                                $profiles = SITEURL . USER_PIC_PATH . $pic;

                                if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
                                    $profiles = SITEURL . USER_PIC_PATH . $pic;
                                } else {
                                    $profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
                                }
                                ?>
                                        <img src="<?php echo $profiles ?>" class="img-circledd tipText" title="<?php echo $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name']; ?>" alt="Personal Image" />

                                    </div>
                                    <div class="comment-people-info people-info">
                                        <p class="doc-type">
                                        <?php
                                        //if (isset($documents) && empty($documents)) {
                                            //foreach ($documents as $doc_key => $doc_val) {
                                                $urlofdoc = SITEURL . WIKI_PAGE_DOCUMENT . $wikipagedocument['WikiPageCommentDocument']['document_name'];
                                                //if(file_exists($urlofdoc)){
                                                $ext = pathinfo($wikipagedocument['WikiPageCommentDocument']['document_name']);
                                                ?>
                                                    <span class="dolist-document">
                                                        <span class="download_asset icon_btn icon_btn_sm icon_btn_teal">

                                                            <span class="icon_text"><?php echo $ext['extension']; ?></span>
                                                        </span>
                                                        <a class="tipText " href="<?php echo $urlofdoc; ?>" download="download" title="<?php echo $wikipagedocument['WikiPageCommentDocument']['document_name']; ?>">
                        <?php echo $ext['filename']; ?>
                                                        </a>
                                                    </span>
                                                <?php
                                                //}
                                            //}
                                        //}
                                        ?>
                                        </p>
                                        <p class="text-normal">
                                            <i>
                                            Created On:
                                            <?php
                                            echo $this->Wiki->_displayDate($wikipagedocument['WikiPageCommentDocument']['created']);
                                            $wiki_page_id = $wikipagedocument['WikiPageCommentDocument']['wiki_page_id'];
                                            $id = $wikipagedocument['WikiPageCommentDocument']['id'];
                                            ?> 		
                                            &nbsp; &nbsp;Created By:
                                            <?php echo $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name']; ?>
                                            </i>
                                            <?php
                                            if (isset($is_full_permission_to_current_login) && $is_full_permission_to_current_login == true) {
                                                ?>
                                                <a data-original-title="Delete Document" class="btn btn-xs btn-danger tipText delete_public_document" data-remote="<?php echo SITEURL . "wikies/wiki_public_document_delete/" . $id; ?>"><i class="fa fa-trash"></i></a>
                                                <?php
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </li>
                        <?php
                            }
                        } else {
                        ?>
                                <li class="text-center">No document found!</li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>
               
            </div>
        </div>
    </div>
</div>
<div class="col-sm-4 col-md-4 col-lg-3 wiki-left-section" >
<?php
echo $this->element('../Wikies/partials/wiki_document/wiki_all_users', array(
    "project_id" => $project_id,
    "user_id" => $this->Session->read('Auth.User.id'),
    "wiki_id" => $wiki_id,
    "wiki_page_id" => null
        )
);
?>
</div>
<div>

<script type="text/javascript">
    $(function ($) {
        $("body").delegate(".wiki_all_users", 'click', function (e) {
            e.preventDefault();
            var $current = $(this), project_id = '<?php echo $project_id; ?>', wiki_id = '<?php echo $wiki_id; ?>', user_id = $current.data("user-id"), actionURL = $current.data("remote");

            $.ajax({
                url: actionURL,
                type: "POST",
                async: false, //blocks window close
                data: {project_id: project_id, wiki_id: wiki_id, user_id: user_id},
                beforeSend: function () {
                    $(".wiki-left-section").html('<div class="loader"></div>');
                },
                complete: function () {
                    $('.tooltip').hide()
                    //$(".wiki-left-section").html("");
                },
                success: function (response) {
                    $('.tooltip').hide()
                    $(".wiki-left-section").html(response);
                }
            });
            return;
        });

        $('body').delegate('.delete_wiki_page', 'click', function (event) {
            event.preventDefault();
            var $current = $(this), project_id = '<?php echo $project_id; ?>', wiki_id = '<?php echo $wiki_id; ?>', wiki_page_id = $current.data("id"), user_id = $current.data("user-id"), actionURL = $current.data("remote");

            var params = {project_id: project_id, user_id: user_id, wiki_id: wiki_id, wiki_page_id: wiki_page_id};

            BootstrapDialog.show({
                title: '<i class="fa fa-check"></i> Confirmation',
                message: 'Are you sure you want to delete this wiki page?',
                type: BootstrapDialog.TYPE_SUCCESS,
                draggable: true,
                buttons: [
                    {
                        //icon: 'fa fa-check',
                        label: ' Yes',
                        cssClass: 'btn-success',
                        autospin: true,
                        action: function (dialogRef) {
                            $.when(
                                    $.ajax({
                                        url: actionURL,
                                        type: "POST",
                                        data: $.param(params),
                                        dataType: "JSON",
                                        global: false,
                                        success: function (response) {
                                            if (response.success) {
                                                $current.addClass('disabled')
                                            }
                                        }
                                    })
                                    ).then(function (data, textStatus, jqXHR) {
                                dialogRef.enableButtons(false);
                                dialogRef.setClosable(false);
                                dialogRef.getModalBody().html('<div class="loader"></div>');
                                setTimeout(function () {
                                    dialogRef.close();
                                }, 500);
                                $('.main-collapse-' + wiki_page_id).addClass("bg-red");
                                $('.main-collapse-' + wiki_page_id).slideUp(500, function () {
                                    $$('.main-collapse-' + wiki_page_id).remove()
                                })

                            })
                        }
                    },
                    {
                        label: ' No',
                        //icon: 'fa fa-times',
                        cssClass: 'btn-danger',
                        action: function (dialogRef) {
                            dialogRef.close();
                        }
                    }
                ]
            });
        })


    });
</script>