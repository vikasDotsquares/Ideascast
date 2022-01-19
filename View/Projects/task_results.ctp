<?php
echo $this->Html->css('projects/task_results', array('inline' => true));
echo $this->Html->script('projects/task_results', array('inline' => true));
echo $this->Html->script('projects/plugins/jquery.dot', array('inline' => true));

?>
<style type="text/css">
	.project-detail.project-dropdown {
		min-width: 290px;
	}
	@media (min-width:1024px) and (max-width:1366px) {
		.project-detail.project-dropdown {
			min-width: 250px;
		}
	}
	@media (min-width:768px) and (max-width:991px) {
		.project-detail.project-dropdown {
			min-width: 0px;
		}
	}
	@media (min-width:992px) and (max-width:1023px) {
		.project-detail.project-dropdown {
			min-width: 350px;
		}
	}
</style>
<script type="text/javascript" >
$(function(){
	$("#myModal").on('hidden.bs.modal', function(e){
		$(this).removeData('bs.modal');
	})


	$('body').on('change', '#user_projects', function(event){
		if($(this).val() != '' && $(this).val() !== undefined){
			location.href = $js_config.base_url + 'projects/task_results/' + $(this).val();
		}
	})
})
</script>
<!-- OUTER WRAPPER	-->
<div class="row">

	<!-- INNER WRAPPER	-->
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">
                <h1 class="pull-left"><?php echo $page_heading; ?>
                    <p class="text-muted date-time" style="padding: 6px 0">
                        <span><?php echo $page_subheading; ?></span>
                    </p>
                </h1>
            </section>
		</div>
		<!-- END HEADING AND MENUS -->

		<span id="project_header_image">
			<?php
				if( isset( $project_id ) && !empty( $project_id ) ) {
					echo $this->element('../Projects/partials/project_header_image', array('p_id' => $project_detail['Project']['id']));
				}
			?>
		</span>

		<!-- MAIN CONTENT -->
		<div class="box-content">

            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder">

						<!-- CONTENT HEADING -->
                        <div class="box-header" style="background: #efefef none repeat scroll 0 0; border: 1px solid #ddd;">

							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->
<?php
    $myprojects = myprojects($this->Session->read('Auth.User.id'));
    $groupprojects = groupprojects($this->Session->read('Auth.User.id'), 1);
    $receivedprojects = receivedprojects($this->Session->read('Auth.User.id'), 1);


    $all_projects = [];
    if (is_array($myprojects)) {
        $myprojects = array_keys($myprojects);
        $all_projects = array_merge_recursive($all_projects, $myprojects);
    }
    if (is_array($groupprojects)) {
        $groupprojects = array_keys($groupprojects);
        $all_projects = array_merge_recursive($all_projects, $groupprojects);
    }
    if (is_array($receivedprojects)) {
        $receivedprojects = array_keys($receivedprojects);
        $all_projects = array_merge_recursive($all_projects, $receivedprojects);
    }

    $all_project = $this->ViewModel->project_signoff($all_projects);
    $user_projects = array_map(function($v){
        return trim(htmlentities($v));
    }, $all_project);
 ?>
							<div class="pull-left project-detail project-dropdown" style=" margin: 0 5px;">
								<?php /* ?>
								<span class="bg-blakish nomargin-left sb_blog">Created: <?php
								echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$project_detail['Project']['created']),$format = 'd M Y h:i:s');
								?></span>
								<span class="bg-black sb_blog">Updated: <?php
								echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime( $project_detail['UserProject']['modified'])),$format = 'd M Y h:i:s');
								?></span><?php */ ?>


									<label class="custom-dropdown" style="width: 100%;">
						                <?php
						                    echo $this->Form->select('Project.id', $user_projects, array('escape' => false, 'empty' => 'Select Project', 'class' => 'form-control aqua', 'id' => 'user_projects', 'default' => $project_id));
						                ?>
						            </label>


							</div>

							<?php
								$toc = 'bg-green';
								$total_daysN = daysLeft(date('Y-m-d',strtotime($project_detail['Project']['start_date'])), date('Y-m-d',strtotime($project_detail['Project']['end_date'])));
								if(date('Y-m-d') > date('Y-m-d',strtotime($project_detail['Project']['start_date']))){
									$total_complete_days = daysLeft($project_detail['Project']['start_date'], date('Y-m-d 12:00:00'));
									}else{
									$total_complete_days = daysLeft($project_detail['Project']['start_date'], date('Y-m-d 12:00:00'));
								}

								if(date('Y-m-d')  <= date('Y-m-d',strtotime($project_detail['Project']['end_date'])) && date('Y-m-d')  >= date('Y-m-d',strtotime($project_detail['Project']['start_date']))){
									$total_remain_days = daysLeft(date('Y-m-d 12:00:00'), $project_detail['Project']['end_date'], 1);
									}else{
									$total_remain_days = daysLeft(date('Y-m-d 12:00:00'), $project_detail['Project']['end_date']);
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

								if(isset($project_detail['Project']['sign_off']) && !empty($project_detail['Project']['sign_off'])){
									$dataP = 100;
									$toc = 'bg-red';
								}

								if (isset($params['project_id']) && !empty($params['project_id'])) {

									$p_permission = $this->Common->project_permission_details($params['project_id'], $this->Session->read('Auth.User.id'));

									$user_project = $this->Common->userproject($params['project_id'], $this->Session->read('Auth.User.id'));

							if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )  ) {
							?>

							<div class="tasks-progress-br search-result-bar" style="margin-left: 5px;">
								<div class="border bg-white ideacast-project-progress"><span class="pull-left hidden-sm hidden-md">Project Elapsed</span>
									<div class="progress tipText" title="Days Remaining: <?php echo $total_remain_days; ?>" >
										<div class="progress-bar <?php echo $toc ?>" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $dataP ?>%" >

										</div>
									</div><span class="pull-left"><?php echo $dataP ?>%</span>
								</div>
							</div>
							<?php }} ?>

							<div class="pull-right search-result-but" style="position: relative;">
									<!-- Project Options -->

									<?php  if( isset($project_id) && !empty($project_id) ){
										$uusde = $this->Common->userproject($project_id,$this->Session->read('Auth.User.id'));
										$pp_perm = $this->Common->project_permission_details($project_id,$this->Session->read('Auth.User.id'));

										//********************* More Button ************************
										echo $this->element('more_button', array('project_id' => $project_id, 'user_id'=>$this->Session->read('Auth.User.id'),'controllerName'=>'projects' ));

								} ?>
									<div class="btn-group action ">
										<a id="btn_go_back" data-original-title="Go Back" href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'index', $this->params['pass']['0'], 'admin' => FALSE ), TRUE); ?>" class="btn btn-warning tipText pull-right btn-sm" > <i class="fa fa-fw fa-chevron-left"></i> Back </a>
									</div>
							</div>

                        </div>
						<!-- END CONTENT HEADING -->


					<div class="box-body clearfix" id="box_body">
						<?php  echo $this->element( '../Projects/partials/task_result', array( 'params' => $params ) ); ?>
					</div>
                </div>
            </div>
        </div>
		<!-- END MAIN CONTENT -->

	</div>
</div>
<!-- END OUTER WRAPPER -->



