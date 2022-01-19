<?php
$is_owner = $this->Common->userproject(element_project($element_id), $this->Session->read('Auth.User.id'));

$p_permission = $this->Common->project_permission_details(element_project($element_id), $this->Session->read('Auth.User.id'));
if (isset($lists['Links']) && !empty($lists['Links'])) {

    foreach ($lists['Links'] as $detail) {
        $data = $detail;
        ?>
<div class="row" data-id="<?php echo $data['id']; ?>">
    <div class="col-sm-2 resp" data-value="<?php echo htmlentities($data['title']); ?>">
        <?php echo (strlen($data['title']) > 32) ? substr($data['title'], 0, 32) . '...' : $data['title']; ?>
    </div>
    <div class="col-sm-2 resp link_reff">
        <?php
        if ($data['link_type'] == 1) {
            echo (strlen($data['references']) > 70) ? substr($data['references'], 0, 70) . '...' : $data['references'];
        } else {
            $html = htmlentities($data['embed_code'], ENT_QUOTES);
            echo (strlen($html) > 90) ? substr($html, 0, 90) . '...' : $html;
        }
        ?>
    </div>
    <div class="col-sm-2 resp">
        <?php if($data['creater_id'] > 0){ ?>
        <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $data['creater_id']; ?>" data-target="#popup_modal" data-toggle="modal" class="view_profile text-maroon">
            <i class="fa fa-user"></i>
        </a>
        <?php $element_creator = $this->Common->elementLink_creator($data['id'], element_project($element_id), $this->Session->read('Auth.User.id'));
                echo $element_creator;
            }else{
            echo "N/A";
            }
        ?>
    </div>
    <div class="col-sm-2 resp"><span class="deta-time-i">
            <?php
             echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['created'])),$format = 'd M, Y g:iA');?></span>
    </div>
    <div class="col-sm-2 resp">
        <span class="deta-time-i"><?php echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['modified'])),$format = 'd M, Y g:iA'); ?></span>
    </div>
    <div class="col-sm-2 text-center resp">
        <div class="btn-group">
            <?php if((isset($is_owner) && !empty($is_owner))  || (isset($project_level) && $project_level==1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){ ?>
            <a href="#" class=" update_link tipText" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'update_link', $data['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['id']; ?>" title="Update" data-action="update">
                <i class="edit-icon"></i>
            </a>
            <?php }else{ ?>
            <a href="#" class=" disabled  tipText" title="Update" data-action="update">
                <i class="edit-icon"></i>
            </a>
            <?php }
            if ($data['link_type'] == 1) {
                $hreff = '';
                $chkLinks = explode("//", $data['references']);
                if (isset($chkLinks['1']) && !empty($chkLinks['1'])) {
                    $hreff = $data['references'];
                } else {
                    $hreff = "http://" . $data['references'];
                }
            ?>
            <a class=" visit_link tipText " title="Visit Link" target="_blank" href="<?php echo $hreff; ?>">
                <i class="openlinkicon"></i>
            </a>
            <?php
        } else if ($data['link_type'] == 2) {
            ?>
            <a class=" play_embeded tipText" title="Open Embeded Video" data-toggle="modal" data-target="#modal_medium" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'play_media', $data['id'], 'admin' => FALSE), TRUE); ?>">
                <i class="fa fa-play"><span class=" " style="position: absolute; top: -0px;">E</span></i>
            </a>
            <?php } ?>
            <?php if((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level==1)  || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($is_edit_share) && $is_edit_share >0)){ ?>
                <a href="javascript:void(0);" class=" history_link tipText history" itemtype="element_links" itemid="historylink_<?php echo $data['id']; ?>" data-id="<?php echo $data['id']; ?>" data-action="remove" title="Open History">
                <i class="historyblack"></i>
            </a>

            <a href="#" class=" tipText delete_resource" title="Remove" data-id="<?php echo $data['id']; ?>" data-msg="Are you sure you want to delete this Link?" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'remove_link', $data['id'], 'admin' => FALSE), TRUE); ?>" data-type="link">
               <i class="deleteblack"></i>
            </a>
            <?php  }else { ?>
            <a href="javascript:void(0);" class=" disabled bg-maroon history_link tipText history" itemtype="element_links" itemid="historylink_<?php echo $data['id']; ?>" data-id="<?php echo $data['id']; ?>" data-action="remove" title="Open History">
                <i class="historyblack"></i>
            </a>
            <a href="#" class=" disabled tipText" data-id="<?php echo $data['id']; ?>" title="Remove" data-msg="Are you sure you want to delete this Link?" data-toggle="confirmation" data-header="Authentication">
                <i class="deleteblack"></i>
            </a>
            <?php } ?>
        </div>
    </div>
    <input type="hidden" class="l_type" value="<?php echo $data['link_type'] ?>">
    <?php if ($data['link_type'] == 1) { ?>
    <textarea class="hide" name="" id="hdata_<?php echo $data['id'] ?>"><?php echo $data['references'] ?></textarea>
    <?php } else if ($data['link_type'] == 2) {
                                                                        ?>
    <textarea class="hide" name="" id="hdata_<?php echo $data['id'] ?>"><?php echo $data['embed_code'] ?></textarea>
    <?php } ?>
</div>
<div id="historylink_<?php echo isset($data['id']) && !empty($data['id']) ? $data['id'] :'';; ?>" class="history_update" style="display: none;">
</div>
<?php
    }
}
?>