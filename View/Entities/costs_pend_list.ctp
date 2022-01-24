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
	if( isset($spendcost) && !empty($spendcost) && count($spendcost) > 0 ){
			foreach($spendcost as $listecost){
	?><ul id="myPopoverSpendCostul">
		<li><?php
		$currency_symbol_sign =  getCurrencySymbolDetails($listecost['ElementCostHistory']['project_currency_id']);
		$currency_symbol = '<i class="fa fa-gbp"></i>';
		if($currency_symbol_sign == 'USD') {
			$currency_symbol = '<i class="fa fa-dollar"></i>';
		}
		else if($currency_symbol_sign == 'GBP') {
			$currency_symbol = '<i class="fa fa-gbp"></i>';
		}
		else if($currency_symbol_sign == 'EUR') {
			$currency_symbol = '<i class="fa fa-eur"></i>';
		}
		else if($currency_symbol_sign == 'DKK' || $currency_symbol_sign == 'ISK') {
			$currency_symbol = '<span style="font-weight: 600">Kr</span>';
		}

		if( isset($listecost['ElementCostHistory']['spend_cost']) && !empty($listecost['ElementCostHistory']['spend_cost']) ){
			echo $currency_symbol." ".number_format($listecost['ElementCostHistory']['spend_cost'],2);
		} else {
			echo $currency_symbol." "."0";
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