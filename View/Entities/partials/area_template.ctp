
<?php 

if(isset($workspace_id) && !empty($workspace_id)) { 
// pr($workspace_id, 1);

?>
<?php 
	$templateData = $this->ViewModel->getAreaTemplate($workspace_id);
	$data['templateRows'] = $templateData;
	// pr($templateData)
?>
<div class="table-wrapper">
							
<?php //$area_id = 19;
if( isset( $data['templateRows'] ) && !empty( $data['templateRows'] ) ) {
		// pr($data['templateRows']);
		?>
	<table class="table table-bordered" id="tbl" >
		
		<tbody>
			<tr>
				<?php
					$max_boxes = max( array_map( 'count', $data['templateRows'] ) );
					$setWidth = 0;
					$row_group = $data['templateRows'];
					
					foreach( $row_group as $row_id => $row_data ) {
						$setWidth++;
						
						foreach( $row_data as $row_index => $row_detail ) {
							
							$last = false;
							$colspan = $rowspan = '';
							
							if( $row_detail['size_w'] > 0 && $row_detail['size_h'] > 0 ) {
								if( $row_detail['size_w'] > 1 ) {
									$colspan = ' colspan="' . $row_detail['size_w'] . '" ';
								}
								if( $row_detail['size_h'] > 1 ) {
									$rowspan = ' rowspan="' . $row_detail['size_h'] . '" ';
								}
							}
							
							$tdWidth = 0;
							if( isset( $setWidth ) && !empty( $setWidth ) ) {
								$tdWidth = ( 100 / $max_boxes );
								$tdWidth = number_format( $tdWidth, 4 );
								$tdWidth = $tdWidth . '%';
							}
							
						?>
						
					<?php 
					$icon = '<i class="fa fa-check" style="opacity: 0"></i>';
					$col_style = 'background: #d1d1d1;';
					if( $row_detail['area_id'] == $area_id ) { 
						$icon = '<i class="fa fa-check text-green" style="opacity: 0"></i>';
						$col_style = 'background: #888888;';
					}
					else{
							
					} ?>	
						
					<td <?php echo (!empty( $colspan )) ? $colspan : ''; ?> <?php echo (!empty( $rowspan )) ? $rowspan : ''; ?> valign="top" class="area_box" id="<?php echo $row_detail['area_id']; ?>" style="text-align: center; <?php echo $col_style; ?>" <?php if( $setWidth == 1 ) { ?> width="<?php echo $tdWidth; ?>" <?php } ?> ></td>
					<?php
					}
					// end first foreach
					// End table row started just after first foreach
					
					$row_group_tot = ( isset($row_group) && !empty($row_group) ) ? count( $row_group ) : 0;
					
					// It prints till the total number of rows reaches
					if( $row_id < ( $row_group_tot - 1) )
					echo '</tr><tr>';
				} // end second foreach
			?>
		</tbody>
	</table>
<?php } ?>
</div> 

<?php } ?> 