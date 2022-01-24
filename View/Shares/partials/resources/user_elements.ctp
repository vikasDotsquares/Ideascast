<?php 
  $projectname = getByDbId('Project',$project_id, array('title') );  
  if( isset($data) && !empty($data) ) {
	?>	
	<ul class="list-group" id="user_elements_list" data-pid="<?php echo $project_id; ?>">
	<?php 
		foreach($data as $key => $id ) {
			
			$detail = getByDbId('Element', $id);
			
			$el = $detail['Element'];
			
			if( isset($el) && !empty($el) ) {
			
			$element_status = element_status($el['id']); 
			$class_name = 'undefined';
			if( isset( $el['date_constraints'] ) && !empty( $el['date_constraints'] ) && $el['date_constraints'] > 0 ) { 
				if( ((isset( $el['start_date'] ) && !empty( $el['start_date'] )) && date( 'Y-m-d', strtotime( $el['start_date'] ) ) > date( 'Y-m-d' )  )  && $el['sign_off'] != 1 ) {
					$class_name = 'not_started';
					$class_order = '4';
				}
				else if( ( (isset( $el['end_date'] ) && !empty( $el['end_date'] )) && date( 'Y-m-d', strtotime( $el['end_date'] ) ) < date( 'Y-m-d' ) )  && $el['sign_off'] != 1 ) { 
					$class_name = 'overdue';
					$class_order = '1';
				}
				else if( isset( $el['sign_off'] ) && !empty( $el['sign_off'] ) ) {
					$class_name = 'completed';
					$class_order = '3';
				}
				else if( (((isset( $el['end_date'] ) && !empty( $el['end_date'] )) && (isset( $el['start_date'] ) && !empty( $el['start_date'] ))) && (date( 'Y-m-d', strtotime( $el['start_date'] ) ) <= date( 'Y-m-d' )) && date( 'Y-m-d', strtotime( $el['end_date'] ) ) >= date( 'Y-m-d' ) )  && $el['sign_off'] != 1 ) {
					$class_name = 'progressing';
					$class_order = '2';
				}
			} 
			else {
				$class_name = 'undefined';
				$class_order = '5';
			}		
							
		?>
				<li class="list-group-item clearfix" data-id="<?php echo $id; ?>" data-uid="<?php echo $user_id; ?>" data-order="<?php echo $class_order;?>">
					 
					<span class="pophoverss" title="<?php echo $projectname['Project']['title']; ?>" data-content="<div><p><?php echo $el['description']; ?></p></div>"><a class="user_element_list" href="<?php echo SITEURL;?>entities/update_element/<?php echo $el['id']; ?>#tasks"><?php echo strip_tags($el['title']); ?></a></span>
						<span id="element_tasks" data-id="15" data-placement="left" title="" class="label label-default label-pill pull-right view_data tipText" data-original-title="Shows Information">
							<i class="fa fa-chevron-right"></i>
						</span>
					<br />	
					 
							<div class="dates ">
								<div class="dates_completed  ">
								   
								   <?php
								   if(isset($el["end_date"]) && !empty($el["end_date"]))
								   {
									//echo '<span class="team_cells cell_'.$element_status.'">End:&nbsp;'.date('d M Y',strtotime($el["end_date"])).'</span>';
									echo '<span class="team_cells cell_'.$element_status.'">End:&nbsp;'.$this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($el["end_date"])),$format = 'd M Y').'</span>';
									
									
								   }else{
									echo '<span class="team_cells cell_'.$element_status.'">&nbsp;</span>';   
								   }
										
									
									$staDate = $el['start_date'];
									
									$daysLeft = daysLeft($staDate, date('Y-m-d', strtotime($el['end_date'])));
									$remainingDays = 100 - $daysLeft;
									$day_text = "No Schedule";
									$stlo= "top:31px;";
									if(  $class_name == 'not_started' ) {
										$daysLeft = daysLeft( date('Y-m-d'), date('Y-m-d', strtotime($el['start_date'])));
										$remainingDays = 100;
										$day_text = "Start in ".$daysLeft." days";
										$stlo= "top:23px;";
									}
									else if(  $class_name == 'progressing' ) {
										$daysLeft = daysLeft( date('Y-m-d'), date('Y-m-d', strtotime($el['end_date'])));
										$day_text = "Due ".$daysLeft." days";	
										$stlo= "top:23px;";
										if( $daysLeft > 100 ) $remainingDays = 100;						
									}
									else if(  $class_name == 'completed' ) {
										$remainingDays = 100;
										$daysLeft = 0;
										$day_text = "100% Completed";											
									}
									else if(  $class_name == 'overdue' ) {
										$daysLeft = daysLeft( date('Y-m-d', strtotime($el['end_date'])), date('Y-m-d'));										 
										$day_text = "Overdue ".$daysLeft." days";
										$stlo= "top:23px;";	
									}
								?>
								<div class="c100 p<?php echo $remainingDays; ?> small <?php echo $class_name ?>">
									<span style="<?php echo $stlo; ?>"><?php echo $day_text; ?> </span>
									<div class="slice">
										<div class="bar"></div>
										<div class="fill"></div>
									</div>
								</div>
								</div>
							</div>
	 
				</li>
			<?php 
			}
		}
		?>				
		</ul>
		<?php 
	}
	else {
		?>
		<div width="100%" style="border-top: medium none; text-align: center; font-size: 16px; padding:10px" class="bg-blakish">No Element found</div>
		<?php 
	}
?>
<script>
$(function(){
	setTimeout(function(){
		var numericallyOrderedDivs = $('#user_elements_list .list-group-item').sort(function (a, b) {
		   return $(a).data('order') > $(b).data('order');
		  
		})					
		$("#user_elements_list").html(numericallyOrderedDivs);
		
		$('.pophoverss').popover({
			placement : 'bottom',
			trigger : 'hover',
			html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
		});
	},400);
})
 
</script>
