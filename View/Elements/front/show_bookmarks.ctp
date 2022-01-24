

<?php
if(isset($type) && !empty($type) && $type == 'projects'){
    $projects = $this->ViewModel->checkCurrentProjectList();
    if (isset($projects) && !empty($projects)) {
        foreach($projects as $key => $project){
    ?>
        <li >
            <a class="" href="<?php echo SITEURL;?>projects/index/<?php echo $key; ?>"><span class="left-icon-all"><i class="left-nav-icon bm-ProjectBlack"></i></span> <span class="bookmarksmenutext">
    <?php
            echo htmlentities(trim($project));
             ?>
				</span>
            </a>
        </li>
            <?php
        }
    }
}
?>

<?php
if(isset($type) && !empty($type) && $type == 'workspaces'){
    $workspaces = $this->ViewModel->checkCurrentWspList();
    if (isset($workspaces) && !empty($workspaces)) {
        foreach($workspaces as $key => $workspace){
            $workspace_pid = workspace_pid($key);
    ?>
        <li >
            <a class="" href="<?php echo SITEURL;?>projects/manage_elements/<?php echo $workspace_pid; ?>/<?php echo $key; ?>"><span class="left-icon-all"><i class="left-nav-icon bm-wspBlack"></i></span><span class="bookmarksmenutext">
    <?php
            echo htmlentities(trim($workspace));
             ?></span>
            </a>
        </li>
            <?php
        }
    }
}
?>

<?php
if(isset($type) && !empty($type) && $type == 'tasks'){
    $tasks = $this->ViewModel->checkCurrentTaskList();
    if (isset($tasks) && !empty($tasks)) {
        foreach($tasks as $key => $task){
    ?>
        <li >
            <a class="" href="<?php echo SITEURL;?>entities/update_element/<?php echo $key; ?>#tasks"><span class="left-icon-all"><i class="left-nav-icon bm-Task-Black"></i></span><span class="bookmarksmenutext">
    <?php
            echo htmlentities(trim($task));
         ?></span>
            </a>
        </li>
        <?php
        }
    }
}
?>
<?php
    if ((!isset($projects) || empty($projects)) && (!isset($workspaces) || empty($workspaces)) && (!isset($task) || empty($task))) {
        ?>
        <li><a style="padding-left: 15px;" class="" href="#">No Bookmarks.</a></li>
        <?php
    }

?>