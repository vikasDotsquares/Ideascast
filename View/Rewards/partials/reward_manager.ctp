<style type="text/css">
    .redeem_ov_section, .ov_settings_section {
        display: block;
        margin: 10px 0 0 0;
    }
    .redeem_ov_section .nav-tabs>li.active>a, .redeem_ov_section .nav-tabs>li.active>a:focus, .redeem_ov_section .nav-tabs>li.active>a:hover, .ov_settings_section .nav-tabs>li.active>a, .ov_settings_section .nav-tabs>li.active>a:focus, .ov_settings_section .nav-tabs>li.active>a:hover {
        color: #555;
        cursor: default;
        background-color: #fff;
         border: none;
        border-bottom-color: #ddd;
    }
    .form-horizontal {
        margin-top: 10px;
    }
    .form-horizontal label.control-label {
        padding-top: 7px;
        margin-bottom: 0;
        font-size: 14px;
        text-align: left;
    }
	.form-horizontal .padding-left30{
		padding-left: 30px;
	}

    .wrap-ov-exchange {
       /* border-bottom: 1px solid #ccc;*/
        padding: 0 0 5px 0;
        margin-bottom: 10px;
    }
    .give_to_charity {
        padding: 0;
        margin: 0;
        vertical-align: bottom;
        position: relative;
    }
    .form-horizontal label.equals-sign {
        font-size: 16px;
    }
    .my-history-wrapper {
        display: block;
		border: 1px solid #ccc;
		padding: 10px 5px 10px 10px;
    }
    .my-history-wrapper .history-inner {
        display: block;
        max-height: 190px;
        overflow: auto;
    }
    .history-row-header {
        /*background-color: #f0f0f0;*/
    }
    .history-row {
        display: block;
        padding-bottom: 2px;
        margin-bottom: 2px;
    }
    .history-inner .history-row:last-child {
        border-bottom: none;
    }
    .history-col {
        padding: 5px;
    }
    .history-col-1 {
        display: inline-block;
        width: 28%;
        font-size: 13px;
    }
    .history-col-2 {
        display: inline-block;
        width: 17%;
        font-size: 13px;
    }
    .history-col-3 {
        display: inline-block;
        width: 33%;
        font-size: 13px;
    }
    .history-col-4 {
        display: inline-block;
        width: 14%;
        font-size: 13px;
    }
    .history-col-5 {
        display: inline-block;
        width: 3%;
        font-size: 15px;
    }
    .history-col .row-box {
        border: 1px solid #ccc;
        display: block;
        padding:2px;
    }

    .inp-the-ov-exchange, .inp-ov-exchange, .inp-ov-exchange-value, .inp-redeem-value {
        pointer-events: none;
    }

    .setting-create-data, .setting-current-data, .setting-accelerate-data {
        display: block;
        padding: 15px 10px 5px;
        border: 1px solid #ccc;
    }
    .create-data-action, .current-data-action, .accelerate-data-action {
        display: block;
        margin: 10px 0 0 0;
        text-align: right;
    }

    .loader-wrap {
        padding-top: 7px;
        padding-left: 10px;
    }
    .give-control-wrap {
        text-align: right;
    }
    .form_chain_confirm {
        display: none;
    }
    .selected-rows-wrap {
        border-bottom: 1px solid #ccc;
        padding-bottom: 10px;
    }
    .give-selected-project, .give-selected-user, .give-selected-type, .give-selected-activity, .giv-remaining-ov, .remaining-the-ov-exchange, .disp-dates, .percent-sign, .current-remaining-ov {
        font-weight: normal;
    }
    .give-popover-info {
        max-width: 150px;
        font-size: 12px;
    }
    .charity-input-wrap span {
        cursor: pointer;
    }
	.ovexchange {
		display: flex;
		width: 100%;
		border: 1px solid #ccc;
	}
	.exchange-nub {
    	width: 45%;
    	padding: 2px;
    	border-right: 1px solid #ccc;
    }
	.exchange-nub-two {
		padding: 2px;
    }
	.fa-gbp-ov{
		/*width:20px;*/
		text-align: center;
		padding: 2px;
		border-right: 1px solid #ccc;
	}
	.form-horizontal .padding-left20 {
		padding-left: 20px;
	}
    /*offers and shopping*/

    .create-offer-wrap {
        display: none;
    }
    .offer-updated-date {
        padding: 5px 0 0 15px;
        float: left;
        font-size: 11px;
    }
    .wrap-btn {
        margin-bottom: 10px;
        display: block;
        padding: 0 15px;
    }
    .create-offer {
        display: block;
        padding: 15px 10px 5px 15px;
        border: 1px solid #ccc;
        margin-bottom: 10px;
        margin-top: 5px;
    }
    .project-offers {
        max-height: 181px;
        overflow-y: auto;
    }
    .project-offers .list-group-item {
        cursor: default;
    }
    .project-offers .list-group-item:hover {
        background-color: #f8f8f8;
    }
    .project-offers .list-group-item-heading {
        font-size: 14px;
        overflow: hidden;
        width: 100%;
        text-overflow: ellipsis;
        white-space: nowrap;
        line-height: normal;
    }
    .project-offers .list-group-item-heading .title-text {
        font-size: 14px;
        white-space: nowrap;
        text-overflow: ellipsis;
        width: 80%;
        display: inline-block;
        overflow: hidden;
    }
    .project-offers .list-group-item-text {
        font-size: 13px;
        margin-bottom: 3px;
        padding-top: 3px;
    }

    .clear-offer-title {
        cursor: pointer;
    }

    .list-group-item.edit, .list-group-item.edit:focus, .list-group-item.edit:hover {
        z-index: 2;
        color: #333;
        background-color: #f0f0f0;
        border-color: #f0f0f0;
    }
    .offer-confirm {
        display: none;
    }
    .btn-confirm {
        padding: 1px 4px;
        border-radius: 50%;
        /*background-color: #ccc;*/
    }
    .shop-outer {
        display: block;
        margin: 0;
    }
    .list-outer {
        margin: 0 -15px;
        padding: 10px 20px 0 20px;
    }

    .shopping-list.project-offers {
        max-height: 200px;
        overflow-y: auto;
    }
    .charity-form-wrapper, .charity-form-error {
        display: none;
    }
	
	.info-msg{
		margin: 20px 0;
	}
	
	
</style>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="createModelLabel">Reward Manager</h3>
</div>
<div class="modal-body clearfix allpopuptabs">
    <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
    <div class="rew-manager">
        <ul class="nav nav-tabs rew-manager-tabs">
            <li class="active">
                <a id="anc_redeem_ov" class="active" href="#main_redeem_ov" data-toggle="tab" aria-expanded="true">Use OV</a>
            </li>
            <li>
                <a id="anc_give_ov" href="#main_give_ov" data-toggle="tab" aria-expanded="false">Give OV</a>
            </li>
            <li>
                <a id="anc_ov_settings" href="#main_ov_settings" data-toggle="tab" aria-expanded="false">OV Settings</a>
            </li>
        </ul>
    </div>
    <div id="rewManagerTabContent" class="tab-content">

        <div class="tab-pane fade active in" id="main_redeem_ov">
            <div class="redeem_ov_section">
                <div class="redeem-manager">
                    <ul class="nav nav-tabs redeem-manager-tabs">
                        <li class="active">
                            <a id="anc_sub_redeem_ov" class="active" href="#sub_redeem_ov" data-toggle="tab" aria-expanded="true">Redeem</a>
                        </li>
                        <li>
                            <a id="anc_sub_shop" href="#sub_shop" data-toggle="tab" aria-expanded="false">Shop</a>
                        </li>
                        <li>
                            <a id="anc_sub_member_history" href="#sub_member_history" data-toggle="tab" aria-expanded="false">Member History</a>
                        </li>
                        <li>
                            <a id="anc_sub_my_history" href="#sub_my_history" data-toggle="tab" aria-expanded="false">My History</a>
                        </li>
                    </ul>
                </div>
                <div id="redeeemTabContent" class="tab-content">
                    <div class="tab-pane fade active in" id="sub_redeem_ov">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-3 nopadding-right padding-left30">Select Project:</label>
                                <div class="col-sm-9">
                                    <?php
                                    $redeem_projects = [];
                                    if(isset($all_projects) && !empty($all_projects)) {
                                        foreach ($all_projects as $key => $value) {
                                            if(user_remaining_redeem_points($user_id, $key)){
                                                $redeem_projects[$key] = $value;
                                            }
                                        }
                                    }
                                    echo $this->Form->input('sel_redeem_ov', array(
                                        'options' => $redeem_projects,
                                        'empty' => 'Project',
                                        'class' => 'form-control',
                                        'id' => 'sel_redeem_ov',
                                        'label' => false,
                                        'div' => false
                                    ));
                                    ?>
                                </div>
                            </div>
                            <div class="redeem-data">
                                <!-- <div class="info-msg">Select A Project</div> -->
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="sub_my_history">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-3 nopadding-right padding-left30">Select Project:</label>
                                <div class="col-sm-9">
                                    <?php
                                        echo $this->Form->input('sel_history_project', array(
                                            'options' => $all_projects,
                                            'empty' => 'Project',
                                            'class' => 'form-control',
                                            'id' => 'sel_history_project',
                                            'label' => false,
                                            'div' => false
                                        ));
                                    ?>
                                </div>
                            </div>
                            <div class="my-history-outer">
                                <!-- <div class="info-msg">Select A Project</div> -->
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="sub_member_history">

                       <div class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-3 nopadding-right padding-left30">Select Project:</label>
                                <div class="col-sm-8">
                                    <?php
                                        echo $this->Form->input('sel_member_history_project', array(
                                            'options' => $owner_projects,
                                            'empty' => 'Project',
                                            'class' => 'form-control',
                                            'id' => 'sel_member_history_project',
                                            'label' => false,
                                            'div' => false
                                        ));
                                    ?>
                                </div>
                            </div>
                           <div class="form-group">
                                <label class="control-label col-sm-3 nopadding-right padding-left30">Select User:</label>
                                <div class="col-sm-8">
                                    <?php
                                        echo $this->Form->input('sel_member_history_users', array(
                                            'options' => [],
                                            'empty' => 'User',
                                            'class' => 'form-control not-editable',
                                            'id' => 'sel_member_history_users',
                                            'label' => false,
                                            'div' => false
                                        ));
                                    ?>
                                </div>
                                <div class="col-sm-1 nopadding-left">
                                    <i class="fa fa-spinner fa-pulse loader-icon stop"></i>
                                </div>
                            </div>

                            <div class="member-history-outer"></div>
                        </div>

                    </div>
                    <div class="tab-pane fade" id="sub_shop">

					   <div class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-3 nopadding-right padding-left30">Select Project:</label>
                                <div class="col-sm-8">
                                    <?php
                                        $offer_projects = [];
                                        if(isset($all_projects) && !empty($all_projects)) {
                                            foreach ($all_projects as $key => $value) {
                                                $offer_exists = project_offers($key);
                                                if($offer_exists){
                                                    $offer_projects[$key] = $value;
                                                }
                                            }
                                        }
                                        echo $this->Form->input('sel_shop_project', array(
                                            'options' => $offer_projects,
                                            'empty' => 'Project',
                                            'class' => 'form-control',
                                            'id' => 'sel_shop_project',
                                            'label' => false,
                                            'div' => false
                                        ));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3 nopadding-right padding-left30">Select Offer:</label>
                                <div class="col-sm-8">
                                    <?php
                                        echo $this->Form->input('sel_an_offer', array(
                                            'options' => [],
                                            'empty' => 'Offer',
                                            'class' => 'form-control not-editable',
                                            'id' => 'sel_an_offer',
                                            'label' => false,
                                            'div' => false
                                        ));
                                    ?>
                                </div>
                                <div class="col-sm-1 nopadding-left">
                                    <i class="fa fa-spinner fa-pulse loader-icon stop"></i>
                                </div>
                            </div>
                            <div class="shop-outer clearfix"></div>
                            <div class="user-shopping-outer"></div>
                        </div>

					</div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="main_give_ov">
            <div class="give-show-info"></div>
            <div class="form-horizontal form_chain_selection">
                <div class="form-group">
                    <label class="control-label col-sm-3 nopadding-right padding-left30">Select Project:</label>
                    <div class="col-sm-8 nopadding-right">
                        <?php
                        $give_ov_projects = [];
                        if(isset($owner_projects) && !empty($owner_projects)) {
                            foreach ($owner_projects as $key => $value) {
                                $project_allocated = 0;
                                $project_reward_setting = project_reward_setting($key, 1);
                                if($project_reward_setting){
                                    $project_reward_setting = $project_reward_setting['RewardSetting'];
                                    if(isset($project_reward_setting['remaining_ov']) && !empty($project_reward_setting['remaining_ov'])) {
                                        $give_ov_projects[$key] = $value;
                                    }
                                }
                            }
                        }
                        echo $this->Form->input('sel_give_project', array(
                            'options' => $give_ov_projects,
                            'empty' => 'Project',
                            'class' => 'form-control',
                            'id' => 'sel_give_project',
                            'label' => false,
                            'div' => false
                        ));
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3 nopadding-right padding-left30">Select User:</label>
                    <div class="col-sm-8 nopadding-right">
                        <select class="form-control not-editable" id="sel_give_user"><option>User</option></select>
                    </div>
                    <div class="col-sm-1 loader-wrap">
                        <i class="fa fa-spinner fa-pulse loader-icon stop"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3 nopadding-right padding-left30">Select Type:</label>
                    <div class="col-sm-8 nopadding-right">
                        <?php
                        echo $this->Form->input('sel_give_type', array(
                            'options' => $reward_types,
                            'empty' => 'Type',
                            'class' => 'form-control not-editable',
                            'id' => 'sel_give_type',
                            'label' => false,
                            'div' => false
                        ));
                        ?>
                    </div>
                    <div class="col-sm-1 loader-wrap">
                        <i class="fa fa-spinner fa-pulse loader-icon stop"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3 nopadding-right padding-left30">Select Activity:</label>
                    <div class="col-sm-8 nopadding-right">
                        <select class="form-control not-editable" id="sel_give_activity"><option value="">Activity</option></select>
                    </div>
                    <div class="col-sm-1 loader-wrap">
                        <i class="fa fa-spinner fa-pulse loader-icon stop"></i>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-11 give-control-wrap nopadding-right">
                        <a href="#" class="btn btn-warning btn-xs give-next not-editable">Next</a>
                        <a href="#" class="btn btn-danger btn-xs give-clear">Clear</a>
                    </div>
                </div>
            </div>
            <div class="form-horizontal form_chain_confirm  col-sm-12">
                <div class="form-group selected-rows-wrap">
                    <div class="selected-row">
                        <label class="control-label col-sm-2 nopadding-right">Project:</label>
                        <label class="control-label col-sm-9 nopadding-right give-selected-project"></label>
                    </div>
                    <div class="selected-row">
                        <label class="control-label col-sm-2 nopadding-right">User:</label>
                        <label class="control-label col-sm-9 nopadding-right give-selected-user"></label>
                    </div>
                    <div class="selected-row">
                        <label class="control-label col-sm-2 nopadding-right">Type:</label>
                        <label class="control-label col-sm-9 nopadding-right give-selected-type"></label>
                    </div>
                    <div class="selected-row">
                        <label class="control-label col-sm-2 nopadding-right">Activity:</label>
                        <label class="control-label col-sm-9 nopadding-right give-selected-activity"></label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4 nopadding-right">OV Currently Available:</label>
                    <div class="col-sm-5 nopadding-right">
                        <input type="text" class="form-control inp-give-current-avail not-editable" name="give_current_avail" value="">
                    </div>
                    <label class="control-label col-sm-3 giv-remaining-ov"></label>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4 nopadding-right">Allocate OV Reward:</label>
                    <div class="col-sm-5 nopadding-right">
                        <input type="text" class="form-control inp-give-allocated-reward" name="give_allocated_reward" autocomplete="off">
                        <span class="error-message error text-danger"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4 nopadding-right">Provide Reason:</label>
                    <div class="col-sm-7 nopadding-right">
                        <textarea rows="3" class="form-control inp-give-reason" name="give_reason" style="resize: vertical;" placeholder="Max 160 Chars"></textarea>
                        <span class="error-message error text-danger"></span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-11 give-control-wrap nopadding-right">
                        <a href="#" class="btn btn-warning btn-xs give-back">Back</a>
                        <a href="#" class="btn btn-success btn-xs give-save">Give</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="main_ov_settings">
            <div class="ov_settings_section">
                <div class="ov-settings-manager">
                    <ul class="nav nav-tabs ov-settings-manager-tabs">
                        <li class="active">
                            <a id="anc_sub_create" class="active" href="#sub_create" data-toggle="tab" aria-expanded="true">New</a>
                        </li>
                        <li>
                            <a id="anc_sub_current" href="#sub_current" data-toggle="tab" aria-expanded="false">Current</a>
                        </li>
                        <li>
                            <a id="anc_sub_offers" href="#sub_offers" data-toggle="tab" aria-expanded="false">Offers</a>
                        </li>
                        <li>
                            <a id="anc_sub_accelerate" href="#sub_accelerate" data-toggle="tab" aria-expanded="false">Accelerate</a>
                        </li>
                        <li>
                            <a id="anc_sub_charity" href="#sub_charity" data-toggle="tab" aria-expanded="false">Charity</a>
                        </li>
                    </ul>
                </div>
                <div id="settingsTabContent" class="tab-content">
                    <div class="tab-pane fade active in" id="sub_create">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-3 nopadding-right padding-left30">Select Project:</label>
                                <div class="col-sm-9">
                                    <?php
                                    $not_allocated_projects = [];
                                    if(isset($owner_projects) && !empty($owner_projects)) {
                                        foreach ($owner_projects as $key => $value) {
                                            $project_reward_setting = project_reward_setting($key, 1);
                                            if(!$project_reward_setting){
                                                $not_allocated_projects[$key] = $value;
                                            }
                                        }
                                    }
                                    echo $this->Form->input('sel_setting_create_ov', array(
                                        'options' => $not_allocated_projects,
                                        'empty' => 'Project',
                                        'class' => 'form-control',
                                        'id' => 'sel_setting_create_ov',
                                        'label' => false,
                                        'div' => false
                                    ));
                                    ?>
                                </div>
                            </div>
                            <div class="setting-create-wrap">
                                <!-- <div class="info-msg">Select A Project</div> -->
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="sub_current">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-3 nopadding-right padding-left30">Select Project:</label>
                                <div class="col-sm-9">
                                    <?php
                                    $allocated_projects = [];
                                    if(isset($owner_projects) && !empty($owner_projects)) {
                                        foreach ($owner_projects as $key => $value) {
                                            $project_reward_setting = project_reward_setting($key, 1);
                                            if($project_reward_setting){
                                                $allocated_projects[$key] = $value;
                                            }
                                        }
                                    }
                                    echo $this->Form->input('sel_setting_current_ov', array(
                                        'options' => $allocated_projects,
                                        'empty' => 'Project',
                                        'class' => 'form-control',
                                        'id' => 'sel_setting_current_ov',
                                        'label' => false,
                                        'div' => false
                                    ));
                                    ?>
                                </div>
                            </div>
                            <div class="setting-current-wrap">
                                <!-- <div class="info-msg">Select A Project</div> -->
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="sub_charity">
                        <div class="charity-show-info"></div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-3 nopadding-right padding-left30">Select Project:</label>
                                <div class="col-sm-9">
                                    <?php
                                    echo $this->Form->input('sel_setting_charity', array(
                                        'options' => $owner_projects,
                                        'empty' => 'Project',
                                        'class' => 'form-control',
                                        'id' => 'sel_setting_charity',
                                        'label' => false,
                                        'div' => false
                                    ));
                                    ?>
                                    <span class="error-message error text-danger"></span>
                                </div>
                            </div>
                            <div class="form-group charity-form-wrapper">
                                <label class="control-label col-sm-3 nopadding-right padding-left30">Charity:</label>
                                <div class="col-sm-8 nopadding-right">
                                    <input type="hidden" class="inp-setting-charity-id" name="setting_charity_id" value="">
                                    <div class="input-group charity-input-wrap">
                                        <input type="text" class="form-control inp-setting-charity-name" name="setting_charity_title" placeholder="50 Chars Max">
                                        <span class="input-group-addon bg-green save-charity" style="border-color: #67a028;"><i class="fa fa-check"></i></span>
                                        <span class="input-group-addon bg-red clear-charity" style="border-color: #dd4b39;"><i class="fa fa-times"></i></span>
                                    </div>
                                    <span class="error-message error text-danger"></span>
                                </div>
                                <div class="col-sm-1 loader-wrap">
                                    <i class="fa fa-spinner fa-pulse loader-icon stop"></i>
                                </div>
                            </div>
                            <div class="form-group charity-form-error">
                                <div class="col-sm-9 col-md-offset-3 error-message error text-danger" style="font-size: 12px;">Cannot create charity because no OV Exchange set</div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="sub_accelerate">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-3 nopadding-right padding-left30">Select Project:</label>
                                <div class="col-sm-9">
                                    <?php
                                    echo $this->Form->input('sel_setting_accelerate', array(
                                        'options' => $owner_projects,
                                        'empty' => 'Project',
                                        'class' => 'form-control',
                                        'id' => 'sel_setting_accelerate',
                                        'label' => false,
                                        'div' => false
                                    ));
                                    ?>
                                </div>
                            </div>
                            <div class="setting-accelerate-wrap">
                                <!-- <div class="info-msg">Select A Project</div> -->
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="sub_offers">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-3 nopadding-right padding-left30">Select Project:</label>
                                <div class="col-sm-9">
                                    <?php
                                    /*$allocated_projects = [];
                                    if(isset($owner_projects) && !empty($owner_projects)) {
                                        foreach ($owner_projects as $key => $value) {
                                            $project_reward_setting = project_reward_setting($key, 1);
                                            if($project_reward_setting){
                                                $allocated_projects[$key] = $value;
                                            }
                                        }
                                    }*/
                                    echo $this->Form->input('sel_offers_project', array(
                                        'options' => $owner_projects,
                                        'empty' => 'Project',
                                        'class' => 'form-control',
                                        'id' => 'sel_offers_project',
                                        'label' => false,
                                        'div' => false
                                    ));
                                    ?>
                                </div>
                            </div>
                            <div class="setting-offers-wrap"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
    <!-- <button type="button" class="btn btn-success submit_data">Confirm</button> -->
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>

<script type="text/javascript">
    $(function(){
        $('.inp-setting-charity-name').limitText(50);
        $('.give-allocate-info').popover({
            placement : 'right',
            trigger : 'hover',
            html : true,
            container: 'body',
            /*delay: {show: 50, hide: 400}*/
        })

        setTimeout(function(){
            if($('.history-inner .history-row').length > 7) {
                $('.history-row-header').css('padding-right', '20px');
            }
            else{
                $('.history-row-header').css('padding-right', 0);
            }
        }, 500);

        $('#sel_redeem_ov').change(function(event){
            var project_id = $(this).val(),
                user_id = $.logged_user_id;
            $('.redeem-data').html('<div class="loading-bar"></div>');
            $.ajax({
                url: $.url + 'rewards/get_redeem_data',
                type: 'POST',
                dataType: 'json',
                data: {project_id: project_id, user_id: user_id},
                success: function(response) {
                    $('.redeem-data').html(response);
                }
            })
        })

        $.get_my_history = function(data) {
            var dfd = new $.Deferred();
            var data = data || {};
            $('.my-history-outer').html('<div class="loading-bar"></div>')
            $.ajax({
                url: $.url + 'rewards/get_my_history',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(response) {
                    $('.my-history-outer').html(response);
                    dfd.resolve('done');
                }
            });
            return dfd.promise();
        };

        $.get_member_history = function(data) {
            var dfd = new $.Deferred();
            var data = data || {};
            $('.member-history-outer').html('<div class="loading-bar"></div>');
            $.ajax({
                url: $.url + 'rewards/get_member_history',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(response) {
                    $('.member-history-outer').html(response);
                    dfd.resolve('done');
                }
            });
            return dfd.promise();
        };

        $('#sel_history_project').change(function(event){
            var project_id = $(this).val(),
                data = {
                    project_id: project_id
                };
            $.get_my_history(data)
        })

        $('#sel_member_history_project').change(function(event){
            var project_id = $(this).val(),
                $user_select = $('#sel_member_history_users'),
                $loader = $user_select.parents('.form-group:first').find('.loader-icon');

            $user_select.addClass('not-editable');
            $user_select.empty().html('<option value="">User</option>');
            $('.member-history-outer').html('');

            if(project_id) {
                $loader.loader(1);
                $.get_project_users(project_id, $user_select, 2)
                    .done(function(message) {
                        $user_select.removeClass('not-editable');
                        $loader.loader(0);
                    })
            }
        })

        $('#sel_member_history_users').change(function(event){
            var project_id = $('#sel_member_history_project').val(),
                user_id = $(this).val(),
                data = {
                    project_id: project_id,
                    user_id: user_id
                };
            $('.member-history-outer').html('');
            if(user_id) {
                $.get_member_history(data);
            }
        })

        $('#sel_setting_create_ov').change(function(event){
            var project_id = $(this).val(),
                user_id = $.logged_user_id;
            $('.setting-create-wrap').html('<div class="loading-bar"></div>');
            $.ajax({
                url: $.url + 'rewards/setting_create_allocation',
                type: 'POST',
                dataType: 'json',
                data: {project_id: project_id, user_id: user_id},
                success: function(response) {
                    $('.setting-create-wrap').html(response);
                }
            })
        })

        $('#sel_setting_current_ov').change(function(event){
            var project_id = $(this).val(),
                user_id = $.logged_user_id;
            $('.setting-current-wrap').html('<div class="loading-bar"></div>');
            $.ajax({
                url: $.url + 'rewards/setting_get_allocation',
                type: 'POST',
                dataType: 'json',
                data: {project_id: project_id, user_id: user_id},
                success: function(response) {
                    $('.setting-current-wrap').html(response);
                }
            })
        })

        $.can_accelerate = function(project_id) {
            var dfd = new $.Deferred();
            $.ajax({
                url: $.url + 'rewards/can_accelerate',
                type: 'POST',
                dataType: 'json',
                data: {'project_id': project_id},
                success: function(response) {
                    dfd.resolve(response);
                }
            });
            return dfd.promise();
        }

        $('#sel_setting_accelerate').change(function(event){
            var project_id = $(this).val();

            $('.setting-accelerate-wrap').html('<div class="loading-bar"></div>');
            $('.can-not-accelerate').text('');


            $.ajax({
                url: $.url + 'rewards/setting_get_accelerate',
                type: 'POST',
                dataType: 'json',
                data: {project_id: project_id},
                success: function(response) {
                    $('.setting-accelerate-wrap').html(response);
                    $('.authorize-yes').addClass('not-editable btn-default').removeClass('btn-success');
                    $.can_accelerate(project_id).done(function(response){
                        if(!response.success) {
                            if(response.content != 'rewards') {
                                $('.authorize-yes').addClass('not-editable btn-default').removeClass('btn-success');
                                $('.can-not-accelerate').text('Cannot authorize acceleration. Increase Project Allocation.');
                            }
                            else{
                                $('.authorize-yes').addClass('not-editable btn-default').removeClass('btn-success');
                            }
                        }
                        else{
                            $('.authorize-yes').removeClass('not-editable btn-default').addClass('btn-success');
                        }
                    });
                }
            })
        })

        $.allocated_projects = function(ele) {
            var $ele = ele;
            var user_id = $.logged_user_id;
            $.ajax({
                url: $.url + 'rewards/get_allocated_projects',
                type: 'POST',
                dataType: 'json',
                data: {user_id: user_id},
                success: function(response) {
                    if (response.success) {
                        var selectValues = response.content;
                        var sorted_values = [];

                        $.each(selectValues, function(key, value) {
                            sorted_values.push({
                                v: value,
                                k: key
                            });
                        });

                        sorted_values.sort(function(a, b) {
                            if (a.v.toLowerCase() > b.v.toLowerCase()) {
                                return 1
                            }
                            if (a.v.toLowerCase() < b.v.toLowerCase()) {
                                return -1
                            }
                            return 0;
                        });
                        $ele.empty();
                        $ele.append(function() {

                            var output = '<option value="">Project</option>';

                            $.each(sorted_values, function(key, obj) {
                                output += '<option value="' + obj.k + '">' + obj.v + '</option>';
                            });
                            return output;
                        });
                    }
                }
            })
        }

        $('#sel_setting_charity').change(function(event){
            var project_id = $(this).val(),
                $loader = $('#sub_charity').find('.loader-icon');
            $('.inp-setting-charity-name').val('').addClass('not-editable');
            $('.inp-setting-charity-id').val('');

            $loader.loader(1);

            $.ajax({
                url: $.url + 'rewards/get_project_charity',
                type: 'POST',
                dataType: 'json',
                data: {project_id: project_id},
                success: function(response) {
                    if(response.success) {
                        if(response.ov_setting) {
                            $('.charity-form-error').slideUp(300, function(){
                                $('.charity-form-wrapper').slideDown(200);
                            })
                            if( Object.keys(response.content).length > 0 ) {
                                var charity_val = response.content;
                                $('.inp-setting-charity-id').val(charity_val.id);
                                $('.inp-setting-charity-name').val(charity_val.title);
                            }
                            $('.inp-setting-charity-name').removeClass('not-editable');
                        }
                        else {
                            if(project_id) {
                                $('.charity-form-wrapper').slideUp(300, function(){
                                    $('.charity-form-error').slideDown(200);
                                })
                            }
                            else{
                                $('.charity-form-wrapper, .charity-form-error').slideUp(300)
                            }
                        }
                    }
                    $loader.loader(0);
                }
            })
        })

        $('.save-charity').click(function(event){
            event.preventDefault();
            $.reload_data = true;
            var project_id = $('#sel_setting_charity').val(),
                charity_id = $('.inp-setting-charity-id').val(),
                charity_title = $('.inp-setting-charity-name').val(),
                user_id = $.logged_user_id,
                $loader = $('#sub_charity').find('.loader-icon');

            $('#sub_charity').find('.error-message').text('');

            if(project_id == '' || project_id <= 0) {
                $('#sel_setting_charity').parents('.form-group:first').find('.error-message').text('Required field.');
                return;
            }
            if(charity_title == '') {
                $('.inp-setting-charity-name').parents('.form-group:first').find('.error-message').text('Required field.');
                return;
            }

            $loader.loader(1);

            $.ajax({
                url: $.url + 'rewards/save_project_charity',
                type: 'POST',
                dataType: 'json',
                data: {
                    project_id: project_id,
                    id: charity_id,
                    title: charity_title,
                    given_by: user_id
                },
                success: function(response) {
                    if(response.success) {
                        $('.inp-setting-charity-id, .inp-setting-charity-name').val('');
                        $('#sel_setting_charity').val('');
                        $.information($('.charity-show-info')).done(function(message) {
                            $('.charity-show-info').find(".success-msg").remove();
                        })
                        $('#sel_setting_charity option[value=""]').prop('selected', true);
                        $('#sel_setting_charity').trigger('change');
                        $('#sel_redeem_ov option[value=""]').prop('selected', true);
                        $('#sel_redeem_ov').trigger('change');
                    }
                    $loader.loader(0);
                }
            })
        })

        $('.clear-charity').click(function(event){
            $('.inp-setting-charity-name').val('').focus();
        })

        $.get_project_users = function(project_id, elem, me) {
            var dfd = new $.Deferred();

            var project_id = project_id || 0;
            var $ele = elem;
            $.ajax({
                url: $.url + 'rewards/get_project_users',
                type: 'POST',
                dataType: 'json',
                data: { project_id: project_id, me: me },
                success: function(response) {
                    if (response.success) {
                        var selectValues = response.content;
                        var sorted_values = [];

                        $.each(selectValues, function(key, value) {
                            sorted_values.push({
                                v: value,
                                k: key
                            });
                        });

                        sorted_values.sort(function(a, b) {
                            if (a.v.toLowerCase() > b.v.toLowerCase()) {
                                return 1
                            }
                            if (a.v.toLowerCase() < b.v.toLowerCase()) {
                                return -1
                            }
                            return 0;
                        });
                        $ele.empty();
                        $ele.append(function() {

                            var output = '<option value="">User</option>';

                            $.each(sorted_values, function(key, obj) {
                                output += '<option value="' + obj.k + '">' + obj.v + '</option>';
                            });
                            return output;
                        });
                        $ele.removeClass('not-editable');
                        dfd.resolve('done');
                    }
                }
            })
            return dfd.promise();
        }

        $.give_ov_users = function(project_id, elem, me) {
            var dfd = new $.Deferred();

            var project_id = project_id || 0;
            var $ele = elem;
            $.ajax({
                url: $.url + 'rewards/give_ov_users',
                type: 'POST',
                dataType: 'json',
                data: { project_id: project_id, me: me },
                success: function(response) {
                    if (response.success) {
                        var selectValues = response.content;
                        var sorted_values = [];

                        $.each(selectValues, function(key, value) {
                            sorted_values.push({
                                v: value,
                                k: key
                            });
                        });

                        sorted_values.sort(function(a, b) {
                            if (a.v.toLowerCase() > b.v.toLowerCase()) {
                                return 1
                            }
                            if (a.v.toLowerCase() < b.v.toLowerCase()) {
                                return -1
                            }
                            return 0;
                        });
                        $ele.empty();
                        $ele.append(function() {

                            var output = '<option value="">User</option>';

                            $.each(sorted_values, function(key, obj) {
                                output += '<option value="' + obj.k + '">' + obj.v + '</option>';
                            });
                            return output;
                        });
                        $ele.removeClass('not-editable');
                        dfd.resolve('done');
                    }
                }
            })
            return dfd.promise();
        }

        $.get_project_ov = function(project_id) {
            var dfd = new $.Deferred();

            var project_id = project_id || 0;
            $.ajax({
                url: $.url + 'rewards/get_project_ov',
                type: 'POST',
                dataType: 'json',
                data: { project_id: project_id },
                success: function(response) {
                    if (response.success) {
                        dfd.resolve(response);
                    }
                }
            })
            return dfd.promise();
        }

        $.get_project_activity = function(post_data) {
            var dfd = new $.Deferred();

            var project_id = project_id || 0;
            var $ele = $('#sel_give_activity');
            $.ajax({
                url: $.url + 'rewards/get_project_activity',
                type: 'POST',
                dataType: 'json',
                data: post_data,
                success: function(response) {
                    if (response.success) {
                        $ele.empty();
                        var selectValues = response.content;
                        var sorted_values = [];
                        if( selectValues != null ) {
                            $.each(selectValues, function(key, value) {
                                sorted_values.push({
                                    v: value,
                                    k: key
                                });
                            });

                            sorted_values.sort(function(a, b) {
                                if (a.v.toLowerCase() > b.v.toLowerCase()) {
                                    return 1
                                }
                                if (a.v.toLowerCase() < b.v.toLowerCase()) {
                                    return -1
                                }
                                return 0;
                            });


                            $ele.append(function() {
                                var output = '<option value="">Activity</option>';

                                $.each(sorted_values, function(key, obj) {
                                    output += '<option value="' + obj.k + '">' + obj.v + '</option>';
                                });
                                return output;
                            });
                        }
                        else{
                            $ele.append('<option value="">Activity</option>')
                        }
                        dfd.resolve('done');
                    }
                }
            })
            return dfd.promise();
        }

        $.get_allocation_projects = function(ele) {
            var $ele = ele;
            var user_id = $.logged_user_id;
            $.ajax({
                url: $.url + 'rewards/get_allocation_projects',
                type: 'POST',
                dataType: 'json',
                data: {user_id: user_id},
                success: function(response) {
                    if (response.success) {
                        var selectValues = response.content;
                        var sorted_values = [];

                        $.each(selectValues, function(key, value) {
                            sorted_values.push({
                                v: value,
                                k: key
                            });
                        });

                        sorted_values.sort(function(a, b) {
                            if (a.v.toLowerCase() > b.v.toLowerCase()) {
                                return 1
                            }
                            if (a.v.toLowerCase() < b.v.toLowerCase()) {
                                return -1
                            }
                            return 0;
                        });
                        $ele.empty();
                        $ele.append(function() {

                            var output = '<option value="">Project</option>';

                            $.each(sorted_values, function(key, obj) {
                                output += '<option value="' + obj.k + '">' + obj.v + '</option>';
                            });
                            return output;
                        });
                    }
                }
            })
        }

        $.get_redeem_projects = function(ele) {
            var $ele = ele;
            var user_id = $.logged_user_id;
            $.ajax({
                url: $.url + 'rewards/get_redeem_projects',
                type: 'POST',
                dataType: 'json',
                data: {user_id: user_id},
                success: function(response) {
                    if (response.success) {
                        var selectValues = response.content;
                        var sorted_values = [];

                        $.each(selectValues, function(key, value) {
                            sorted_values.push({
                                v: value,
                                k: key
                            });
                        });

                        sorted_values.sort(function(a, b) {
                            if (a.v.toLowerCase() > b.v.toLowerCase()) {
                                return 1
                            }
                            if (a.v.toLowerCase() < b.v.toLowerCase()) {
                                return -1
                            }
                            return 0;
                        });
                        $ele.empty();
                        $ele.append(function() {

                            var output = '<option value="">Project</option>';

                            $.each(sorted_values, function(key, obj) {
                                output += '<option value="' + obj.k + '">' + obj.v + '</option>';
                            });
                            return output;
                        });
                    }
                }
            })
        }

        $('#sel_give_project').change(function(event){
            var $sel_give_user = $('#sel_give_user'),
                project_id = $(this).val(),
                $loader = $sel_give_user.parents('.form-group:first').find('.loader-icon');


            // $('#sel_give_user').val('');
            $('#sel_give_type').val('');
            $('#sel_give_activity').val('');
            $('.inp-give-current-avail').val('');
            $('.inp-give-allocated-reward').val('');
            $('.inp-give-reason').val('');
            $('.giv-remaining-ov').text('');
            $('.form_chain_confirm').find('.error-message').text('');
            $('#sel_give_user, #sel_give_type, #sel_give_activity, .give-next').addClass('not-editable');
            $('.give-save').removeClass('not-editable');
            if(project_id != '') {
                $loader.loader(1);
                $.give_ov_users(project_id, $sel_give_user, 1)
                    .done(function(message) {
                        $.get_project_ov(project_id)
                            .done(function(message) {
                                if(message.content == "" || message.content == "0") {
                                    $('.give-save').addClass('not-editable');
                                }
                                $('.inp-give-current-avail')
                                    .val(message.content)
                                    .val(function(index, value) {
                                        return value
                                            .replace(/\D/g, "")
                                            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                    });
                            });
                        $loader.loader(0);
                    })
            }
        })

        $('#sel_give_user').change(function(event){
            var $sel_give_type = $('#sel_give_type'),
                user_id = $(this).val(),
                $loader = $sel_give_type.parents('.form-group:first').find('.loader-icon');
            $loader.loader(1);

            $('#sel_give_type, #sel_give_activity').val('');
            $('#sel_give_type, #sel_give_activity, .give-next').addClass('not-editable');
            if(user_id != '') {
                $('#sel_give_type').removeClass('not-editable');
            }
            $loader.loader(0);
        })

        $('#sel_give_type').change(function(event){
            var type_text = $(this).val(),
                $loader = $('#sel_give_activity').parents('.form-group:first').find('.loader-icon');


            $('#sel_give_activity').val('');
            $('#sel_give_activity, .give-next').addClass('not-editable');

            if(type_text != '') {
                if(type_text == 'project' || type_text == 'other') {
                    $('.give-next').removeClass('not-editable');
                    $('#sel_give_activity').empty().html('<option value="">No Selection Required</option>')
                }
                else{
                    $('#sel_give_activity').removeClass('not-editable');
                    $loader.loader(1);
                    var post_data = {
                            project_id: $('#sel_give_project').val(),
                            user_id:    $('#sel_give_user').val(),
                            type:       type_text,
                        };
                    $.get_project_activity(post_data)
                        .done(function(message){
                            $loader.loader(0);
                            $('.give-next').addClass('not-editable');
                        })
                }
            }
        })

        $('#sel_give_activity').change(function(event){
            var activity_id = $(this).val();

            $('.give-next').addClass('not-editable');

            if(activity_id != '') {
                $('.give-next').removeClass('not-editable');
            }
        })

        $.form_chain = '.form_chain_selection';
        $('.give-next').click(function(event) {
            event.preventDefault();
            var project = $('#sel_give_project option:selected').text(),
                user = $('#sel_give_user option:selected').text(),
                type = $('#sel_give_type option:selected').text(),
                activity = $('#sel_give_activity option:selected').text();

            $('.give-selected-project').text(project);
            $('.give-selected-user').text(user);
            $('.give-selected-type').text(type);
            if(type == 'Project' || type == 'Other') {
                $('.give-selected-activity').text('N/A');
            }
            else {
                $('.give-selected-activity').text(activity);
            }

            $($.form_chain).hide('fade', {direction: 'left'}, 600, function(){
                $.form_chain = '.form_chain_confirm';
                $($.form_chain).show('fade', {direction: 'right'}, 400);
            });
        })

        $('.give-back').click(function(event) {
            event.preventDefault();
            $($.form_chain).hide('fade', {direction: 'left'}, 600, function(){
                $.form_chain = '.form_chain_selection';
                $($.form_chain).show('fade', {direction: 'right'}, 400);
            });
        })

        $('.give-clear').click(function(event) {
            event.preventDefault();
            $('#sel_give_project').val('');
            $('#sel_give_user option[value=""]').prop('selected', true);
            $('#sel_give_user').addClass('not-editable');
            $('#sel_give_type').val('').addClass('not-editable');
            $('#sel_give_activity').val('').addClass('not-editable');
            $('.give-next').addClass('not-editable');
            $('.inp-give-current-avail').val('');
        })

        $.clear_give_form = function() {
            $('.give-clear').trigger('click');
            $('.inp-give-allocated-reward, .inp-give-reason').val('');
            $('#main_give_ov .error-message').text('');
            $('.giv-remaining-ov').text('');
        }

        $('.give-save').click(function(event) {
            event.preventDefault();
            $.reload_data = true;
            var $this = $(this);
            var post_data = {
                    project_id: $('#sel_give_project').val(),
                    user_id: $('#sel_give_user').val(),
                    type: $('#sel_give_type').val(),
                    activity: $('#sel_give_activity').val(),
                    allocated_reward: $('.inp-give-allocated-reward').deformat(),
                    reason: $('.inp-give-reason').val()
                },
                current_allocated = $('.inp-give-current-avail').deformat();

            if(current_allocated == '' || parseInt(current_allocated) == 0) {
                return;
            }

            if(post_data.allocated_reward == '' || parseInt(post_data.allocated_reward) == 0 || isNaN(post_data.allocated_reward)) {
                $('.inp-give-allocated-reward').parents('.form-group:first').find('.error-message').text('Required field.');
                return;
            }

            if($.trim(post_data.reason) == '') {
                $('.inp-give-reason').parents('.form-group:first').find('.error-message').text('Required field.');
                return;
            }

            if(parseInt(post_data.allocated_reward) > parseInt(current_allocated)) {
                $('.inp-give-allocated-reward')
                    .parents('.form-group:first')
                    .find('.error-message')
                    .text("You can't allocate more than available OV.");
                return;
            }

            $this.addClass('not-editable');

            $.ajax({
                url: $.url + 'rewards/save_give_allocation',
                type: 'POST',
                dataType: 'json',
                data: post_data,
                success: function(response) {
                    if (response.success) {

                        $.form_chain = '.form_chain_confirm';
                        $($.form_chain).hide('fade', {direction: 'left'}, 600, function(){
                            $.form_chain = '.form_chain_selection';
                            $.get_redeem_projects($('#sel_redeem_ov'));
                            $.get_allocation_projects($('#sel_give_project'));
                            $($.form_chain).show('fade', {direction: 'right'}, 400);
                            $.clear_give_form();
                            $.information($('.give-show-info')).done(function(message) {
                                $('.give-show-info').find('.success-msg').remove();
                            })
                            $('#sel_setting_current_ov option[value=""]').prop('selected', true);
                            $('#sel_setting_current_ov').trigger('change');
                            $('#sel_setting_accelerate option[value=""]').prop('selected', true);
                            $('#sel_setting_accelerate').trigger('change');
                            $('#sel_shop_project option[value=""]').prop('selected', true);
                            $('#sel_shop_project').trigger('change');
                            $('.redeem-data').slideUp(300);

                        });

                        if(response.hasOwnProperty('content')){
                            $.socket.emit('socket:notification', response.content.socket, function(userdata) {});
                        }
                    }
                }
            })
        })

        $('.inp-give-allocated-reward').keyup(function(event) {
            var current_allocated = $('.inp-give-current-avail').deformat(),
                $error = $(this).parents('.form-group:first').find('.error-message');

            $error.text('');
            $('.giv-remaining-ov').text('');

            if(parseInt($(this).deformat()) > parseInt(current_allocated)) {
                $error.text("You can't allocate more than available OV.");
                return;
            }

            var remaining = parseInt(current_allocated) - parseInt($(this).deformat());

            remaining = remaining.toString().replace(/\D/g, "")
                            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            $('.giv-remaining-ov').text(remaining);
        })

        $.fn.char_count = function(characters) {
            var value = $(this).val();
            if(value.length > characters){
                $(this).val($(this).val().substr(0, characters));
            }
            var remaining = characters -  value.length;
            $(this).parent().find('.error').html("Char "+characters+" , <strong>" +value.length+ "</strong> characters entered.");
            return this;
        }

        var characters = 160;
        $('.inp-give-reason').keyup(function(event){
            event.preventDefault();
            $(this).char_count(characters);
        })


    })// END document ready
    $(function(){
        $('#sel_offers_project').change(function(event){
            var project_id = $(this).val();
            $('.setting-offers-wrap').html('<div class="loading-bar"></div>');
            $.ajax({
                url: $.url + 'rewards/project_offers',
                type: 'POST',
                dataType: 'json',
                data: { project_id: project_id },
                success: function(response) {
                    $('.setting-offers-wrap').html(response);
                }
            })
        })

        $.project_offers_list = function(project_id) {
            var dfd = new $.Deferred();
            $('.project-offers-list').html('<div class="loading-bar"></div>');
            if(project_id){
                $.ajax({
                    url: $.url + 'rewards/project_offers_list/',
                    type: 'POST',
                    dataType: 'json',
                    data: {project_id: project_id},
                    success: function(response) {
                        $('.project-offers-list').html(response);
                        dfd.resolve('done');
                    }
                });
            }
            return dfd.promise();
        };

        $.project_offers_detail = function(id) {
            var dfd = new $.Deferred();
            if(id){
                $.ajax({
                    url: $.url + 'rewards/project_offers_detail/',
                    type: 'POST',
                    dataType: 'json',
                    data: {id: id},
                    success: function(response) {
                        dfd.resolve(response);
                    }
                });
            }
            return dfd.promise();
        };

        $.get_project_offers = function(project_id, elem) {
            var dfd = new $.Deferred();

            var project_id = project_id || 0;
            var $ele = elem;
            $ele.addClass('not-editable');
            $.ajax({
                url: $.url + 'rewards/get_project_offers',
                type: 'POST',
                dataType: 'json',
                data: { project_id: project_id },
                success: function(response) {
                    if (response.success) {
                        var selectValues = response.content;
                        var sorted_values = [];

                        $.each(selectValues, function(key, value) {
                            sorted_values.push({
                                v: value,
                                k: key
                            });
                        });

                        sorted_values.sort(function(a, b) {
                            if (a.v.toLowerCase() > b.v.toLowerCase()) {
                                return 1
                            }
                            if (a.v.toLowerCase() < b.v.toLowerCase()) {
                                return -1
                            }
                            return 0;
                        });
                        $ele.empty();
                        $ele.append(function() {

                            var output = '<option value="">Offer</option>';

                            $.each(sorted_values, function(key, obj) {
                                output += '<option value="' + obj.k + '">' + obj.v + '</option>';
                            });
                            return output;
                        });
                        $ele.removeClass('not-editable');
                        dfd.resolve('done');
                    }
                }
            })
            return dfd.promise();
        }

        $.get_offering_project = function(elem) {
            var dfd = new $.Deferred();

            var $ele = elem;
            // $ele.addClass('not-editable');
            $.ajax({
                url: $.url + 'rewards/get_offering_project',
                type: 'POST',
                dataType: 'json',
                data: { },
                success: function(response) {
                    if (response.success) {
                        var selectValues = response.content;
                        var sorted_values = [];

                        $.each(selectValues, function(key, value) {
                            sorted_values.push({
                                v: value,
                                k: key
                            });
                        });

                        sorted_values.sort(function(a, b) {
                            if (a.v.toLowerCase() > b.v.toLowerCase()) {
                                return 1
                            }
                            if (a.v.toLowerCase() < b.v.toLowerCase()) {
                                return -1
                            }
                            return 0;
                        });
                        $ele.empty();
                        $ele.append(function() {

                            var output = '<option value="">Project</option>';

                            $.each(sorted_values, function(key, obj) {
                                output += '<option value="' + obj.k + '">' + obj.v + '</option>';
                            });
                            return output;
                        });
                        // $ele.removeClass('not-editable');
                        dfd.resolve('done');
                    }
                }
            })
            return dfd.promise();
        }

        $.user_project_shopping = function(project_id) {
            var dfd = new $.Deferred();
            $('.user-shopping-outer').html('<div class="loading-bar"></div>');
            if(project_id){
                $.ajax({
                    url: $.url + 'rewards/user_project_shopping/',
                    type: 'POST',
                    dataType: 'json',
                    data: {project_id: project_id},
                    success: function(response) {
                        $('.user-shopping-outer').html(response);
                        dfd.resolve('done');
                    }
                });
            }
            return dfd.promise();
        };

        $('#sel_shop_project').change(function(event){
            var project_id = $(this).val();

            $('#sel_an_offer').empty().html('<option value="">Offer</option>');
            if(project_id != '') {
                $.get_project_offers(project_id, $('#sel_an_offer')).done(function(){
                    $.user_project_shopping(project_id);
                    $('.shop-outer').html('');
                })
            }
            else{
                $('#sel_an_offer').addClass('not-editable');
                $('.shop-outer').html('');
                $('.user-shopping-outer').html('');
            }
        })

        $('#sel_an_offer').change(function(event){
            var project_id = $('#sel_shop_project').val();
                offer_id = $(this).val();
            var data = {
                project_id: project_id,
                offer_id: offer_id
            }
            $.ajax({
                url: $.url + 'rewards/project_offer_shop',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(response) {
                    $('.shop-outer').html(response);
                }
            })
        })

        $('body').delegate('.inp-setting-charity-name', 'keyup focus contextmenu', function(event){
            var characters = 50;
            event.preventDefault();
            var $error_el = $(this).parents(".col-sm-8.nopadding-right:first").find('.error');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })

        $('body').delegate('[name="offer_title"]', 'keyup focus contextmenu', function(event){
            var characters = 100;
            event.preventDefault();
            var $error_el = $(this).parents(".col-sm-9:first").find('.error');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })

    })
</script>