
<?php
$all_users = [];
$exclude_user = $this->Session->read('Auth.User.id');
$disable_user = $this->Session->read('Auth.User.id');
if(isset($project_users) && !empty($project_users)) {
    $risk_users = [];
    if(isset($risk_id) && !empty($risk_id)) {
        $risk_users = $this->ViewModel->risk_users($risk_id);

        $risk_detail = risk_detail($risk_id);
        if($risk_detail) {
            $exclude_user = $risk_detail['RmDetail']['user_id'];
            if($risk_detail['RmDetail']['user_id'] != $this->Session->read('Auth.User.id')) {
                // $disable_user = [$this->Session->read('Auth.User.id')];
            }
        }
    }
// e($exclude_user);
    foreach ($project_users as $key => $id) {
        if($id != $exclude_user && $id != $disable_user){
            $user = $this->ViewModel->get_user_data($id);
            if($user) {
                $all_users[$id] = $user['UserDetail']['first_name'] . ' ' . $user['UserDetail']['last_name'];
            }
        }
    }

    echo $this->Form->select('project_users', $all_users, array('escape' => false, 'empty' => false, 'class' => 'form-control', 'id' => 'project_users', 'multiple' => 'multiple', 'value' => $risk_users));
}
else {
?>
<select class="form-control" id="project_users"></select>
<?php
}
?>

<script type="text/javascript">
    $(function(){
        // ELEMENT'S MULTISELECT BOX INITIALIZATION
        $.project_user = $('#project_users').multiselect({
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'users[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search User',
            enableCaseInsensitiveFiltering: true,
            enableUserIcon: true,
            nonSelectedText: 'Myself',
            onChange: function(option, checked, select) {},
            onDropdownHidden: function(option, closed, select) {
                var selectedUsers = $("#project_users").val();
                var project_id = $('#risk_projects').val();
                // if(selectedUsers) {
                    var data = {
                        project_id: project_id,
                        users: selectedUsers,
                    }
                    $.risk_leaders(data).done(function(message) {
                        // console.log(message);
                    });
                // }
            }
        });
    })
</script>