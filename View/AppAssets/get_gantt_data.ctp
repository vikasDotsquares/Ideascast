<?php
if( isset($resultData) && !empty($resultData) ){
	$allTaskDetail = array();
	pr($resultData);
	foreach( $resultData as $wsp_list ){
		
		//echo $wsp_list['workspaces']['id']."<br />";
		$all_tasks = $wsp_list[0]['all_tasks'];
		//,$ele_status,1,$filter_by_user
		$allTaskDetail[] = $this->Permission->task_detail_filter($all_tasks,'',1,26);	
			
			
	}
	
	
	
	//pr( array_filter($allTaskDetail));
	
}

?>