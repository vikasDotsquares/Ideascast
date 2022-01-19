<style>
	#myPopoverEstimateCostul {
		list-style:none;
		padding-left: 0;
		margin-top:0px;
		border-bottom:1px solid #ccc;
		width:300px;
		font-size: 12px;
		padding-bottom: 5px;
	}
</style>
<?php

if( isset($estimatedcost) && !empty($estimatedcost) && count($estimatedcost) ){

		foreach($estimatedcost as $listecost){

?><ul id="myPopoverEstimateCostul">
	<li>
	<?php

		if( isset($listecost['ElementCostHistory']['estimated_cost']) && !empty($listecost['ElementCostHistory']['estimated_cost']) ){
			echo number_format($listecost['ElementCostHistory']['estimated_cost'],2);
		} else {
			echo " "."0";
		}
	?>
	</li>
	<li>Updated: <span><?php //echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($listecost['ElementCostHistory']['modified'])),$format = 'd M, Y');
	echo date('d M, Y: h:iA',strtotime($listecost['ElementCostHistory']['modified']));

	?></span></li>
	<li>Updated By: <span><?php echo $this->Common->userFullname($listecost['ElementCostHistory']['updated_by']);?>

</ul>
<?php }
} else {  ?>
<ul id="myPopoverEstimateCostul" style="border-bottom:none;">
	<li>No History</li>
</ul>
<?php } ?>