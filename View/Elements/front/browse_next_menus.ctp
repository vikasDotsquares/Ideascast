<?php
$user_id = $this->Session->read('Auth.User.id');

$icon_class = '';
if($type == 'browse-workspace') {
    $all_list = $this->permission->wsp_of_project($id, $user_id);
    $all_list = Set::combine($all_list, '/Workspace/id', '/Workspace/title');
    $icon_class = 'brs-flag-wspsquares';
}
else if($type == 'browse-area') {
    $all_list = $this->permission->area_of_wsp($id);
    $all_list = Set::combine($all_list, '/Area/id', '/Area/title');
    $icon_class = 'brs-flag-areasquares';
}
else if($type == 'browse-task') {
    $all_list = $this->permission->task_of_area(null, $id );
    $all_list = Set::combine($all_list, '/Element/id', '/Element/title');
    $icon_class = 'brs-flag-task';
}

?>
<script type="text/javascript">
    $(function(){

        $('.browse-nav-dropdown.child-menus').scroll(function(event) {
            event.preventDefault();
            var data = $(this).data();
            if(data && data.children){
                if($(this).hasClass('browse-project')) {
                    if($('.browse-nav-dropdown.browse-workspace').length > 0){
                        $('.browse-nav-dropdown.browse-workspace').remove();
                    }
                    if($('.browse-nav-dropdown.browse-area').length > 0){
                        $('.browse-nav-dropdown.browse-area').remove();
                    }
                }
                else if($(this).hasClass('browse-workspace')) {
                    if($('.browse-nav-dropdown.browse-area').length > 0){
                        $('.browse-nav-dropdown.browse-area').remove();
                    }
                }
                console.log('adfdsfsdf', $(this).data())
            }
            // $('.browse-nav-dropdown.child-menus').remove();
        });
    })
</script>
<!-- <ul class="browse-nav-dropdown"> -->
<?php
if(isset($all_list) && !empty($all_list)){
    ?>
    <li class="clear-filter">
        <a href="#">
            <span class="back-menu"></span>
            <span class="browsemenutext">Clear Status Filter</span>
        </a>
    </li>
    <?php
    foreach ($all_list as $pid => $title) {
        $title = str_replace("'", "", $title);
        $title = str_replace('"', "", $title);
        $link = '';

        // GET PROJECT STATUS
        $status_class = 'brs-flag-completed';
        $status_title = '';
        $entity_status = '';
        if($type == 'browse-workspace') {
            $entity_status = $this->Permission->wsp_status($pid);
            // pr($pid);
            $entity_status = $entity_status[0][0]['ws_status'];
            $prjid = workspace_pid($pid);
            $link = Router::Url( array( 'controller' => 'projects', 'action' => 'manage_elements', $prjid, $pid, 'admin' => FALSE ), TRUE );
        }
        else if($type == 'browse-area') {
            $wspid = area_workspace_id($pid);
            $prjid = workspace_pid($wspid);
            $link = Router::Url( array( 'controller' => 'projects', 'action' => 'manage_elements', $prjid, $wspid, 'admin' => FALSE ), TRUE );
        }
        else if($type == 'browse-task') {
            $entity_status = $this->Permission->task_status($pid);
            $entity_status = $entity_status[0][0]['ele_status'];
            $link = Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $pid, 'admin' => FALSE ), TRUE ).'#tasks';
        }

        if($entity_status == 'not_spacified'){
            $status_title = 'Not Set';
            $status_class = 'brs-flag-undefined';
        }
        else if($entity_status == 'progress'){
            $status_title = 'In Progress';
            $status_class = 'brs-flag-progressing';
        }
        else if($entity_status == 'overdue'){
            $status_title = 'Overdue';
            $status_class = 'brs-flag-overdue';
        }
        else if($entity_status == 'completed'){
            $status_title = 'Completed';
            $status_class = 'brs-flag-completed';
        }
        else if($entity_status == 'not_started'){
            $status_title = 'Not Started';
            $status_class = 'brs-flag-not_started';
        }

 ?>
    <li data-id="<?php echo $pid ?>" data-status="<?php echo $entity_status; ?>" data-type="<?php echo $type; ?>" class="browse-list child-list">
        <a href="#" class="browse-title">
            <span class="browsemenuicon">
                <i class="browseiconleft <?php echo $icon_class; ?>"></i>
                <?php if($type != 'browse-area'){ ?>
                    <i class="browseiconleft <?php echo $status_class; ?> status-flag tipText" title="<?php echo $status_title; ?>"></i>
                <?php } ?>
            </span>
            <span class="browsemenutext browse-to-link" data-url="<?php echo $link; ?>"><?php echo htmlentities($title); ?></span>
            <?php if($type != 'browse-task') { ?>
            <span class="browsemenuarrow open-next"></span>
            <?php } ?>
        </a>
    </li>
<?php }
}else{ ?>
    <?php
    $no_data_text = '';
    if($type == 'browse-workspace') {
        $no_data_text = 'No Workspaces.';
    }
    else if($type == 'browse-area') {
        $no_data_text = 'No Areas.';
    }
    else if($type == 'browse-task') {
        $no_data_text = 'No Tasks.';
    }
    ?>
    <li>
        <a href="#" class="browse-title browse-noproject">
            <span class="browsemenutext"><?php echo $no_data_text; ?></span>
        </a>
    </li>
<?php } ?>
