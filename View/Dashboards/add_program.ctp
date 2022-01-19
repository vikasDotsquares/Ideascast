<?php

	echo $this->Html->css('projects/dropdown');
	echo $this->Html->css('projects/program_center');

	echo $this->Html->css('projects/gs_multiselect/multi.select');
	echo $this->Html->script('projects/plugins/gs_multiselect/multi.select', array('inline' => true));

	echo $this->Html->script('projects/program_center', array('inline' => true));

?>
<style>
.popover .popover-content {
    font-size: 12px;
    font-weight: normal;
}
span.clear_program, span.remove_program  {
	cursor: pointer;
}
</style>
<script type="text/javascript">

</script>
<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left">
					<?php echo $page_heading; ?>
					<p class="text-muted date-time" style="padding:5px 0; margin: 0 !important;">
						<span style="text-transform: none;"><?php echo $page_subheading; ?></span>
					</p>
				</h1>
			</section>
		</div>
		<div class="box-content ">
			<div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">
						<div class="box-header" style="background: rgb(239, 239, 239) none repeat scroll 0px 0px; cursor: move; border-bottom: none; border: 1px solid #dddddd; padding: 8px 10px; min-height: 48px;">
							<a class="pull-left programinfo" data-placement="left" data-content="Create one or more Programs. To update Programs created open the Program Manager on the Program Center page." data-original-title=""><i class="fa fa-info template_info" title=""></i></a>
						</div>
                        <div class="box-body clearfix list-shares" style="min-height: 550px;">
							<?php echo $this->Form->create('SaveProgram', array('url' => array('controller' => 'projects', 'action' => 'add_program'),'class' => 'formAddSharing form-horizontal')); ?>
								<div class="panel panel-default user_selection clearfix" style="margin-bottom:0px;">
									<div class="panel-body" >
										<div class="add-user-body clearfix" id="clone_section">
											<div class="col-md-12 program_section">
												<div class="form-group">
													<label class="">Program Name:</label>
													<div class="input-group">
														<?php
														/*echo $this->Form->input('Program.program_name.0', array(
															'type' => 'text',
															'label' => false,
															'div' => false,
															'div' => 'program_name',
															'class' => 'form-control program_name',
															'placeholder' => '50 Chars Max'
														));*/
														?>
														<input name="data[Program][program_name][]" class="form-control program_name" placeholder="50 Chars Max" autocomplete="off" type="text" id="ProgramProgramName" required="required">
														<span class="input-group-addon btn-times tipText clear_program bg-red" data-title="Clear Program">
															<span class="fa fa-times"></span>
														</span>
														<span class="input-group-addon btn-times tipText remove_program hide bg-red" data-title="Remove Program">
															<span class="fa fa-trash"></span>
														</span>
													</div>
													<span class="error-message-pname text-danger error chars_left char_left_program" ></span>
													<span class="text-danger error validate-error-pname chars_left" ></span>
												</div>
											</div>
											<div id="cloned_program"></div>
										</div>
										<div class="row">
											<div class="col-md-6 col-xs-6">
												<p>
													<a href="javascript:void(0)" title="" class="btn btn-sm btn-warning addsection" data-original-title="Another Program">
														<i class="fa fa-plus"></i> Another Program
													</a>
												</p>
											</div>
											<div class="form-group text-right margin-right col-md-6 col-xs-6">
												<button type="submit" id="add_program" class="btn btn-success btn-sm">Create</button>
												<a href="<?php echo Router::url(array('controller' => 'dashboards', 'action' => 'program_center')); ?>" class="btn btn-danger btn-sm" id="cancel_group" >Cancel</a>
											</div>
										</div>
									</div>
								</div>
							<?php  echo $this->Form->end(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" >
$(function() {
	$('.programinfo').popover({
		placement : 'right',
		trigger : 'hover',
		html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
	});
	/*$('body').delegate('.program_name', 'keyup focus', function(event){
        event.preventDefault();
		$(this).parents('.program_section').find('.validate-error-pname').text('')
        var characters = 50;
        var $error_el = $(this).parents('.program_section').find('.error-message-pname');
        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
            $.input_char_count(this, characters, $error_el);
        }
    })*/
	$(window).on('click', function(event) {
		if(!$(event.target).is("input") ){
			if ($('.error-message-pname').length) {
				$('.error-message-pname').text("")
			}
		}
    })
	$('body').delegate('.clear_program', 'click', function(event){
		event.preventDefault();
		$prog_input = $(this).parents('.program_section').find(':input');
		$prog_input.val('');
	})
	var maxProgram = 10;
	var sectionsCount = 0;
	$('body').delegate('.remove_program', 'click', function(event){
		event.preventDefault();
		sectionsCount--;
		$('a.addsection').attr('disabled', false)
		console.log(sectionsCount);
		$prog_input = $(this).parents('.program_section').remove();
	})
	var template = $('#clone_section .program_section:first').clone();
	$('a.addsection').click(function(e){
		sectionsCount++;
		console.log(sectionsCount);
		if(sectionsCount >= maxProgram) {
			$(this).attr('disabled', true)
			return;
		}
		var section = template.clone().find(':input').each(function(){
			var newId = this.id + sectionsCount;
			this.id = newId;
		}).end();
		section.appendTo('#clone_section')
		section.find('.remove_program').removeClass('hide');
		if((sectionsCount+1) == maxProgram) {
			$(this).attr('disabled', true)
			return;
		}
		return false;
    });
})
</script>