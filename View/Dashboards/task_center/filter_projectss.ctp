<?php
if (isset($allprojects) && !empty($allprojects)) {
$current_user_id = $this->Session->read('Auth.User.id');
?>
	<ul class="list-group">
	  <li class="list-group-item justify-content-between">
	    <input type="checkbox" name="project_all" id="select_project" /><span class="project_select">Select All</span>
	   <!-- <span class="pull-right">
			<a class="tipText show_selected in-active small btn btn-success btn-sm" title="Show Selection">Show</a>
			<span class="btn btn-danger btn-xs pull-right1 tipText clear_projects" title="" data-original-title="Clear Selection"><i class="fa fa-times"></i></span>
		</span>-->
	    <span class="pull-right">
			<a class="tipText show_selected in-active small " title="Show Selection"><button type="button" class="btn btn-success btn-sm">Show</button></a>
			<span class="btn btn-danger btn-xs pull-right1 tipText clear_projects" title="" data-original-title="Clear Selection"><i class="fa fa-times"></i></span>
		</span>
	  </li>
	  <?php

		foreach ($allprojects as $key => $value) {
			$user_role = $value['a']['role'];

			$project_id = $value['a']['project_id'];

			$project_title = $value['projects']['title'];
			$project_task = ( isset($value[0]['total_tasks']) && !empty($value[0]['total_tasks']) )? $value[0]['total_tasks'] : 0;
			$bold = '';
			$costUrl = 'javascript:void(0);';
	  		 if($user_role =='Owner' || $user_role == "Creator" || $user_role =="Group Owner"){
	  			$bold = 'style="font-weight: 600;"';

				if($user_role =='Creator')
					$projectType = "m_project";
				if($user_role =='Owner')
					$projectType = "r_project";
				if($user_role =='Group Owner')
					$projectType = "g_project";

				$costUrl = Router::Url(['controller' => 'projects', 'action' => 'index', $project_id, 'tab' => 'cost', 'admin' => false], true);
				// SITEURL.'costs/index/'.$projectType.':'.$project_id;
				/* if (isset($project_id) && !empty($project_id)) {
					$projectType =  CheckProjectType($project_id, $current_user_id);
					$costUrl = SITEURL.'costs/index/'.$projectType.':'.$project_id;
				} */
	  		}
	  		else{
	  			$bold = 'style="color: #3c8dbc; font-weight: 600;"';
				$costUrl = 'javascript:void(0);';
	  		}

			/*<p class='pop_para pop_paras' style='margin-top: 5px;font-weight:normal;'><a href='".Router::Url(['controller' => 'risks', 'action' => 'index', $project_id, 'admin' => false], true)."'  >  View Risks </a></p><p class='pop_para pop_paras' style='margin-top: 5px;font-weight:normal;'><a href='".$costUrl."' class=''>  View Costs</a></p>*/

			$engPopover = "<p class='pop_para pop_paras' style='margin-top: 5px; font-weight:normal;'><a   id='trigger_uploadss'    href='".SITEURL . "projects/index/".$project_id."'> Open Project </a></p>";

		?>
		  <li class="list-group-item justify-content-between">
			<input type="checkbox" value="<?php echo $project_id; ?>" id="<?php echo "prj_".$project_id; ?>" class="project_name" name="project[]" />
			<label class="ptitle tipText" title="<?php echo html_entity_decode(htmlentities(ucfirst($project_title)));  ?>" for="<?php echo "prj_".$project_id; ?>" <?php echo($bold); ?>><?php echo html_entity_decode(htmlentities($project_title));  ?></label>
			<div class="badge   tipTexts" title="<?php //echo ($prj_elements==0) ?  'No Element Sharing in Project' : 'Elements in Project' ?>">
				<div  class="pull-left info pophoverss " title="<?php echo $project_task." Tasks"; ?>" data-content="<?php  echo $engPopover;  ?>" style="color: #333; cursor: default;"><?php echo $project_task; ?></div>

			</div>
		  </li>

	  <?php } ?>
	</ul>
	<?php }
	else { ?>
		<ul class="list-group">
			<li class="list-group-item justify-content-between">
				<div class="no-row-found">No Project Found</div>
			</li>
	  	</ul>
	<?php } ?>
<script type="text/javascript">
	$(function(){
		setTimeout(function(){
			// $('.ptitle').ellipsis_word();
		}, 1000)

		$('.pophoverss').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });

	$('#select_project').on('click',function(){
        if(this.checked){
            $('.project_name').each(function(){
                this.checked = true;
				$(".show_selected").removeClass("in-active");
            });
        }else{
             $('.project_name').each(function(){
                this.checked = false;
				$(".show_selected").addClass("in-active");
            });
        }
    });

	})

</script>
<style>
/* .icon_element_black {
	min-width: 27px;
	padding: 10px 5px;
	font-size: 15px;
	max-width: 70px;
	margin-top: 0px;

	background-position :  6px 0;
	background-size : 75% auto !important;

}
 */


.elem_ion{ font-size : 16px !important;}

.pop_paras a{ font-weight : normal !important; font-size:12px !important; line-height : 12px !important;}

.popover p.pop_paras{ width: auto !important;}

.list-group-item .badge { background : none; padding:0;}
</style>