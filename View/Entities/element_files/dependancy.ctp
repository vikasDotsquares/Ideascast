<?php
	$elementDetail = $this->ViewModel->getElementDetail($element_id);
	
	$ele_start_date = $elementDetail['Element']['start_date'];
	$ele_end_date = $elementDetail['Element']['end_date'];
	
	$tiptext = 'Critical And Relationships';
	$critalandrelatioscss = '';
	$criticalandrelationurl = '';
	$crmodalcls ='notavail';
	if( isset( $element_id ) && !empty($element_id) ){
		$criticalstatus = $this->Common->element_criticalStaus($element_id); 
			$criticalstatus_tot = ( isset($criticalstatus['ElementDependancyRelationship']) && !empty($criticalstatus['ElementDependancyRelationship']) ) ? count($criticalstatus['ElementDependancyRelationship']) : 0;
	//	pr($criticalstatus);
		if( isset($criticalstatus['ElementDependency']['is_critical']) && $criticalstatus['ElementDependency']['is_critical'] > 0 ){
			//$tiptext = 'Dependencies and Critical';
			
			
			//$criticalstatus_Eel_Dep_tot = ( isset($criticalstatus['ElementDependency']['is_critical']) && !empty($criticalstatus['ElementDependency']['is_critical']) ) ? count($criticalstatus['ElementDependency']['is_critical']) : 0;
			
			$tiptext = 'Priority Task, Task Dependencies: '.$criticalstatus_tot;
			$critalandrelatioscss = 'television-arrow-red';
			$criticalandrelationurl = SITEURL.'entities/element_dependancy_critical/'.$element_id;

			// added to hide modal box when Dependencies not exists
			if( isset($criticalstatus['ElementDependancyRelationship']) && $criticalstatus_tot > 0 )
				$crmodalcls ='modal';
			else
				$crmodalcls ='modal';

		} else if( isset($criticalstatus['ElementDependancyRelationship']) && $criticalstatus_tot > 0 && isset($criticalstatus['ElementDependency']['is_critical']) && $criticalstatus['ElementDependency']['is_critical'] > 0  ){
			
			$tiptext = 'Priority Task, Task Dependencies: '.$criticalstatus_tot;
			$critalandrelatioscss = 'television-arrow';
			$criticalandrelationurl = SITEURL.'entities/element_dependancy_critical/'.$element_id;
			$crmodalcls ='modal';
			
		} else if( isset($criticalstatus['ElementDependancyRelationship']) && $criticalstatus_tot > 0  ){
			
			$tiptext = 'Task Dependencies: '.$criticalstatus_tot;
			$critalandrelatioscss = 'television-arrow';
			$criticalandrelationurl = SITEURL.'entities/element_dependancy_critical/'.$element_id;
			$crmodalcls ='modal';
			
		} else if( !empty($ele_start_date) && !empty($ele_end_date) ) {
			
			$tiptext = 'Task Dependencies: 0';
			$criticalandrelationurl = SITEURL.'entities/element_dependancy_critical/'.$element_id;
			$critalandrelatioscss = 'television-arrow-gray';
			$crmodalcls ='modal';
			
		} else {
			
			$tiptext = '';
			$criticalandrelationurl = '';
			$critalandrelatioscss = '';
			$crmodalcls ='';
		}
    }	
if( $criticalandrelationurl != "" && !empty($critalandrelatioscss)  ){
?>

 
	<a  class="h-common-btn wssb tipText"   title="<?php echo $tiptext;?>" data-toggle="<?php echo $crmodalcls; ?>" data-remote="<?php echo $criticalandrelationurl;?>"  >
					<i class="dependencyblack"></i>
	</a>

<?php } ?>