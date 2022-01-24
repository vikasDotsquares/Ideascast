<?php

$all_list = $this->ViewModel->all_projects_UP();
usort($all_list, function($a, $b){
    $t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['projects']['title']);
    $t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['projects']['title']);
    return strcasecmp($t1, $t2);
    // return $a['projects']['title'] > $b['projects']['title'];
});


$all_list = Set::combine($all_list, '/projects/id', '/projects/title');

 ?>

<?php if(isset($all_list) && !empty($all_list)){
    ?>
    <li class="clear-filter">
        <a href="#">
            <span class="back-menu"></span>
            <span class="browsemenutext">Clear Status Filter</span>
        </a>
    </li>
    <?php
    foreach ($all_list as $pid => $ptitle) {
        $ptitle = str_replace("'", "", $ptitle);
        $ptitle = str_replace('"', "", $ptitle);

        // GET PROJECT STATUS
        $status_class = 'brs-flag-completed';
        $status_title = '';
        $status_type = '';
        $prj_status = $this->Permission->project_status($pid);
        if(isset($prj_status) && !empty($prj_status)) {
            $prj_status = $prj_status[0][0]['prj_status'];
            $status_type = $prj_status;
            if($prj_status == 'not_spacified'){
                $status_title = 'Not Set';
                $status_class = 'brs-flag-undefined';
            }
            else if($prj_status == 'progress'){
                $status_title = 'In Progress';
                $status_class = 'brs-flag-progressing';
            }
            else if($prj_status == 'overdue'){
                $status_title = 'Overdue';
                $status_class = 'brs-flag-overdue';
            }
            else if($prj_status == 'completed'){
                $status_title = 'Completed';
                $status_class = 'brs-flag-completed';
            }
            else if($prj_status == 'not_started'){
                $status_title = 'Not Started';
                $status_class = 'brs-flag-not_started';
            }
        }
 ?>
    <li data-id="<?php echo $pid ?>" data-status="<?php echo $status_type; ?>" data-type="browse-project" class="browse-list">
        <a href="#" class="browse-title">
            <span class="browsemenuicon">
                <i class="browseiconleft brs-ProjectBlack"></i>
                <i class="browseiconleft <?php echo $status_class; ?> status-flag tipText" title="<?php echo $status_title; ?>"></i>
            </span>
            <span class="browsemenutext browse-to-link" data-url="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'index', $pid, 'admin' => FALSE ), TRUE ); ?>"><?php echo htmlentities($ptitle); ?></span>
            <span class="browsemenuarrow open-next"></span>
        </a>
    </li>
<?php }
}else{ ?>
    <li>
        <a href="#" class="browse-title browse-noproject">
            <span class="browsemenutext">No Projects.</span>
        </a>
    </li>
<?php } ?>


