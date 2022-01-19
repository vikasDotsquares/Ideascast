<?php $resourcer = ($this->Session->read('Auth.User.UserDetail.resourcer') > 0) ? true : false; ?>
<?php $current_org = $this->Permission->current_org(); ?>
<?php echo $this->Form->hidden('cuser', ['value' => $cuser, 'id' => 'cuser']); ?>
<?php echo $this->Form->hidden('cdate', ['value' => $date, 'id' => 'cdate']); ?>
<?php echo $this->Form->hidden('cdate_type', ['value' => $cdate_type, 'id' => 'cdate_type']); ?>
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h3 class="modal-title annotationeleTitle" id="myModalLabel">Utilization: <?php echo ($cdate_type == 'w') ? 'W/C '.date('d M, Y', strtotime($date)) : ($cdate_type == 'm' ? date('M, Y', strtotime($date)) : date('d M, Y', strtotime($date)))  ;  ?> - <?php echo $udata; ?></h3>
</div>
<div class="modal-body popup-select-icon">
   <div class="unit-details-tab">
      <ul class="nav nav-tabs tab-list">
         <li class="active">
            <a data-toggle="tab" class="active" href="#util_work" aria-expanded="true">Work</a>
         </li>
         <?php if($resourcer){ ?>
         <li class="">
            <a data-toggle="tab" href="#util_adjustment" aria-expanded="false">Adjustments</a>
         </li>
         <?php } ?>
         <li class="">
            <a data-toggle="tab" href="#util_availability" aria-expanded="false">Availability</a>
         </li>
         <li class="">
            <a data-toggle="tab" href="#util_workblock" aria-expanded="false">Work Blocks</a>
         </li>
         <li class="">
            <a data-toggle="tab" href="#util_absences" aria-expanded="false">Absences</a>
         </li>
      </ul>
      <div class="tab-content">
         <div id="util_work" class="tab-pane fade active in">
            <div class="unit-filt-adj-warp">
               <div class="planning-unit-header work-header">
                  <div class="pln-col ut-col-1">
                     <span class="ps-h-one sort_order ws" data-type="projects" data-by="first_name" data-order="" data-original-title="" title="">
                        Name
                        <i class="fa fa-sort" aria-hidden="true"></i>
                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                     </span>
                     <span class="ps-h-one sort_order ws" data-type="projects" data-by="last_name" data-order="" data-original-title="" title="">
                        <i class="fa fa-sort" aria-hidden="true"></i>
                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                     </span>
                  </div>
                  <div class="pln-col ut-col-2">
                     <span class="ps-h-one sort_order ws" data-type="projects" data-by="pname" data-order="" data-original-title="" title="">
                        Project
                        <i class="fa fa-sort" aria-hidden="true"></i>
                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                     </span>
                     <span class="ps-h-two sort_order ws" data-type="projects" data-by="wname" data-order="" data-original-title="" title="">
                        Workspace
                        <i class="fa fa-sort" aria-hidden="true"></i>
                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                     </span>
                     <span class="ps-h-two sort_order ws" data-type="projects" data-by="ename" data-order="" data-original-title="" title="">
                        Task
                        <i class="fa fa-sort" aria-hidden="true"></i>
                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                     </span>
                  </div>
                  <div class="pln-col ut-col-3">
                     <span class="ps-h-one sort_order ws active" data-type="projects" data-by="start_date" data-order="desc" data-original-title="" title="">
                        Start
                        <i class="fa fa-sort" aria-hidden="true"></i>
                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                     </span>
                     <span class="ps-h-two sort_order ws" data-type="projects" data-by="end_date" data-order="" data-original-title="" title="">
                        End
                        <i class="fa fa-sort" aria-hidden="true"></i>
                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                     </span>
                  </div>
                  <div class="pln-col ut-col-4">
                     <span class="ps-h-one sort_order ws" data-type="projects" data-by="completed_hours" data-order="" data-original-title="" title="">
                        Completed
                        <i class="fa fa-sort" aria-hidden="true"></i>
                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                     </span>
                  </div>
                  <div class="pln-col ut-col-5">
                     <span class="ps-h-one sort_order ws" data-type="projects" data-by="remaining_hours" data-order="" data-original-title="" title="">
                        Remaining
                        <i class="fa fa-sort" aria-hidden="true"></i>
                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                     </span>
                  </div>
                  <div class="pln-col ut-col-6 plnactions">
                     Actions
                  </div>
               </div>
               <div class="unit-popup-cont work-list-wrap">
                  <?php if(isset($ef_data) && !empty($ef_data)){ ?>
                  <?php foreach ($ef_data as $key => $value) {
                     $detail = $value;
                     $el_eff = $detail['ef'];

                     $user_id = $el_eff['user_id'];
                       $profile_pic = $detail['ud']['profile_pic'];
                       if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
                           $profilesPic = SITEURL . USER_PIC_PATH . $profile_pic;
                       } else {
                           $profilesPic = SITEURL . 'images/placeholders/user/user_1.png';
                       }

                       $profile_url = Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'admin' => FALSE ), true );
                  ?>
                  <div class="pln-data-row">
                     <div class="pln-col ut-col-1">
                        <div class="style-people-com">
                           <span class="style-popple-icon-out">
                              <a class="style-popple-icon ud" href="#" data-toggle="modal" data-remote="<?php echo $profile_url; ?>" data-target="#popup_modal">
                                 <img alt="User Profile Pic" src="<?php echo $profilesPic; ?>" data-content="<div class='wpop'><p><?php echo htmlspecialchars($detail[0]['username']); ?></p><p><?php echo htmlspecialchars($detail['ud']['job_title']); ?></p></div>" class="user-image ud" align="left" data-original-title="" title="">
                              </a>
                                   <?php if($current_org['organization_id'] != $detail['ud']['organization_id']){ ?>
                                       <i class="communitygray18 tipText community-g ud" style="cursor: pointer;" title="" data-original-title="Not In Your Organization" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo $profile_url; ?>"></i>
                                   <?php } ?>
                           </span>
                           <div class="style-people-info">
                              <span class="style-people-name ud" data-toggle="modal" data-remote="<?php echo $profile_url; ?>" data-target="#popup_modal"><?php echo ($detail[0]['username']); ?></span>
                              <span class="style-people-title"><?php echo ($detail['ud']['job_title']); ?></span>
                           </div>
                        </div>
                     </div>
                     <div class="pln-col ut-col-2">
                        <a href="<?php echo Router::Url( array( "controller" => "projects", "action" => "index", $el_eff['project_id'], 'admin' => FALSE ), true ); ?>"><div class="text-ellipsis proworktask"><?php echo htmlentities($detail['prj']['pname'], ENT_QUOTES, "UTF-8"); ?></div></a>
                        <a href="<?php echo Router::Url( array( "controller" => "projects", "action" => "manage_elements", $el_eff['project_id'], $el_eff['workspace_id'], 'admin' => FALSE ), true ); ?>"><div class="text-ellipsis proworktask"><?php echo htmlentities($detail['wsp']['wname'], ENT_QUOTES, "UTF-8"); ?></div></a>
                        <a href="<?php echo Router::Url( array( "controller" => "entities", "action" => "update_element", $el_eff['element_id'], 'admin' => FALSE ), true ); ?>"><div class="text-ellipsis proworktask"><?php echo htmlentities($detail['task']['ename'], ENT_QUOTES, "UTF-8"); ?></div></a>
                     </div>
                     <div class="pln-col ut-col-3">
                        <div class="pln-date"> <?php echo date('d M, Y', strtotime($detail['task']['start_date'])); ?> </div>
                        <div class="pln-date"> <?php echo date('d M, Y', strtotime($detail['task']['end_date'])); ?> </div>
                     </div>
                     <div class="pln-col ut-col-4">
                        <span class="tipText" title="<?php echo htmlspecialchars($el_eff['comment']); ?>"><?php echo ($el_eff['completed_hours'] == 1) ? $el_eff['completed_hours'].' Hr' : $el_eff['completed_hours'].' Hrs'; ?></span>
                     </div>
                     <div class="pln-col ut-col-5">
                        <span class="tipText" title="<?php echo htmlspecialchars($el_eff['comment']); ?>"><?php echo ($el_eff['remaining_hours'] == 1) ? $el_eff['remaining_hours'].' Hr' : $el_eff['remaining_hours'].' Hrs'; ?></span>
                     </div>
                     <div class="pln-col ut-col-6 plnactions">
                        <a class="tipText edit edit-works" title="Add Adjustment" href="#" data-user="<?php echo $el_eff['user_id'] ?>" data-project="<?php echo $el_eff['project_id'] ?>" data-workspace="<?php echo $el_eff['workspace_id'] ?>" data-task="<?php echo $el_eff['element_id'] ?>"> <i class="workspace-icon"></i></a>
                     </div>
                  </div>
                  <?php } ?>
                  <?php }else{ ?>
                     <div class="no-add-adu">No Tasks</div>
                  <?php } ?>
               </div>
            </div>
         </div>
         <?php if($resourcer){ ?>
         <div id="util_adjustment" class="tab-pane fade">
            <div class="unit-filt-adj-warp">
               <div class="planning-unit-header adj-header">
                  <div class="pln-col adj-col-1">
                     <span class="ps-h-one sort_order active adjl" data-type="projects" data-by="first_name" data-order="desc" data-original-title="" title="">
                        Name
                        <i class="fa fa-sort" aria-hidden="true"></i>
                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                     </span>
                     <span class="ps-h-one sort_order adjl" data-type="projects" data-by="last_name" data-order="" data-original-title="" title="">
                        <i class="fa fa-sort" aria-hidden="true"></i>
                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                     </span>
                  </div>
                  <div class="pln-col adj-col-2">
                     <span class="ps-h-one sort_order adjl" data-type="projects" data-by="creator" data-order="desc" data-original-title="" title="">
                        Added By
                        <i class="fa fa-sort" aria-hidden="true"></i>
                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                     </span>
                     <span class="ps-h-two sort_order adjl" data-type="projects" data-by="created" data-order="" data-original-title="" title="">
                        On
                        <i class="fa fa-sort" aria-hidden="true"></i>
                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                     </span>
                  </div>
                  <div class="pln-col adj-col-3">
                     <span class="ps-h-one sort_order adjl" data-type="projects" data-by="pname" data-order="desc" data-original-title="" title="">
                        Project
                        <i class="fa fa-sort" aria-hidden="true"></i>
                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                     </span>
                     <span class="ps-h-two sort_order adjl" data-type="projects" data-by="wname" data-order="" data-original-title="" title="">
                        Workspace
                        <i class="fa fa-sort" aria-hidden="true"></i>
                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                     </span>
                     <span class="ps-h-two sort_order adjl" data-type="projects" data-by="ename" data-order="" data-original-title="" title="">
                        Task
                        <i class="fa fa-sort" aria-hidden="true"></i>
                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                     </span>
                  </div>
                  <div class="pln-col adj-col-4">
                     <span class="ps-h-one sort_order adjl" data-type="projects" data-by="remaining_hours" data-order="desc" data-original-title="" title="">
                        Remaining (Hrs)
                        <i class="fa fa-sort" aria-hidden="true"></i>
                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                     </span>
                  </div>
                  <div class="pln-col adj-col-5 plnactions">
                     Actions
                  </div>
               </div>
               <div class="unit-popup-cont adj-wrap">
                  <!--<div class="no-add-adu">No Adjustments</div>-->
                  <?php if(isset($adj_data) && !empty($adj_data)){ ?>
                  <?php foreach ($adj_data as $key => $value) {
                     $detail = $value;
                     $plan_eff = $detail['pe'];

                     $user_id = $plan_eff['user_id'];
                       $profile_pic = $detail['ud']['profile_pic'];
                       if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)){
                           $profilesPic = SITEURL . USER_PIC_PATH . $profile_pic;
                       } else {
                           $profilesPic = SITEURL . 'images/placeholders/user/user_1.png';
                       }

                       $profile_url = Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'admin' => FALSE ), true );
                  ?>
                  <div class="pln-data-row" data-id="<?php echo $plan_eff['id']; ?>">
                     <div class="pln-col p-col-1">
                        <div class="style-people-com">
                           <span class="style-popple-icon-out">
                              <a class="style-popple-icon ud" href="#" data-toggle="modal" data-remote="<?php echo $profile_url; ?>" data-target="#popup_modal">
                                 <img alt="User Profile Pic" src="<?php echo $profilesPic; ?>" data-content="<div class='wpop'><p><?php echo htmlspecialchars($detail[0]['username']); ?></p><p><?php echo htmlspecialchars($detail['ud']['job_title']); ?></p></div>" class="user-image ud" align="left" data-original-title="" title="">
                              </a>
                              <?php if($current_org['organization_id'] != $detail['ud']['organization_id']){ ?>
                                 <i class="communitygray18 tipText community-g ud" style="cursor: pointer;" title="" data-original-title="Not In Your Organization" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo $profile_url; ?>"></i>
                              <?php } ?>
                           </span>
                           <div class="style-people-info">
                              <span class="style-people-name ud" data-toggle="modal" data-remote="<?php echo $profile_url; ?>" data-target="#popup_modal"><?php echo ($detail[0]['username']); ?></span>
                              <span class="style-people-title"><?php echo ($detail['ud']['job_title']); ?></span>
                           </div>
                        </div>
                     </div>
                     <div class="pln-col p-col-2">
                        <div class="text-ellipsis lineheight17"><a class="ud"  data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $plan_eff['created_by'], 'admin' => FALSE ), true ); ?>" data-target="#popup_modal" href="#"><?php echo ($detail[0]['creator']); ?></a></div>
                        <div class="pln-date"><?php echo date('d M, Y', strtotime($plan_eff['created'])); ?></div>
                     </div>
                     <div class="pln-col p-col-3">
                        <a href="<?php echo Router::Url( array( "controller" => "projects", "action" => "index", $plan_eff['project_id'], 'admin' => FALSE ), true ); ?>"><div class="text-ellipsis proworktask"><?php echo htmlentities($detail['prj']['pname'], ENT_QUOTES, "UTF-8"); ?></div></a>
                        <a href="<?php echo Router::Url( array( "controller" => "projects", "action" => "manage_elements", $plan_eff['project_id'], $plan_eff['workspace_id'], 'admin' => FALSE ), true ); ?>"><div class="text-ellipsis proworktask"><?php echo htmlentities($detail['wsp']['wname'], ENT_QUOTES, "UTF-8"); ?></div></a>
                        <a href="<?php echo Router::Url( array( "controller" => "entities", "action" => "update_element", $plan_eff['element_id'], 'admin' => FALSE ), true ); ?>"><div class="text-ellipsis proworktask"><?php echo htmlentities($detail['task']['ename'], ENT_QUOTES, "UTF-8"); ?></div></a>
                     </div>
                     <div class="pln-col p-col-4">
                        <span class="tipText" title="<?php echo htmlspecialchars($plan_eff['comment']); ?>"><?php echo $plan_eff['remaining_hours']; ?></span>
                     </div>
                     <div class="pln-col p-col-5 plnactions">
                        <a class="tipText edit-pes" title="Edit" href="#"><i class="edit-icon"></i></a>
                        <a class="tipText delete-pes" title="Delete" href="#"><i class="deleteblack"></i></a>
                     </div>
                  </div>
                  <?php } ?>
               <?php }else{ ?>
                  <div class="no-add-adu">No Adjustments</div>
               <?php } ?>
               </div>
            </div>
         </div>
         <?php } ?>
         <div id="util_availability" class="tab-pane fade">
            <div class="unit-filt-adj-warp">
               <div class="planning-unit-header">
                  <div class="pln-col avb-col-1">
                     Effective From
                  </div>
                  <div class="pln-col avb-col-2">
                     Monday
                  </div>
                  <div class="pln-col avb-col-2">
                     Tuesday
                  </div>
                  <div class="pln-col avb-col-2">
                     Wednesday
                  </div>
                  <div class="pln-col avb-col-2">
                     Thursday
                  </div>
                  <div class="pln-col avb-col-2">
                     Friday
                  </div>
                  <div class="pln-col avb-col-2">
                     Saturday
                  </div>
                  <div class="pln-col avb-col-2">
                     Sunday
                  </div>
                  <div class="pln-col avb-col-2">
                     Total
                  </div>
               </div>
               <div class="unit-popup-cont avb-popup-cont">
                  <?php if(isset($avail_data) && !empty($avail_data)){ ?>
                  <?php foreach ($avail_data as $key => $value) {
                     $detail = $value['ua'];
                  ?>
                  <div class="pln-data-row">
                     <div class="pln-col avb-col-1">
                        <?php echo date('d M, Y', strtotime($detail['effective'])); ?>
                     </div>
                     <div class="pln-col avb-col-2">
                        <?php echo $detail['monday']; ?>
                     </div>
                     <div class="pln-col avb-col-2">
                        <?php echo $detail['tuesday']; ?>
                     </div>
                     <div class="pln-col avb-col-2">
                        <?php echo $detail['wednesday']; ?>
                     </div>
                     <div class="pln-col avb-col-2">
                        <?php echo $detail['thursday']; ?>
                     </div>
                     <div class="pln-col avb-col-2">
                        <?php echo $detail['friday']; ?>
                     </div>
                     <div class="pln-col avb-col-2">
                        <?php echo $detail['saturday']; ?>
                     </div>
                     <div class="pln-col avb-col-2">
                        <?php echo $detail['sunday']; ?>
                     </div>
                     <div class="pln-col avb-col-2">
                        <?php echo $detail['monday'] + $detail['tuesday'] + $detail['wednesday'] + $detail['thursday'] + $detail['friday'] + $detail['saturday'] + $detail['sunday']; ?>
                     </div>
                  </div>
                  <?php } ?>
                  <?php }else{ ?>
                     <div class="no-add-adu">No Availability</div>
                  <?php } ?>
               </div>
            </div>
         </div>
         <div id="util_workblock" class="tab-pane fade">
            <div class="unit-filt-adj-warp">
               <div class="planning-unit-header">
                  <div class="pln-col wb-col-1">
                     From
                  </div>
                  <div class="pln-col wb-col-1">
                     To Date
                  </div>
                  <div class="pln-col wb-col-2">
                     Comment
                  </div>
               </div>
               <div class="unit-popup-cont wb-popup-cont">
                  <?php if(isset($wb_data) && !empty($wb_data)){ ?>
                  <?php foreach ($wb_data as $key => $value) {
                     $detail = $value['ub'];
                  ?>
                  <div class="pln-data-row">
                     <div class="pln-col wb-col-1">
                        <?php echo date('d M, Y', strtotime($detail['work_start_date'])); ?>
                     </div>
                     <div class="pln-col wb-col-1">
                        <?php echo date('d M, Y', strtotime($detail['work_end_date'])); ?>
                     </div>
                     <div class="pln-col wb-col-2">
                        <?php echo htmlentities($detail['comments'], ENT_QUOTES, "UTF-8"); ?>
                     </div>
                  </div>
                  <?php } ?>
                  <?php }else{ ?>
                     <div class="no-add-adu">No work blocks</div>
                  <?php } ?>

               </div>
            </div>
         </div>
         <div id="util_absences" class="tab-pane fade">
            <div class="unit-filt-adj-warp">
               <div class="planning-unit-header">
                  <div class="pln-col abs-col-1">
                     From
                  </div>
                  <div class="pln-col abs-col-1">
                     To Date
                  </div>
                  <div class="pln-col abs-col-2">
                     Comment
                  </div>
               </div>
               <div class="unit-popup-cont wb-popup-cont">
                  <?php if(isset($abs_data) && !empty($abs_data)){ ?>
                  <?php foreach ($abs_data as $key => $value) {
                     $detail = $value['av'];
                  ?>
                  <div class="pln-data-row">
                     <div class="pln-col abs-col-1">
                        <?php echo date('d M, Y', strtotime($detail['avail_start_date'])); ?>
                     </div>
                     <div class="pln-col abs-col-1">
                        <?php echo date('d M, Y', strtotime($detail['avail_end_date'])); ?>
                     </div>
                     <div class="pln-col abs-col-2">
                        <?php echo htmlentities($detail['avail_reason'], ENT_QUOTES, "UTF-8"); ?>
                     </div>
                  </div>
                  <?php } ?>
                  <?php }else{ ?>
                     <div class="no-add-adu">NO ABSENCES</div>
                  <?php } ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal-footer">
   <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>
<script type="text/javascript">
   $(() => {
      var tab = '<?php echo $tab; ?>';
      $('a[href="#'+tab+'"]').tab('show');

      $('.ud').off('click').on('click', function(event) {
         event.preventDefault();
         $('#modal_util').modal('hide');
      });

      $('.edit-pes').off('click').on('click', function(event) {
         event.preventDefault();
         $('#modal_util').modal('hide');
         var id = $(this).parents('.pln-data-row:first').data('id');
         $('#modal_add_adj').modal({
            remote: $js_config.base_url + 'searches/add_adjustment/' + id
         })
         .modal('show');
      });

      $('.edit-works').off('click').on('click', function(event) {
         event.preventDefault();
         $('#modal_util').modal('hide');
         var user_id = $(this).data('user');
         var project_id = $(this).data('project');
         var workspace_id = $(this).data('workspace');
         var task_id = $(this).data('task');
         $('#modal_add_adj').modal({
            remote: $js_config.base_url + 'searches/add_adjustment/0/' + user_id + '/' + project_id + '/' + workspace_id + '/' + task_id
         })
         .modal('show');
      });

        $('.delete-pes').off('click').on('click', function(event) {
            event.preventDefault();
            var $that = $(this);
            var $parent = $(this).parents('.pln-data-row:first');
            var id = $parent.data('id');
            $.ajax({
                url: $js_config.base_url + 'searches/delete_plan_effort',
                type: 'POST',
                dataType: 'json',
                data: {id: id},
                success: function(response){
                    if(response.success){
                        $parent.slideUp(200, function(){
                            $(this).remove();
                            $('.tooltip').remove();
                            if($('.unit-filt-adj-cont .pln-data-row').length <= 0){
                              var data = {
                                  user_id: $('#cuser').val(),
                                  date: $('#cdate').val()
                              }
                                $.ajax({
                                    url: $js_config.base_url + 'searches/utill_adj_list',
                                    type: 'POST',
                                    data: data,
                                    success: function(response){
                                        $(".adj-wrap").html(response);
                                    }
                                });
                            }
                        })
                    }
                }
            });

        });

   })
</script>