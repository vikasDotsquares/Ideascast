
<?php

$projectOwner = $this->Common->userprojectOwner($project_id);
$user_id = null;
if( isset($projectOwner) && !empty($projectOwner) ){
	$user_id= $projectOwner;
}
//$user_id = $this->Session->read('Auth.User.id');
$p_permission = $this->Common->project_permission_details($project_id,$user_id);
$user_project = $this->Common->userproject($project_id,$user_id);
$e_permission = $this->Common->element_permission_details($workspace_id, $project_id, $user_id);

$areaElements = [];
if( isset( $areadata ) && !empty( $areadata ) ) {

	foreach( $areadata as $k => $v ) {

			$elements_details_temp = null;
			if((isset($e_permission) && !empty($e_permission)))
			{
				$all_elements = $this->ViewModel->area_elements_permissions($v['Area']['id'], false,$e_permission);

			}
			if(((isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level==1)   ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) )){
				$all_elements = $this->ViewModel->area_elements($v['Area']['id']);
			}
			
						
			if( isset( $all_elements ) && !empty( $all_elements ) ) {

				foreach( $all_elements as $element_index => $e_data ) {

					$element = $e_data['Element'];

					$element_decisions = $element_feedbacks = [];
					if( isset($element['studio_status']) && empty($element['studio_status']) ) {
						$elements_details_temp[] = ['id' => $element['id'], 'title' => strip_tags($element['title']) ];
					}
				}

				$areaElements = array_merge($areaElements, $elements_details_temp);
			}
	}
} 
if( isset($areaElements) && !empty($areaElements) ){	
foreach($areaElements as $listele){
	if( !empty($listele['id']) ){
?>
	<option value="<?php echo $listele['id'];?>"><?php echo $listele['title']; ?></option>
<?php 	}
	}
}?>