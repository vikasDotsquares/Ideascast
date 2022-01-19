<?php 
if( isset($elementlist['ElementDependancyRelationship']) && !empty($elementlist['ElementDependancyRelationship']) && count($elementlist['ElementDependancyRelationship']) > 0 ){ ?>
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
		 margin : 0px 0 0 -29px
	 }
</style> 
<?php	$predecessorCount = 0;
	$successorCount = 0;
	$i=0;
	foreach($elementlist['ElementDependancyRelationship'] as $listelement){ 
		if($listelement['dependency'] == 1){
			$i++;				
			$predecessorCount = $i;
		}
	}
	$j=0;
	foreach($elementlist['ElementDependancyRelationship'] as $listelement){ 
		if($listelement['dependency'] == 2){
			$j++;				
			$successorCount = $j;
		}
	}

 // pr($dependancytypes);
		
		if( $dependancytypes == 1 || $dependancytypes == 3 ){
		
		?>
		<div id="myPopoverElementLists" class="ele-predecessor myPopoverElementLists" >
		
		<?php if( $dependancytypes == 3 ){ ?>
		<!--<h3 class="popover-title" style="display:block; margin-left: -55px !important; position:relative;margin-right: -15px; margin-bottom:5px; background : #d6e9c6 !important;border-radius:0;">Predecessor (<?php echo $predecessorCount;?>)</h3> -->
		<?php }
		 
		?>
		<div class="wptitle">Predecessor (<?php echo $predecessorCount;?>)</div> 
		<div class="wpdeails">
		<ol>
		<?php foreach($elementlist['ElementDependancyRelationship'] as $listelement){ 
		if($listelement['dependency'] == 1){ ?>
		
		
		
		<li>
			
			<?php $element =  $this->ViewModel->getElementDetail($listelement['element_id']); 
				if( isset($element) && !empty($element) ){?> 
				<a href="<?php echo Router::Url(array("controller" => "entities", "action" => "update_element", $element['Element']['id']), true); ?>#tasks" style="color: #000;    text-decoration: none; ">
					<?php echo strip_tags($element['Element']['title']); ?>
				</a>
				<?php } ?>
			</li>
		<?php }
		  } ?>
		  </ol>
		</div></div>
		 <?php }else{ } ?>
		
		 <?php if( $dependancytypes == 2 || $dependancytypes == 3 ){ ?>
		 
		 
		<div id="myPopoverElementList" class="ele-successor myPopoverElementList">	
		
		<?php if( $dependancytypes ==   3 ){
 
		?>
		<!--<h3 class="popover-title" style="display:block; margin-left: -55px !important; position:relative;margin-right: -15px;margin-bottom:5px; background : #d6e9c6 !important;border-radius:0;">Successor (<?php echo $successorCount;?>)</h3>-->
		<?php } ?>
		<div class="wptitle">Successor (<?php echo $successorCount;?>)</div>
		<div class="wpdeails">
		<ol>
		<?php foreach($elementlist['ElementDependancyRelationship'] as $listelement1){ 
		if($listelement1['dependency'] == 2){ ?>
		
		
			<li>
			<?php $element =  $this->ViewModel->getElementDetail($listelement1['element_id']); 
				if( isset($element) && !empty($element) ){ ?> 
				<a href="<?php echo Router::Url(array("controller" => "entities", "action" => "update_element", $element['Element']['id']), true); ?>#tasks" style="color: #000;    text-decoration: none;">
					<?php echo strip_tags($element['Element']['title']); ?>
				</a>
				<?php }
			?>
			</li>
			 
			
		<?php }
		  } ?>
		   </ol>
		</div></div>
		 <?php } else{ } ?>
		
<?php } else {  ?>
	<ul id="myPopoverElementList" class="myPopoverElementList" style="border-bottom:none;margin: 14px 0;padding-left: 72px;left: 0;list-style: none;font-weight: 550; display:block;">
		<li style="font-size:13px;">No Dependencies</li>
	</ul>	
<?php } ?>