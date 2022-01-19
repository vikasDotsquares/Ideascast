<style>
	#myPopoverEstimateCostul {
		list-style:none;
		padding-left: 0;
		margin-top:0px;						
		border-bottom:1px solid #ccc;
		width:300px;
	}
 .popover-content li{
	 font-size:11px;
	 margin : 0px 0 0 -29px;
	 word-wrap: break-word;
 }

</style>
<?php 

if( isset($dependancy_value) && !empty($dependancy_value) ){
	$predecessorCount = 0;
	$successorCount = 0;
	
	$predecessorCount = ( isset($dependancy_value[0][0]['dependency_predessor']) && !empty($dependancy_value[0][0]['dependency_predessor']) ) ? $dependancy_value[0][0]['dependency_predessor'] : 0;
	
	$successorCount = ( isset($dependancy_value[0][0]['dependency_successor']) && !empty($dependancy_value[0][0]['dependency_successor']) ) ? $dependancy_value[0][0]['dependency_successor'] : 0;
	
	
	if (isset($predecessorCount) && isset($successorCount) && !empty($predecessorCount) && !empty($successorCount) ) {
		$dependancytypes = 3;
	} else if (isset($predecessorCount) &&  !empty($predecessorCount) ) {
		$dependancytypes = 1;
	} else if (isset($successorCount) && !empty($successorCount) ) {
		$dependancytypes = 2;
	} else {
		$dependancytypes = ''; 
	}

		
		$elementlist['ElementDependancyRelationship'] = json_decode($dependancy_value[0][0]['ele_all_dependancy'],TRUE);
		
		if( $dependancytypes == 1 || $dependancytypes == 3 ){
		
		?>
		<ol id="myPopoverElementLists" class="ele-predecessor myPopoverElementLists" >
		
		<?php if( $dependancytypes == 3 ){ ?>
		<h3 class="popover-title" style="margin-left: -55px !important; position:relative;margin-right: -15px; margin-bottom:5px; background : #d6e9c6 !important;border-radius:0;">Predecessor (<?php echo $predecessorCount;?>)</h3>
		<?php } 
		
		foreach($elementlist['ElementDependancyRelationship'] as $listelement){ 
		if( isset($listelement) && !empty($listelement) && $listelement['dependency'] == 1){ ?>
			<li>
			<?php  
				if( isset($listelement) && !empty($listelement) ){?> 
				<a href="<?php echo Router::Url(array("controller" => "entities", "action" => "update_element", $listelement['element_id']), true); ?>#tasks" style="color: #888;">
					<?php echo strip_tags($listelement['ele_title']); ?>
				</a>
				<?php } ?>
			</li>
		<?php }
		  } ?>
		</ol>
		 <?php } else { } ?>
		
		 <?php if( $dependancytypes == 2 || $dependancytypes == 3 ){ ?>
		<ol id="myPopoverElementList" class="ele-successor myPopoverElementList">	
		
		<?php if( $dependancytypes ==   3 ){ ?>
		<h3 class="popover-title" style="margin-left: -55px !important; position:relative;margin-right: -15px;margin-bottom:5px; background : #d6e9c6 !important;border-radius:0;">Successor (<?php echo $successorCount;?>)</h3>
		<?php } ?>
		<?php 
		
		foreach($elementlist['ElementDependancyRelationship'] as $listelement1){ 
		if( isset($listelement1) && !empty($listelement1) && $listelement1['dependency'] == 2){ ?>
			<li>
			<?php 
				if( isset($listelement1) && !empty($listelement1) ){ ?> 
				<a href="<?php echo Router::Url(array("controller" => "entities", "action" => "update_element", $listelement1['element_id']), true); ?>#tasks" style="color: #888;">
					<?php echo strip_tags($listelement1['ele_title']); ?>
				</a>
				<?php }
			?>
			</li>			 
		<?php }
		  } ?>
		</ol>
		 <?php } else{ } ?>
		
<?php } else {  ?>
<ul id="myPopoverElementList" class="myPopoverElementList" style="border-bottom:none;">
	<li>No record found</li>
</ul>	
<?php } ?>