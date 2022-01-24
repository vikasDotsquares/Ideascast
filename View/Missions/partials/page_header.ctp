<?php
	$ws_exists = true;
	$ws_count = $prj_count = 0;
	if (isset($menu_project_id) && !empty($menu_project_id)) {
		// echo $menu_project_id;
		$prj_count = $this->ViewModel->user_project_count();
		$ws_count = $this->ViewModel->project_workspace_count($menu_project_id);
		if (empty($ws_count)) {
			$ws_exists = false;
		}
	}

	$owner = $this->Common->ProjectOwner($project_id,$this->Session->read('Auth.User.id'));

	$participants = participants($project_id,$owner['UserProject']['user_id']);

	$participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);

	$participantsGpOwner = participants_group_owner($project_id );

	$participantsGpSharer = participants_group_sharer($project_id );

	$participants = (isset($participants) && !empty($participants)) ? array_filter($participants) : array();
	$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : array();
	$participantsGpOwner = (isset($participantsGpOwner) && !empty($participantsGpOwner)) ? array_filter($participantsGpOwner) : array();
	$participantsGpSharer = (isset($participantsGpSharer) && !empty($participantsGpSharer)) ? array_filter($participantsGpSharer) : array();

	$total = 0;

	$participants_tot = ( isset($participants) && !empty($participants) )? count($participants) : 0;
	$participants_owners_tot = ( isset($participants_owners) && !empty($participants_owners) )? count($participants_owners) : 0;
	$participantsGpOwner_tot = ( isset($participantsGpOwner) && !empty($participantsGpOwner) )? count($participantsGpOwner) : 0;
	$participantsGpSharer_tot = ( isset($participantsGpSharer) && !empty($participantsGpSharer) )? count($participantsGpSharer) : 0;

	$total = $participants_tot + $participants_owners_tot + $participantsGpOwner_tot + $participantsGpSharer_tot;

?>
<section class="content-header clearfix nopadding sb_blog_parent">
	<div class="pull-left project-detail">
		<a class="bg-gray nomargin sb_blog mission_peopl" data-target="#modal_people" data-toggle="modal" href="<?php echo SITEURL ?>projects/project_people/<?php echo $project_id; ?>">Project Team: <?php echo $total; ?></a>
		<span class="bg-blakish sb_blog" style="cursor:default;">Start: <?php echo ( isset($projects['Project']['start_date']) && !empty($projects['Project']['start_date'])) ? _displayDate($projects['Project']['start_date'], 'd M, Y') : 'N/A';  ?></span>
		<span class="bg-black nomargin-left sb_blog" style="cursor:default;">End: <?php echo ( isset($projects['Project']['end_date']) && !empty($projects['Project']['start_date'])) ? _displayDate($projects['Project']['end_date'], 'd M, Y') : 'N/A';  ?></span>
	</div>

	<?php $toc = 'bg-green';
		$total_daysN = daysLeft(date('Y-m-d',strtotime($projects['Project']['start_date'])), date('Y-m-d',strtotime($projects['Project']['end_date'])));
		if(date('Y-m-d') > date('Y-m-d',strtotime($projects['Project']['start_date']))){
			$total_complete_days = daysLeft($projects['Project']['start_date'], date('Y-m-d 12:00:00'));
			}else{
			$total_complete_days = daysLeft($projects['Project']['start_date'], date('Y-m-d 12:00:00'));
		}

		if(date('Y-m-d')  <= date('Y-m-d',strtotime($projects['Project']['end_date'])) && date('Y-m-d')  >= date('Y-m-d',strtotime($projects['Project']['start_date']))){
			$total_remain_days = daysLeft(date('Y-m-d 12:00:00'), $projects['Project']['end_date'], 1);
			}else{
			$total_remain_days = daysLeft(date('Y-m-d 12:00:00'), $projects['Project']['end_date']);
		}

		if(!empty($total_daysN) && !empty($total_complete_days)) {
			$dataP =   (round( ( ($total_complete_days  * 100 ) / $total_daysN ), 0, 1)) > 0 ?   (round( ( ($total_complete_days  * 100 ) / $total_daysN ), 0, 1)): 0;
			if($dataP > 100){
				$dataP = 100;
				$toc = 'bg-red';
			}

			}else{
			$dataP = 0;
		}

		if(isset($projects['Project']['sign_off']) && !empty($projects['Project']['sign_off'])){
			$dataP = 100;
			$toc = 'bg-red';
		}

		if (isset($project_id) && !empty($project_id)) {

			$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

			$user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));


			if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )  ) {
			?>
			<div class="mission-room-bar">
				<div class="border bg-white ideacast-project-progress"><span class="pull-left hidden-md">Project Elapsed</span>
					<div   class="progress tipText" title="Days Remaining: <?php echo $total_remain_days; ?>">
						<div style="width:<?php echo $dataP."%"; ?>" aria-valuemax="100" aria-valuemin="50" aria-valuenow="50" role="progressbar" class="progress-bar <?php echo $toc ?>">

						</div>
					</div><span class="pull-left"><?php echo $dataP."%"; ?></span>
				</div>

			</div>
		<?php  }
		} ?>
	<div class="pull-right">
		<div class="btn-group action ">
			<?php //********************* More Button ************************
				//echo $this->element('more_button', array('project_id' => $project_id, 'user_id'=>$this->Session->read('Auth.User.id'),'controllerName'=>'missions' ));
			?>
		</div>
	</div>
</section>