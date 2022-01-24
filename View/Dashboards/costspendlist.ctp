<style>
   #myPopoverSpendCostul {
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
	if( isset($spendcost) && !empty($spendcost) && count($spendcost) ){
			foreach($spendcost as $listecost){
	?><ul id="myPopoverSpendCostul">
		<li><?php
		if( isset($listecost['ElementCostHistory']['spend_cost']) && !empty($listecost['ElementCostHistory']['spend_cost']) ){
			echo number_format($listecost['ElementCostHistory']['spend_cost'],2);
		} else {
			echo "0";
		}

		?></li>
		<li>Updated: <span><?php //echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($listecost['ElementCostHistory']['modified'])),$format = 'd M, Y');
		echo date('d M, Y: h:iA',strtotime($listecost['ElementCostHistory']['modified']));
		?></span></li>
		<li>Updated By: <span><?php echo $this->Common->userFullname($listecost['ElementCostHistory']['updated_by']);?></span></li>

	</ul>
<?php }
} else {  ?>
<ul id="myPopoverSpendCostul" style="border-bottom:none;">
	<li>No History</li>
</ul>
<?php } ?>