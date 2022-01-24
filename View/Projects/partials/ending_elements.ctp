<?php 
// e($project_id);
?>
 
	<?php
	 
	if( isset($data) && !empty($data) ) {
	?>
	<ul class="list-unstyled">
	<?php
	// pr($data);
		foreach($data as $index => $element_data ) {
			$element = $element_data['element'];
			$element_id = $element['id'];
			$area_id = $element['area_id'];
			
			if( isset($area_id) && !empty($area_id)) {
					
				$area_detail = $this->ViewModel->getAreaDetail( $area_id );
				$ws_id = area_workspace( $area_id );
				
				if( isset($ws_id) && !empty($ws_id)) {
				
					$ws_detail = $this->ViewModel->getWorkspaceDetail( $ws_id );
			// pr($ws_detail);
			 
	?>
	<li>
		<div class="el-detail">
			<div class="detail-head">
				<?php
					$class_name = 'undefined';
					$overclass = 'text-black';
					if( isset( $element['date_constraints'] ) && !empty( $element['date_constraints'] ) && $element['date_constraints'] > 0 ) {
						if( ((isset( $element['start_date'] ) && !empty( $element['start_date'] )) && date( 'Y-m-d', strtotime( $element['start_date'] ) ) > date( 'Y-m-d' )  )  && $element['sign_off'] != 1 ) {
							$class_name = 'not_started';
						}
						else if( ( (isset( $element['end_date'] ) && !empty( $element['end_date'] )) && date( 'Y-m-d', strtotime( $element['end_date'] ) ) < date( 'Y-m-d' ) )  && $element['sign_off'] != 1 ) {
							$class_name = 'overdue';
							$overclass = 'text-red';
						}
						else if( isset( $element['sign_off'] ) && !empty( $element['sign_off'] ) ) {
							$class_name = 'completed';
						}
						else if( (((isset( $element['end_date'] ) && !empty( $element['end_date'] )) && (isset( $element['start_date'] ) && !empty( $element['start_date'] ))) && (date( 'Y-m-d', strtotime( $element['start_date'] ) ) <= date( 'Y-m-d' )) && date( 'Y-m-d', strtotime( $element['end_date'] ) ) >= date( 'Y-m-d' ) )  && $element['sign_off'] != 1 ) {
							$class_name = 'progressing';
						}
					}
					else {
						$class_name = 'undefined';
					}
				?>
				<!--<div class="el-box element-<?php echo $class_name ; ?>">
					<a href="<?php echo SITEURL . 'entities/update_element/'.$element['id'].'#tasks' ?>" class="tipText" title="Open Task"></a>
				</div>-->
				<h5><a href="<?php echo SITEURL . 'entities/update_element/'.$element['id'].'#tasks' ?>" class="tipText text-black" title="Open Task"><?php echo $element['title'] ?></a></h5>
			</div>
			<div class="detail-body">
				<p>
					<span class="text-bold">Key Result Target: </span>
					<span class=""><?php echo strip_tags($ws_detail['Workspace']['title']); ?></span>
				</p>
				<p>
					<span class="text-bold">Zone: </span>
					<span class=" "><?php echo $area_detail['title'] ; ?></span>
				</p>
				<p> 
					<span class="text-blakish">End date: </span>
					<span class="<?php echo $overclass; ?> text-bold" style="margin-right: 10px; "><?php if( isset($element['date_constraints']) && !empty($element['date_constraints']) ) { echo _displayDate($element['end_date'], 'd M, Y'); } else { echo 'N/A'; } ?></span>
				 
 
				</p>
			</div>
		</div>
	</li>

	<?php
				} // check ws
			} // check area
		} // end foreach
	?>
	</ul>
	<?php
	}
	else {
	?>
	<div class="bg-blakish no-result"  style="">No Tasks</div>
	<?php 
	}
	?> 
