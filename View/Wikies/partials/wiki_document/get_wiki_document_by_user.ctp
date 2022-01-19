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
if (isset($wikipagedocuments) && !empty($wikipagedocuments)) {
    foreach ($wikipagedocuments as $wikipagedocument) {
        ?>
        <li id="wiki-documents-<?php echo $wikipagedocument['WikiPageCommentDocument']['id']; ?>">
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
                <?php 
                if(isset($wikipagedocument['WikiPageCommentDocument']['user_id']) && $wikipagedocument['WikiPageCommentDocument']['user_id'] == $this->Session->read('Auth.User.id')){
                    $is_full_permission_to_current_login = true;
                }
                ?>
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