<?php
echo $this->Html->css('projects/task_reminder');
echo $this->Html->script('projects/task_reminder');

echo $this->Html->script('projects/plugins/ellipsis-word', array('inline' => true));
?>
<style>.pophover{cursor:pointer;}</style>
<script type="text/javascript">
	$(function(){
		/* $('.pophover').popover({
	        placement : 'bottom',
	        trigger : 'hover',
	        html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
	    }); */

	    $('#modal_small').on('hidden.bs.modal', function () {
	    	$(this).removeData('bs.modal');
	    	$(this).find('modal-content').html('');
	    });
		
		
		$('body').delegate('.reminder-sort', 'click', function(){
			
			var field = $(this).data('field');
			var direction = $(this).data('direction');
			
			$.ajax({
				url: $js_config.base_url + 'dashboards/task_reminder_sort',
				type: "POST",
				data: $.param({ 'field': field, 'direction': direction }),
				dataType: "JSON",
				global: false,
				success: function(response) {
					$(".reminder-sorting-data").html(response);
					$('.tooltip').hide();
				}
			})
			
			
		})
		
	})
</script>

<div class="row">
    <div class="col-xs-12">
		<div class="row">
	       <section class="content-header clearfix">
				<h1 class="pull-left">
					<?php echo $page_heading; ?>
					<p class="text-muted date-time">
						<span style="text-transform: none;"><?php echo $page_subheading; ?></span>
					</p>
				</h1>
	       </section>
		</div>

     	<div class="box-content reminders-wrap">
			<div class="row ">
				<div class="col-xs-12">
					<div class="box noborder margin-top">
						<div class="box-header filters" style="">
						<!-- Modal Boxes -->
							<div class="modal modal-success fade" id="modal_large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog modal-lg">
									<div class="modal-content"></div>
								</div>
							</div>

							<div class="modal modal-success fade" id="modal_small" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog ">
									<div class="modal-content"></div>
								</div>
							</div>
						<!-- /.modal -->
							<div class="col-sm-12 top-cols">
								<div  class="col-xs-12 col-sm-8 col-md-6 col-lg-4 dd-wrap dd-wrap">
									<?php /* <label class="pull-left" style="margin-top: 7px;">Reminders:</label>*/ ?>
									<div class="col-sm-9 no-padding">
										<label class="custom-dropdown" style="width: 100%; ">
											<select class="aqua filter_reminder">
												<option value="all">All Reminders</option>
												<option value="sent">Sent Reminders</option>
												<option value="received">Received Reminders</option>
											</select>
										</label>
									</div>
								</div>
								<?php if (isset($reminder_filter) && !empty($reminder_filter)) { ?>
								<div class="pull-right">
									<a href="<?php echo Router::Url(array("controller" => "dashboards", "action" => "task_reminder", 'admin' => FALSE), true); ?>"  class="btn btn-sm btn-danger tipText" title="Reset Filters">Reset</a>
								</div>
								<?php } ?>
								<?php /* ?><span class="popup_setting_wrap">
									<input type="checkbox" id="show_reminder_popup" name="show_reminder_popup" class="fancy_input" value="1" <?php if(isset($reminder_pop_up) && !empty($reminder_pop_up)) { ?> checked="checked" <?php } ?> >
									<label class="fancy_label text-black" for="show_reminder_popup">Show Reminders when I sign in</label>
								</span><?php */ ?>
							</div>
						</div>

						<div class="box-body clearfix"  >

							<div class="data-container reminder-sorting-data">
								<?php
									echo $this->element('../Dashboards/partials/reminders');
								?>
							</div>

						</div><!-- /.box-body -->
					</div><!-- /.box -->
     		    </div>
		   </div>
		</div>
    </div>
</div>