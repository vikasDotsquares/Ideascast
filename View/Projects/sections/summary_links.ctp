
<?php
$projectPermitType = $this->ViewModel->projectPermitType($project_id, $this->Session->read('Auth.User.id'));
$list = $this->Permission->project_links($project_id);
// pr($list );
if(isset($list) && !empty($list)){  ?>
	<ul class="summary-doc proj-link-list">
		<?php foreach ($list as $key => $value) {
		$data = $value['project_links'];
		// pr($data);
		$show_list = true;
		if(!$projectPermitType){
			if(empty($data['is_sharers'])){
				$show_list = false;
			}
		}
			if($show_list){
		?>
		<li class="li-links" data-id="<?php echo $data['id']; ?>" data-sort="<?php echo $data['sort_order']; ?>">
			<a href="<?php echo $data['link']; ?>" <?php if($data['is_open_new_tab']){ ?> target="_blank" <?php } ?>>
				<span class="all-link-icon"></span>
				<div class="documents-list-text">
					<h6><?php echo htmlentities($data['title'], ENT_QUOTES, "UTF-8"); ?></h6>
					<div class="documents-list-info"><?php echo htmlentities($data['summary'], ENT_QUOTES, "UTF-8"); ?></div>
				</div>
			</a>
		</li>
			<?php } ?>
		<?php } ?>
	</ul>
<?php }else{ ?>
	<div class="no-sec-data-found">No Links</div>
<?php } ?>
<script type="text/javascript">
	$(function(){

		if($('.summary-doc.proj-link-list .li-links').length <= 0){
			$('.link-list-wrap').html('<div class="no-sec-data-found">No Links</div>');
		}
		$('.links-section').find('.ts-count').html($('.li-links').length);

	})
</script>