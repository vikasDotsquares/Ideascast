<div class="modal-header  bg-green">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h3 class="modal-title">People on <?php if(empty($users['DoList']['parent_id'])){ ?>To-do<?php }else{ ?>Sub To-do<?php } ?></h3>
</div>
<div class="modal-body">
<ul class="list-group list-group-root  ">
    <?php
    // pr($users);
	$current_org = $this->Permission->current_org();

    if (isset($users['DoListUser']) && !empty($users['DoListUser'])) {
        $udetailC = $this->Common->UserDetail($users['DoList']['user_id']);
        $udC = $this->ViewModel->get_user($users['DoList']['user_id']);
		$current_org_other = $this->Permission->current_org($users['DoList']['user_id']);
        ?>

        <div class="row row-pep  "><div class="col-sm-2">
			<span class="style-popple-icon-out">
												<span class="style-popple-icon" >
			<?php
    echo $this->Html->image($this->Common->get_profile_pic($users['DoList']['user_id']), array("width" => 36, "height" => 36, "class" => "tipText", "title" => $this->Common->userFullname($users['DoList']['user_id'])));
        ?></span>
				<?php
                if($current_org['organization_id'] != $current_org_other['organization_id']){ ?>
					 <i class="communitygray18 tipText community-g" data-original-title="Not In Your Organization" ></i>
				<?php }  ?>
			</span>
        </div><div class="col-sm-8 user-detail"><p class="user_name"><?php echo 'Creator'; ?> : <?php echo $this->Common->userFullname($users['DoList']['user_id']); ?></p><p><?php echo $udC['User']['email']; ?></p>
		<?php if( !empty(trim($udetailC['UserDetail']['org_name'])) ){?>
		<p><span class="ucompany">Organization: </span><?php echo $udetailC['UserDetail']['org_name']; ?></p><?php } ?><p><span class="jobrole">Role: </span><?php
							echo ( isset($udetailC['UserDetail']['job_role']) && !empty($udetailC['UserDetail']['job_role']) && strlen(trim($udetailC['UserDetail']['job_role'])) > 0 )? htmlentities(trim($udetailC['UserDetail']['job_role']),ENT_QUOTES) : 'Not Given';
							?></p></div>
            <div class="col-sm-2">
                    <!-- <i class="fa fa-comment"></i> -->
            </div>
        </div>




        <?php
        foreach ($users['DoListUser'] as $user) {
            $udetail = $this->Common->UserDetail($user['user_id']);
            $ud = $this->ViewModel->get_user($user['user_id']);
			$current_org_other = $this->Permission->current_org($user['user_id']);
            ?><div class="row row-pep  "><div class="col-sm-2">
	<span class="style-popple-icon-out">
												<span class="style-popple-icon" ><?php
            echo $this->Html->image($this->Common->get_profile_pic($user['user_id']), array("", "class" => "tipText", "width" => 36, "height" => 36,  "title" => $this->Common->userFullname($user['user_id'])));
													?></span>
				<?php   if($current_org['organization_id'] != $current_org_other['organization_id']){ ?>
					<i class="communitygray18 tipText community-g" data-original-title="Not In Your Organization" ></i>
				<?php }  ?>

													</span>

													</div><div class="col-sm-8 user-detail"><p class="user_name"><?php echo 'Assigned'; ?> : <?php echo $this->Common->userFullname($user['user_id']); ?></p><p><?php echo $ud['User']['email']; ?></p><?php if( !empty(trim($udetail['UserDetail']['org_name'])) ){?><p><span class="ucompany">Organization: </span><?php echo $udetail['UserDetail']['org_name']; ?></p><?php } ?><p><span class="jobrole">Role: </span><?php
							echo ( isset($udetail['UserDetail']['job_role']) && !empty($udetail['UserDetail']['job_role']) && strlen(trim($udetail['UserDetail']['job_role'])) > 0 )? htmlentities(trim($udetail['UserDetail']['job_role']),ENT_QUOTES) : 'Not Given';
							?></p></div>
                <div class="col-sm-2">
                        <!-- <i class="fa fa-comment"></i> -->
                </div>
            </div><?php }
}
    ?></ul>

</div>
<div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
<style> .row-pep {
        border-top: 1px solid #ccc;
        margin-top: 5px;
        padding-top: 5px;
    }
    .row-pep:first-child {
        border-top: none;
    }

</style>