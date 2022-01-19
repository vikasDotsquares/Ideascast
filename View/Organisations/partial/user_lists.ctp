<?php
if(isset($lists) && !empty($lists)) {
	foreach ($lists as $list){
		$user_data = $list['u'];
		$user_details = $list['ud'];
		$user_name = $list[0]['full_name'];
		$uid = $user_data['id'];
		$CheckProfileEdit = CheckProfileEdit($uid);
	?>

	<div class="manage-data-row" data-id=" ">

		<?php
			$listcheckbx = "disable";
			$checkAll = "empties";
			$property = "disabled";
			if( $this->Session->read('Auth.User.id') != $uid ){
				$listcheckbx = "checkDomainList";
				$checkAll = "checkAll";
				$property = "";
			}
			if(   ($CheckProfileEdit != 1 )) {
				$listcheckbx = "disable";
				$checkAll = "empties";
				$property = "disabled";
			}
		?>
		<div class="manage-users-col mu-col1">
			<input type="checkbox" class="<?php echo $listcheckbx;?>" name="<?php echo $checkAll;?>" value="<?php echo $uid; ?>" <?php echo $property;?> />
		</div>
		<div class="manage-users-col mu-col2"><?php echo $user_name; ?></div>
		<div class="manage-users-col mu-col3"><span class="mu-email"><?php echo $user_data['email']; ?></span></div>
		<div class="manage-users-col mu-col4"><?php echo $this->Wiki->_displayDate(date('Y-m-d H:i',$user_data["created"]), 'd M, Y'); ?></div>
		<div class="manage-users-col mu-col5">
			<?php
				$disabledadminstratorsts = "btn btn-default disable";
				if( $this->Session->read('Auth.User.id') != $uid ){
					$disabledadminstratorsts = "btn btn-default RecordUpdateClass";
				}

				if(   ($CheckProfileEdit != 1 )) {
					$disabledadminstratorsts = "btn btn-default disable";
				}
				$disabledadminstratorstsAct = "";

				if(   ($CheckProfileEdit != 1 )) {
					$disabledadminstratorstsAct = "disable";
				}

				if(empty($user_data['is_activated']) && !empty($user_data['activation_time'])){
			 ?>
			<span class="btn btn-default tipText" title="Awaiting User Activation" data-toggle="tooltip" data-placement="top" style="cursor: default;">
				<i class="waitingblack <?php echo $disabledadminstratorstsAct; ?>"></i>
			</span>
			<?php
			}
			else{
				if($user_data['status'] == 1){ ?>
					<button  title="Active" data-toggle="tooltip" data-placement="top" rel="deactivate" id="<?php echo $uid; ?>"  class="<?php echo $disabledadminstratorsts;?> tipText"><i class="activegreen"></i></button>
				<?php } else { ?>
					<button  rel="activate"  title="Inactive" data-toggle="tooltip" data-placement="top" id="<?php echo $uid; ?>"  class="<?php echo $disabledadminstratorsts;?> tipText"><i class="inactivered"></i></button>
				<?php }
			} ?>
		</div>
		<div class="manage-users-col mu-col6">
			<?php
				$disabledadminstrator = "btn btn-default disable";
				if( $this->Session->read('Auth.User.id') != $uid ){
					$disabledadminstrator = "btn btn-default ProjectUpdateClass";
				}

				if(   ($CheckProfileEdit != 1 )) {
					$disabledadminstrator = "btn btn-default disable";
				}

				if( $this->Session->read('Auth.User.id') == $uid ){
					$disabledadminstrator = "btn btn-default disable";
				}

				if($user_details['create_project'] == 1){ ?>
					<button data-toggle="tooltip" data-placement="top" title="Granted" rel="deactivate" data-userdetailid="<?php echo $user_details['ud_id'];?>" id="<?php echo $uid; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default tipText"> <i class="activegreen"></i></button>
			<?php } else { ?>
					<button data-toggle="tooltip" data-placement="top" title="Denied" rel="activate" data-userdetailid="<?php echo $user_details['ud_id'];?>" id="<?php echo $uid; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default tipText"><i class="inactivered"></i> </button>
			<?php } ?>
		</div>
		<div class="manage-users-col mu-col7">
			<?php
				$disabledadminstrator = "btn btn-default disable";
				if( $this->Session->read('Auth.User.id') != $uid ){
					$disabledadminstrator = "btn btn-default TemplateUpdateClass";
				}

				if(   ($CheckProfileEdit != 1 )) {
					$disabledadminstrator = "btn btn-default disable";
				}

				if( $this->Session->read('Auth.User.id') == $uid ){
					$disabledadminstrator = "btn btn-default disable";
				}

				if($user_details['create_template'] == 1){ ?>
					<button data-toggle="tooltip" data-placement="top" title="Granted" rel="deactivate" data-userdetailid="<?php echo $user_details['ud_id'];?>" id="<?php echo $uid; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default tipText"> <i class="activegreen"></i></button>
			<?php } else { ?>
					<button data-toggle="tooltip" data-placement="top" title="Denied" rel="activate" data-userdetailid="<?php echo $user_details['ud_id'];?>" id="<?php echo $uid; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default tipText"><i class="inactivered"></i> </button>
			<?php } ?>
		</div>
		<div class="manage-users-col mu-col12">
			<?php
				$disabledadminstrator = "btn btn-default disable";
				if( $this->Session->read('Auth.User.id') != $uid ){
					$disabledadminstrator = "btn btn-default AnalyticsUpdateClass";
				}

				if(   ($CheckProfileEdit != 1 )) {
					$disabledadminstrator = "btn btn-default disable";
				}

				if( $this->Session->read('Auth.User.id') == $uid ){
					$disabledadminstrator = "btn btn-default disable";
				}

				if($user_details['analytics'] == 1){ ?>
					<button data-toggle="tooltip" data-placement="top" title="Granted" rel="deactivate" data-userdetailid="<?php echo $user_details['ud_id'];?>" id="<?php echo $uid; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default tipText"> <i class="activegreen"></i></button>
			<?php } else { ?>
					<button data-toggle="tooltip" data-placement="top" title="Denied" rel="activate" data-userdetailid="<?php echo $user_details['ud_id'];?>" id="<?php echo $uid; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default tipText"><i class="inactivered"></i> </button>
			<?php } ?>
		</div>
		<div class="manage-users-col mu-col8">
			<?php
				$disabledadminstrator = "btn btn-default disable";
				if( $this->Session->read('Auth.User.id') != $uid ){
					$disabledadminstrator = "btn btn-default ResourcerUpdateClass";
				}

				if(   ($CheckProfileEdit != 1 )) {
					$disabledadminstrator = "btn btn-default disable";
				}

				if( $this->Session->read('Auth.User.id') == $uid ){
					$disabledadminstrator = "btn btn-default disable";
				}

				if($user_details['resourcer'] == 1){ ?>
					<button data-toggle="tooltip" data-placement="top" title="Granted" rel="deactivate" data-userdetailid="<?php echo $user_details['ud_id'];?>" id="<?php echo $uid; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default tipText"> <i class="activegreen"></i></button>
			<?php } else { ?>
					<button data-toggle="tooltip" data-placement="top" title="Denied" rel="activate" data-userdetailid="<?php echo $user_details['ud_id'];?>" id="<?php echo $uid; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default tipText"><i class="inactivered"></i> </button>
			<?php } ?>
		</div>
		<?php

			$auther = two_factor_check();
			if(isset($auther ) && !empty($auther )){
		?>
		<div class="manage-users-col mu-col9">
			<?php
				if(isset($user_details['membership_code']) && !empty($user_details['membership_code'])){?>
				<button title="" data-toggle="tooltip" style="cursor: default;" data-placement="top"    class="btn btn-default tipText" data-original-title="Registered"><i class="activegreen"></i></button>
			<?php }else{ ?>
				<span class="btn btn-default tipText" title="" data-toggle="tooltip" data-placement="top" style="cursor: default;" data-original-title="Not Registered">
				 	<i class="waitingblack"></i>
				</span>
			<?php }  ?>
		</div>
		<?php } ?>
		<div class="manage-users-col mu-col10">
			<?php
				$disabledadminstrator = "btn btn-default disable";
				if( $this->Session->read('Auth.User.id') != $uid ){
					$disabledadminstrator = "btn btn-default RecordCreateClass";
				}

				if(   ($CheckProfileEdit != 1 )) {
					$disabledadminstrator = "btn btn-default disable";
				}

				if( $this->Session->read('Auth.User.id') == $uid ){
					$disabledadminstrator = "btn btn-default disable";
				}

				if($user_details['administrator'] == 1){ ?>
					<button data-toggle="tooltip" data-placement="top" title="Granted" rel="deactivate" data-userdetailid="<?php echo $user_details['ud_id'];?>" id="<?php echo $uid; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default tipText"><i class="activegreen"></i></button>
			<?php } else { ?>
					<button data-toggle="tooltip" data-placement="top" title="Denied" rel="activate" data-userdetailid="<?php echo $user_details['ud_id'];?>" id="<?php echo $uid; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default tipText"><i class="inactivered"></i></button>
			<?php } ?>
		</div>
		<div class="manage-users-col mu-col11 mu-actions">
			<?php

				if( ($CheckProfileEdit == 1 )) {

					$editURL = "javascript:void(0);";
					$deleteURL = "javascript:void(0);";
					$disablededitcls = "tipText disable";
					$disableddelcls = "tipText disable";
					if( $this->Session->read('Auth.User.id') != $uid ){

						$editURL = SITEURL."users/myaccountedit/".$uid."?refer=".SITEURL.'organisations/manage_users';
						$disablededitcls = "edituser tipText";
						$disableddelcls = "RecordDeleteClass tipText";
						$deleteURL = SITEURL."organisations/organisation_user_delete";
					}
					?>
					<a class="<?php echo $disablededitcls;?> tipText" title="Edit" href="<?php echo $editURL; ?>"  data-tooltip="tooltip" data-placement="top" style="cursor:pointer;" ><i class="edit-icon"></i></a>
					<a class="<?php echo $disableddelcls;?> tipText" rel="<?php echo $uid; ?>" title="Delete" data-whatever="<?php echo $deleteURL; ?>"  data-tooltip="tooltip" data-placement="top" style="cursor:pointer;"><i class="deleteblack"></i></a>


					<?php // activation code
					if(empty($user_data['is_activated']) && !empty($user_data['activation_time'])){
					$today = date('Y-m-d h:i:s');
					$activation_time = date('Y-m-d h:i:s', strtotime($user_data['activation_time']));
					$hour_diff = round((strtotime($today) - strtotime($activation_time))/3600, 1);
					?>
						<a class="resend-activation tipText" data-user="<?php echo $uid; ?>" title="Resend Activation Email"   data-toggle="tooltip" data-placement="top" style="cursor:pointer;"><i class="emailblack"></i></a>
					<?php
					}


					if(isset($auther ) && !empty($auther )){

					$restURL = SITEURL."organisations/organisation_user_reset";
					if(isset($user_details['membership_code']) && !empty($user_details['membership_code'])){ ?>
						<a class="reset-fa" data-user="<?php echo $uid; ?>" data-whatever="<?php echo $restURL; ?>" title="Reset 2FA"   data-toggle="tooltip" data-placement="top" style="cursor:pointer;"><i class="reset-2fa"></i></a>

				<?php
						}
					}
				}
			?>
		</div>
	</div>
	<?php } ?>
<?php }else{ ?>
	<div class="no-summary-found">No User</div>
<?php } ?>