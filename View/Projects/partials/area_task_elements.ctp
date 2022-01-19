<div class="ovrelay"></div>
<?php
$user_id = $this->Session->read('Auth.User.id');

$template_detail_id = $area_detail['Area']['template_detail_id'];
$template_detail = getByDbId('TemplateDetail', $template_detail_id);

$panel_width = 100;
if( $template_detail['TemplateDetail']['elements_counter'] > 0)
$panel_width = (100/$template_detail['TemplateDetail']['elements_counter']) - 2;

$elements_details_temp = null;
$elcount = 1;
$result = array();

if( isset( $all_elements ) && !empty( $all_elements ) ) {
	$element_counter = 0;

	foreach( $all_elements as $element_index => $e_data ) {

		if($e_data['user_permissions']['a_id'] == $area_id){


		$element_decisions = $element_feedbacks = [];

		$e_data['Element']['id'] = $e_data['user_permissions']['e_id'];
		$e_data['Element']['permit_read'] = $e_data['user_permissions']['p_read'];
		$e_data['Element']['permit_delete'] = $e_data['user_permissions']['p_delete'];
		$e_data['Element']['permit_edit'] = $e_data['user_permissions']['p_edit'];
		$e_data['Element']['permit_add'] = $e_data['user_permissions']['p_add'];
		$e_data['Element']['permit_copy'] = $e_data['user_permissions']['p_copy'];
		$e_data['Element']['permit_move'] = $e_data['user_permissions']['p_move'];

		$e_data['Element']['title'] = $e_data['elements']['e_title'];
		$e_data['Element']['description'] = $e_data['elements']['e_description'];
		$e_data['Element']['comments'] = $e_data['elements']['e_outcome'];
		$e_data['Element']['sort_order'] = $e_data['elements']['e_sort_order'];
		$e_data['Element']['color_code'] = $e_data['elements']['e_color_code'];
		$element = $e_data['Element'];

		$wspsignoff = $e_data['workspaces']['wsp_sign_off'];

		$curr_status = $e_data[0]['e_status'];
		$element_statuses['status_short_term'] = $e_data[0]['e_status_short_term'];
		$element_statuses['status_tiptext'] = $e_data[0]['e_status'];

		$element_assets['links'] = $e_data[0]['e_links_count'];
		$element_assets['notes'] = $e_data[0]['e_notes_count'];
		$element_assets['docs'] = $e_data[0]['e_documents_count'];
		$element_assets['votes'] = ( isset($e_data[0]['e_votes_in_progress_count']) && !empty($e_data[0]['e_votes_in_progress_count']) )? $e_data[0]['e_votes_in_progress_count'] : 0;
		$element_assets['feedbacks'] = ( isset($e_data[0]['e_feedbacks_in_progress_count']) && !empty($e_data[0]['e_feedbacks_in_progress_count']) )? $e_data[0]['e_feedbacks_in_progress_count'] : 0;
		$element_assets['mindmaps'] = $e_data[0]['e_mind_maps_count'];

		///echo $e_data[0]['e_decision_status'];
		//echo "<br >";
		//$e_data[0]['e_decision_short_term'];
		//echo "<br >";
		$element_decisions['decision_tiptext'] = $e_data[0]['e_decision_status'];
		$element_decisions['decision_short_term'] = $e_data[0]['e_decision_short_term'];


		$wsp_sign_off = ( isset($wsp_sign_off) && empty($wsp_sign_off) ) ? $wsp_sign_off : $wspsignoff;


		$element_id = $element['id'];
		$total_permit = true;
		$is_blocked = '';
		$is_allowd = '';

		$allowed_drag = $open = false;
		$menus = [];

		if($element['permit_read']) {
			$open = true;
		}

			if( isset($wsp_sign_off) && empty($wsp_sign_off) ){
				if($element_counter != 0 ) { $menus[] = 'moveup';  }
			}

			$allEleCnt = ( isset($all_elements) && !empty($all_elements) ) ? count($all_elements)-1 : 0;

			if( isset($wsp_sign_off) && empty($wsp_sign_off) ){
				if($element_counter != ($allEleCnt) ) { $menus[] = 'movedown'; }
			}

			if( isset($wsp_sign_off) && empty($wsp_sign_off) ){

				if($element['permit_move']) {
					$menus[] = 'cut';
					$allowed_drag = true;
					$menus[] = 'move_to';
				}
				if($element['permit_copy']) {
					$menus[] = 'copy';
					$menus[] = 'copy_to';
				}
				if($element['permit_edit']) {
					// $menus[] = 'color';
				}
				if($element['permit_delete']) {
					$menus[] = 'delete';
				}

			} else {

				if($element['permit_copy']) {
					$menus[] = 'copy';
					$menus[] = 'copy_to';
				}

			}



		$element_counter++;


		//echo $element_statuses['status_short_term'];
		if( $element_statuses['status_short_term'] == 'CMP' ){
			$element_states = 'completed';
		} else if( $element_statuses['status_short_term'] == 'NOS' ){
			$element_states = 'not_spacified';
		} else if( $element_statuses['status_short_term'] == 'PND' ){
			$element_states = 'not_started';
		} else if( $element_statuses['status_short_term'] == 'PRG' ){
			$element_states = 'progress';
		} else if( $element_statuses['status_short_term'] == 'OVD' ){
			$element_states = 'overdue';
		} else {
			$element_states = 'not_spacified';
		}

		//el_progress el_overdue el_completed el_not_started


?>

<div id="el_<?php echo $area_id ?>_<?php echo $element['id'] ?>" data-id="el_<?php echo $area_id ?>_<?php echo $element['id'] ?>" data-element="<?php echo $element['id']; ?>" data-editable="<?php echo $total_permit; ?>"  data-prev-area="0" data-current-area="<?php echo $area_id ?>" data-order="<?php echo $element['sort_order']; ?>" data-remote="<?php echo Router::Url( array( "controller" => "entities", "action" => "create_element", 'admin' => FALSE ), true ); ?>"  data-remove-remote="<?php echo Router::Url( array( "controller" => "entities", "action" => "remove_element", 'admin' => FALSE ), true ); ?>" class="el panel no-box-shadow <?php echo $element['color_code']; ?> el-draggable" style="display: none; width:<?php echo number_format($panel_width, 4) ?>%; <?php if($template_detail['TemplateDetail']['elements_counter'] > 1){ ?>margin-left: 9px; <?php } ?> "  >
<div class="inner-el">
	<div class="panel-heading clearfix  " >

		<h3 style=" " class="panel-title ">
			<span class="element-title tipText" data-href="<?php echo Router::Url( array( "controller" => "entities", "action" => "update_element", $element['id'], 'admin' => FALSE ), true ); ?>#tasks"   style="text-transform: initial !important; "><?php echo htmlentities($element['title']); ?></span>
			<div class="pull-right btn-open-w">

					 <?php $menu_list = implode(',', $menus); ?>
					<?php if(isset($menus) && !empty($menus)) { ?>
						<a class="btn btn-xs btn-settings element-menus tipText" title="Options"  href="#" data-options="<?php echo $menu_list; ?>" data-element="<?php echo $element_id; ?>" data-order="<?php echo (isset($element['sort_order']) && !empty($element['sort_order'])) ? $element['sort_order'] : $elcount++; ?>" data-area="<?php echo $area_id; ?>">
							<i class="optionswhite18"></i>
						</a>
					<?php }  ?>
					<?php if($open) { ?>
						<a class="btn btn-xs btn-open tipText" title="Open Task" href="<?php echo Router::url(['controller' => 'entities', 'action' => 'update_element', $element['id'], 'admin' => FALSE], true); ?>">
							<i class="openwhite18"></i>
						</a>
					<?php } ?>

			</div>
		</h3>
	</div>

	<div class="panel-footer clearfix padding-top el_<?php echo $element_states; ?>" >

		<div class="text-center el-icons summary_icons icon-grid-view">

			<?php /* ?>

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
				<?php */ ?>


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
					<span class="label bg-mix"><?php echo $element_decisions['decision_short_term'] ?></span>

					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#decisions"  data-original-title="<?php echo $element_decisions['decision_tiptext']; ?>" class="btn btn-xs bg-orange tipText {is_permited}"><i class="asset-all-icon decisionwhite"></i></span>

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

	</div>

	<div class="panel-body collapse fade no-padding" id="collapse_body_<?php echo $area_id ?>_<?php echo $element['id'] ?>">

		<div class="sub-heading clearfix" style="">

			<span>Task Description</span>

		</div>

		<div class="body-content" ><?php  echo $element['description'] ?></div>

		<div class="sub-heading clearfix" style="">

			<span>Task Outcome</span>

		</div>

		<div class="body-content" ><?php  echo (issemp($element['comments'])) ? $element['comments'] : 'None'; ?></div>

	</div>


</div>
</div>
<?php	}
	}
}
else{
	e('<div class="no-summary-found">No Tasks</div>');
}

// }

?>



<script type="text/javascript">
	$(function(){
		if(!$.isMobile)
			$('.element-title').addClass('tipText');
		else
			$('.element-title').removeClass('tipText');


	})
</script>
<script>
$(function(){

$('.cost-tooltip').tooltip({
'placement': 'top',
'container': 'body',
'html': true
})

})
</script>