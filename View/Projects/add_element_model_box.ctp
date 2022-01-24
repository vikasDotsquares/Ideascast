<?php
// header('Content-Type: application/json');
// echo json_encode( $data ); 
?> 

<!-- modal boxes -->
	<div id="tpl_add_element" style="display: none;">
		<div class="box-body"> 
			<div class="form-group">
				<label class="" for="title">Workspace:</label>
				<?php echo $data['workspace']['Workspace']['title'] ?>
			</div>
			
			<div class="form-group">
				<label class="" for="title">Area:</label>
				<?php
					// LOOP THROUGH ALL AREAS WITHIN THE A WORKSPACE OF THE SELECTED PROJECT
					if( isset($data['area']) && !empty($data['area']) ) {
						$area = $data['area'];
					?>
					
					<select class="form-control" name="data[Element][area_id]" id="area_id" >
						<option value="" selected>Select an Area</option>
						<?php foreach( $area as $k => $v ) { ?>
							<option value="<?php echo $k ?>"><?php echo $v ?></option>
						<?php }?>
					</select>
				<?php }?>
			</div>
			
			<div class="form-group">
				<label class="" for="title">Title:</label>
				<?php echo $this->Form->textarea('Element.title', [ 'class'	=> 'form-control', 'id' => 'title', 'escape' => true, 'rows' => 1, 'placeholder' => 'max chars allowed 50' ] ); ?>
			</div>
			
			<div class="form-group">
				<label class="" for="description">Description:</label>
				<?php echo $this->Form->textarea('Element.description', [ 'class'	=> 'form-control', 'id' => 'description', 'escape' => true, 'rows' => 3, 'placeholder' => 'max chars allowed 250' ] ); ?>
			</div>
			
			<div class="form-group">  
				<label class="col-sm-6" for="">Date Constraints:</label>
				
				<input type="radio" id="dc_no" name="data[Element][date_constraints]" class="dc" value="0" checked />
				<label for="dc_no"> No</label>
				
				<input type="radio" id="dc_yes" name="data[Element][date_constraints]" class="dc" value="1" />
				<label for="dc_yes"> Yes</label> 
			</div>
			
			<div class="form-group" id="date_constraints_dates" style="display: none;">
				<div class="col-sm-12"> 
					<label class="col-sm-6" for="title">Start date:</label>
					<label class="col-sm-6" for="title">End date:</label> 
				</div>
				<div class="col-sm-12 form-group"> 
						<div class="col-sm-6">
							<input class="form-control" name="data[Element][start_date]" id="start_date" />
						</div>
						<div class="col-sm-6">
							<input class="form-control" name="data[Element][end_date]" id="end_date" />
						</div> 
				</div>
			</div>
			
			<div class="col-sm-12 form-group">
				<label class="pull-left col-sm-5" for="">Pick Color:</label>
				 
					<span class="colors_wrapper pull-left">
						<span class="elementPicColors pick_green"></span>
						<span class="elementPicColors pick_red"></span>
						<span class="elementPicColors pick_yellow"></span>
						<span class="elementPicColors pick_brown"></span>
						<input type="hidden" name="data[Element][color_code]" id="color_code" />
					</span>
				 
			</div>
			
		</div>
	</div>
		
		
	<!-- End add an Element to an Area -->
	
	
	