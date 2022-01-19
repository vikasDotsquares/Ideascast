
	<?php 
		$current_user_id = $this->Session->read('Auth.User.id');
		
	
	if( isset($data) && !empty($data)) { ?>
		<ul class="list-group">
	<?php 
			foreach( $data as $key => $val ) {
				$row = $val['Activity'];				 
				$type_text = '';
				
				$type = $row['element_type'];
				$db_id = $row['relation_id'];
				$user_id = $row['updated_user_id'];
				$message = $row['message'];
			
				$table = null;
				if($type == 'element_tasks') {
					$table = 'Element';
				}
				if($type == 'element_documents') {
					$table ='ElementDocument';
				}
				if($type == 'element_links') {
					$table = 'ElementLink';
				}
				if($type == 'element_notes'){
					$table = 'ElementNote';
				} 
				if($type == 'element_decisions'){
					$table = 'ElementDecision';
				} 
				if($type == 'element_mindmaps'){
					$table = 'ElementMindmap';
				} 
				if($type == 'feedback'){
					$table = 'Feedback' ; 
				} 
				if($type == 'votes'){
					$table = 'Vote' ; 
				}
				
			if( getByDbId($table, $db_id) ) {
	?>
		<?php 
			
			$userDetail = $this->ViewModel->get_user( $user_id, null, 1 );
			$user_image = SITEURL . 'images/placeholders/user/user_1.png';
			$user_name = 'Not Available';
			$job_title = 'Not Available';
			$html = '';
			if( $user_id != $current_user_id ) {
				$html = CHATHTML($user_id);
			}
			if(isset($userDetail) && !empty($userDetail)) {
				$user_name = htmlentities($userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name']);
				$profile_pic = $userDetail['UserDetail']['profile_pic'];
				$job_title = htmlentities($userDetail['UserDetail']['job_title']);
				
				if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
					$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
				}
			}
		?>
		<li class="list-group-item">
			
				<img src="<?php echo $user_image; ?>" class="user-image pophover" style="float: none;"  data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
			<?php 
				if($type == 'element_tasks') {
					$db_data = getByDbId('Element', $db_id);
					
					$db_data = ( isset($db_data) && !empty($db_data)) ? $db_data['Element'] : null;
					echo 'Task: ' . htmlentities($db_data['title']) . ' (Start: ' . ((isset($db_data['start_date']) && !empty($db_data['start_date'])) ? _displayDate($db_data['start_date'], 'd M, Y') : 'N/A') . ' - End: ' . ((isset($db_data['end_date']) && !empty($db_data['end_date'])) ? _displayDate($db_data['end_date'], 'd M, Y') : 'N/A') . ')';
					// pr($db_data);
					echo '<span class="label label-default">'.$message .': '._displayDate($row['updated']).'</span>'; 
				}
				if($type == 'element_documents') {
					$db_data = getByDbId('ElementDocument', $db_id);
					$db_data = ( isset($db_data) && !empty($db_data)) ? $db_data['ElementDocument'] : null;
					echo 'Document: ' . $db_data['title'];
					echo '<span class="label label-default ">'.$message .': '._displayDate($row['updated']).'</span>'; 
				}
				if($type == 'element_links') {
					$db_data = getByDbId('ElementLink', $db_id);
					$db_data = ( isset($db_data) && !empty($db_data)) ? $db_data['ElementLink'] : null;
					echo 'Link: ' . $db_data['title'];
					echo '<span class="label label-default ">'.$message .': '._displayDate($row['updated']).'</span>';
				}
				if($type == 'element_notes'){
					$db_data = getByDbId('ElementNote', $db_id);
					$db_data = ( isset($db_data) && !empty($db_data)) ? $db_data['ElementNote'] : null;
					echo 'Note: ' . $db_data['title'];
					echo '<span class="label label-default ">'.$message .': '._displayDate($row['updated']).'</span>';
				} 
				if($type == 'element_decisions'){
					$db_data = getByDbId('ElementDecision', $db_id);
					$db_data = ( isset($db_data) && !empty($db_data)) ? $db_data['ElementDecision'] : null;
					echo 'Decision: ' . $db_data['title'];
					echo '<span class="label label-default ">'.$message .': '._displayDate($row['updated']).'</span>';
				} 
				if($type == 'element_mindmaps'){
					$db_data = getByDbId('ElementMindmap', $db_id);
					$db_data = ( isset($db_data) && !empty($db_data)) ? $db_data['ElementMindmap'] : null;
					echo 'Mind Map: ' . $db_data['title'];
					echo '<span class="label label-default ">'.$message .': '._displayDate($row['updated']).'</span>';
				} 
				if($type == 'feedback'){
					$db_data = getByDbId('Feedback', $db_id);
					$db_data = ( isset($db_data) && !empty($db_data)) ? $db_data['Feedback'] : null;
					echo 'Feedback: ' . $db_data['title'];
					echo '<span class="label label-default ">'.$message .': '._displayDate($row['updated']).'</span>';
				} 
				if($type == 'votes'){
					$db_data = getByDbId('Vote', $db_id);
					$db_data = ( isset($db_data) && !empty($db_data)) ? $db_data['Vote'] : null;
					echo 'Vote: ' . $db_data['title'];
					echo '<span class="label label-default ">'.$message .': '._displayDate($row['updated']).'</span>';
				} 
			?>
			
		</li> 
			<?php	}
				
			}
	?>
			</ul>
	<?php }

else {
 ?>
<div width="100%" style="border-top: medium none; text-align: center; font-size: 16px; padding:10px" class="bg-blakish">No result found.</div>
 <?php 
}	
?>


<script type="text/javascript" >
$(function(){ 
	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });
	
})
</script>