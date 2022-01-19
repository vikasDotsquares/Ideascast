<style>
    .panel-title .right-options .show-hide-program[aria-expanded="false"] i:before {
    content: "\f078";
}
.panel-title .right-options .show-hide-program[aria-expanded="true"] i:before {
    content: "\f077";
}
.right-options {
	float: right;
}
</style>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h3 class="modal-title">Risks in Workspace Area </h3>
</div>
<div class="modal-body risks-workspace-popup">
    <div class="panel-group panel-custom" id="accordion">
	<?php
		 //pr($intersectElements); die;
		//echo $project_id;
if( isset($intersectElements) && !empty($intersectElements) ){
	
	$rmdetail_id = $this->ViewModel->getRiskbyElementid($project_id,$intersectElements);
	
	if( isset($rmdetail_id) && !empty($rmdetail_id) ){
		
		foreach($rmdetail_id as $rmdetail_ids){
		
		$rmdetail_id['RmElement']['rm_detail_id'] = $rmdetail_ids; 
		
		if( isset($rmdetail_id['RmElement']['rm_detail_id']) && !empty($rmdetail_id['RmElement']['rm_detail_id']) ){
		
		$rm_details = $this->ViewModel->getRiskById($rmdetail_id['RmElement']['rm_detail_id']);
		if( isset($rm_details) && !empty($rm_details) ){				
		
		$riskexposer = risk_exposer($rm_details['RmDetail']['id']);
		
		$prob = 'N/A';
		$impact = 'N/A';
		$riskExposer = '';
		if( isset($riskexposer) && !empty($riskexposer) ){
			
			if( isset($riskexposer['RmExposeResponse']['percentage']) && !empty($riskexposer['RmExposeResponse']['percentage'])  ){
				
				$rskPercentage = $riskexposer['RmExposeResponse']['percentage'];
				//$list_percentages = [1 => 'Rare', 2 => 'Unlikely', 3 => 'Possible', 4 => 'Likely', 5 => 'Almost Certain'];
				if( isset($rskPercentage) && !empty($rskPercentage) ){
					if( $rskPercentage == 1 ){
						$prob = 'Rare';
					} else if( $rskPercentage == 2 ){
						$prob = 'Unlikely';
					} else if( $rskPercentage == 3 ){
						$prob = 'Possible';
					} else if( $rskPercentage == 4 ){
						$prob = 'Likely';
					} else if( $rskPercentage == 5 ){
						$prob = 'Almost Certain';
					} 
				}	
			}
			
			$rskImpact = $riskexposer['RmExposeResponse']['impact'];
			//$list_impacts = [1 => 'Negligible', 2 => 'Minor', 3 => 'Moderate', 4 => 'Major', 5 => 'Critical'];
			if( isset($rskImpact) && !empty($rskImpact) ){
				if( $rskImpact == 1 ){
					$impact = 'Negligible';
				} else if( $rskImpact == 2 ){
					$impact = 'Minor';
				} else if( $rskImpact == 3 ){
					$impact = 'Moderate';
				} else if( $rskImpact == 4 ){
					$impact = 'Major';
				} else if( $rskImpact == 5 ){
					$impact = 'Critical';
				}		
			}
			
			if( isset($rskPercentage) && !empty($rskPercentage) && isset($rskImpact) && !empty($rskImpact) ){
				$riskExposer = calculate_exposer($rskImpact, $rskPercentage) ;
			}
		}	
				
		?>	
			<div class="panel panel-default list-panel">
				<div class="panel-heading">
					<h4 class="panel-title">
					
						<a class="exclamation-icon" href="<?php echo SITEURL;?>risks/index/<?php echo $project_id; ?>"><i class="fa fa-exclamation" aria-hidden="true"></i></a> <span class="risks-ellipsis"><?php echo strip_tags($rm_details['RmDetail']['title']);?></span>
					
						<div class="right-options">
							<span class="show-hide-program" title="Expand" data-toggle="collapse" data-parent="#accordion" data-accordionid="collapse<?php echo $rmdetail_id['RmElement']['rm_detail_id'];?>" href="#collapse<?php echo $rmdetail_id['RmElement']['rm_detail_id'];?>" aria-expanded="false" >
								<i class="fa fa-chevron-down"></i>
							</span>
						</div>
				</h4>
				</div>
				<div id="collapse<?php echo $rmdetail_id['RmElement']['rm_detail_id'];?>" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="work-project-info-tab">
							<div class="row work-project-info-mainrow">
								<div class="work-project-info-col col-md-12">                                
									<div class="row">
										<div class="col-md-3 nopadding-right">
											<div class="risk-assigned-user"><span>Assignees:</span>
												<?php
													$riskUsers = $this->ViewModel->risk_users($rm_details['RmDetail']['id']);
													$assigneesUser = '';
													if( isset($riskUsers) && !empty($riskUsers) ){
														foreach($riskUsers as $ulist){
															$ulistfullname = $this->Common->userFullname($ulist);
															$assigneesUser .="<div>".$ulistfullname."</div>";
														}
													}
												?>
													
												<?php if( isset($assigneesUser) && !empty($assigneesUser) ){ ?>
												<a class="btn btn-default btn-xs pophover-popup" data-content="<div class='risk_el_users'><?php echo $assigneesUser; ?></div>" style="cursor: default;" data-original-title="" title=""><i class="fa fa-user-plus"></i></a>
												<?php } else { ?>
												<a class="btn btn-default btn-xs tipText" title="<?php echo ( empty($assigneesUser))? 'None':''; ?>">
													<i class="fa fa-user-plus"></i>
												</a>
												<?php } ?>
											</div>
										</div>	
										<div class="col-md-9">
											<div class="risk-created"><span>Created:</span> <?php echo $this->Common->userFullname($rm_details['RmDetail']['user_id']);?>, <?php echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($rm_details['RmDetail']['created'])),$format = 'd M, Y H:iA') ;?></div>
										</div>	
									</div>
								   
									<div class="row ">
										<div class="col-md-12 risk-type"><span>Risk Type:</span> <?php echo $this->ViewModel->getProjectRiskTypeName($rm_details['RmDetail']['rm_project_risk_type_id']); ?></div>
									</div>
									
									<div class="row">
										
										<div class="col-md-6">
											<div class="risk-info"><span>Risk Info:</span>
												<ul>
													<li><span>Status:</span> <?php
														if($rm_details['RmDetail']['status'] == 1){
															echo '<span class="" >Open</span>';
														} else if($rm_details['RmDetail']['status'] == 2){
															echo '<span class="" >Review</span>';
														} else if($rm_details['RmDetail']['status'] == 3){
															echo '<span style="color:#92d050" >Signed-off</span>';
														} else if($rm_details['RmDetail']['status'] == 4){
															echo '<span style="color:#c00000" >Overdue</span>';
														} else {
															echo "N/A";
														}
													?></li>
													<li><span>Impact:</span> <?php echo $impact;?></li>
													<li><span>Prob %:</span> <?php echo $prob;?></li>
													<li><span>When:</span> <?php 
														echo (isset($rm_details['RmDetail']['possible_occurrence']) && !empty($rm_details['RmDetail']['possible_occurrence'])) ? date( 'd M, Y',strtotime($rm_details['RmDetail']['possible_occurrence'])) : 'N/A'; 
													?></li>
													<?php 
													$exposerclass = '';
													if( isset($riskExposer['class']) && $riskExposer['class'] == 'low'){
														$exposerclass = 'style="color:#92d050"';
													} else if( isset($riskExposer['class']) && $riskExposer['class'] == 'mid'){
														$exposerclass = 'style="color:#ffc000"';
													} else if( isset($riskExposer['class']) && $riskExposer['class'] == 'high'){
														$exposerclass = 'style="color:#c00000"';
													} else if( isset($riskExposer['class']) && $riskExposer['class'] == 'severe'){
														$exposerclass = 'style="color:#ff0000"';
													}
													?>
													<li><span>Exposure:</span> <?php
													echo ( (isset($riskExposer['text']) && !empty($riskExposer['text']))? "<span ".$exposerclass.">".$riskExposer['text']."</span>" : 'N/A' );?></li>
												</ul>	
											</div>
										</div>
										
										<div class="col-md-6">
											<div class="risk-elements"><span>Related Elements:</span>
												<ul>
												<?php 
												
													$riskElements = $this->ViewModel->wsp_permission_risk_area_element($project_id,$workspace_id,$area_id,$rm_details['RmDetail']['id']);
													
													if( isset($riskElements) && !empty($riskElements) ){ 
														foreach($riskElements as $listElements){
														$eledetail = $this->ViewModel->getElementDetail($listElements);
														?>
															<li><a href="<?php echo SITEURL;?>entities/update_element/<?php echo $eledetail['Element']['id'];?>#tasks"><?php echo $eledetail['Element']['title']; ?></a></li>
													<?php 
														}
													}	
													?>		
												</ul> 
											</div>
										</div>								
									</div>								
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php 	}
			}
		}
	}	
}	?>    
</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>

<script>
$(function(){
	 $('.pophover-popup').popover({
		placement : 'bottom',
		trigger : 'hover',
		html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });

	$('.show-hide-program').tooltip({
		container: 'body',
		placement: 'top',
		trigger: 'hover'
	})

	$("#accordion .panel-collapse").on("hidden.bs.collapse", function() {
		var $panel = $(this).parents('.panel:first');
		$('.tooltip').hide();
		$(".show-hide-program", $panel).attr('data-original-title', 'Expand')
			.tooltip('fixTitle');
		$(".show-hide-program", $panel).tooltip('show');
	
	});
	
	$("#accordion .panel-collapse").on("shown.bs.collapse", function() {
		var $panel = $(this).parents('.panel:first');
		$('.tooltip').hide();
		$(".show-hide-program", $panel).attr('data-original-title', 'Collapse')
			.tooltip('fixTitle');
		$(".show-hide-program", $panel).tooltip('show');
	
	});
	
	
})
</script>