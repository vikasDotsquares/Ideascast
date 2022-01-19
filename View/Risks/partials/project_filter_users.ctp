
<?php
$users_list = [];
if(isset($project_risk_users) && !empty($project_risk_users)) {
    foreach ($project_risk_users as $key => $value) {
        $users_list[$value] = user_full_name($value);
    }
    asort($users_list);
} ?>

<div class="dropdown">
    <button class="btn btn-xs dropdown-toggle tipText" title="People Filter" type="button" id="peopleFilterDropdown" data-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-user"></i>
    </button>
    <ul class="dropdown-menu people-filter" role="menu" aria-labelledby="peopleFilterDropdown">
        <li><a href="#" data-user="all">All</a></li>
        <li><a href="#" data-user="my">My Risks</a></li>
        <?php
        if(isset($users_list) && !empty($users_list)){
            foreach ($users_list as $user_id => $fullname) {
        ?>
        <li><a href="#" data-user="<?php echo $user_id; ?>"><?php echo $fullname; ?></a></li>
        <?php
            }
            asort($users_list);
        }
        ?>
    </ul>
</div>