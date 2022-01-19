
<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>

<?php $current_user_id = $this->Session->read('Auth.User.id');


$cost_types = $this->Scratch->cost_type_list(null, false);
if(isset($cost_types) && !empty($cost_types)){
    $cost_types = Set::combine($cost_types, '{n}.ctypes.id', '{n}.ctypes.type');
    $cost_types = htmlentity($cost_types);
}
$element_cost = $this->ViewModel->element_cost($element_id, $cost_type);
// pr($element_cost);
$user_element_cost = $this->ViewModel->user_element_cost($element_id, $cost_type);
$element_cost_users = (isset($user_element_cost) && !empty($user_element_cost)) ? Set::extract($user_element_cost, '/UserElementCost/user_id') : [];


$team_type = $member_type = false;
if(isset($element_cost['ElementCost']['team_member_flag']) && !empty($element_cost['ElementCost']['team_member_flag']) && $element_cost['ElementCost']['team_member_flag'] == 1) {
	$team_type = true;
	$member_type = false;
}
else if(isset($element_cost['ElementCost']['team_member_flag']) && !empty($element_cost['ElementCost']['team_member_flag']) && $element_cost['ElementCost']['team_member_flag'] == 2) {
	$team_type = false;
	$member_type = true;
}
else {
	$team_type = true;
	$member_type = false;
}

$element_project = element_project($element_id);
?>
<div class="work-unit-wrapper">
    <div class="team_member_wrapper">
        <div class="work-unit">
            <div class="bs-checkbox radio" style="padding: 0;">
                <label>
                    <input type="radio" value="1" name="team_member" id="team_type" <?php if($team_type){ ?> checked="checked" <?php } ?>>
                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span> Team
                </label>
            </div>
            <div class="bs-checkbox radio">
                <label>
                    <input type="radio" value="2" name="team_member" id="member_type" <?php if($member_type){ ?> checked="checked" <?php } ?>>
                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span> Member
                </label>
            </div>
        </div>
        <?php
        echo $this->Form->input('CostType.id', array('type' => 'select', 'options' => $cost_types, 'label' => false, 'div' => false, 'class' => 'form-control cost-types',  'id' => 'cost_types', 'empty' => 'Select Type', 'default' => $element_cost['ElementCost']['cost_type_id'] ));
        ?>
    </div>
    <div class="bottom-wrapper clearfix">
		<div class="set-budget-cont">
        <div class="col-sm-7 nopadding-left">
        	<?php
        	$input = [];
            $element_user_rates = $this->Permission->element_user_rates($element_project, $element_id, $element_cost_users);
            // pr($element_user_rates);
        	?>
        	<select class="aqua members-dd" <?php if($team_type){ ?> disabled="disabled" <?php } ?> >
        		<option value="">Select</option>
        	<?php
            if(isset($element_user_rates) && !empty($element_user_rates)){
                foreach ($element_user_rates as $key => $value) {
                    $data_work = $value['upc']['day_rate'];
                    $data_hour = $value['upc']['hour_rate'];
                    $data_work_rates = 'data-day="'.$data_work.'"';
                    $data_hour_rates = 'data-hour="'.$data_hour.'"';
                    echo '<option value="'.$value['user_details']['user_id'].'" '.$data_work_rates.' '.$data_hour_rates.'>'.htmlentities($value[0]['fullname']).'</option>';
                }
            }
        	?>
            </select>
            <div class="team-wise-data" <?php if($member_type){ ?> style="display: none" <?php } ?>>
            	<?php if(isset($element_cost['ElementCost']['team_member_flag']) && !empty($element_cost['ElementCost']['team_member_flag']) && $element_cost['ElementCost']['team_member_flag'] == 1) { ?>
            	<?php if(isset($element_cost['UserElementCost']) && !empty($element_cost['UserElementCost'])) {
            			foreach ($element_cost['UserElementCost'] as $key => $value) {
	            			$userElementCost = $value;
	            			if($userElementCost['estimate_spend_flag'] == $cost_type) {
	            				$slash_str = ($userElementCost['work_unit'] == 1) ? '/d' : '/h';
	    			?>
			                <div class="member-data">
                                <input type="hidden" name="team_data_exists" class="team_data_exists" value="1" />
			                    <span class="name-tital-sec">Team</span>
			                    <span class="cost-count"><?php echo number_format($element_cost['ElementCost']['cost'], 2, '.', ',').$slash_str; ?></span>
			                    <input type="hidden" name="local_id" class="hid_local_id" value="<?php echo $userElementCost['id']; ?>" />
			                    <input type="hidden" name="local_unit" class="hid_local_unit" value="<?php echo $userElementCost['work_unit']; ?>" />
			                    <input type="hidden" name="local_rate" class="hid_local_rate" value="<?php echo $userElementCost['work_rate']; ?>" />
			                    <input type="hidden" name="local_quantity" class="hid_local_quantity" value="<?php echo $userElementCost['quantity']; ?>" />
			                    <i class="fa fa-pencil edit-mdata"></i>
			                    <i class="fa fa-trash delete-mdata"></i>
			                </div>
			                <?php } // end estimate_spend_flag check ?>
		                <?php } // end loop $element_cost['UserElementCost'] ?>
	                <?php } ?>
                <?php } // end team_member_flag check ?>
            </div>
            <div class="member-wise-data" <?php if($team_type){ ?> style="display: none" <?php } ?> >
            	<?php if(isset($element_cost['ElementCost']['team_member_flag']) && !empty($element_cost['ElementCost']['team_member_flag']) && $element_cost['ElementCost']['team_member_flag'] == 2) {  ?>
            		<?php if(isset($element_cost['UserElementCost']) && !empty($element_cost['UserElementCost'])) {
                        ?>
                        <input type="hidden" name="member_data_exists" class="member_data_exists" value="1" />
                        <?php
            		foreach ($element_cost['UserElementCost'] as $key => $value) {
            			$userElementCost = $value;
            			if($userElementCost['estimate_spend_flag'] == $cost_type) {
            				$slash_str = ($userElementCost['work_unit'] == 1) ? '/d' : '/h';
	            			$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
	            			$userDetail = $this->ViewModel->get_user( $userElementCost['user_id'], $unbind, 1 );
	            			if(isset($userDetail) && !empty($userDetail)) {
	            				$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
	            			}

                            $user_project_cost = $this->ViewModel->user_project_cost($userElementCost['user_id'], $element_project);
                            $data_work_rates = '';
                            $data_hour_rates = '';
                            if(isset($user_project_cost) && !empty($user_project_cost)) {
                                $data_work = $user_project_cost['UserProjectCost']['day_rate'];
                                $data_hour = $user_project_cost['UserProjectCost']['hour_rate'];
                                if(!empty($data_work)) {
                                    $data_work_rates = $data_work;
                                }
                                if(!empty($data_hour)) {
                                    $data_hour_rates = $data_hour;
                                }
                            }
            		?>
				                <div class="member-data">
				                    <span class="name-tital-sec"><?php echo $user_name; ?></span>
				                    <span class="cost-count"><?php echo number_format(($userElementCost['quantity'] * $userElementCost['work_rate']), 2, '.', ',').$slash_str; ?></span>
				                    <input type="hidden" name="local_id" class="hid_local_id" value="<?php echo $userElementCost['id']; ?>" />
				                    <input type="hidden" name="local_unit" class="hid_local_unit" value="<?php echo $userElementCost['work_unit']; ?>" />
				                    <input type="hidden" name="local_user_id" class="hid_local_user_id" value="<?php echo $userElementCost['user_id']; ?>" />
				                    <input type="hidden" name="local_rate" class="hid_local_rate" value="<?php echo $userElementCost['work_rate']; ?>" />
                                    <input type="hidden" name="local_quantity" class="hid_local_quantity" value="<?php echo $userElementCost['quantity']; ?>" />

                                    <input type="hidden" name="local_day_rate" class="hid_day_rate" value="<?php echo $data_work_rates; ?>" />
				                    <input type="hidden" name="local_hour_rate" class="hid_hour_rate" value="<?php echo $data_hour_rates; ?>" />
				                    <i class="fa fa-pencil edit-mdata"></i>
				                    <i class="fa fa-trash delete-mdata"></i>
				                </div>
							<?php } ?>
		                <?php } ?>
	                <?php } ?>
                <?php } ?>
            </div>
        </div>
        <div class="col-sm-5 nopadding">
            <div class="form-elements">
                <label class="">Work Unit: </label>
                <div class="work-unit">
                    <div class="bs-checkbox radio" style="padding: 0;">
                        <label>
                            <input type="radio" value="1" name="pop_radio" class="pop_radio" checked="checked">
                            <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span> Day
                        </label>
                    </div>
                    <div class="bs-checkbox radio" style="padding: 0">
                        <label>
                            <input type="radio" value="2" name="pop_radio" class="pop_radio">
                            <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span> Hr
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-fild-sec">
                <label class="">Quantity: </label>
                <div class=""><input type="text" name="" class="form-control pop-quantity" style="height: 27px;"></div>
            </div>
            <div class="form-elements">
                <label class="">Work Rate: </label>
                <div class=""><input type="text" name="" class="form-control pop-rate" style="height: 27px;"></div>
            </div>

        </div>
</div>
        <div class="form-but-elements">
            <div class="btn-groups">
            	<a href="#" class="btn btn-primary save-iocn"  <?php if($team_type){ ?> style="display: none;" <?php } ?>>Save Member Details</a>
                <a href="#" class="btn btn-primary value-accepted" >Update</a>
                <a href="#" class="btn btn-primary value-rejected" >Clear</a>
				<a href="#" class="btn  outline-btn-budget " onclick="$(this).parents('.popover').popover('hide');">Cancel</a>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
    .form-control.cost-types {
        /*height: 27px;
        padding: 0px 12px;*/
        margin-bottom: 5px;
    }
    .multiselect-container>li>a>label {
        margin: 0;
        height: 100%;
        cursor: pointer;
        font-weight: 400;
        padding: 3px 20px 3px 10px;
        width: 100%;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }
    .multiselect.error{border: 1px solid #c00;}
    .multiselect.dropdown-toggle.btn .multiselect-selected-text {
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        max-width: 88%;
        flex-grow: 1;
    }

.popover {
    max-width: 414px;
	width: 100%;
	border: none;
	border-radius: 5px 5px 0 0;
	padding: 0;
}
.popover-content {
    padding-bottom: 0;
}
.work-unit-wrapper .team_member_wrapper {
    padding-top: 10px;
}
.popover-title {
    padding: 11px 15px;
    margin: 0;
    font-size: 18px;
    background-color: #3c8dbc;
    border-bottom: 1px solid #3c8dbc;
    border-radius: 5px 5px 0 0;
    color: #fff;
    font-weight: 400;
    line-height: 1.42857143;
}
.set-budget-cont {
    padding-bottom: 23px;
    display: inline-block;
    width: 100%;
}
.form-but-elements {
    border-top: 1px solid #dcdcdc;
    display: block;
    width: auto;
    margin: 0 -14px;
    padding: 15px;
    background-color: #f1f3f4;
}

button.close {
    opacity: 1;
    width: 18px;
    height: 18px;
    display: inline-block;
    background-image: url(../../images/icons/closewhite18x18.png);
    font-size: 0;
    margin-top: 15px;
    margin-right: 15px;
}
.form-but-elements .btn{
	min-width: 70px;
}
.form-but-elements .btn-primary:focus{
	background-color: #3c8dbc;
    border-color: #367fa9;
}

	.outline-btn-budget {
		background-color: transparent;
    color: #3c8dbc;
    border-color: #3c8dbc;
	}
	.outline-btn-budget:hover {
			background-color: #3c8dbc;
    border-color: #367fa9;
		color: #fff;
	}

.cost-count {
    padding-left: 0;
}
</style>
<script type="text/javascript">
    $cost_types = $('#cost_types').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '164',
            checkboxName: 'dept[]',
            includeSelectAllOption: false,
            enableFiltering: false,
            enableCaseInsensitiveFiltering: false,
            nonSelectedText: 'Select Type',
            onSelectAll:function(){
                var selected = $('#cost_types').val();
            },
            onDeselectAll:function(){
            },
            onChange: function(element, checked) {
                var selected = $('#cost_types').val();
            }
        });

</script>