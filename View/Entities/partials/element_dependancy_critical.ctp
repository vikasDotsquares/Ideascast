
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		<h3 class="modal-title" id="myModalLabel">Dependencies and Priority</h3> 
	</div>			
	<div class="modal-body dependencyrelationlist" id="modal_body"   > 
	<ul class="col-sm-12" id="listitems">
		<?php
			 
			$this->request->data['Element']['id'] = $element_id;
		
			if( isset( $this->request->data['Element']['id'] ) && !empty($this->request->data['Element']['id']) ){
				
			$elelist = $this->Common->element_dependancy_list($this->request->data['Element']['id']);
			
			if( isset($elelist) && !empty($elelist) && count($elelist) > 0 ){
			
				foreach( $elelist as $showelement ){ 						
			
				$elementDetails = getByDbId('Element', $showelement['element_id'] );	

				$element_status = element_status($elementDetails['Element']['id']);
							
				$status_class = 'not_spacified';
				$status_class = 'bg-gray';
				switch ($element_status) {
					case 'not_started':
						$status_class = 'cell_not_started';
						break;
					case 'overdue':
						$status_class = 'bg-red';
						break;
					case 'completed':
						$status_class = 'bg-green';
						break;
					case 'progress':
						$status_class = 'bg-yellow';
						break;
				}
				
				$elecrital = $this->Common->element_criticalStaus($showelement['element_id']);
				
		?>
		
		
				<li class="panel clearfix <?php echo $status_class;?>" data-dependcy="<?php echo (isset($showelement['dependency']) && $showelement['dependency'] == 1)? 1 : 2;?>" >
					<div class="col-sm-5 dependencyTitle" ><?php echo $elementDetails['Element']['title'];?></div>
					<div class="col-sm-5 "><?php							
							
							
							if( isset($elementDetails['Element']['start_date']) && !empty($elementDetails['Element']['start_date']) ){
								echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($elementDetails['Element']['start_date'])),$format = 'd M Y');
							}
							if( isset($elementDetails['Element']['end_date']) && !empty($elementDetails['Element']['end_date']) ){
								echo " - ".$this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($elementDetails['Element']['end_date'])),$format = 'd M Y');
							}

							if( (!isset($elementDetails['Element']['start_date']) && empty($elementDetails['Element']['start_date'])) && (!isset($elementDetails['Element']['end_date']) && empty($elementDetails['Element']['end_date'])) ){
								echo "No Schedule";
							}		
									
					?></div>
					<div class="col-sm-2">
						<?php 
							if( isset($elecrital['ElementDependency']['is_critical']) && $elecrital['ElementDependency']['is_critical'] == 1 ){
								//echo '<i class="fa fa-arrow-left tipText" title="Predecessor"></i>';
								echo '<i class="fa fa-arrow-right red-arrow-task tipText" title="Priority Task" style="color: #dd4c3a;margin-right: 5px;"></i>';
								
							}
							
							if( isset($showelement['dependency']) && $showelement['dependency'] == 1 ){
								echo '<i class="fa fa-arrow-left tipText" title="Predecessor"></i>';	
							} else if( isset($showelement['dependency']) && $showelement['dependency'] == 2 ){
								echo '<i class="fa fa-arrow-right tipText" title="Successor"></i>';
							}
						?>
					</div>
				</li>		
				<?php 
				} 
				
			} else {?>
					<li>No Record Found</li>
				<?php } 
			}
		?>
	</ul>	
	</div> 			

	 <div class="modal-footer">
		<button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
	</div>
<script>
$(function() {
  $("#listitems li").sort(sort_li).appendTo('#listitems');
  function sort_li(a, b) {
    return ($(b).data('dependcy')) < ($(a).data('dependcy')) ? 1 : -1;
  }
});
</script>