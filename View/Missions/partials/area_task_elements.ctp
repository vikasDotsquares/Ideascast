<div class="ovrelay"></div>
<?php
$user_id = $this->Session->read('Auth.User.id');

$template_detail_id = $area_detail['Area']['template_detail_id'];
$template_detail = getByDbId('TemplateDetail', $template_detail_id);

$panel_width = 100;
if( $template_detail['TemplateDetail']['elements_counter'] > 0)
$panel_width = (100/$template_detail['TemplateDetail']['elements_counter']) - 2;

$elements_details_temp = null;

$user_project = $this->Common->userproject($project_id, $user_id);
$p_permission = $this->Common->project_permission_details($project_id, $user_id);

$project_level = 0;
// Get group id
$grp_id = $this->Group->GroupIDbyUserID($project_id, $user_id);
// Get Elements permissions
$e_permission = $this->Common->element_permission_details($workspace_id, $project_id, $user_id);

// Group permissions
if(isset($grp_id) && !empty($grp_id))
	$group_permission = $this->Group->group_permission_details($project_id, $grp_id);
// Project level according to the group permissions
if(isset($group_permission['ProjectPermission']['project_level']) && !empty($group_permission['ProjectPermission']['project_level'])){
	if($group_permission['ProjectPermission']['project_level'] == 1)
		$project_level = $group_permission['ProjectPermission']['project_level'];
}

if((isset($grp_id) && !empty($grp_id))) {

	if(isset($e_permission) && !empty($e_permission)){
		$e_permissions =  $this->Group->group_element_permission_details( $workspace_id, $project_id, $grp_id);
		$e_permission = array_merge($e_permission,$e_permissions);
	}else{
		$e_permission =  $this->Group->group_element_permission_details( $workspace_id, $project_id, $grp_id);
	}
}

$all_elements = null;
if((isset($e_permission) && !empty($e_permission)))
{
	$all_elements = $this->ViewModel->area_elements_permissions($area_id, false, $e_permission);
}


if(((isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level==1)   ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) )){
	$all_elements = $this->ViewModel->area_elements($area_id);
}
$elcount = 1;
if( isset( $all_elements ) && !empty( $all_elements ) ) {
	foreach( $all_elements as $element_index => $e_data ) {

		$element = $e_data['Element'];
		// pr($element);
		$element_id = $element['id'];
		$total_permit = true;
		$is_blocked = '';
		$is_allowd = '';

		$element_decisions = $element_feedbacks = [];
		if( isset($element['studio_status']) && empty($element['studio_status']) ) {
			$element_decisions = _element_decisions( $element['id'], 'decision' );
			$element_feedbacks = _element_decisions( $element['id'], 'feedback' );
			$element_statuses = _element_statuses( $element['id'] );
			$element_assets = element_assets( $element['id'], true );
			// pr($element_decisions);
			$arraySearch = arraySearch( $all_elements, 'id', $element['id'] );

			if( isset( $arraySearch ) && !empty( $arraySearch ) ) {
				$elements_details_temp[] = array_merge( $arraySearch[0], $element_assets, $element_decisions, $element_feedbacks, $element_statuses );
			}
		}

		$allowed_drag = $open = false;
		$menus = [];

		if(((isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level==1)   ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) )){
			// $menus = ['open', 'moveup', 'movedown', 'delete', 'cut', 'copy', 'color', 'copy_to', 'move_to'];
			$open = true;
			$eleCounts = (isset($all_elements) && !empty($all_elements)) ? count($all_elements)-1 : 0;
			if($element_index != 0 ) { $menus[] = 'moveup'; }
			if($element_index != ($eleCounts) ) { $menus[] = 'movedown'; }
			$menus[] = 'cut';
			$menus[] = 'copy';
			// $menus[] = 'color';
			$menus[] = 'delete';
			$allowed_drag = true;

			$menus[] = 'copy_to';
			$menus[] = 'move_to';
		}
		else if((isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] != 1)) {
			$ep = $this->Common->element_share_permission($element_id, $project_id, $user_id);
			if(isset($ep) && !empty($ep)) {
				$epe = $ep['ElementPermission'];
				$elecounts = (isset($all_elements) && !empty($all_elements)) ? count($all_elements)-1 : 0;
				if($epe['permit_read']) {
					$open = true;
				}
				if($element_index != 0 ) { $menus[] = 'moveup'; }
				if($element_index != ($elecounts) ) { $menus[] = 'movedown'; }
				if($epe['permit_move']) {
					$menus[] = 'cut';
					$allowed_drag = true;
					$menus[] = 'move_to';
				}
				if($epe['permit_copy']) {
					$menus[] = 'copy';
					$menus[] = 'copy_to';
				}
				if($epe['permit_edit']) {
					// $menus[] = 'color';
				}
				if($epe['permit_delete'] || $epe['is_editable'] == 1) {
					$menus[] = 'delete';
				}

			}
		}
		else if(isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] !=1 ){

			$ep = $this->Group->group_element_share_permission($element_id, $project_id, $grp_id);
			if(!isset($ep) || empty($ep)) {
				$ep = $this->Common->element_share_permission($element_id, $project_id, $user_id);
			}

			if(isset($ep) && !empty($ep)) {
				$elcounts = (isset($all_elements) && !empty($all_elements)) ? count($all_elements)-1 : 0;
				$epe = $ep['ElementPermission'];
				if($epe['permit_read']) {
					$open = true;
				}
				if($element_index != 0 ) { $menus[] = 'moveup'; }
				if($element_index != ($elcounts) ) { $menus[] = 'movedown'; }
				if($epe['permit_move']) {
					$menus[] = 'cut';
					$allowed_drag = true;
				}
				if($epe['permit_copy']) {
					$menus[] = 'copy';
				}
				if($epe['permit_edit']) {
					// $menus[] = 'color';
				}
				if($epe['permit_delete'] || $epe['is_editable'] == 1) {
					$menus[] = 'delete';
				}

			}
		}
?>

<div id="el_<?php echo $area_id ?>_<?php echo $element['id'] ?>" data-id="el_<?php echo $area_id ?>_<?php echo $element['id'] ?>" data-element="<?php echo $element['id']; ?>" data-editable="<?php echo $total_permit; ?>"  data-prev-area="0" data-current-area="<?php echo $area_id ?>" data-order="<?php echo $element['sort_order']; ?>" data-remote="<?php echo Router::Url( array( "controller" => "entities", "action" => "create_element", 'admin' => FALSE ), true ); ?>"  data-remove-remote="<?php echo Router::Url( array( "controller" => "entities", "action" => "remove_element", 'admin' => FALSE ), true ); ?>" class="el panel no-box-shadow <?php echo $element['color_code']; ?> <?php if($allowed_drag){ ?> allowed el-draggable<?php } ?>" style="width:<?php echo number_format($panel_width, 4) ?>%; <?php if($template_detail['TemplateDetail']['elements_counter'] > 1){ ?>margin-left: 4px; <?php } ?>  border: none !important;" >
	<div class="inner-el">
	<div class="panel-heading clearfix">

		<h3 style=" " class="panel-title ">
			<span class="element-title tipText"  data-original-title="Double Click to Collapse/Expand" style="text-transform: initial !important;"><?php echo htmlentities($element['title']); ?></span>
			<div class="pull-right">
				<?php $menu_list = implode(',', $menus); ?>
				<?php if(isset($menus) && !empty($menus)) { ?>
					<a class="btn btn-settings btn-xs element-menus"  href="#" data-options="<?php echo $menu_list; ?>" data-element="<?php echo $element_id; ?>" data-order="<?php echo (isset($element['sort_order']) && !empty($element['sort_order'])) ? $element['sort_order'] : $elcount++; ?>" data-area="<?php echo $area_id; ?>">
						<i class="far fa-clone"></i>
					</a>
				<?php } ?>
				<?php if($open) { ?>
					<a class="btn btn-xs btn-default btn-open tipText" title="Open Task" href="<?php echo Router::url(['controller' => 'entities', 'action' => 'update_element', $element['id'], 'admin' => FALSE], true); ?>">
						<i class="fas fa-folder-open"></i>
					</a>
				<?php } ?>
			</div>
		</h3>
	</div>

	<div class="panel-footer clearfix padding-top el_<?php echo element_status($element['id']); ?>" >


		<?php /*?>
		<div class="text-center el-icons summary_icons icon-grid-view">
		<?php
						
			$taskID = $element['id'];
			$taskCountAst = $this->ViewModel->getTaskCountAssetByTask($taskID);

			$total_assets = (isset($taskCountAst['0']['0']['total_assets']) && !empty($taskCountAst['0']['0']['total_assets'])) ? $taskCountAst['0']['0']['total_assets'] : 0;
			$dc_tot = (isset($taskCountAst['0']['0']['dc_tot']) && !empty($taskCountAst['0']['0']['dc_tot'])) ? $taskCountAst['0']['0']['dc_tot'] : 0;
			$fb_tot = (isset($taskCountAst['0']['0']['fb_tot']) && !empty($taskCountAst['0']['0']['fb_tot'])) ? $taskCountAst['0']['0']['fb_tot'] : 0;
			$vt_tot = (isset($taskCountAst['0']['0']['vt_tot']) && !empty($taskCountAst['0']['0']['vt_tot'])) ? $taskCountAst['0']['0']['vt_tot'] : 0;
			$links_tot = (isset($taskCountAst['0']['wwel']['links_tot']) && !empty($taskCountAst['0']['wwel']['links_tot'])) ? $taskCountAst['0']['wwel']['links_tot'] : 0;
			$notes_tot = (isset($taskCountAst['0']['wwen']['notes_tot']) && !empty($taskCountAst['0']['wwen']['notes_tot'])) ? $taskCountAst['0']['wwen']['notes_tot'] : 0;
			$docs_tot = (isset($taskCountAst['0']['wwed']['docs_tot']) && !empty($taskCountAst['0']['wwed']['docs_tot'])) ? $taskCountAst['0']['wwed']['docs_tot'] : 0;
			$mms_tot = (isset($taskCountAst['0']['wwem']['mms_tot']) && !empty($taskCountAst['0']['wwem']['mms_tot'])) ? $taskCountAst['0']['wwem']['mms_tot'] : 0;

			$prg_dec_tot = (isset($taskCountAst['0']['wdc']['dc_prg']) && !empty($taskCountAst['0']['wdc']['dc_prg'])) ? $taskCountAst['0']['wdc']['dc_prg'] : 0;
			$cmp_dec_tot = (isset($taskCountAst['0']['wdc']['dc_cmp']) && !empty($taskCountAst['0']['wdc']['dc_cmp'])) ? $taskCountAst['0']['wdc']['dc_cmp'] : 0;


			$nst_fb_tot = (isset($taskCountAst['0']['wfc']['fb_nst']) && !empty($taskCountAst['0']['wfc']['fb_nst'])) ? $taskCountAst['0']['wfc']['fb_nst'] : 0;
			$prg_fb_tot = (isset($taskCountAst['0']['wfc']['fb_prg']) && !empty($taskCountAst['0']['wfc']['fb_prg'])) ? $taskCountAst['0']['wfc']['fb_prg'] : 0;
			$ovd_fb_tot = (isset($taskCountAst['0']['wfc']['fb_ovd']) && !empty($taskCountAst['0']['wfc']['fb_ovd'])) ? $taskCountAst['0']['wfc']['fb_ovd'] : 0;
			$cmp_fb_tot = (isset($taskCountAst['0']['wfc']['fb_cmp']) && !empty($taskCountAst['0']['wfc']['fb_cmp'])) ? $taskCountAst['0']['wfc']['fb_cmp'] : 0;

			$nst_vot_tot = (isset($taskCountAst['0']['wvc']['vt_nst']) && !empty($taskCountAst['0']['wvc']['vt_nst'])) ? $taskCountAst['0']['wvc']['vt_nst'] : 0;
			$prg_vot_tot = (isset($taskCountAst['0']['wvc']['vt_prg']) && !empty($taskCountAst['0']['wvc']['vt_prg'])) ? $taskCountAst['0']['wvc']['vt_prg'] : 0;
			$ovd_vot_tot = (isset($taskCountAst['0']['wvc']['vt_ovd']) && !empty($taskCountAst['0']['wvc']['vt_ovd'])) ? $taskCountAst['0']['wvc']['vt_ovd'] : 0;
			$cmp_vot_tot = (isset($taskCountAst['0']['wvc']['vt_cmp']) && !empty($taskCountAst['0']['wvc']['vt_cmp'])) ? $taskCountAst['0']['wvc']['vt_cmp'] : 0;

			$deci_class = 'light-gray';
			$dec_total = 0;
			$dec_total_tip = 'Not Started';
			if($prg_dec_tot > 0){
				$deci_class = 'yellow';
				$dec_total = $prg_dec_tot;
				$dec_total_tip = 'In Progress';
				
			}else if($cmp_dec_tot > 0){
				$deci_class = 'green-bg';
				$dec_total = $cmp_dec_tot;
				$dec_total_tip = 'Completed';
			}
			 
			$vot_class = 'light-gray';
			$fb_class = 'light-gray';

			$vot_total = $fb_total = 0; 

			if($ovd_fb_tot > 0){
				$fb_class = 'red';
				$fb_total = $ovd_fb_tot;
			}else if($prg_fb_tot > 0){
				$fb_class = 'yellow';
				$fb_total = $prg_fb_tot;
			}else if($nst_fb_tot > 0){
				$fb_class = 'dark-gray';
				$fb_total = $nst_fb_tot;
			}else if($cmp_fb_tot > 0){
				$fb_class = 'green-bg';
				$fb_total = $cmp_fb_tot;
			}


			if($ovd_vot_tot > 0){
				$vot_class = 'red';
				$vot_total = $ovd_vot_tot; 
			}else if($prg_vot_tot > 0){
				$vot_class = 'yellow';
				$vot_total = $prg_vot_tot; 	
			}else if($nst_vot_tot > 0){
				$vot_class = 'dark-gray';
				$vot_total = $nst_vot_tot; 
			}else if($cmp_vot_tot > 0){
				$vot_class = 'green-bg';
				$vot_total = $cmp_vot_tot; 
			}


			//pr($taskCountAst);

			 ?>

				 <div class="progress-col task_assets">
 
					 <div class="progress-col-cont">
					  <ul class="progress-assets progress-col-cont">
						<li>
						<span class="assets-count blue tipText" title="Total Links"><?php echo $links_tot; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-LinkBlack tipText" title="Links"></i> </span>
						</li> 
						
						  <li>
						<span class="assets-count blue tipText" title="Total Notes"><?php echo $notes_tot; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-NoteBlack tipText" title="Notes"></i> </span>
						</li>
						<li>
						<span class="assets-count blue  tipText" title="Total Documents"><?php echo $docs_tot; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-DocumentBlack tipText" title="Documents"></i> </span>
						</li>
						  <li>
						<span class="assets-count blue tipText" title="Total Mind Maps"><?php echo $mms_tot; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-MindMapBlack tipText" title="Mind Maps"></i> </span>
						</li>
						  <li>
						<span class="assets-count <?php echo $deci_class; ?> cost-tooltip" title="<?php echo $dec_total_tip; ?> "><?php echo $dec_total; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-DecisionBlack tipText" title="Decisions"></i> </span>
						</li>
						 <li>
						<span class="assets-count <?php echo $fb_class; ?> cost-tooltip" title="<?php echo $cmp_fb_tot; ?> Completed <br /> <?php echo $ovd_fb_tot; ?> Overdue <br /> <?php echo $prg_fb_tot; ?> In Progress <br /> <?php echo $nst_fb_tot; ?> Not Started ""><?php echo $fb_total; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-FeedbackBlack tipText" title="Feedback"></i> </span>
						</li> 
						
						 <li>
						<span class="assets-count  <?php echo $vot_class; ?> cost-tooltip" title="<?php echo $cmp_vot_tot; ?> Completed <br /> <?php echo $ovd_vot_tot; ?> Overdue <br /> <?php echo $prg_vot_tot; ?> In Progress <br /> <?php echo $nst_vot_tot; ?> Not Started "><?php echo $vot_total; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-VoteBlack tipText" title="Votes"></i> </span>
						</li> 
						  
					  </ul>
					 </div>

				 </div>
			</div><?php */?>
		
		
		<div class="text-center el-icons summary_icons icon-grid-view">
			<ul class="list-unstyled element-icons-list">
				<li class="istatus">
					<span class="label bg-mix"><?php echo $element_statuses['status_short_term'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#tasks"  data-original-title="<?php echo $element_statuses['status_tiptext'] ?>" class="btn btn-xs bg-element tipText" data-original-title="<?php echo $element_statuses['status_tiptext'] ?>"><i class="asset-all-icon overduewhite"></i></span>
				</li>
				<li class="ico_links">
					<span class="label bg-mix "><?php echo $element_assets['links'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#links"  data-original-title=" Links" class="btn btn-xs bg-maroon tipText "><i class="asset-all-icon linkwhite"></i></span>
				</li>
				<li class="inote">
					<span class="label bg-mix"><?php echo $element_assets['notes'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#notes"  data-original-title=" Notes" class="btn btn-xs bg-purple tipText "><i class="asset-all-icon notewhite"></i></span>
				</li>

				<li class="idoc">
					<span class="label bg-mix"><?php echo $element_assets['docs'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#documents"  data-original-title=" Documents" class="btn btn-xs bg-blue tipText "><i class="asset-all-icon documentwhite"></i></span>
				</li>


			</ul>

			<ul class="list-unstyled element-icons-list">
				<li class="imup">
					<span class="label bg-mix"><?php echo $element_assets['mindmaps'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#mind_maps"  data-original-title="" class="btn btn-xs bg-green tipText " title=" Mind Maps"><i class="asset-all-icon mindmapwhite"></i></span>
				</li>
				<li class="idiss">
					<span class="label bg-mix"><?php echo $element_decisions['decision_short_term']; ?></span>

					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#decisions"  data-original-title="<?php echo $element_decisions['decision_tiptext'] ?>" class="btn btn-xs bg-orange tipText {is_permited}"><i class="asset-all-icon decisionwhite"></i></span>

				</li>

				<li class="ifeed">
					<span class="label bg-mix"><?php echo $element_assets['feedbacks'] ?></span>

					<span data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#feedbacks"  data-original-title=" Live Feedbacks" class="btn btn-xs bg-teal tipText {is_permited}" data-original-title=" Feedbacks"><i class="asset-all-icon feedbackwhite"></i></span>

				</li>

				<li class="ivote">
					<span class="label bg-mix"><?php echo $element_assets['votes'] ?></span>

					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#votes"  data-original-title=" Live Votes" class="btn btn-xs bg-yellow tipText {is_permited}" data-original-title=" Votes"><i class="asset-all-icon votewhite"></i></span>

				</li>
			</ul>

		</div>

		<div class="text-center el-icons summary_icons icon-list-view">
			<ul class="list-unstyled element-icons-list">
				<li class="istatus">
					<span class="label bg-mix"><?php echo $element_statuses['status_short_term'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#tasks"  data-original-title="<?php echo $element_statuses['status_tiptext'] ?>" class="btn btn-xs bg-element tipText" ><i class="fa fa-exclamation"></i></span>
				</li>

				<li class="ico_links">
					<span class="label bg-mix "><?php echo $element_assets['links'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#links"  data-original-title=" Links" class="btn btn-xs bg-maroon tipText "><i class="asset-all-icon linkwhite"></i></span>
				</li>
			</ul>
			<ul class="list-unstyled element-icons-list">

				<li class="inote">
					<span class="label bg-mix"><?php echo $element_assets['notes'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#notes"  data-original-title=" Notes" class="btn btn-xs bg-purple tipText"><i class="asset-all-icon notewhite"></i></span>
				</li>
				<li class="idoc">
					<span class="label bg-mix"><?php echo $element_assets['docs'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#documents"  data-original-title=" Documents" class="btn btn-xs bg-blue tipText"><i class="asset-all-icon documentwhite"></i></span>
				</li>


			</ul>

			<ul class="list-unstyled element-icons-list">
				<li class="imup">
					<span class="label bg-mix"><?php echo $element_assets['mindmaps'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#mind_maps"  data-original-title="" class="btn btn-xs bg-green tipText" title=" Mindmaps"><i class="asset-all-icon mindmapwhite"></i></span>
				</li>

				<li class="idiss">
					<span class="label bg-mix"><?php echo $element_decisions['decision_short_term'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#decisions"  data-original-title="<?php echo $element_decisions['decision_tiptext'] ?>" class="btn btn-xs bg-orange tipText" ><i class="asset-all-icon decisionwhite"></i></span>
				</li>
			</ul>
			<ul class="list-unstyled element-icons-list">
				<li class="ifeed">
					<span class="label bg-mix"><?php echo $element_assets['feedbacks']; ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#feedbacks" class="btn btn-xs bg-teal tipText" data-original-title="<?php echo $element_feedbacks['feedback_tiptext']; ?>"><i class="asset-all-icon feedbackwhite"></i></span>
				</li>

				<li class="ivote">
					<span class="label bg-mix"><?php echo $element_assets['votes'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#votes"  data-original-title=" Votes" class="btn btn-xs bg-yellow tipText" data-original-title=" Votes"><i class="asset-all-icon votewhite"></i></span>
				</li>
			</ul>

		</div>
		 
	</div>

	<div class="panel-body collapse fade no-padding" id="collapse_body_<?php echo $area_id ?>_<?php echo $element['id'] ?>">

		<div class="sub-heading clearfix" style="">

			<span>Task Description</span>

		</div>

		<div class="body-content" ><?php echo $element['description'] ?></div>

		<div class="sub-heading clearfix" style="">

			<span>Task Outcome</span>

		</div>

		<div class="body-content" ><?php echo $element['comments'] ?></div>

	</div>
	</div>

</div>
	<?php }

}

?>
<script type="text/javascript">
	$(function(){
		if(!$.isMobile)
			$('.element-title').addClass('tipText');
		else
			$('.element-title').removeClass('tipText');
	})
</script>