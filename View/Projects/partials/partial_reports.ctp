 
<?php 
	if( isset($project_detail) && !empty($project_detail) ) {
		$owner_user = $project_detail['User']['UserDetail']['first_name'] . ' ' .$project_detail['User']['UserDetail']['last_name'];
	}
if( isset($project_workspaces) && !empty($project_workspaces) ) {
// pr($project_workspaces, 1);
		$row_counter = 0;
		?>
	<div class="ajax-pagination clearfix" style="border-bottom: 1px solid #67a028;">
		<?php  echo $this->element('jeera_paging', [ 'project_workspaces' => $project_workspaces, 'project_detail' => $project_detail ]);  ?>
	</div>
	<div class="row  padding-top">
		<?php 
		// pr($project_workspaces, 1);
		$graph_data = null;
		foreach( $project_workspaces as $key => $val ) {
			
			$project_workspace_data = $val['ProjectWorkspace'];
			if( workspace_exists($project_workspace_data['workspace_id'])) {
				 
				$project_data = $val['Project'];
				$workspace_data = $val['Workspace'];
			  
				
				// get areas
				$element_detail = null;
				$sum_value = 0;
				// echo $workspace_data['id'];
				$area_id = $this->ViewModel->workspace_areas($workspace_data['id'], false, true);
				// $area_id = $this->ViewModel->workspace_areas(116, false, true);
				$el = $this->ViewModel->area_elements($area_id);
				if(!empty($el)) {
					$element_detail = _element_detail(null, $el); 
					
					if(!empty($element_detail)) {
						// $counts = $this->ViewModel->ws_el_detail_count($element_detail, $workspace_data['id'], true);
						$filter = arraySearch($element_detail, 'date_constraint_flag' );
						// pr($filter);
						if( !empty($filter) ) {
							// $data[$workspace_data['id']] = $filter[0];
							
							$sum_value = array_sum(array_columns( $element_detail, 'date_constraint_flag'));
							if( !empty($sum_value) && $workspace_data['id'] == 96) {
								// echo $workspace_data['id'].' = '.$sum_value.'<br />';
								// pr($element_detail);
							}
							
						}
					} 
				}
				
				
				
				
				// Get Color Code of the panel
				$color_code = 'bg-gray';
				if( isset($project_workspaces) && !empty($project_workspaces) ) {
					$color_code = $workspace_data['color_code'];
				}
				$colorBG = explode('-', $color_code);
				$color_code = ( isset($colorBG[1]) && !empty($colorBG[1]) ) ? 'panel-'.$colorBG[1] : 'panel-gray';
				
				// Get total areas in workspace
				$ws_area_total = $this->ViewModel->workspace_areas($workspace_data['id'], true);
				
				$total_elements = $total_links = $total_notes = $total_docs = $total_overdue_status = $total_mindmaps = $total_votes =  $total_decisions = $total_feedbacks = 0;
				$total_overdue_status = $sum_value;
				if( $ws_area_total > 0 ) {
					// Get all assets in workspace
					$count_data = $this->ViewModel->countAreaElements($workspace_data['id']);
					// pr($count_data,1);
					if( isset($count_data) && !empty($count_data) ) {
							
						$total_elements = ( isset($count_data['active_element_count']) && !empty($count_data['active_element_count'])) ? $count_data['active_element_count'] : 0;
							
						$total_links = ( isset($count_data['assets_count']) && !empty($count_data['assets_count'])) ? ( ( isset($count_data['assets_count']['links']) && !empty($count_data['assets_count']['links'])) ? $count_data['assets_count']['links'] : 0 ) : 0;
						
						$total_notes = ( isset($count_data['assets_count']) && !empty($count_data['assets_count'])) ? ( ( isset($count_data['assets_count']['notes']) && !empty($count_data['assets_count']['notes'])) ? $count_data['assets_count']['notes'] : 0 ) : 0;
						
						$total_docs = ( isset($count_data['assets_count']) && !empty($count_data['assets_count'])) ? ( ( isset($count_data['assets_count']['docs']) && !empty($count_data['assets_count']['docs'])) ? $count_data['assets_count']['docs'] : 0 ) : 0;
						
						$total_mindmaps = ( isset($count_data['assets_count']) && !empty($count_data['assets_count'])) ? ( ( isset($count_data['assets_count']['mindmaps']) && !empty($count_data['assets_count']['mindmaps'])) ? $count_data['assets_count']['mindmaps'] : 0 ) : 0;
						
						// $total_overdue_status = ( isset($count_data['assets_count']) && !empty($count_data['assets_count'])) ? ( ( isset($count_data['assets_count']['due_status']) && !empty($count_data['assets_count']['due_status'])) ? $count_data['assets_count']['due_status'] : 0 ) : 0;
						
						$total_votes = ( isset($count_data['assets_count']) && !empty($count_data['assets_count'])) ? ( ( isset($count_data['assets_count']['votes']) && !empty($count_data['assets_count']['votes'])) ? $count_data['assets_count']['votes'] : 0 ) : 0;
						
						$graph_data[] = [
								'ybar' => _substr_text( $workspace_data['title'], 5, false),
								'yline' => ($total_links + $total_notes + $total_docs),
								'links' => $total_links,
								'notes' => $total_notes,
								'documents' => $total_docs,
								'due_status' => $total_overdue_status,
								// 'votes' => $total_votes,
								// 'mindmaps' => $total_mindmaps,
							]; 
					} 
				}
			?>
				
				
			<div class="col-md-4 fix-height">
															<div class="panel clearfix <?php echo $color_code; ?>">

																<div class="panel-heading clearfix">
																	<h4 class="panel-title pull-left" style="width: 80%">
																		<span class="ws-heading"> <!-- <i class="fa fa-tasks"></i>
							   <p class="ui-ellipsis"><?php echo $workspace_data['title']; ?></p>
																			--> 		
															<?php echo $workspace_data['title']; ?>							  
																			<?php //echo _substr_text( $workspace_data['title'], 30, false ); ?></span>
																	</h4>
																	<div class="btn-group pull-right">

																		<a class="btn btn-default btn-xs tipText" title="Open Workspace" href="<?php echo Router::url( array( 'controller' => 'projects', 'action' => 'manage_elements', $project_data['id'], $workspace_data['id'] ) ); ?>">
																			<i class="fa fa-folder-open"></i> 
																		</a> 
																	</div>
																</div>
																<div class="panel-footer padding noborder-radius clearfix">	

																	<div class="prjct-rprt-icons"> 
																		<ul class="list-unstyled text-center">
																			<li class="iele">
																				<span title="" class="label bg-mix "><?php echo $total_elements; ?></span>
																				<span href="#" data-original-title="Elements " class="icon_element_white btn btn-xs bg-dark-gray tipText"> </span>
																			</li>
																			<li class="ico_links">
																				<span class="label bg-mix"><?php echo $total_links; ?></span> 
																				<span href="#" title="" class="btn btn-xs bg-mix tipText bg-maroon" data-original-title="Links "><i class="fa fa-link"></i></span>
																			</li>
																			<li class="inote">
																				<span class="label bg-mix"><?php echo $total_notes; ?></span> 
																				<span href="#" title="" class="btn btn-xs bg-mix tipText bg-purple" data-original-title="Notes "><i class="fa fa-file-text-o"></i></span>
																			</li>
																			<li class="idoc">
																				<span class="label bg-mix"><?php echo $total_docs; ?></span>
																				<span href="#" title="Documents " class="btn bg-blue btn-xs bg-mix tipText"><i class="fa fa-folder-o"></i></span>
																			</li>
																			<li class="odue">
																				<span class="label bg-mix"><?php echo $total_overdue_status; ?></span>
																				<span href="#" title="" class="btn btn-xs bg-mix bg-navy tipText" data-original-title="Overdue Statuses "><i class="fa fa-exclamation"></i></span>
																			</li>
																			<li class="green">
																				<span class="label bg-mix"><?php echo $total_mindmaps; ?></span>
																				<span href="#" title="" class="btn btn-xs bg-mix bg-green tipText" data-original-title="Mind Maps "><i class="fa fa-sitemap"></i></span>
																			</li>
																			<li class="orang">
																				<span class="label bg-mix"><?php echo $total_decisions; ?></span>
																				<span href="#" title="" class="btn btn-xs bg-mix bg-orange  tipText decisions" data-original-title="Decisions "><i class="far fa-arrow-alt-circle-right"></i></span>
																			</li>
																			<li class="l-blue">
																				<span class="label bg-mix"><?php
																					echo $total_feedbacks;
																					//$total_incomplete_feedback; 
																					?></span>
																				<span href="#" title="" class="btn btn-xs bg-mix bg-aqua tipText" data-original-title="Feedback "><i class="fa fa-bullhorn"></i></span>
																			</li>

																			<li class="l-orang">
																				<span class="label bg-mix"><?php echo $total_votes; ?></span>
																				<span href="#" title="Votes " class="btn btn-xs bg-mix tipText bg-orange-active"><i class="fa fa-inbox"></i></span>
																			</li>
																		</ul>  
																	</div>
																</div>
																<div class="panel-body report-ws-desc"> 
																	<!-- workspace-template detail -->
																	<div class="row">
																		<div class="col-md-7 nopadding-right">
																			<p class="text-muted timing"> 
																				<span>Created: <?php 
																				//echo date( 'd M Y h:i:s', strtotime( $workspace_data['created'] ) ); 
																				echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($workspace_data['created'])),$format = 'd M Y h:i:s'); 
																				?></span>
																				<span>Updated: <?php 
																				//echo date( 'd M Y h:i:s', strtotime( $workspace_data['modified'] ) ); 
																				echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($workspace_data['modified'])),$format = 'd M Y h:i:s');
																				?></span>
																				<span>Created By: <?php echo $owner_user; ?></span>
																				<span>Updated By: <?php echo $owner_user; ?></span>
																				
																				<span>Start Date: <?php
																				//echo ( isset($workspace_data['start_date']) && !empty($workspace_data['start_date'])) ? date('d M Y', strtotime($workspace_data['start_date'])) : 'N/A';  
																				echo ( isset($workspace_data['start_date']) && !empty($workspace_data['start_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($workspace_data['start_date'])),$format = 'd M Y h:i:s') : 'N/A';  
																				
																				?></span>
																				<span>End Date: <?php 
																				//echo ( isset($workspace_data['end_date']) && !empty($workspace_data['end_date'])) ? date('d M Y', strtotime($workspace_data['end_date'])) : 'N/A';  
																				echo ( isset($workspace_data['end_date']) && !empty($workspace_data['end_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($workspace_data['end_date'])),$format = 'd M Y h:i:s') : 'N/A';  
																				
																				?></span>
																				
																			</p>

																		</div>
																		<div class="col-md-5 nopadding-left">
																			<?php echo workspace_template( $workspace_data['template_id'], true ); // template image  ?>
																		</div>                              
																	</div>

																	<!-- workspace-description -->
																	<div class="row ws-desc-row">
																		<div class="sub-heading clearfix noborder-radius nopadding margin-top " style="position: relative"> 
																			<h6 class="panel-title pull-left opacity_content" style="font-size: 12px; margin: 3px;"> 
																				<span>Workspace Description</span>
																			</h6> 
																		</div>

																		<div class="col-md-12 ws-desc mar_s">
																			<?php echo nl2br( $workspace_data['description'] ); ?>
																		</div>
																	</div>

																	<!-- workspace-areas -->
																	<div class="row">
																		<div class="sub-heading clearfix noborder-radius nopadding margin-top " style="position: relative"> 
																			<h6 class="panel-title pull-left opacity_content" style="font-size: 12px; margin: 3px;"> 
																				<span>Area information</span>
																			</h6> 
																		</div>

																		<div class="col-md-12">
																			<p class="area_detail"> 
																				<?php
																				if( $ws_area_total > 0 ) {
																					// Get all assets in workspace
																					$workspace_areas = $this->ViewModel->workspace_area_data( $workspace_data['id'] );
																					if( isset( $workspace_areas ) && !empty( $workspace_areas ) ) {
																						foreach( $workspace_areas as $val ) {
																							$area = $val['Area'];
																							?>	
																							<span class="title">
																								<?php echo $area['title']; ?>
																							</span>
																							<span class="tooltip_text">
																								<?php echo $area['tooltip_text']; ?>
																							</span>
																							<?php
																						}
																					}
																				}
																				?>
																			</p> 
																		</div>
																	</div>

																</div>
															</div>
														</div><!-- /.col-md-4 -->
			
		<?php 
				if( $row_counter == 2 ) {
					echo '</div><div class="row">';
					$row_counter = 0;
				}
				else {
					$row_counter++;
				}
			}
		} // END FOREACH
		
	?>
	</div><!-- /.row -->
	
	<!-- -->
		<div class="ajax-pagination clearfix">
			<?php  echo $this->element('jeera_paging', [ 'project_workspaces' => $project_workspaces, 'project_detail' => $project_detail ]);  ?>
		</div>
	
	<?php } // END CHECK PROJECT-WORKSPACES ?>
	
	
<script type="text/javascript" >
$(function(){
	$('.ws-heading').find('br').remove()
	$(".fix-height .panel .panel-body").slimscroll({
		height: "400px",
		alwaysVisible: false,
		color: '#67a028',
		size: "6px",
		borderRadius: "4px"
	}).css("width", "100%");
	
	$(".panel-body.report-ws-desc .ws-desc").slimscroll({
		height: "120px",
		alwaysVisible: false,
		color: '#67a028',
		size: "6px",
		borderRadius: "4px"
	}).css("width", "100%");
	//$.trim_title( $('.panel-heading'), '.panel-title', '.ws-heading', 9)
})
</script>
<style>
.mar_s{ fornt-size:16px;margin:5px 0 0 0 !important}
</style>

