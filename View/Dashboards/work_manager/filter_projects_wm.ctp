<?php
// e('filter projects');
// pr($named_params);
	$current_user_id = $this->Session->read('Auth.User.id');

	$alluserarray = $this->TaskCenter->userByProject(array_keys($allprojects));


	$selected_user_projects = null;
	if(isset($filter_users) && !empty($filter_users)){
		$selectedusers = $filter_users;
		foreach ($alluserarray['project_by_user'] as $key => $value) {
			foreach ($value as $k => $v) {
				if(in_array($v, $selectedusers)) {
					$selected_user_projects[$key] = $key;
				}
			}
		}
		$projects = (isset($selected_user_projects) && !empty($selected_user_projects)) ? array_unique($selected_user_projects) : null;
	}else{
		$projects = array_keys($allprojects);
	}


	$all_element_keys = $overdue_projects = $todays_projects = $engaged_projects = $all_project_keys = [];
	if( (isset($projects) && !empty($projects)) && (isset($named_params) && !empty($named_params)) ) {
		foreach ($projects as $key => $prjid) {
			// get all elements of the selected projects
			$all_element_keys = array_merge($all_element_keys, $this->TaskCenter->userElements($current_user_id, [$prjid] ));
		}
		$all_element_keys = array_unique($all_element_keys);

		foreach ($all_element_keys as $key => $elid) {

				if($named_params == 1) {
					// GET ALL OVERDUE ELEMENT'S PROJECTS
					$element_status = element_status($elid);
					if( $element_status == 'overdue' ) {
						$epoid = element_project($elid);
						if(!in_array($epoid, $overdue_projects)) {
							$overdue_projects[] = $epoid;
						}
					}
				}
				else if($named_params == 2) {
					// GET PROJECTS THOSE HAVE ELEMENTS THAT ARE COMPLETING TODAY AND TOMORROW
					if(completing_tdto($elid) > 0){
						$epid = element_project($elid);
						if(!in_array($epid, $todays_projects)) {
							$todays_projects[] = $epid;
						}
					}
				}
				else if($named_params == 3) {
					// ALL NOT ENGAGED AND DISENGAGED ELMENT'S PROJECTS
					$el_is_engaged = getByDbId('Element', $elid, ['is_engaged']);
					if(isset($el_is_engaged) && !empty($el_is_engaged)) {
						$element_status = element_status($elid);
						if(($el_is_engaged['Element']['is_engaged'] == 0 || $el_is_engaged['Element']['is_engaged'] == 1) && ($element_status == 'progress' || $element_status == 'overdue')) {
							$epeid = element_project($elid);
							if(!in_array($epeid, $engaged_projects)) {
								$engaged_projects[] = $epeid;
							}
						}
					}
				}
		}
		$projects = array_merge($overdue_projects, $todays_projects, $engaged_projects);
	}


	// pr($projects);
	// pr($all_project_keys);


?>
<?php if (isset($projects) && !empty($projects)) { ?>
	<ul class="list-group">
	  <li class="list-group-item justify-content-between">
	    <input type="checkbox" id="project_all" name="project_all" class="project_all" />
	    <label for="project_all">All</label>
	    <span class="btn btn-danger btn-xs pull-right tipText clear_projects" title="" data-original-title="Clear Projects"><i class="fa fa-times"></i></span>
	  </li>
	  <?php

	foreach ($projects as $key => $value) {

	  	if(empty($value)) continue;
	  		$bold = '';
			$element_keys = null;
	  		if(isset($filter_users) && !empty($filter_users)) {
	  			$element_keys = $this->TaskCenter->usersElements($filter_users, [$value]);
	  		}
	  		else {
	  			$all_elements = $this->TaskCenter->userElements($current_user_id, [$value]);
				$element_keys = (isset($all_elements) && !empty($all_elements)) ? $all_elements : null;
	  		} 
			
			$costUrl = 'javascript:void(0);';
	  		if($this->ViewModel->projectPermitType( $value, $current_user_id )) {
	  			$bold = 'style="font-weight: 600;"'; 
				 
				if (isset($value) && !empty($value)) {
					
					$projectType =  CheckProjectType($value, $current_user_id);
					
					//$projectType = $this->requestAction('/projects/CheckProjectType/'.$value.'/'.$this->Session->read('Auth.User.id'));
					
					$costUrl = SITEURL.'costs/index/'.$projectType.':'.$value;
					
				}	
				 
				
	  		}
	  		else{
	  			$bold = 'style="color: #3c8dbc; font-weight: 600;"';
				$costUrl = 'javascript:void(0);';
	  		}
	  		$els = [];
	  		if (isset($element_keys) && !empty($element_keys)) {
	  			foreach ($element_keys as $ekey => $evalue) {
	  				$wsp_area_studio_status = wsp_area_studio_status($evalue);
	  				if(!$wsp_area_studio_status) {
						if(isset($named_params) && !empty($named_params)) {
							if($named_params == 1) {
								// GET ALL OVERDUE ELEMENT'S PROJECTS
								$element_status = element_status($evalue);
								if( $element_status == 'overdue' ) {
									$els[] = $evalue;
								}
							}
							else if($named_params == 2) {
								// GET PROJECTS THOSE HAVE ELEMENTS THAT ARE COMPLETING TODAY AND TOMORROW
								if(completing_tdto($evalue) > 0){
									$els[] = $evalue;
								}
							}
							else if($named_params == 3) {
								// ALL NOT ENGAGED AND DISENGAGED ELMENT'S PROJECTS
								$el_is_engaged = getByDbId('Element', $evalue, ['id', 'is_engaged', 'date_constraints']);
								if(isset($el_is_engaged) && !empty($el_is_engaged)) {
									$element_status = element_status($evalue);
									if(  ($el_is_engaged['Element']['is_engaged'] == 0 || $el_is_engaged['Element']['is_engaged'] == 1) && ($element_status == 'progress' || $element_status == 'overdue') ) {
										$els[] = $evalue;
									}
								}
							}
						}
						else {
							$els[] = $evalue;
						}

						//
	  				}
	  			}
	  		}
	  		// mpr($els, $element_keys);
			$prj_elements = (isset($els) && !empty($els)) ? count($els) : 0;
	  ?>

	  <?php 

			  	
	  

			$htms = "<div><p class='pop_para pop_paras elem_ion'>".$prj_elements." Tasks</p>";



			$htms .="<div class=''></div><p class='pop_para pop_paras bordrTop'>

			<a   id='trigger_uploadss' href='".SITEURL . "projects/index/".$value."'> Open Project </a>
			</p>

			<p class='pop_para pop_paras '>
			<a href='javascript:void(0)'  >  View Relationships </a>
			</p>";

			//if( $this->Session->read('Auth.User.role_id') != 1 || $this->Session->read('Auth.User.role_id') != 3 ){




			$htms .= "<p class='pop_para pop_paras'> <a href='".$costUrl."' class=''>  View Costs</a>
			</p>

			</div>";
			
			
			
			$engPopover = '';
						 
								$engPopover = "<p class='pop_para pop_paras' style='margin-top: 5px; font-weight:normal;'><a   id='trigger_uploadss'    href='".SITEURL . "projects/index/".$value."'> Open Project </a></p>";
								
								$engPopover .= "<p class='pop_para pop_paras' style='margin-top: 5px;font-weight:normal;'><a href='javascript:void(0)'  >  View Risks </a></p>";
									
								$engPopover .= "<p class='pop_para pop_paras' style='margin-top: 5px;font-weight:normal;'><a href='".$costUrl."' class=''>  View Costs</a></p>";
							 
 
			

			?>
	  <li class="list-group-item justify-content-between">
	    <input type="checkbox" value="<?php echo $value; ?>" id="<?php echo "prj_".$value; ?>" class="project_name" name="project[]" />
	    <label class="ptitle tipText" title="<?php echo strip_tags($allprojects[$value]) ?>" for="<?php echo "prj_".$value; ?>" <?php echo($bold); ?>><?php echo strip_tags($allprojects[$value]) ?></label>
	    <div class="badge   tipTexts" title="<?php //echo ($prj_elements==0) ?  'No Element Sharing in Project' : 'Elements in Project' ?>">
	    	<div  class="pull-left info pophoverss icon_element_add_black" title="<?php echo $prj_elements." Tasks"; ?>" data-content="<?php  echo $engPopover;  ?>" style="float: left;"><?php //echo $prj_elements; ?></div>

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
.icon_element_add_black {
	background-attachment: scroll;
	background-repeat: no-repeat;
	background-image: url("../images/icons/black_md.png");
	background-position: 7px 4px;
	background-size: 50% auto;
	margin: 0;
	padding: 1px 0 !important;
	display: inline-block;
	min-height: 22px;
	min-width: 27px;
}

.elem_ion{ font-size : 16px !important;}

.pop_paras a{ font-weight : normal !important; font-size:12px !important; line-height : 12px !important;}
 
.popover p.pop_paras{ width: auto !important;} 

.badge{ background : none;padding:0;}
</style>