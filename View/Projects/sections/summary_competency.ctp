<?php
$projectPermitType = $this->ViewModel->projectPermitType($project_id, $this->Session->read('Auth.User.id'));
$list = $this->Permission->project_competency($project_id);

if(isset($list) && !empty($list)){  ?>
<div class="com-list-wrap">
	<ul class="competencies-ul" >
		<?php
		$all_skills = (!empty($list[0][0]['skills'])) ? json_decode($list[0][0]['skills'], true) : [];
		$all_subjects = (!empty($list[0][0]['subjects'])) ? json_decode($list[0][0]['subjects'], true) : [];
		$all_domains = (!empty($list[0][0]['domains'])) ? json_decode($list[0][0]['domains'], true) : [];
		?>

		<?php foreach ($all_skills as $key => $value) {  ?>
			<li class="skill-border-left">
				<span class="com-list-bg">
					<i class="com-skills-icon tipText" title="Skill"></i>
					<?php if(isset($value['users_skill']) && !empty($value['users_skill'])){
						$users_arr = explode(',', $value['users_skill']);
						$total_users = count($users_arr);
					 ?>
						<i class="activegreen tipText" title="<?php echo $total_users; ?> Team <?php echo ($total_users > 1) ? 'Members Have' : 'Member Has'; ?> This Skill"></i>
					<?php }else{ ?>
						<i class="inactivered tipText" title="No Team Members Have This Skill"></i>
					<?php } ?>
					<span class="com-sks-title open-comp-modal" data-type="skill" data-id="<?php echo $value['id']; ?>"><?php echo htmlentities($value['title'], ENT_QUOTES, "UTF-8"); ?></span>
				</span>
			</li>
		<?php } ?>
		<?php foreach ($all_subjects as $key => $value) { ?>
			<li class="subjects-border-left">
				<span class="com-list-bg">
					<i class="com-subjects-icon tipText" title="Subject"></i>
					<?php if(isset($value['users_subject']) && !empty($value['users_subject'])){
						$users_arr = explode(',', $value['users_subject']);
						$total_users = count($users_arr);
					 ?>
						<i class="activegreen tipText" title="<?php echo $total_users; ?> Team <?php echo ($total_users > 1) ? 'Members Have' : 'Member Has'; ?> This Subject"></i>
					<?php }else{ ?>
						<i class="inactivered tipText" title="No Team Members Have This Subject"></i>
					<?php } ?>
					<span class="com-sks-title open-comp-modal" data-type="subject" data-id="<?php echo $value['id']; ?>"><?php echo htmlentities($value['title'], ENT_QUOTES, "UTF-8"); ?></span>
				</span>
			</li>
		<?php } ?>
		<?php foreach ($all_domains as $key => $value) { ?>
			<li class="domain-border-left" >
				<span class="com-list-bg">
					<i class="com-domain-icon tipText" title="Domain"></i>
					<?php if(isset($value['users_domain']) && !empty($value['users_domain'])){
						$users_arr = explode(',', $value['users_domain']);
						$total_users = count($users_arr);
					 ?>
						<i class="activegreen tipText" title="<?php echo $total_users; ?> Team <?php echo ($total_users > 1) ? 'Members Have' : 'Member Has'; ?> This Domain"></i>
					<?php }else{ ?>
						<i class="inactivered tipText" title="No Team Members Have This Domain"></i>
					<?php } ?>
					<span class="com-sks-title open-comp-modal" data-type="domain" data-id="<?php echo $value['id']; ?>"><?php echo htmlentities($value['title'], ENT_QUOTES, "UTF-8"); ?></span>
				</span>
			</li>
		<?php } ?>
	</ul>
	</div>
<?php }else{ ?>
	<div class="no-sec-data-found">No Competencies</div>
<?php } ?>


<script type="text/javascript">
	$(function(){
		if($('.competencies-ul li').length <= 0){
			$('.com-list-wrap').html('<div class="no-sec-data-found">No Competencies</div>');
		}
		$('.competency-section').find('.ts-count').html($('.competencies-ul li').length);

		$('.competencies-ul .open-comp-modal').off('click').on('click', function(event) {
			event.preventDefault();
			var data = $(this).data();
			var url = $js_config.base_url + 'competencies/view_skills/' + data.id
			if(data.type == 'subject'){
				url = $js_config.base_url + 'competencies/view_subjects/' + data.id
			}
			else if(data.type == 'domain'){
				url = $js_config.base_url + 'competencies/view_domains/' + data.id
			}
			$('#modal_view_skill').modal({
				remote: url
			})
			.modal('show');
		});
	})
</script>