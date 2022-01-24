
<?php if( isset($template_categories) && !empty($template_categories) ) {
die("00000000000");
	?>

	<!-- LIST AND GRID VIEW START	-->
	<ul id="new_templates" class="clearfix templates_list">
		<?php foreach( $template_categories as $key => $val ) {
			// pr($val);
			$item = $val['TemplateCategory'];
			$cat_templates = category_templates($item['id']);
			$icon_name = explode('.', $item['cat_icon']);
			$icon_file = ( !empty($icon_name) && count($icon_name) > 1) ? 'template-'.$icon_name[0] : 'icon_folder';
		?>
			
			<li class="utemp_cat_list" data-remote="<?php echo Router::Url(array('controller' => 'templates', 'action' =>  'create_workspace_tab', $project_id, $item['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $item['id']; ?>">
			
				<div class="icon-wrapper">
					<div class="icon-inner"><span class="cat-icon <?php echo $icon_file; ?>"></span></div>
				</div>
				<div class="cat-title">
					<?php 
						echo $item['title'].' (' . $cat_templates . ')';
					?>
				</div> 
			</li>
		<?php } ?>
	</ul>

	<div class="ajax-paginations">
		<?php  // echo $this->element('jeera_paging');  ?>
	</div>
<?php } ?>

