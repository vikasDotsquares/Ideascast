
<style>
	.no-scroll {
	    overflow: hidden;
	}
	#viewcontrols a:not(.active) {
	    background-color: #d3d3d3 !important;
	    border-color: #c8c8c8;
	    color: #9c9c9c;
	}
	.row section.content-header h1 p.text-muted span {
	    color: #7c7c7c;
	    font-weight: normal;
	    text-transform: none;
	}
	.box-header.filters {
	    background-color: #ebebeb;
	    border-color: transparent  #ddd #ddd;
	    border-image: none;
	    border-style: none solid solid;
	    border-width: medium 1px 1px;
	    cursor: move;
	}
	.group_users .table {
	    margin: 2px;
	    max-width: 100%;
	    width: 99.7%;
		border-collapse: separate;
	}
	.group_users .table tr:first-child td {
		background-color: #57a8d7;
		color: #fff;
	}
	.group_users .table tr:first-child td:first-child {
	    border-radius: 5px 0 0;
	}
	.group_users .table tr:first-child td:last-child {
	    border-radius: 0 5px 0 0;
	}
	.view_group_users > i {
	    font-size: 12px;
	    margin: 3px 5px 3px -2px;
		color: #c3c3c3;
	}
	.table-rows [class^="col-sm-"], .table-rows [class*=" col-sm-"] {
		margin: 7px 0;
	}
	.view_profile {
	/* 	background: #ed5b49 none repeat scroll 0 0;
		border: 1px solid transparent;
		border-radius: 3px; */
		color: #fff;
	/* 	margin: 0 0 0 10px; */
		padding: 0 4px;
		float:left;
	}
	.view_profile:hover, .view_profile:focus {
	/* 	background: #dd4b39 none repeat scroll 0 0;
		border: 1px solid #dd4b39; */
		color: #fff;
	}
	.group_users{
		width: 100%;
	}
	.group-members-sec{
		width: 100%;
		display: -ms-flexbox;
		display: -webkit-flex;
		display: flex;
		flex-flow: column;
		flex-wrap: wrap;
				align-items: flex-start;
		justify-content: flex-start;
		padding: 2px;

	}

	.group-members-header {
		display: -ms-flexbox;
		display: -webkit-flex;
		display: flex;
		background: #57a8d7;
		color: #fff;
		align-items: flex-start;
		justify-content: flex-start;
		width: 100%;
		border-radius: 3px 3px 0 0;
	}
	.group-members-sec .col1, .group-members-sec .col2{
		padding: 8px 15px;
	}
	.group-members-sec .col1 {
		width: 25%;
		padding-left: 13px;
				display: -ms-flexbox;
		display: -webkit-flex;
		display: flex;
	}
	.group-members-sec .col2 {
		width: 25%;
	}
	.group-members-sec .col3 {
		width: 50%;
	}

	.group-members-sec .group-members-header .col2 {
		padding-left: 38px;
	}
	.group-members-cont {
		display: -ms-flexbox;
		display: -webkit-flex;
		display: flex;
		width: 100%;
	}
	.group-members-cont:nth-child(2n+1) {
	    background-color: #f7f7f7;
	}
	.group-members-cont .group-check-icon{
			min-width: 60px;
	}
	.group-members-cont .group-user-name{
			flex-grow: 1;
	}


	@media (max-width:767px) {
		.group-members-cont .group-check-icon {
			min-width: 30px;
		}
		.group-members-sec .col1 {
			width: auto;
			min-width: 170px;
		}
	}

	.group-check-icon i {
		cursor: default !important;
	    width: 20px !important;
	    height: 20px !important;
	    font-size: 12px !important;
	    line-height: 0.8 !important;
	}
	.btn-none {
	    background-color: #808080;
    	border-color: #666666;
    	color: #fff;
	}
	.btn-none:hover {
	    background-color: #777777;
    	border-color: #666666;
    	color: #fff;
	}

</style>
<!-- Modal Confirm -->
<div class="modal modal-warning fade" id="confirm_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>

<!-- /.modal -->
<script type="text/javascript">
	jQuery(function($) {

		// RESIZE MAIN FRAME
	    $('html').addClass('no-scroll');
	    ($.adjust_resize = function(){
	        $(".list-shares").animate({
	            minHeight: (($(window).height() - $(".list-shares").offset().top) ) - 17,
	            maxHeight: (($(window).height() - $(".list-shares").offset().top) ) - 17
	        }, 1)
	    })();

	    // WHEN DOM STOP LOADING CHECK AGAIN FOR MAIN FRAME RESIZING
	    var interval = setInterval(function() {
	        if (document.readyState === 'complete') {
	            $.adjust_resize();
	            clearInterval(interval);
	        }
	    }, 1);

	    // RESIZE FRAME ON SIDEBAR TOGGLE EVENT
	    $(".sidebar-toggle").on('click', function() {
	        $.adjust_resize();
	        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
	        setTimeout( () => clearInterval(fix), 1500);
	    })

	    // RESIZE FRAME ON WINDOW RESIZE EVENT
	    $(window).resize(function() {
	        $.adjust_resize();
	    })

		$("#ul_list_grid > li > div.box-success").on('click', function(event) {
			var $this = $(this);
			$("#ul_list_grid > li > div.selected").removeClass("selected")
			$this.addClass("selected")
		})

	 	$('#popup_model_box').on('hidden.bs.modal', function () {
	        $(this).removeData('bs.modal')//.find(".modal-content").html('<img src="../images/ajax-loader-1.gif" style="margin: auto;">');
	    });

	 	$('.view_group_users').on('click', function (event) {
				event.preventDefault();
				var ic = $(this).find('i.fa'),
					view_group_users = $('.view_group_users').not(this),
					openTo = $($(this).data('target')),
					others = $('.group_users').not(openTo);

				ic.toggleClass('fa-plus fa-minus')

				openTo.slideToggle(500, function(){
					others.slideUp(300, function(){
						view_group_users.each(function(i, ele) {
							$(ele).find('i.fa').removeClass('fa-minus').addClass('fa-plus')
						})
					})
				})
	    });

	})
</script>

<div class="row">
	<div class="col-xs-12">

		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left">Group Requests
					<p class="text-muted date-time">
						<span>View Group Requests from users</span>
					</p>
				</h1>

			</section>
		</div>


		<div class="box-content">

            <div class="row ">
                <div class="col-xs-12">
                    <div class="box border-top margin-top">
                        <div class="box-header no-padding" style="">
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->
                        </div>

						<div class="box-body clearfix list-shares " style="overflow: auto;">
							<?php //if(!isset($projects) || empty($projects)){ ?>


								<div data-limit="1" data-model="share" id="shares_table" class="table_wrapper clearfix">
									<div class="table_head">
										<div class="row">
											<div class="col-sm-3 resp">
												<h5> Group </h5>
											</div>
											<div class="col-sm-3 resp">
												<h5> Project</h5>
											</div>

											<div class="col-sm-2 resp  ">
												<h5> Request By</h5>
											</div>

											<div class="col-sm-2 resp text-center">
												<h5> Accepted On</h5>
											</div>
											<div class="col-sm-2 text-center resp">
												<h5>Action</h5>
											</div>
										</div>
									</div>
							<?php //}
							if(isset($permit_data) && !empty($permit_data)){ ?>
								<div class="table-rows data_catcher">

									<?php if(isset($permit_data) && !empty($permit_data)) {
										$i=1;
										foreach($permit_data as $project){

										$pdata = project_primary_id($project['ProjectGroup']['user_project_id'], 1);
										?>
										<div class="row">

											<div class="col-sm-3 resp">

												<a href="#" class="view_group_users" data-target="#group_users_<?php echo $project['ProjectGroup']['id'] ?>">
													<i class="fa fa-plus"></i>
													<?php echo htmlentities($project['ProjectGroup']['title']); ?>
												</a>
											</div>

											<div class="col-sm-3 resp">
												<a href="#" data-remote="<?php echo SITEURL ?>projects/project_description/<?php echo $pdata['id'] ?>"  data-target="#popup_model_box"  data-toggle="modal"  >
													<?php echo htmlentities($pdata['title']); ?>
												</a>
											</div>

											<div class="col-sm-2 resp">
											<?php
											$request_by = $project['ProjectGroup']['group_owner_id'];
											if(isset($project['ProjectGroupUser']['request_by']) && !empty($project['ProjectGroupUser']['request_by'])){
													$request_by = $project['ProjectGroupUser']['request_by'];
											} ?>

												<a href="#" class="show_profile text-black" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo SITEURL; ?>shares/show_profile/<?php echo $request_by; ?>" ><i class="fa fa-user text-maroon"></i>&nbsp;
													<?php echo $this->Common->userFullname($request_by); ?>
												</a>
											</div>

											<div class="col-sm-2 resp text-center accept_date"><?php echo (isset($project['ProjectGroupUser']['approved']) && $project['ProjectGroupUser']['approved'] > 0) ? _displayDate($project['ProjectGroupUser']['modified']) : 'N/A'; ?></div>

											<div class="col-sm-2 text-center resp action_button_parent">
											<?php if( isset($project['ProjectGroupUser']['approved']) && $project['ProjectGroupUser']['approved'] == 0) { ?>
												<div class="action_buttons">

													<button class="btn btn-xs btn-success request_action tipText" style="" title="Accept Group Request" type="button" data-remote="<?php echo SITEURL ?>shares/AcceptGroupRequest" id="" data-id="<?php echo $project['ProjectGroupUser']['id']; ?>" data-accept="1">
														<i class="fa  fa-check"></i>
													</button>


													<button class="btn btn-xs btn-danger request_action tipText" title="Decline Group Request" type="button" data-remote="<?php echo SITEURL ?>shares/AcceptGroupRequest" id=""  data-id="<?php echo $project['ProjectGroupUser']['id']; ?>" data-accept="2">
														<i class="fa  fa-times"></i>
													</button>
												</div>
											<?php
											}
											else if( isset($project['ProjectGroupUser']['approved']) && $project['ProjectGroupUser']['approved'] == 1 ) {
											?>
												<i class="fa  fa-check text-green"></i>
											<?php
											}
											else if( isset($project['ProjectGroupUser']['approved']) && $project['ProjectGroupUser']['approved'] == 2 ) {
											?>
												<i class="fa  fa-times text-red"></i>
											<?php }
											 ?>


											</div>
										</div>

										<div class="table-responsive group_users" style="border: 1px solid #ccc; margin-top: -1px; display: none;" id="group_users_<?php echo $project['ProjectGroup']['id'] ?>">
											<?php
												$group_users = group_users($project['ProjectGroup']['id'], null);
												if( isset($group_users) && !empty($group_users) ) {
											?>

											<div class="group-members-sec">
											<div class="group-members-header">
												<div class="col1">Group Members</div>
												<div class="col2">Responded on</div>
												<div class="col3"></div>
											</div>

											<?php
												foreach($group_users as $key => $val ) {
											?>
											<div class="group-members-cont">
												<div class="col1">
													<?php if($val['ProjectGroupUser']['approved'] == 1) { ?>
														<span class="group-check-icon"><i class="fa fa-check btn btn-success btn-xs btn-circle" ></i></span>
													<?php }else if($val['ProjectGroupUser']['approved'] == 2){ ?>
														<span class="group-check-icon"><i class="fa fa-times btn btn-danger btn-xs btn-circle" ></i></span>
													<?php }else{ ?>
														<span class="group-check-icon"><i class="fa fa-check btn btn-none btn-xs btn-circle" ></i></span>
													<?php } ?>

													<span class="group-user-name"><?php
													if( $val['ProjectGroupUser']['user_id'] != $this->Session->read('Auth.User.id') ){
														echo $this->Common->userFullname($val['ProjectGroupUser']['user_id']) ;
													} else {
														echo "Me";
													} ?>
														<a href="#" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $val['ProjectGroupUser']['user_id'] ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile" style="margin-left: -4px;">
															<i class="fa fa-user text-maroon"></i>
														</a>
													</span>
												</div>
											<div class="col2">
												<?php if($val['ProjectGroupUser']['approved'] == 1 || $val['ProjectGroupUser']['approved'] == 2) {
													echo _displayDate($val['ProjectGroupUser']['modified']);
												} ?>
											</div>
											<div class="col3"></div>
											</div>


											<?php } ?>

											</div>

												<?php } else { ?>
												<div class="panel panel-default margin padding bg-light-gray">
													No members in group.
												</div>
												<?php } ?>
										</div>
									<?php $i++;
										}
									}  ?>

								</div>
								<?php }else{ ?>
								<div class="table-rows data_catcher">
									<div class="row">
										<div class="col-lg-12 text-center" style="padding: 20px 0px;">
											No Requests.
										</div>
									</div>
								</div>
							<?php } ?>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" >
	$(function(){
		$('.request_action').click(function(event) {
			event.preventDefault()
			var $t = $(this),
				$el =  $(this).parent().parent().parent(),
				data = $t.data(),
				url = data.remote,
				id = $(this).attr('data-id'),
				accept = $(this).attr('data-accept'),
				user_project_id = $(this).parent().attr('data-upid'),
				project_group_id = $(this).parent().attr('data-gid'),
				post = {
				   'data[ProjectGroupUser][id]': id,
				   'data[ProjectGroupUser][approved]': accept,

				};
			var message = 'Are you sure you want to accept this Group request?';
				dialog_type = BootstrapDialog.TYPE_SUCCESS
			var tTitle = 'Accept Group Request',
			    lText = 'Accept';
			if(accept > 1){
				message = 'Are you sure you want to decline this Group request?',
				dialog_type = BootstrapDialog.TYPE_DANGER;
				tTitle = 'Decline Group Request';
				lText = 'Decline';
			}

			BootstrapDialog.show({
	            title: tTitle,
	            message: message,
	            type: dialog_type,
	            draggable: true,
	            buttons: [{
	                    //icon: 'fa fa-check',
	                    label: lText,
	                    cssClass: 'btn-success',
	                    autospin: true,
	                    action: function(dialogRef) {
	                        $.when(
	                        	$.ajax({
									url: url,
									global: false,
									data: $.param(post),
									type: 'post',
									dataType: 'json',
									success: function (response) {
										if(response.success) {
											location.reload();
											var content = response.content,
												date = content.date,
												status_type = content.status,
												icon = '';

											$t.parents('.row:first').find('.accept_date').text(date)

											if( status_type == '1' ) {
												$t.parents('.row:first').find('.action_button_parent').html('<i class="fa fa-check text-green"></i>');

												$.socket.emit("project:share", {creator: content.group_owner_id, project: content.project_id, sharer: $js_config.USER.id});

											}
											else if( status_type == '2' ) {
												$t.parents('.row:first').find('.action_button_parent').html('<i class="fa fa-times text-red"></i>')
											}


											if( $("#all_request").length && $("#group_total_request").length ) {
												var all_request = parseInt( $("#all_request").text() ) - 1
													$("#all_request").text(all_request)
												var group_total_request = parseInt( $("#group_total_request").text() ) - 1
													$("#group_total_request").text(group_total_request)
											}
										}else{
										   location.reload();
										}
									}
								})
                        	)
                            .then(function(data, textStatus, jqXHR) {
                                dialogRef.enableButtons(false);
                                dialogRef.setClosable(false);
                                dialogRef.getModalBody().html('<div class="loader"></div>');
                                setTimeout(function() {
                                    dialogRef.close();
                                }, 500);
                            })
	                    }
	                },
	                {
	                    label: ' Cancel',
	                   // icon: 'fa fa-times',
	                    cssClass: 'btn-danger',
	                    action: function(dialogRef) {
	                        dialogRef.close();
	                    }
	                }
	            ]
	        });
		});

     })

</script>