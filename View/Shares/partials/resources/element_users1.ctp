 
<?php 
	if( isset($data) && !empty($data) ) {
	?>	
	<ul class="list-group" id="element_users_list" data-pid="<?php echo $project_id; ?>">
	<?php 
		foreach($data as $key => $id ) {
			
			$detail = getByDbId('Element', $id);
			$el = $detail['Element'];
			if( isset($el) && !empty($el) ) {
			?>
				<li href="#" class="list-group-item clearfix" data-id="<?php echo $id; ?>">
					<span class="el-panel <?php echo (strpos($el['color_code'], 'panel') !== false ) ? 'bg-'.str_replace('panel-', '', $el['color_code']): ((strpos($el['color_code'], 'bg') !== false ) ? $el['color_code'] : 'bg-gray') ; ?>" ></span>
					<?php echo strip_tags($el['title']); ?>
					 
						<span class="pull-right" title="Shows Information" data-placement="left" >
							<i class="fa fa-chevron-right"></i>
						</span>
					
				</li>
			<?php 
			}
		}
		?>				
		</ul>
		<?php 
	}
	else {
		?>
		<div width="100%" style="border-top: medium none; text-align: center; font-size: 16px; padding:10px" class="bg-blakish">No Element found</div>
		<?php 
	}
?>