<?php

$all_list = $this->ViewModel->all_projects_UP();
usort($all_list, function($a, $b){
    $t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['projects']['title']);
    $t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['projects']['title']);
    return strcasecmp($t1, $t2);
    // return $a['projects']['title'] > $b['projects']['title'];
});


$all_list = Set::combine($all_list, '/projects/id', '/projects/title');
// pr($all_list);

if(isset($all_list) && !empty($all_list)){
    ?>
    <li class="talk-clear-filter">
        <a href="#">
            <span class="talk-back-menu"></span>
            <span class="talk-menu-text">Clear Status Filter</span>
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
    <li data-id="<?php echo $pid ?>" data-status="<?php echo $status_type; ?>" data-type="talk-project" class="talk-list">
        <a href="#" class="talk-title">
            <span class="talk-menu-icon">
                <i class="talk-icon-left brs-ProjectBlack"></i>
                <i class="talk-icon-left <?php echo $status_class; ?> talk-status-flag tipText" title="<?php echo $status_title; ?>"></i>
            </span>
            <span class="talk-menu-text talk-to-link open-chat-win" data-project="<?php echo $pid; ?>"><?php echo htmlentities($ptitle, ENT_QUOTES, "UTF-8"); ?></span>
        </a>
    </li>
<?php }
}else{ ?>
    <li>
        <a href="#" class="talk-title talk-noproject">
            <span class="talk-menu-text">No Projects.</span>
        </a>
    </li>
<?php } ?>
<script type="text/javascript">
    $(function(){
        /*$('body').on('click', '.open-chat-win', function(event) {
            event.preventDefault();
            console.log('test111111111111')
            $.win_opened = false;

            $.chating_closed = false;
            $.chating_visibility = true;

            var url = $js_config.CHATURL + '/opuscast?auth=' + $js_config.stoken;
            if ($.current_project_id != 0 && $.current_project_id != '' && $.current_project_id !== undefined) {
                url = $js_config.CHATURL + '/opuscast' + '?auth=' + $js_config.stoken + '&tab=contact' + '&projectid=' + $.current_project_id;
            }

            if (!w || w.closed) {
                $.win_opened = true;
                width = Math.max(Math.round(window.innerWidth * 0.8), 850);
                left = window.outerWidth / 2 + window.screenX - (width / 2);
                height = Math.max(Math.round(window.innerHeight * 0.8), 500);
                tops = window.outerHeight / 2 + window.screenY - (height / 2);

                w = window.open(url,
                    'newChatWindow',
                    'left=' + left + ',top=' + tops + ',width=' + width + ',height=' + height + ',menubar=no,toolbar=no,location=no,status=no,resizable=yes,scrollbars=yes,noopener=no,noreferrer=no,directories=no');

            } else {
                w.focus();
            }
            $(this).blur()
        });*/
    })
</script>


