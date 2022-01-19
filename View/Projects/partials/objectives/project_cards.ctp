<?php
/*
 * R - #FE000F - rgba(254, 0, 15, 0.3)
 * A - #FFD31C - rgba(255, 211, 28, 0.3)
 * G - #78FD00 - rgba(120, 253, 0, 0.2)
*  */
$ragSelected = '';
if(isset($this->request->data['rag_status']) && $this->request->data['rag_status'] > 0){
	$ragSelected = $this->request->data['rag_status'];
}
?>
<style>
	.custom-dropdown::after {
	    background-color: #ffffff;
	    border-left: none;
	    box-sizing: border-box;
	    color: #b7b7b7;
	    content: "";
	    display: inline-block;
	    font-family: "FontAwesome";
	    height: 16px !important;
	    margin-left: -17px;
	    padding: 3px 4px 0 3px !important;
	    pointer-events: none;
	    position: absolute;
	    right: 1px;
	    text-align: center;
	    top: 6px !important;;
	    vertical-align: middle;
	    width: 23px;
	    z-index: 2;
	}
</style>

<?php
	$program_title = '';
	if (isset($program_id) && !empty($program_id)) {
		$project_data = getByDbId('Program', $program_id);
		$program_title = '';// $project_data['Program']['program_name'];
	}


$project_count = 0;
if( isset($projects) && !empty($projects) ){
	$project_count = count($projects);
	if($_SERVER['REMOTE_ADDR'] == '111.93.41.194'){
		// pr($projects, 1);
	}
}

 ?>
<div class="panel-heading" style="position: relative;">
	<h3 class="panel-title"><?php echo (isset($program_title) && !empty($program_title)) ? $program_title.': ':''; ?>PROJECT CARDS (<span id="cost_type_search_count"><?php echo ( isset($projects) && !empty($projects) ) ? count($projects) : '0'; ?></span>)</h3>
	<div class="cost-custom-dropdown-one">
	<label class="custom-dropdown">
		<select class="aqua" id="cost_type_search" <?php /*if($project_count <= 1){ echo 'disabled'; }*/ ?>>
			<option value="">All Costs</option>
			<option value="None Set">None Set</option>
			<option value="Estimates Initiated">Estimates Initiated</option>
			<option value="Spending Initiated">Spending Initiated</option>
			<option value="Exceeded Estimate">Exceeded Estimate</option>
			<option value="Within Estimates">Within Estimates</option>
			<option value="Budget Set">Budget Set</option>
			<option value="Over Budget">Over Budget</option>
			<option value="On Budget">On Budget</option>
			<option value="On Budget, at Risk">On Budget, at Risk</option>
		</select>
	</label>
    </div>
	<div class="cost-custom-dropdown-two">

	<label class="custom-dropdown">
		<select class="aqua" id="rag_status" name="data[Project][rag_status]" <?php /*if($project_count <= 1){ echo 'disabled'; }*/ ?>>
			<option value="">All RAG</option>
			<option value="1" <?php if( $ragSelected == 1 ){ echo 'selected="selected"';}?> >RED</option>
			<option value="2" <?php if( $ragSelected == 2 ){ echo 'selected="selected"';}?>>AMBER</option>
			<option value="3" <?php if( $ragSelected == 3 ){ echo 'selected="selected"';}?>>GREEN</option>
		</select>
	</label>
    </div>

	<span class="clickable_grid clicked_grid" title="" ><i class="fa fa-th-large"></i></span>
</div>
<div class="panel-body scroll-vertical toggle-scrolling" >
	<div class="inner-horizontal">
	<?php
	$overdues = [];
	if( isset($projects) && !empty($projects) ) {
			foreach( $projects as $key => $value ) {
				$projectData = $value['project'];
				$taskData = $value['task'];

				$total_task = $taskData['CMP'] + $taskData['NON'] + $taskData['PRG'] + $taskData['PND'] + $taskData['OVD'];
				$project_details[$key] = $value;
				$rag_data = $this->ViewModel->getRAGStatus($key, true, $total_task, $taskData['OVD'], $projectData['rag_status']);
				$project_details[$key]['rag_percent'] = $rag_data['rag_color'];
				$project_details[$key]['rag_data'] = $rag_data;
			}
			$project_details = array_sort($project_details, 'rag_percent');
			// pr($project_details);

			$rag_status_1 = $rag_status_2 = $rag_status_3 = null;

			$rag_status_1 = arraySearch($project_details, 'rag_percent', 1);
			$rag_status_2 = arraySearch($project_details, 'rag_percent', 2);
			$rag_status_3 = arraySearch($project_details, 'rag_percent', 3);
			// mpr($rag_status_1, $rag_status_2, $rag_status_3);die;

			if( isset($rag_status_1) && !empty($rag_status_1) ) {
				echo $this->element('../Projects/partials/objectives/project_cards_sorting', array('projects' => $rag_status_1 ));
			}

			if( isset($rag_status_2) && !empty($rag_status_2) ) {
				echo $this->element('../Projects/partials/objectives/project_cards_sorting', array('projects' => $rag_status_2 ));
			}

			if( isset($rag_status_3) && !empty($rag_status_3) ) {
				echo $this->element('../Projects/partials/objectives/project_cards_sorting', array('projects' => $rag_status_3 ));
			} ?>
	<?php }
	else if ( (isset($program_id) && !empty($program_id)) && (!isset($projects) || empty($projects) ) ) { ?>
		<div class="no-project" style=" ">NO PROJECT</div>
	<?php
	}else if ( (!isset($program_id) || empty($program_id)) && (!isset($projects) || empty($projects) ) ) {
	?>
		<div class="no-project" style=" ">NO PROJECT</div>
	<?php
	}else { ?>
		<div style="" class="no-project">SELECT A PROGRAM OR PROJECT</div>
	<?php }
	 ?>

	</div>
</div>
<script type="text/javascript" >
$(function(){


})
</script>
<style>

</style>