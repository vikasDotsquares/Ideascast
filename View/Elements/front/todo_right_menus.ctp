<?php
$dolist_count = 0;

$counters = $this->Group->dolist_counters();


    $projects = $this->Group->header_dolist();
    if (isset($counters['total']) && !empty($counters['total'])) {
    	$dolist_count += $counters['total'];
    }

    $projects = $this->Group->header_dolist();
    if(isset($projects) && !empty($projects)) {
    $projects = array_unique($projects);
    }

    if (isset($projects) && !empty($projects)) {
    	foreach ($projects as $toid => $prid) {

    		$projectData = project_detail($prid);
    		$pcounters = $this->Group->dolist_counters($prid);
    		if (isset($pcounters['total']) && !empty($pcounters['total'])) {
    			// $dolist_count += $pcounters['total'];
    			$dolist_count += $pcounters['today_count'] + $pcounters['tom_count'] + $pcounters['up_count'] + $pcounters['ns_count'] + $pcounters['over_count'];
    		}
    	}
    }
if( (isset($counters['total']) && !empty($counters['total'])) || (isset($dolist_count) && !empty($dolist_count)) ) {
    /* ?>
            <li class="dropdown-submenu todoul" data-today="<?php echo $counters['today_count']; ?>" data-tomorrow="<?php echo $counters['tom_count']; ?>" data-upcoming="<?php echo $counters['up_count']; ?>" data-notset="<?php echo $counters['ns_count']; ?>" data-overdue="<?php echo $counters['over_count']; ?>">
                <?php

                if (isset($counters['total']) && !empty($counters['total'])) {
    			?>
                    <a href="<?php echo SITEURL; ?>todos/index" class=""  style="padding-left: 33px;">
                        <span class="todo-ptitle">No Project</span> (<?php echo $counters['total']; ?>)
                    </a>
                <?php } ?>
            </li>
            <?php */
    		if(isset($projects) && !empty($projects)){
    			$projects = array_unique($projects);
    		}
            if (isset($projects) && !empty($projects)) {
                foreach ($projects as $toid => $prid) {
                    $projectData = project_detail($prid);

                    $counters = $this->Group->dolist_counters($prid);

                    if ((isset($counters['total']) && !empty($counters['total'])) && !empty($projectData)) {
                        // pr($d);
                        ?>
                        <li class="dropdown-submenu todoul" data-today="<?php echo $counters['today_count']; ?>" data-tomorrow="<?php echo $counters['tom_count']; ?>" data-upcoming="<?php echo $counters['up_count']; ?>" data-notset="<?php echo $counters['ns_count']; ?>" data-overdue="<?php echo $counters['over_count']; ?>" data-project="<?php echo $prid; ?>">
                            <a href="<?php echo SITEURL; ?>todos/index/project:<?php echo $prid; ?>" class="">
                                <span class="left-icon-all"><i class="left-nav-icon bm-ProjectBlack"></i></span>
                                <span class="todo-ptitle">
                                <?php echo strip_tags($projectData['title']); ?></span> (<?php echo $counters['total']; ?>)</a>

                    <?php }
                    }
                    ?>
            </li>
    <?php } ?>

<?php }else{ ?>
<li class="dropdown-submenu" style="border-bottom: none;"><a style="padding-left: 16px; height: 32px;" href="<?php echo SITEURL; ?>todos/index" class="notodo">No To-dos.</a></li>
<style type="text/css">
  /*  .notodo{
        padding:7px 20px 7px 31px;
        max-width: 240px;
        display: block;
        clear: both;
        font-weight: 400;
        line-height: 1.42857143;
        color: #777;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        background-color: #fff;
    }
   .to-dos-nav-dropdown li:hover .notodo{
    background-color: #e1e3e9;
       cursor: pointer'
}*/
 .to-dos-nav-dropdown li:hover

</style>
<?php } ?>
