<?php
	echo $this->Html->css('projects/project_hub');
	echo $this->Html->script('projects/project_hub');
	$current_user_id = $this->Session->read('Auth.User.id');
?>
<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left"><?php echo $page_heading; ?>
					<p class="text-muted date-time" style=" ">
						<span style="text-transform: none;"><?php echo $page_subheading; ?></span>
					</p>
				</h1>
			</section>
		</div>
		<div class="box-content ">
			<div class="row ">
                <div class="col-xs-12">

                    <div class="box noborder margin-top" style="border-radius: 0px 0px 3px 3px;">
                        <div class="box-header filter-header border formdatacontrol " style="">

							<div class="col-sm-custom">
                            	<label class="custom-dropdown" style="width:100%;">

									<select class="form-control aqua" name="user_projects" id="owner_user_projects">
										<option value="">Select a Project</option>
										<?php if( isset($projects) && !empty($projects) ){
											foreach($projects as $key => $myProjectlist){
										?>
										<option value="<?php echo $key?>"><?php echo $myProjectlist; ?></option>
										<?php }
										}
										?>
									</select>
								</label>
                            </div>

							<div class="col-sm-custom pull-right">
								<div class="input-group">
									<input id="temp_search" type="text" class="form-control" placeholder="Search for..." style="border: 1px solid rgb(210, 214, 222);">
									<span class="input-group-btn">
										<button class="btn btn-danger search_clear" style="display: none;" type="button"><i class="fa fa-times"></i></button>
										<button class="btn btn-success search_submit" type="button" style="display: inline-block;"><i class="fa fa-search"></i></button>
									</span>
								</div>
							</div>


                           </div>
							<div class="box-body clearfix list-shares" id="work-center-task-list" style="margin: 6px 2px 6px 4px; ">
								<?php
								echo $this->element('../ProjectHubs/partials/workcenterlist', array('projects' => $projectids));
								?>
							</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
$(function() {

	$('body').delegate('.search_clear', 'click', function (event) {

	});


	$('body').delegate('.search_clear', 'click', function(event){
		event.preventDefault();
		$('#temp_search').val('').trigger('keyup');

		$(this).hide();
		$(".search_submit").show();

	})

	$('body').delegate('#temp_search', 'keyup', function (event) {
		event.preventDefault();

		$(".search_clear").show();
		$(".search_submit").hide();

		var filter, ul, li, a, i, txtValue, divsts;
		filter = $(this).val().toLowerCase();

		if( filter.length == 0 ){
			$(".search_clear").hide();
			$(".search_submit").show();
		}

		ul = document.getElementById("accordion");
		li = ul.getElementsByClassName("list-panel");
		divsts = 0;
		for (i = 0; i < li.length; i++) {
			a = li[i].getElementsByTagName("strong")[0];

			txtValue = a.textContent || a.innerText;

			if (txtValue.toLowerCase().indexOf(filter) > -1) {
				li[i].style.display = "";
				divsts = divsts+i;
			} else {
				li[i].style.display = "none";

			}
		}
		var html ='';
		$(".no-record-found").show();
		if( divsts == 0 ){

			$("#accordion").css({"min-height":"100px", "overflow-y":""});
			html = '<div class="col-sm-12 select_msg_main"> <div class="select_msg col-sm-12"  > No Project found </div></div>';
			//$("#accordion").html(html);
			$(".select_msg_main").show();

		} else {

			$("#accordion").css({"min-height":"100px", "overflow-y":"scroll"});
			$('.select_msg').remove();
			$('.select_msg_main').remove();

		}


	});

});

</script>