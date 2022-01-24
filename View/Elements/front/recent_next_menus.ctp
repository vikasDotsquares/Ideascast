<?php
$user_id = $this->Session->read('Auth.User.id');
// pr($data);
$url = $icon_class = '';
if($type == 'recent-project') {
    $icon_class = 're-ProjectBlack';
}
else if($type == 'recent-wsp') {
    $icon_class = 're-WorkspaceBlack';
}
else if($type == 'recent-tasks') {
    $icon_class = 're-TaskBlack';
}


if(isset($data) && !empty($data)){

    foreach ($data as $key => $val) {
        $detail = $val['recent'];
        $detail['title'] = str_replace("'", "", $detail['title']);
        $detail['title'] = str_replace('"', "", $detail['title']);
        $link = '';

        $status_title = '';
        $entity_status = '';
        if($type == 'recent-project') {
            $link = Router::Url( array( 'controller' => 'projects', 'action' => 'index', $detail['project_id'], 'admin' => FALSE ), TRUE );
        }
        else if($type == 'recent-wsp') {
            $link = Router::Url( array( 'controller' => 'projects', 'action' => 'manage_elements', $detail['project_id'], $detail['workspace_id'], 'admin' => FALSE ), TRUE );
        }
        else if($type == 'recent-tasks') {
            $link = Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $detail['element_id'], 'admin' => FALSE ), TRUE ).'#tasks';
        }
        else if($type == 'recent-assets') {
            // e($detail['element_type']);
            $asset_type = 'links';
            $icon_class = 're-LinkBlack';
            if($detail['element_type'] == 'element_links'){ $asset_type = 'links'; $icon_class = 're-LinkBlack';}
            else if($detail['element_type'] == 'element_notes'){ $asset_type = 'notes'; $icon_class = 're-NoteBlack';}
            else if($detail['element_type'] == 'element_documents'){ $asset_type = 'documents'; $icon_class = 're-DocumentBlack';}
            else if($detail['element_type'] == 'element_mindmaps'){ $asset_type = 'mind_maps'; $icon_class = 're-MindMapBlack';}
            else if($detail['element_type'] == 'element_decisions'){ $asset_type = 'decisions'; $icon_class = 're-DecisionBlack';}
            else if($detail['element_type'] == 'feedback'){ $asset_type = 'feedbacks'; $icon_class = 're-FeedbackBlack';}
            else if($detail['element_type'] == 'votes'){ $asset_type = 'votes'; $icon_class = 're-VoteBlack';}
            $link = Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $detail['element_id'], $detail['relation_id'],$detail['element_id'], 'admin' => FALSE ), TRUE ).'#'.$asset_type;
        }

 ?>
    <li>
        <a href="<?php echo $link; ?>" class="list-link">
            <span class="left-icon-all"><i class="left-nav-icon <?php echo $icon_class; ?>"></i></span>
            <span class="recentmenutext"><?php echo htmlentities($detail['title']); ?></span>
        </a>
    </li>
<?php }
}else{ ?>
    <?php
    $no_data_text = '';
    if($type == 'recent-project') {
        $no_data_text = 'No Recent Projects.';
    }
    else if($type == 'recent-wsp') {
        $no_data_text = 'No Recent Workspaces.';
    }
    else if($type == 'recent-tasks') {
        $no_data_text = 'No Recent Tasks.';
    }
    else if($type == 'recent-assets') {
        $no_data_text = 'No Recent Assets.';
    }
    ?>
    <li>
        <a href="#" class="recent-noproject">
            <span class="recentmenutext"><?php echo $no_data_text; ?></span>
        </a>
    </li>
<?php } ?>