<?php echo $this->Html->css('projects/dropdown', array('inline' => true)); ?>
<style>
    .filters {
        background-color: #f1f3f4;
    }

    label.fancy_label {
        height: 28px;
        margin: 0;
    }

    .panel-heading {
        padding: 10px 15px 28px;
    }

    .panel-title.col-md-6 {
        padding: 0;
    }
    .project-boards-panel-colms.fix-height > label {
        width: 100%;
    }

    .fixed_span_block p{ margin:0;}


	.tipText {
		text-transform: none !important;
	}

	.declineres{
		text-transform:none;
	}
	.no-row-wrapper {
		color: #bbbbbb;
		font-size: 30px;
		left: 0;
		position: absolute;
		text-align: center;
		text-transform: uppercase;
		width: 100%;
		padding: 60px 0 0 0;
	}
    .board-col-2 {
        padding: 0;
    }
    .board-col-1 {
        padding: 2px 0;
    }
</style>
<script type="text/javascript" >
    $(function () {
        $('#popup_model_box_new').on('hidden.bs.modal', function () {
            $(this).removeData('bs.modal').find(".modal-content").html('<div style="background: #303030 none repeat scroll 0 0; display: block; padding: 100px; width: 100%;"><img src="'+$js_config.base_url+'/images/ajax-loader-1.gif" style="margin: auto;"></div>');
        });
        // $(".modal-content").hide()
        setTimeout(function () {
            // $(".modal-content").show()
        }, 1500)

        // Reset Filter
        $('body').delegate('#filter_reset', 'click', function (event) {
            event.preventDefault();
            window.location.href = $js_config.base_url + 'boards';
        })

		$('#modal_small').on('hidden.bs.modal', function(){
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('');
		});

		$("body").on('hover',".tipText", function(){
			$(".tooltip").css("text-transform","none");
		})
		$(".board-response").tooltip({
			placement:'top',
			template:'<div class="tooltip declineres" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>',
			container:'body',
			html: true

		})
    })
</script>

<?php
//echo $this->request->data['sort_by'];
?>
<div class="row">
    <div class="col-xs-12">

        <div class="row">
            <section class="content-header clearfix">
                <h1 class="box-title pull-left"><?php echo $viewData['page_heading']; ?>
                    <p class="text-muted date-time">
                        <span style="text-transform:none;"><?php echo $viewData['page_subheading']; ?></span>
                    </p>
                </h1>

                <div class="header-right-side-icon">
					<span class="ico-nudge ico-project-summary tipText" style="margin-right: 0" title="" data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::url('/',true)?>boards/send_nudge_board/type:board" data-original-title="Send Nudge"></span>
				</div>
            </section>
        </div>
        <?php echo $this->Session->flash(); ?>
        <div class="box-content">
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box border-top margin-top">


                        <div class="box-header filters" style="">
                            <!-- Modal Confirm -->
                            <div class="modal modal-warning fade" id="confirm_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">

                                    </div>
                                </div>
                            </div>

                            <!-- /.modal -->
                            <!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade" id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>

                            <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="popup_model_box_new" class="modal modal-success fade">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <!-- END MODAL BOX -->
                            <?php echo $this->Form->create('boards', array("controller" => "boards", "url" => "index", "method" => "post")); ?>
                            <div class="col-lg-12 no-padding project-board-top">


                                <div class="col-md-4 col-lg-3  project-board-top-cols board-col-2">

                                    <label class="custom-dropdown" style="width: 100%">
                                        <?php
                                        echo $this->Form->input('Project.aligned_id', array(
                                            'type' => 'select',
                                            'empty' => 'All Project Types',
                                            $aligneds,
                                            'label' => false,
                                            'div' => false,
                                            'class' => 'form-control aqua',
                                            'style' => 'width:100%;',
                                            'required' => false
                                        ));
                                        ?>
                                    </label>
                                </div>

                                <div class="col-md-5 col-lg-3  project-board-top-cols board-col-3">

                                    <!--<label for="AlignedTo" class="pull-left" style="margin-top: 5px; margin-right: 5px; font-weight: normal;"><span class="hidden-md">Projects</span> Created:</label>-->
                                    <label class="custom-dropdown" style="width:100%">
                                        <?php
                                        $options = array('today' => 'Today', 'last_7_day' => 'Last 7 days', 'last_14_day' => 'Last 14 days', 'last_30_day' => 'Last 30 days', 'last_90_day' => 'Last 90 days');

                                        echo $this->Form->input('Project.created', array(
                                            'empty' => 'Created Anytime',
                                            'type' => 'select',
                                            'options' => $options,
                                            'label' => false,
                                            'div' => false,
                                            'class' => 'form-control aqua',
                                            'style' => 'width:100%;'
                                        ));
                                        ?>
                                    </label>
                                </div>
 <div class="col-lg-4  project-board-top-cols board-col-1">
                                    <?php
                                    $selectedyes = '';
                                    $selectedno = '';
                                    if (isset($this->request->data['sort_by'])) {

                                        if ($this->request->data['sort_by'] == 1) {
                                            $selectedyes = 'checked="checked"';
                                        }
                                        if ($this->request->data['sort_by'] == 2) {
                                            $selectedno = 'checked="checked"';
                                        }
                                    }

                                    if (empty($selectedyes) && empty($selectedno)) {
                                        $selectedno = 'checked="checked"';
                                    }
                                    ?>
                                    <label class="" for="sort_by_toggle" style="margin-top: 5px; font-weight: normal;" >Started:</label>
                                    <input type="radio" id="sort_by_soonest" name="sort_by" class="fancy_input" value="1" <?php echo $selectedyes ?> />
                                    <label class="fancy_label" for="sort_by_soonest">Yes</label>

                                    <input type="radio" id="sort_by_last" name="sort_by" class="fancy_input"  value="2" <?php echo $selectedno ?>  />
                                    <label class="fancy_label" for="sort_by_last">No</label>
<?php
                                    $selectedyes = '';
                                    $selectedno = '';
                                    if (isset($this->request->data['filter_by_user'])) {

                                        if ($this->request->data['filter_by_user'] == 1) {
                                            $selectedyes = 'checked="checked"';
                                        }
                                        if ($this->request->data['filter_by_user'] == 2) {
                                            $selectedno = 'checked="checked"';
                                        }
                                    }

                                    if (empty($selectedyes) && empty($selectedno)) {
                                        $selectedno = 'checked="checked"';
                                    }
                                    ?>
                                    <label class="" for="sort_by_toggle" style="margin-top: 5px; font-weight: normal; padding-left: 15px" >My Projects:</label>
                                    <input type="radio" id="filter_by_other" name="filter_by_user" class="fancy_input" value="1" <?php echo $selectedyes ?> />
                                    <label class="fancy_label" for="filter_by_other">Yes</label>

                                    <input type="radio" id="filter_by_self" name="filter_by_user" class="fancy_input"  value="0" <?php echo $selectedno ?>  />
                                    <label class="fancy_label" for="filter_by_self">No</label>


                                </div>
                                <div class="col-md-3 col-lg-2 pull-right text-right  project-board-top-cols board-col-4">
                                    <button type="submit" id="filter_list" class="btn btn-success btn-sm">Apply Filter</button>
                                    <button id="filter_reset" class="btn btn-danger btn-sm">Reset</button>

                                </div>
                            </div>
                            <?php
                            echo $this->Form->end();
                            /* Filter Form End */
                            ?>
                        </div>
                        <div class="box-body" id="box_body" style="min-height: 600px;">
                            <div class="project-boards" style="position: relative;">
                                <!-- Row Start -->
                                <?php
                                //pr($viewData['projectsBoard']);
                                $projectCount = false;
								//pr($viewData['projectsBoard']);
                                if ( isset($viewData['projectsBoard']) && !empty($viewData['projectsBoard']) && count($viewData['projectsBoard']) > 0) {

                                    $bsr = 1;
                                    $uid = $this->Session->read('Auth.User.id');

                                    $boardsCount =  (isset($viewData['projectsBoard']) && !empty($viewData['projectsBoard']))? count($viewData['projectsBoard']) : 0;

                                    foreach ($viewData['projectsBoard'] as $key => $boardData) {

                                        $level = $this->Common->project_permission_details($boardData['Project']['id'], $uid);

                                        if (isset($level) && !empty($level)) {
                                            $one = isset($level['ProjectPermission']['project_level']) ? $level['ProjectPermission']['project_level'] : 0;
                                        }


                                        $gpid = 0;
                                        if (empty($level)) {

                                            $gpid = $this->Group->GroupIDbyUserID($boardData['Project']['id'], $uid);

                                            if (!empty($gpid))
                                                $level = $this->Group->group_permission_details($boardData['Project']['id'], $gpid);
                                        }



                                        if ((empty($level) || ( isset($level) && ($level['ProjectPermission']['project_group_id'] != $gpid && $level['ProjectPermission']['user_id'] != $uid) ))  || ($usertype > 0)) {

											if( $key == 0 ){
												echo '<h4> Most Recent Project Opportunities</h4>';
											}

                                            $projectOwner = $boardData['UserProject']['user_id'];
                                            //echo "Project ID =".$boardData['Project']['id'];

                                            $ownerProjectStatus = false;

                                            $pid = $boardData['Project']['id'];


                                            $data1 = $this->Common->project_permission_details($pid, $uid);
                                            $data2 = $this->Group->group_permission_details($pid, $uid);
                                            $data3 = $this->Common->userproject($pid, $uid);

                                            if (!empty($data1) && $data1['ProjectPermission']['project_level'] != 1) {
                                                $ownerProjectStatus = true;
                                            }
                                            if (!empty($data2) && $data2['ProjectPermission']['project_level'] != 1) {
                                                $ownerProjectStatus = true;
                                            }
                                            if (empty($data3)) {
                                                $ownerProjectStatus = true;
                                            }
											if($usertype > 0){
												$ownerProjectStatus = true;
											}





                                            $projectCount = true;
                                            $plusMinusCounter = 1;
                                            if ($ownerProjectStatus) {

                                                $boardCount = $this->Common->checkProjectBoardStatus($boardData['Project']['id']);

                                                //echo $boardCount."boardCount== ID == ".$boardData['Project']['id'];
                                                ?>

                                                <div data-id="panels-<?php echo $bsr; ?>" style="clear: both" class="panel <?php echo $boardData['Project']['color_code']; ?>">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title col-md-6">
                                                            <span class="trim-text">

                                                                <a data-toggle="collapse" data-parent="#accordion"  data-original-title="Show/Hide" href="#open_by<?php echo $bsr; ?>" style="cursor:pointer" style="margin-right: 10px" class="fa fa-briefcase tipText text-white  show_hide_panel pull-left" title="" href="#">


                                                                </a>

                <?php echo $boardData['Project']['title']; ?>




                                                            </span>
                                                        </h4>
                                                        <span class="pull-right" style="display:inline-block;" >
                                                            Start: <?php
															echo ( isset($boardData['Project']['start_date']) && !empty($boardData['Project']['start_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($boardData['Project']['start_date'])),$format = 'd M, Y') : 'None';
															//echo date('d M, Y', strtotime($boardData['Project']['start_date'])); ?> End: <?php
															echo ( isset($boardData['Project']['end_date']) && !empty($boardData['Project']['end_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($boardData['Project']['end_date'])),$format = 'd M, Y') : 'None';
															//echo date('d M, Y', strtotime($boardData['Project']['end_date'])); ?>
                                                        </span>

                                                    </div>

                                                    <div aria-expanded="false" data-toggle="collapse" id="open_by<?php echo $bsr; ?>" class="panel-body panel-collapse collapse in" style="" >

                                                        <div class="panel-cols-wrap">
                                                            <div class="col-md-3 no-padding panel-boxes">

                                                                <div class="sub-heading" style="padding: 5px; font-weight: normal; color: black;">Project Objective</div>

                                                                <div class="project-boards-panel-colms  project-objective-desc"><?php echo $boardData['Project']['objective']; ?></div>


                                                                <div class="sub-heading" style="padding: 5px; font-weight: normal; color: black;">Project Type</div>


                                                                <div class="project-boards-panel-colms ">	<?php
                                                                    $alignement = get_alignment($boardData['Project']['aligned_id']);
                                                                    if (!empty($alignement))
                                                                        echo $alignement['title'];
                                                                    else
                                                                        echo "N/A";
                                                                    ?></div>

                                                            </div>
                                                            <div class=" col-md-3 no-padding panel-boxes">

                                                                <div class="sub-heading" style="padding: 5px; font-weight: normal; color: black;">Description</div>

                                                                <div class="project-boards-panel-colms fix-height"><?php echo $boardData['Project']['description']; ?></div>
                                                            </div>
                                                            <div class=" col-md-2 no-padding panel-boxes">

                                                                <div class="sub-heading" style="padding: 5px; font-weight: normal; color: black;">Project Team</div>

                                                                <div class="project-boards-panel-colms fix-height">
                                                                    <?php
                                                                    //echo ucfirst($boardData['User']['UserDetail']['first_name']).' '.ucfirst($boardData['User']['UserDetail']['last_name']);
                                                                    $owner = $this->Common->ProjectOwner($boardData['Project']['id']);

                                                                    $allParticipateUser = array();
																	$participants = array();
																	if( isset($boardData['Project']['id']) && isset($owner['UserProject']['user_id']) ){
																		$participants = participants($boardData['Project']['id'],$owner['UserProject']['user_id']);
																	}

                                                                   // $participants = participants($boardData['Project']['id']);


                                                                    $show_sharer = null;
                                                                    $allparticip = array();
                                                                    if (isset($participants) && !empty($participants)) {
                                                                        foreach ($participants as $k => $part) {
                                                                            $show_sharer[$part] = $this->Common->userFullname($part);
                                                                        }

                                                                        foreach ($participants as $part) {
                                                                            $allparticip[] = $this->Common->userFullname($part);
                                                                        }
                                                                    }

                                                                    $participantsGpOwner = participants_group_owner($boardData['Project']['id'] );



								$showG_owners = null;

								if(isset($participantsGpOwner) && !empty($participantsGpOwner)) {
									foreach($participantsGpOwner as $participantsGpOwnerU){
										$allparticipOWN[] = $this->Common->userFullname($participantsGpOwnerU);
										if( !empty($participantsGpOwnerU) )
											$showG_owners[$participantsGpOwnerU] = $this->Common->userFullname($participantsGpOwnerU);
									}
								}



								$participantsGpSharer = participants_group_sharer($boardData['Project']['id'] );

								$showG_sharers = null;

								if(isset($participantsGpSharer) && !empty($participantsGpSharer)) {
									foreach($participantsGpSharer as $participantsGpsharerU){
										$allparticipOWNS[] = $this->Common->userFullname($participantsGpsharerU);
										if( !empty($participantsGpsharerU) )
											$showG_sharers[$participantsGpsharerU] = $this->Common->userFullname($participantsGpsharerU);
									}
								}

                                                                  //  pr($participants);

                                                                    if (isset($allparticip) && !empty($allparticip)) {

                                                                        $key = array_search($this->Common->userFullname($boardData['UserProject']['user_id']), $allparticip);
                                                                        if (isset($key) && !empty($key)) {
                                                                            $tmp = $allparticip[$key];
                                                                            unset($allparticip[$key]);
                                                                            $allparticip = array($key => $tmp) + $allparticip;
                                                                        }
                                                                    }

                                                                    //pr($allparticip);

                                                                    $owner = $this->Common->ProjectOwner($boardData['Project']['id'], $projectOwner);

                                                                   // $allParticipateUser[] = $owner[][];

																	$participants_owners = array();
																	if( isset($boardData['Project']['id']) && isset($owner['UserProject']['user_id']) ){

																		$participants_owners = participants_owners($boardData['Project']['id'], $owner['UserProject']['user_id']);

																	}
                                                                     $allParticipateUser[] = $participants_owners;
                                                                     $allParticipateUser[] = $participants;
                                                                     $allParticipateUser[] = $participantsGpOwner;
                                                                     $allParticipateUser[] = $participantsGpSharer;


                                                                    $filter_all_user = $user_ids = null;
                                                                    $filter_all_user = array_filter($allParticipateUser);

                                                                    if(isset($filter_all_user) && !empty($filter_all_user)){

                                                                        foreach($filter_all_user as $forUs){

                                                                            $len = ( isset($forUs) && !empty($forUs) )? count($forUs) : 0;
                                                                            for($i= 0;$i <=$len;$i++ ){
                                                                                if(isset($forUs[$i]) && !empty($forUs[$i])){
                                                                                $user_ids[] = $forUs[$i];
                                                                                }
                                                                            }
                                                                        }
                                                                    }

                                                                  //  pr($user_ids);

                                                                    $show_owners = null;

                                                                    if (isset($participants_owners) && !empty($participants_owners)) {
                                                                        foreach ($participants_owners as $participantss) {
                                                                            $allparticipOW[] = $this->Common->userFullname($participantss);
                                                                            if (!empty($participantss))
                                                                                $show_owners[$participantss] = $this->Common->userFullname($participantss);
                                                                        }
                                                                    }

                                                                    if (isset($allparticipOW) && !empty($allparticipOW)) {
                                                                        $keyW = array_search($this->Common->userFullname($this->Session->read('Auth.User.id')), $allparticipOW);
                                                                        if (isset($keyW) && !empty($keyW)) {
                                                                            $tmpW = $allparticipOW[$keyW];
                                                                            unset($allparticipOW[$keyW]);
                                                                            $allparticipOW = array($keyW => $tmpW) + $allparticipOW;
                                                                        }
                                                                    }
                                                                    ?>

                                                                    <label> <?php
                                                    //pr($show_owners);
                                                    $OwnerCounter = 0;
                                                    $totalSharerCount = 0;
                                                    if ( (isset($show_sharer) && !empty($show_sharer) && count($show_sharer) > 0)  || ( isset($show_owners) && !empty($show_owners) && count($show_owners) > 0) ) {



$totalSharerCount_shrere =	( isset($show_sharer) && !empty($show_sharer) && count($show_sharer) > 0 )? count($show_sharer) : 0 ;
$totalSharerCount_owner = ( isset($show_owners) && !empty($show_owners) && count($show_owners) > 0 )? count($show_owners) : 0;


                                                        //$totalSharerCount = count($show_sharer) + count($show_owners);

                                                        /* if((isset($owner['UserProject']['user_id']) && !empty($owner['UserProject']['user_id'])) && $totalSharerCount > 0){
                                                          $totalSharerCount  = $totalSharerCount  - 1 ;

                                                          } */
                                                          ?>
                                                            Team Members:<span style="font-weight: normal;">
                                                          <?php
                                                        echo $totalSharerCount = $totalSharerCount_shrere+$totalSharerCount_owner;

                                                    } else {
                                                        echo "0";
                                                    }
                                                       $owner_flag = true;
                                                       $sharer_flag = true
                                                       ?></span></label>

													   <label> Creator: </label>
                                                                    <ul style="list-style:none; padding-left:0;">
                                                                        <?php
																		$ownerFullName = 'None';
																		$ownerprojectuserid = '';
																		if( isset($owner['UserProject']['user_id']) ){

																			$ownerFullName = $this->Common->userFullname($owner['UserProject']['user_id']);
																			$ownerprojectuserid = $owner['UserProject']['user_id'];
																		}
                                                                        ?>
                                                                        <li> <a href="#" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $ownerprojectuserid )); ?>"  data-target="#popup_modal" data-toggle="modal" ><i class="fa fa-user text-maroon"></i></a> <?php echo $ownerFullName; ?></li>

                                                                    </ul>

                                                                    <label> Owners: </label>
                                                                    <ul style="list-style:none; padding-left:0;">
                                                                        <?php if (isset($show_owners) && !empty($show_owners)) { ?>
                                                                            <?php
                                                                            $totalOwnerCounter = 0;
                                                                            foreach ($show_owners as $key => $val) {

                                                                                if ($owner['UserProject']['user_id'] != $key) {
                                                                                    $owner_flag = false;

                                                                                    $totalOwnerCounter++;
                                                                                    ?>
                                                                                    <li> <a href="#" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $key)); ?>"  data-target="#popup_modal" data-toggle="modal" ><i class="fa fa-user text-maroon"></i></a> <?php echo $val; ?></li>
                                                                                <?php
                                                                                }
                                                                            }

                                                                            ?>
                                                                        <?php }
                                                                        if (isset($showG_owners) && !empty($showG_owners)) { ?>
                                                                            <?php
                                                                            $totalOwnerCounter = 0;
                                                                            foreach ($showG_owners as $key => $val) {

                                                                                if ($owner['UserProject']['user_id'] != $key) {
                                                                                     $owner_flag = false;
                                                                                    $totalOwnerCounter++;
                                                                                    ?>
                                                                                    <li> <a href="#" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $key)); ?>"  data-target="#popup_modal" data-toggle="modal" ><i class="fa fa-user text-maroon"></i></a> <?php echo $val; ?></li>
                                                                                <?php
                                                                                }
                                                                            }

                                                                            ?>
                                                                        <?php }

                                                                             if($owner_flag == true){ ?>
                                                                            <li class="not_avail">None</li>
                                                                            <?php } ?>
                                                                    </ul>

                                                                    <label> Sharers: </label>
                                                                    <ul  style="list-style:none; padding-left:0;">
                                                                        <?php


                                                                        if (isset($show_sharer) && !empty($show_sharer)) { ?>
                                                                            <?php foreach ($show_sharer as $key => $val) {
                                                                                $sharer_flag = false;
                                                                                ?>
                                                                                <li> <a href="#" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $key)); ?>"  data-target="#popup_modal" data-toggle="modal" ><i class="fa fa-user text-maroon"></i></a> <?php echo $val; ?></li>
                                                                            <?php } ?>
                                                                        <?php }

                                                                        if (isset($showG_sharers) && !empty($showG_sharers)) { ?>
                                                                            <?php foreach ($showG_sharers as $key => $val) {
                                                                                $sharer_flag = false;
                                                                                ?>
                                                                                <li> <a href="#" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $key)); ?>"  data-target="#popup_modal" data-toggle="modal" ><i class="fa fa-user text-maroon"></i></a> <?php echo $val; ?></li>
                                                                            <?php } ?>
                                                                        <?php }
                                                                        if($sharer_flag == true) { ?>
                                                                            <li class="not_avail">None</li>
                                                                        <?php } ?>
                                                                    </ul>

                                                                </div>


                                                            </div>
                                                            <div class="col-md-2 no-padding panel-boxes">

                                                                <div class="sub-heading" style="padding: 5px; font-weight: normal; color: black;">Team Skills</div>


                                                                <div class="project-boards-panel-colms fix-height">
                                                                    <?php



                                                                    $userSkillArr = $projectSkillArr = array();
                                                                    $userAllSkill = get_userSkills($user_ids,true);
                                                                    if(isset($pid) && !empty($pid)){
                                                                        $projectSkillArr =  $this->Common->get_skill_of_project($pid);
                                                                        $projectSkillArr = Set::extract('/ProjectSkill/skill_id', $projectSkillArr);
                                                                    }
                                                                    if(isset($userAllSkill) && !empty($userAllSkill)){
                                                                        $userSkillArr = Set::extract('/UserSkill/skill_id', $userAllSkill);
                                                                    }

                                                                    $array_intersect = array_diff($projectSkillArr, $userSkillArr);
																	//pr($projectSkillArr);
																	//pr($userSkillArr);
                                                                    $skill_on_projects = array_intersect($projectSkillArr, $userSkillArr);
																	//pr($skill_on_projects);
                                                                    ?>
                                                                    <label>Skills in Demand:</label>
                                                                    <ul style="list-style:none; padding-left:0;">
				<?php
				if(empty($array_intersect))
				{
				   echo "None";
				}else
				{
					foreach ($array_intersect as $result) {
						$title = get_SkillName($result);
						if( isset($title['Skill']['title']) && !empty($title['Skill']['title']) ){
							echo "<li>";
							echo $title['Skill']['title'];
							echo "</li>";
						}
					}
				}
				?>
                                                                    </ul>


                                                                    <label>Skills on Project:</label>
                                                                    <ul style="list-style:none; padding-left:0;">
                                                                        <?php
																		//pr($user_ids);
                                                                        //$userTopSkill = get_userSkills($user_ids);
																		//pr($userTopSkill);
																		if(empty($skill_on_projects))
                                                                        {
                                                                           echo "None";
                                                                        }else
                                                                        {

                                                                        $UserSkillList = array();
                                                                        foreach ($skill_on_projects as $skillID) {
                                                                            /* $skillsname =  get_SkillName($skillID['UserSkill']['skill_id']);
                                                                              echo "<li>";
                                                                              echo $skillsname['Skill']['title'];
                                                                              echo "</li>"; */

                                                                            $skillsname = get_SkillName($skillID);
																			if(isset($skillsname['Skill']['title']))
                                                                            $UserSkillList[] = $skillsname['Skill']['title'];
                                                                        }
                                                                        sort($UserSkillList);

                                                                        foreach ($UserSkillList as $skillIDList) {
                                                                            echo "<li>";
                                                                            echo $skillIDList;
                                                                            echo "</li>";
                                                                        }
																		}
                                                                        ?>
                                                                    </ul>

                                                                </div>

                                                            </div>
                                                            <div class="col-md-2 no-padding panel-boxes">

                                                                <div class="sub-heading" style="padding: 5px; font-weight: normal; color: black;">Action</div>

																<?php if($usertype > 0) {?>
																	<div class="project-boards-panel-colms fix-height actions">If you would like to encourage others to take part, send a Nudge.
                                                                    <br>
																	</div>
																	<div class="btn-section">
																		<a class="btn btn-success btn-sm" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'send_nudge_board', 'type' => 'prj_board', 'project' => $boardData['Project']['id'])); ?>" data-target="#modal_nudge" data-toggle="modal" id="">Nudge</a>
																	</div>

																<?php } else { ?>

																	<div class="project-boards-panel-colms fix-height actions">If you are interested in taking part in this Project, send a Request.
																		<br>

																		<span class="small fixed_span_block">
																			<?php
																			$checkProjectexists = $this->Common->ProjectBoardData($boardData['Project']['id']);

																			$dds = $this->Common->ProjectBoardData($boardData['Project']['id']);

																			$userFullName = $this->Common->userFullname($dds['receiver']);

																			if (isset($dds) && !empty($dds)) {
																				?>
																				<label>Request Sent:</label>
																				<?php
																				//$sentInterestDate =  ( isset($dds['created']) && !empty($dds['created'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($dds['created'])),$format = 'd M Y g:iA') : 'None';
																				//echo $dds['created'];
																				$sentInterestDate =  ( isset($dds['created']) && !empty($dds['created'])) ? date("d M Y g:iA",strtotime($dds['created'])) : 'None';

																				echo "<p>" . $sentInterestDate . "</p>"; ?>

																		  <?php }
																			?>
																		</span>

																		<?php /* ?><span class="small">

																			<?php if (isset($checkProjectexists['project_status']) && $checkProjectexists['project_status'] > 1) {
																				?>
																				<label>Not Possible:</label>
																				<?php
																				if (isset($dds) && !empty($dds)) {

																					$notPossibleDate =  ( isset($dds['updated']) && !empty($dds['updated'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($dds['updated'])),$format = 'd M Y g:i:sA') : 'None';

																					//echo "<p>" . date('d M Y h:i:s', strtotime($dds['updated'])) . "</p>";
																					echo "<p>" . $notPossibleDate . "</p>";

																				}
																			}
																			?>
																		</span><?php */ ?>
																		<span class="small fixed_span_block">
																		 <?php

																		if (isset($checkProjectexists['project_status']) && $checkProjectexists['project_status'] < 1) {

																		?>

																				<label>Response Received:&nbsp;</label>
																				<span class="board-response response-grey tipText" title="" data-toggle="modal" data-target="#popup_model_box11" data-remote="#" data-original-title="No Response, from <?php echo $userFullName;?>" ></span>


																		<?php } else if (isset($checkProjectexists['project_status']) && $checkProjectexists['project_status'] > 1) {

																			$reasonresponse = $this->Common->board_data_by_project_id($boardData['Project']['id'],$uid);
																			$declinereason = '';
																			if( isset($reasonresponse) && !empty($reasonresponse) ){
																				$declinereasons = $this->Common->show_reason($reasonresponse['BoardResponse']['reason']);
																				if( isset($declinereasons) && !empty($declinereasons) ){
																					$declinereason = $declinereasons['DeclineReason']['reasons'];

																					if($declinereasons['DeclineReason']['reasons']=='Give no reason'){
																						$declinereason = "No reason given" ;

																					}

																					$notPossibleDate =  ( isset($dds['updated']) && !empty($dds['updated'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i A',strtotime($dds['updated'])),$format = 'd M Y g:iA') : 'None';
																					$notPossibleDatere = $notPossibleDate;
																				}

																		?>
																				<label>Response Received:&nbsp;</label>
																				<span class="board-response response-red" data-toggle="modal" data-target="#popup_model_box112" data-remote="#" title="Declined: <?php echo $declinereason;?> <?php echo "<br />".$notPossibleDatere;?>" ></span>
																		<?php }
																			} ?>
																		</span>
																	</div>
																	<div class="btn-section">
																		<?php
																		// pr($checkProjectexists);
																		if (isset($checkProjectexists['project_status']) && $checkProjectexists['project_status'] < 1) {
																			?>
																			<a class="btn btn-success btn-sm disable tipText" title="Request Sent"   > Request</a>

																		<?php }
																		if (isset($checkProjectexists['project_status']) && $checkProjectexists['project_status'] > 1) { ?>

																			<a class="btn btn-success btn-sm disable tipText11">Request</a>
																			<br />


																		<?php } else if (!isset($checkProjectexists['project_status'])) { ?>
																			<a class="btn btn-success btn-sm" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'send_interest', $projectOwner, 'project' => $boardData['Project']['id'])); ?>" data-target="#popup_model_box_new" data-toggle="modal" id="">Request</a>
																		<?php } ?>


																	</div>
																<?php } ?>
															</div>
                                                        </div>

                                                    </div>

                                                </div>
                                                <script type="text/javascript">
                                                    var selectIds<?php echo $bsr; ?> = $('#open_by<?php echo $bsr; ?>');

                                                    $(function ($) {
                                                        selectIds<?php echo $bsr; ?>.on('show.bs.collapse hidden.bs.collapse ', function (e) {
                                                           // $(this).prev().find('.glyphicon').toggleClass('fa-plus fa-minus');
                                                        })
                                                    });
                                                </script>
                                                <?php
                                                $bsr++;
                                                $projectCount = true;
                                                $plusMinusCounter++;
                                            }
                                            //$projectCount = false;
                                        }
                                        //$projectCount = false;
                                    }
                                }
                                ?>
                                <?php if (isset($projectCount) && $projectCount == false) { ?>

                                    <!--<div data-id="panels-1" align="center" style="color: #bbbbbb; font-size: 30px; left: 0; position: absolute; text-align: center; text-transform: uppercase; top: 35%; width: 100%; padding: 20px 0 0 0;">SELECT PROJECT</div>-->
									<div data-id="panels-1" align="center" class="no-row-wrapper">NO PROJECTS</div>
                                <?php } ?>
                                <!-- Row End -->
                                <div>
                                </div>
                                <!-- <div class="box-body clearfix list-acknowledge" style="min-height: 600px;"></div> -->
                            </div>

<?php
echo $this->element('../Boards/partial/nologerproject');
?>



                        </div>
                    </div>
                </div>
            </div>
        </div>