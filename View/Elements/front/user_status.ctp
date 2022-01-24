
<?php
    // $mongo_user_status = $this->Permission->mongo_user_status();
// pr($mongo_user_status);
 ?>
<a data-toggle="dropdown" class="dropdown-toggle anc-user-status"  title="<?php echo ucfirst($mongo_user_status) ; ?>" data-placement="bottom" href="javascript:void(0)"  aria-expanded="false">
    <i class="fas fa-circle user-oc-status status-<?php echo $mongo_user_status; ?>"   ></i>
</a>
    <ul class="dropdown-menu">
        <li class="dropdown-submenu">
            <a href="#" class="update-user-status" data-status="online"><span class="status-nav-icon"><i class="status-all-icon status-online-icon"></i></span> Online</a>
        </li>
        <li class="dropdown-submenu">
            <a href="#" class="update-user-status" data-status="away"><span class="status-nav-icon"><i class="status-all-icon status-away-icon"></i></span> Away</a>
        </li>
        <li class="dropdown-submenu">
            <a href="#" class="update-user-status" data-status="dnd"><span class="status-nav-icon"><i class="status-all-icon status-disturb-icon"></i></span> Do Not Disturb</a>
        </li>
        <li class="dropdown-submenu">
            <a href="#" class="update-user-status" data-status="invisible"><span class="status-nav-icon"><i class="status-all-icon status-invisible-icon"></i></span> Invisible</a>
        </li>
    </ul>


<script type="text/javascript">
    $(function(){
        $('.anc-user-status').tooltip({
            placement: 'bottom',
            container: 'body'
        })

    })
</script>

