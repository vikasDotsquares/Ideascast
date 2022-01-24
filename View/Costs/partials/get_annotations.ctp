<?php
	$current_user_id = $this->Session->read('Auth.User.id');
	$current_org = $this->Permission->current_org();
?>

<?php if( isset($data) && !empty($data) ) {
	$i=1;
	foreach($data as $key => $row) { ?>
		<?php
			$userDetail = $this->ViewModel->get_user( $row['ElementCostComment']['user_id'], null, 1 );
			$user_image = SITEURL . 'images/placeholders/user/user_1.png';
			$user_name = 'Not Available';
			$job_title = 'Not Available';
			if(isset($userDetail) && !empty($userDetail)) {
				$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
				$profile_pic = $userDetail['UserDetail']['profile_pic'];
				$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

				$html = '';
				if( $row['ElementCostComment']['user_id'] != $current_user_id ) {
					$html = CHATHTML($row['ElementCostComment']['user_id']);
				}

				if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
					$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
				}
			}

			$project_id = element_project($row['ElementCostComment']['element_id']);
			$project_currency_symbol = project_currency_symbol($project_id);
		?>
	<div class="annotate-item" data-id="<?php echo $row['ElementCostComment']['id']; ?>">
		<div class="annotate">
			<div class="annotate-text-image">
				<img src="<?php echo $user_image; ?>" class="annotate-user-image pophover tipText" title="<?php echo $user_name; ?>" align="left" width="40" height="40" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
				<?php if( ($userDetail['UserDetail']['organization_id'] != $current_org['organization_id']) &&  ($userDetail['UserDetail']['user_id'] != $this->Session->read('Auth.User.id')) ){ ?>	
						<i class="communitygray18 team-meb-com tipText" title="Not In Your Organization"></i>
				<?php } ?>	
			</div>
			<div class="annotate-text">
				<div class="annotate-cost"><?php echo isset($row['ElementCostComment']['cost'])? "<span class='cost-symbol'>".$project_currency_symbol."</span>".$row['ElementCostComment']['cost'] : "<span class='cost-symbol'>".$project_currency_symbol."</span>"."0"; ?></div>
				<span class="editannotatetext">
				<?php
					 $string =   $row['ElementCostComment']['comments'];
					 echo stripslashes(str_replace('\r\n','<br/>',$string));
				?>
				</span>
			</div>
		</div>

		<div class="date-options">
			<span class="date-text"><?php echo _displayDate($row['ElementCostComment']['modified']); ?></span>
			<span class="controls">
			<?php if( $row['ElementCostComment']['user_id'] == $current_user_id ) { ?>
				<a type="button" id="" class="btn btn-default btn-xs edit_annotate">
					<i class="fa fa-pencil"></i>
				</a>
				<a type="button" id="" class="btn btn-danger btn-xs delete_annotate">
					<i class="fa fa-trash"></i>
				</a>
			<?php } ?>
			</span>
		</div>
	</div>
	<?php $i++; } ?>
<?php }
else { ?>
<div id="no-annotate-list" >No Annotations</div>
<?php } ?>
