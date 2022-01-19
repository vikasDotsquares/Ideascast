<style>
    .pr_charts {
      position: absolute;
      left: 0;
      top: 15px;
      width: 78%;
      height: 90%;
    }
    .pr_chart {
      position:absolute;
      visibility: hidden;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      z-index: 1;
    }
    .pr_selector {
      position: absolute;
      top: 0;
      right: 0;
      width: 20%;
      height: 100%;
      background-color: #f1f3f4;
      font-family: Open Sans;
      font-size: 14px;
      color: #444;
      font-weight: 400;
      border-left: #dcdcdc 1px solid;
    }
    .pr_label {
      padding:14px 15px 0 20px;
    }
    .pr_ul{
      list-style: none;
      padding: 15px 15px 15px 25px;
      
    }
    .pr_li{
      margin: 0 0 2px 0;
      padding: 5px;
      cursor: pointer;
    }
    .pr_li:hover {
      background-color: #f1f3f4;
    }
    tspan {
      font-family: monospace;
    }
    .morris-hover {
      position: absolute;
      z-index: 1000;
    }
    .morris-hover.morris-default-style {
      border-radius: 5px;
      border: #ccc solid 2px;
      padding: 6px 6px 0 6px;
      color: #444;
      background: #FFF;
      font-family: monospace;
      font-size: 11px;
      text-align: center;
      font-weight: bold;
    }
    .morris-hover.morris-default-style .morris-hover-row-label {
      font-weight: bold;
      margin:0.25em 0;
    }
    .morris-hover.morris-default-style .morris-hover-point {
      white-space: nowrap;
      margin: 0.1em 0;
    }
    .morris-hover.morris-default-style p:first-child {
	    margin-top: 10px;
	}
	.pr_li.li_selected {
		border-left: 5px solid rgb(60, 141, 188);
	}
	.li_tborder {
		border-top: 1px dashed #3c8dbc;
	}
	.dtext {
	    color: #bbbbbb;
	    font-size: 30px;
	    text-align: center;
	    text-transform: uppercase;
	    min-height: 150px;
	    display: block;
	    width: 100%;
	    margin-top: 4%;
	}
</style>

    <div class="pr_charts">
    	<div id="dtext" class="dtext">Select Chart</div>
      <div id="pr_task_status" class="pr_chart"></div>
      <div id="pr_costs" class="pr_chart"></div>
      <div id="pr_confidence_levels" class="pr_chart"></div>
      <div id="pr_user_activity" class="pr_chart"></div>
      <div id="pr_effort" class="pr_chart"></div>
    </div>
    <div class="pr_selector">
      <div class="pr_label">Select one or more charts to display, click-drag to reorder:</div>
      <ul class='pr_ul'>

        <li id='no_pr_cost' class='pr_li' draggable="true"
          onclick="pr_click(event)"
          ondragstart="pr_dragstart(event)"
          ondragenter="pr_dragenter(event)"
          ondragleave="pr_dragleave(event)"
          ondragover="pr_cancelDefault(event)"
          ondrop="pr_drop(event)" data-type="costs">
          Costs
        </li>

        <li id='no_pr_effort' class='pr_li' draggable="true"
          onclick="pr_click(event)"
          ondragstart="pr_dragstart(event)"
          ondragenter="pr_dragenter(event)"
          ondragleave="pr_dragleave(event)"
          ondragover="pr_cancelDefault(event)"
          ondrop="pr_drop(event)" data-type="effort">
          Effort
        </li>

        <li id='no_pr_confidence_level' class='pr_li' draggable="true"
          onclick="pr_click(event)"
          ondragstart="pr_dragstart(event)"
          ondragenter="pr_dragenter(event)"
          ondragleave="pr_dragleave(event)"
          ondragover="pr_cancelDefault(event)"
          ondrop="pr_drop(event)" data-type="confidence_levels">
          Confidence Levels
        </li>

        <li id='no_pr_user_activity' class='pr_li' draggable="true"
          onclick="pr_click(event)"
          ondragstart="pr_dragstart(event)"
          ondragenter="pr_dragenter(event)"
          ondragleave="pr_dragleave(event)"
          ondragover="pr_cancelDefault(event)"
          ondrop="pr_drop(event)" data-type="user_activity">
          User Activity
        </li>

        <li id='no_pr_task_status' class='pr_li' draggable="true"
          onclick="pr_click(event)"
          ondragstart="pr_dragstart(event)"
          ondragenter="pr_dragenter(event)"
          ondragleave="pr_dragleave(event)"
          ondragover="pr_cancelDefault(event)"
          ondrop="pr_drop(event)" data-type="task_status">
          Task Status
        </li>

      </ul>
    </div>
<?php
$presult = $this->Scratch->project_data($project_id);
// pr($presult);
$total_members = (!empty($presult['members']['total_members'])) ? $presult['members']['total_members'] : 0;
$sign = (!empty($presult['currencies']['sign'])) ? $presult['currencies']['sign'] : "GBP";
/*if($sign == 'USD') {
	$sign = "$";
}
else if($sign == 'GBP') {
	$sign = "£";
}
else if($sign == 'EUR') {
	$sign = "€";
}
else if($sign == 'DKK' || $sign == 'ISK') {
	$sign = "Kr";
}*/
// e($total_members.', '.$sign);
?>
<script type="text/javascript">
	var pr_dragging = null;
    var pr_costs, pr_user_activity, pr_task_status, pr_confidence_levels, pr_effort;
    const pr_names = ['pr_task_status','pr_costs','pr_confidence_levels','pr_user_activity','pr_effort'];

    //OV variables
    const pr_currency = '<?php echo $sign; ?> '; //CHANGE AT RUNTIME TO PROJECT CURRENCY SYMBOL
    const pr_total_team_members = <?php echo $total_members; ?>; //CHANGE AT RUNTIME TO TOTAL NUMBER OF PROJECT TEAM MEMBERS

    $(() => {
    })
</script>
<?php echo $this->Html->script('projects/my_summary_performance', array('inline' => true)); ?>
