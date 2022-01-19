
<?php
$all_users = [];
$exclude_user = $this->Session->read('Auth.User.id');
$disable_user = $this->Session->read('Auth.User.id');

if(isset($risk_leaders) && !empty($risk_leaders)) {
    $selected_risk_leader = [];
    if(isset($risk_id) && !empty($risk_id)) {
        $selected_risk_leader = risk_leader($risk_id);

        $risk_detail = risk_detail($risk_id);
        if($risk_detail) {
            $exclude_user = $risk_detail['RmDetail']['user_id'];
            if($risk_detail['RmDetail']['user_id'] != $this->Session->read('Auth.User.id')) {
                // $disable_user = [$this->Session->read('Auth.User.id')];
            }
        }
    }

    foreach ($risk_leaders as $key => $id) {
        if($id != $exclude_user && $id != $disable_user) {
            $user = $this->ViewModel->get_user_data($id);
            if($user) {
                $all_users[$id] = $user['UserDetail']['first_name'] . ' ' . $user['UserDetail']['last_name'];
            }
        }
    }

    echo $this->Form->select('risk_leaders', $all_users, array('escape' => false, 'empty' => false, 'class' => 'form-control', 'id' => 'risk_leaders', 'multiple' => 'multiple', 'value' => $selected_risk_leader));
}
else {
?>
<select class="form-control" id="risk_leaders"></select>
<?php
}
?>

<script type="text/javascript">
    $(function(){
        // ELEMENT'S MULTISELECT BOX INITIALIZATION
        $.project_user = $('#risk_leaders').multiselect({
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
            nonSelectedText: 'Select Leader',
            onChange: function(option, checked, select) {},
            onDropdownHidden: function(option, closed, select) {}
        });

        // $('#example-select').multiselect('select', ['1', '2', '4']);
    })
</script>