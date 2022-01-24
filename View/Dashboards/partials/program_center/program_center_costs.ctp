<?php
// pr($prg_prj_id);
$over_budget = $on_budget = null;
$total_budget = $total_estimate = $total_spend = 0;
$curTotal_budget = $curTotal_estimate = $curTotal_spend = array();

$currencylist =  array_map('utf8_decode', $this->Common->getcurrencybyid());
$currencyIdArray = array();
if( isset($currencylist) && !empty($currencylist) ){
	foreach($currencylist as $ckey => $currencylist){
		$currencyIdArray[] = $ckey;
	}
}

// pr($currencyIdArray);

/****************** Project Budget *********************/
$all_budget = [];
if(isset($prg_prj_id) && !empty($prg_prj_id)){
    foreach ($prg_prj_id as $key => $prjid) {
        $project_detail = getByDbId('Project', $prjid, ['title', 'id', 'budget', 'currency_id']);
		// pr($project_detail['Project']);
        $projectbudget = $project_detail['Project']['budget'];
        $estimatcost = $this->ViewModel->project_element_ids($prjid, 'estimated_cost');
        $spendcost = $this->ViewModel->project_element_ids($prjid, 'spend_cost');
        $total_budget += $projectbudget;
        $total_estimate += $estimatcost;
        $total_spend += $spendcost;
		$pcid = $project_detail['Project']['currency_id'];
		if(!isset($all_budget[$pcid])) {
			$all_budget[$pcid] = ['sign' => '$', 'budget' => 0, 'estimate' => 0, 'spend' => 0];
		}

			$currency_detail = getByDbId("Currency", $pcid, ['id', 'name', 'sign']);
			$currency_detail = $currency_detail['Currency'];
			$currency_symbol = $currency_detail['sign'];

			$all_budget[$pcid]['sign'] = $currency_symbol;
			$all_budget[$pcid]['budget'] += $projectbudget;
			$all_budget[$pcid]['estimate'] += $estimatcost;
			$all_budget[$pcid]['spend'] += $spendcost;

            $ptitle = str_replace("'", "", $project_detail['Project']['title']);
            $ptitle = str_replace('"', "", $ptitle);

        if( (isset($projectbudget) && $projectbudget > 0) && ( !isset($estimatcost) || $estimatcost == 0 ) && ( isset($spendcost) && $spendcost > 0 && $spendcost > $projectbudget ) ){
            $costStatus = 'Over Budget';
            $over_budget[$prjid] = ['project' => strip_tags($ptitle)];
        }
        else if( (isset($projectbudget) && $projectbudget > 0) && ( isset($estimatcost) && $estimatcost > 0 && $estimatcost > $projectbudget ) && ( isset($spendcost) && $spendcost > 0 && $spendcost <= $projectbudget ) ){
            $costStatus = 'On Budget, at Risk';
            $on_budget[$prjid] = ['project' => strip_tags($ptitle)];
        }
        else if( (isset($projectbudget) && $projectbudget > 0) && ( isset($estimatcost) && $estimatcost > 0 && $estimatcost > $projectbudget ) && ( isset($spendcost) && $spendcost > 0 && $spendcost > $projectbudget ) ){
            $costStatus = 'Over Budget';
            $over_budget[$prjid] = ['project' => strip_tags($ptitle)];
        }
        else if( (isset($projectbudget) && $projectbudget > 0) && ( isset($estimatcost) && $estimatcost > 0 && $estimatcost < $projectbudget ) && ( isset($spendcost) && $spendcost > 0 && $spendcost > $projectbudget ) ){
            $costStatus = 'Over Budget';
            $over_budget[$prjid] = ['project' => strip_tags($ptitle)];
        }
        else if( (isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost > $projectbudget) && ( !isset($spendcost) || $spendcost <= 0 ) ){
            $costStatus = 'On Budget, at Risk';
            $on_budget[$prjid] = ['project' => strip_tags($ptitle)];
        }
    }
}

/****************** Project Budget *********************/

 if(isset($all_budget) && !empty($all_budget)) {
	 foreach($all_budget as $key => $value) {
 ?>
        <div class="idcomp-schedule " >
            <!-- <div class="col-cost">
                <label class="label-text">Budget:</label>
                <div class="field-area-select">
        			<?php //echo $value['sign']; ?><span class="form-control"><?php //echo number_format($value['budget'], 2); ?></span>
                </div>
            </div> -->

            <div class="col-cost">
                <label class="label-text">Total Budget (<?php echo $value['sign']; ?>):</label>
                <div class="field-area-select">
                    <span class="form-control"><?php echo number_format($value['estimate'], 2); ?></span>
                </div>
            </div>
            <div class="col-cost">
                <label class="label-text">Total Actual (<?php echo $value['sign']; ?>):</label>
                <div class="field-area-select">
                    <span class="form-control"><?php echo number_format($value['spend'], 2); ?></span>
                </div>
            </div>
        </div>
<?php
	}
}

$over_budget_html = '<div class="budget-popover">';
    if(isset($over_budget) && !empty($over_budget)){
        foreach ($over_budget as $prjid => $value) {
            $cky = $this->requestAction('/projects/CheckProjectType/'.$prjid.'/'.$this->Session->read('Auth.User.id'));
            // echo $cky;
            $over_budget_html .= '<a href="'.Router::Url(array("controller" => "costs", "action" => "index", $cky => $prjid), true).'" class="budget-project">'.$value['project'].'</a>';
        }
    }
$over_budget_html .= '</div>';

$on_budget_html = '<div class="budget-popover">';
    if(isset($on_budget) && !empty($on_budget)){
        foreach ($on_budget as $prjid => $value) {
            $cky = $this->requestAction('/projects/CheckProjectType/'.$prjid.'/'.$this->Session->read('Auth.User.id'));
            $on_budget_html .= '<a href="'.Router::Url(array("controller" => "costs", "action" => "index", $cky =>  $prjid), true).'" class="budget-project">'.$value['project'].'</a>';
        }
    }
$on_budget_html .= '</div>';
?>
<div class="project-schedulecost">
	<div class="schedulecost-number">
        <div class="input-group">
        	<label class="input-group-addon">Project Over Budget</label>
        	<span class="form-control <?php if(isset($over_budget) && !empty($over_budget)){ ?>over_budget_count<?php } ?>" data-content='<?php echo $over_budget_html; ?>'><?php echo (isset($over_budget) && !empty($over_budget)) ? count($over_budget) : 0; ?></span>
        </div>
	</div>
    <div class="schedulecost-number">
        <div class="input-group">
        	<label class="input-group-addon">Projects: On Budget, at Risk</label>
        	<span class="form-control <?php if(isset($on_budget) && !empty($on_budget)){ ?>on_budget_count<?php } ?>" data-content='<?php echo $on_budget_html; ?>'><?php echo (isset($on_budget) && !empty($on_budget)) ? count($on_budget) : 0; ?></span>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $('.over_budget_count, .on_budget_count').popover({
            placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400}
        })
    })
</script>