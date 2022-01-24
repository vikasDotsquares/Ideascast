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

/***************** TASK FILTER ****************************/
	$filter_status = $element_list = [];
	if(isset($fstatus) && !empty($fstatus)){
		$filter_status = $fstatus;
		if( isset( $all_elements ) && !empty( $all_elements ) ) {
			foreach( $all_elements as $element_index => $e_data ) {
				$element = $e_data['Element'];
				$curr_status = element_status($element['id']);
				if(in_array($curr_status, $filter_status)){
					$element_list[] = $e_data;
				}
			}
			$all_elements = $element_list;
		}
	}

/***************** TASK FILTER ****************************/

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
			// $menus = ['open', 'moveup', 'movedown', 'delete', 'cut', 'copy', 'color'];
			$open = true;
			if($element_index != 0 ) { $menus[] = 'moveup'; }
			$eleCnt = ( isset($all_elements) && !empty($all_elements) ) ? count($all_elements)-1 : 0;
			if($element_index != ($eleCnt) ) { $menus[] = 'movedown'; }
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
				if($epe['permit_read']) {
					$open = true;
				}
				if($element_index != 0 ) { $menus[] = 'moveup'; }
				$allEleCnt = ( isset($all_elements) && !empty($all_elements) ) ? count($all_elements)-1 : 0;
				if($element_index != ($allEleCnt) ) { $menus[] = 'movedown'; }
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
				$epe = $ep['ElementPermission'];
				if($epe['permit_read']) {
					$open = true;
				}
				if($element_index != 0 ) { $menus[] = 'moveup'; }
				$allEleCnts = ( isset($all_elements) && !empty($all_elements) ) ? count($all_elements)-1 : 0;
				if($element_index != ($allEleCnts) ) { $menus[] = 'movedown'; }
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
/*
$filter_status = [];
if(isset($fstatus) && !empty($fstatus)){
	$filter_status = $fstatus;
}
$curr_status = element_status($element['id']);
$show_el = false;
if(in_array($curr_status, $filter_status)){
	$show_el = true;
}*/
// if($show_el){
?>

<div id="el_<?php echo $area_id ?>_<?php echo $element['id'] ?>" data-id="el_<?php echo $area_id ?>_<?php echo $element['id'] ?>" data-element="<?php echo $element['id']; ?>" data-editable="<?php echo $total_permit; ?>"  data-prev-area="0" data-current-area="<?php echo $area_id ?>" data-order="<?php echo $element['sort_order']; ?>" data-remote="<?php echo Router::Url( array( "controller" => "entities", "action" => "create_element", 'admin' => FALSE ), true ); ?>"  data-remove-remote="<?php echo Router::Url( array( "controller" => "entities", "action" => "remove_element", 'admin' => FALSE ), true ); ?>" class="el panel no-box-shadow <?php echo $element['color_code']; ?> <?php if($allowed_drag){ ?> allowed el-draggable<?php } ?>" style="display: none; width:<?php echo number_format($panel_width, 4) ?>%; <?php if($template_detail['TemplateDetail']['elements_counter'] > 1){ ?>margin-left: 4px; <?php } ?>  border: none !important;" data-status="<?php echo element_status($element['id']); ?>" >
<div class="inner-el">
	<div class="panel-heading clearfix  " >

		<h3 style=" " class="panel-title ">
			<span class="element-title tipText"  data-original-title="Double Click to Collapse/Expand" style="text-transform: initial !important;"><?php echo html_entity_decode($element['title']); ?></span>
			<div class="pull-right">
				<?php $menu_list = implode(',', $menus); ?>
				<?php if(isset($menus) && !empty($menus)) { ?>
					<a class="btn btn-settings btn-xs element-menus tipText" title="Options"  href="#" data-options="<?php echo $menu_list; ?>" data-element="<?php echo $element_id; ?>" data-order="<?php echo (isset($element['sort_order']) && !empty($element['sort_order'])) ? $element['sort_order'] : $elcount++; ?>" data-area="<?php echo $area_id; ?>">
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

		<div class="text-center el-icons summary_icons icon-grid-view">
			<ul class="list-unstyled element-icons-list">
				<li class="istatus">
					<span class="label bg-mix"><?php echo $element_statuses['status_short_term'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#tasks"  data-original-title="<?php echo $element_statuses['status_tiptext'] ?>" class="btn btn-xs bg-element tipText" data-original-title="<?php echo $element_statuses['status_tiptext'] ?>"><i class="fa fa-exclamation"></i></span>
				</li>
				<li class="ico_links">
					<span class="label bg-mix "><?php echo $element_assets['links'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#links"  data-original-title=" Links" class="btn btn-xs bg-maroon tipText "><i class="fa fa-link"></i></span>
				</li>
				<li class="inote">
					<span class="label bg-mix"><?php echo $element_assets['notes'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#notes"  data-original-title=" Notes" class="btn btn-xs bg-purple tipText "><i class="fa fa-file-text-o"></i></span>
				</li>

				<li class="idoc">
					<span class="label bg-mix"><?php echo $element_assets['docs'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#documents"  data-original-title=" Documents" class="btn btn-xs bg-blue tipText "><i class="fa fa-folder-o"></i></span>
				</li>


			</ul>

			<ul class="list-unstyled element-icons-list">
				<li class="imup">
					<span class="label bg-mix"><?php echo $element_assets['mindmaps'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#mind_maps"  data-original-title="" class="btn btn-xs bg-green tipText " title=" Mind Maps"><i class="fa fa-sitemap"></i></span>
				</li>
				<li class="idiss">
					<span class="label bg-mix"><?php echo $element_decisions['decision_short_term'] ?></span>

					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#decisions"  data-original-title="<?php echo $element_decisions['decision_tiptext'] ?>" class="btn btn-xs bg-orange tipText {is_permited}"><i class="far fa-arrow-alt-circle-right"></i></span>

				</li>

				<li class="ifeed">
					<span class="label bg-mix"><?php echo $element_assets['feedbacks'] ?></span>

					<span data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#feedbacks"  data-original-title=" Live Feedbacks" class="btn btn-xs bg-teal tipText {is_permited}" data-original-title=" Feedbacks"><i class="fa fa-bullhorn"></i></span>

				</li>

				<li class="ivote">
					<span class="label bg-mix"><?php echo $element_assets['votes'] ?></span>

					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#votes"  data-original-title=" Live Votes" class="btn btn-xs bg-yellow tipText {is_permited}" data-original-title=" Votes"><i class="fa fa-inbox"></i></span>

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
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#links"  data-original-title=" Links" class="btn btn-xs bg-maroon tipText "><i class="fa fa-link"></i></span>
				</li>
			</ul>
			<ul class="list-unstyled element-icons-list">

				<li class="inote">
					<span class="label bg-mix"><?php echo $element_assets['notes'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#notes"  data-original-title=" Notes" class="btn btn-xs bg-purple tipText"><i class="fa fa-file-text-o"></i></span>
				</li>
				<li class="idoc">
					<span class="label bg-mix"><?php echo $element_assets['docs'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#documents"  data-original-title=" Documents" class="btn btn-xs bg-blue tipText"><i class="fa fa-folder-o"></i></span>
				</li>


			</ul>

			<ul class="list-unstyled element-icons-list">
				<li class="imup">
					<span class="label bg-mix"><?php echo $element_assets['mindmaps'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#mind_maps"  data-original-title="" class="btn btn-xs bg-green tipText" title=" Mindmaps"><i class="fa fa-sitemap"></i></span>
				</li>

				<li class="idiss">
					<span class="label bg-mix"><?php echo $element_decisions['decision_short_term'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#decisions"  data-original-title="<?php echo $element_decisions['decision_tiptext'] ?>" class="btn btn-xs bg-orange tipText" ><i class="far fa-arrow-alt-circle-right"></i></span>
				</li>
			</ul>
			<ul class="list-unstyled element-icons-list">
				<li class="ifeed">
					<span class="label bg-mix"><?php echo $element_assets['feedbacks']; ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#feedbacks" class="btn btn-xs bg-teal tipText" data-original-title="<?php echo $element_feedbacks['feedback_tiptext']; ?>"><i class="fa fa-bullhorn"></i></span>
				</li>

				<li class="ivote">
					<span class="label bg-mix"><?php echo $element_assets['votes'] ?></span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element['id']; ?>#votes"  data-original-title=" Votes" class="btn btn-xs bg-yellow tipText" data-original-title=" Votes"><i class="fa fa-inbox"></i></span>
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