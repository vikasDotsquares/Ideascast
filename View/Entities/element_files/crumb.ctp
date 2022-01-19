<div class="main-heading-sec">
<h1><?php echo htmlentities($page_heading, ENT_QUOTES, "UTF-8") ?> </h1>

<div class="subtitles">
<?php if ($date_status != STATUS_NOT_SPACIFIED) { ?>
    <?php echo date('d M, Y', strtotime($this->data['Element']['start_date'])) ?> → <?php echo date('d M, Y', strtotime($this->data['Element']['end_date'])); ?>
	<?php //echo htmlentities($page_subheading); ?>
<?php }else{ echo "No Schedule";} ?>
</div>
</div>
<div class="header-right-side-icon">
<span class="headertag ico-project-summary tipText" title="Tag Team Members" data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::url(array('controller' => 'tags', 'action' => 'add_tags_team_members', 'project' => $project_id, 'workspace' => $workspace_id, 'task' => $element_id, 'type' => 'task', 'admin' => false)); ?>"></span>
<i class="ico-nudge ico-task tipText " title="Send Nudge"  data-toggles="modal" data-targets="#modal_nudge" data-url="<?php echo Router::url(array('controller' => 'boards', 'action' => 'send_nudge_board', 'project' => $project_id, 'workspace' => $workspace_id, 'task' => $element_id, 'type' => 'task', 'admin' => false)); ?>"></i>

<?php

    $currentTasks = $this->ViewModel->checkCurrentTasks($project_id, $this->data['Element']['id']);

    $showTip = 'Set Bookmark';
    $pinClass = '';
    $pinitag = '<i class="headerbookmark"></i>';
    if( $currentTasks > 0 ){
        $showTip = 'Clear Bookmark';
        $pinClass = 'remove_pin';
        //$pinitag = '<i class="current_task_icon_logo"></i>';
        $pinitag = '<i class="headerbookmarkclear"></i>';
    }
$tasktitlelhs = '';
    if( !empty($this->data['Element']['title']) ){

        if( strlen(trim(strip_tags($this->data['Element']['title']))) > 19 ){
            $tasktitlelhs = substr(strip_tags(trim($this->data['Element']['title'])),0,19)."...";
        } else {
            $tasktitlelhs = strip_tags($this->data['Element']['title']);
        }
    }
 ?>
    <a class="tipText fav-current-task <?php echo $pinClass;?>" data-projectid="<?php echo $project_id; ?>" data-taskid="<?php echo $this->data['Element']['id']; ?>" data-tasktitle="<?php echo $tasktitlelhs; ?>" href="#" data-original-title="<?php echo $showTip;?>"><?php echo $pinitag; ?></a>

    </div>