<?php

echo $this->Html->script('projects/rewards/rewards', array('inline' => true));
echo $this->Html->css('projects/reward-center');

echo $this->Html->script('projects/plugins/owl/owl.carousel', array('inline' => true));
echo $this->Html->css('projects/owl/owl.carousel', array('inline' => true));

?>

<?php $current_user_id = $this->Session->read('Auth.User.id'); ?>

<style type="text/css">
    .no-scroll {
        overflow: hidden;
    }
    #rewards_accordion {
        overflow-x: hidden;
        overflow-y: auto;
    }
    .owl-carousel .item {
        /*border: 1px solid #ccc;*/
    }
    .owl-carousel .item img {
        max-width: 40px;
		max-height: 40px;
		border-radius: 50%;
		overflow: hidden;
		border: 2px solid #ccc;
    }
    .owl-nav {
        position: relative;
    }
    .owl-nav button {
        width: 30px;
    }
    .owl-nav button i {
        font-size: 30px;
    }
    button.owl-prev {
        position: absolute;
        left: -30px;
        top: -55px;
    }
    button.owl-next {
        position: absolute;
        right: -30px;
        top: -55px;
    }
    button.owl-next.disabled, button.owl-prev.disabled {
        display: none;
    }
    button.owl-next, button.owl-prev {
        outline: none;
    }
	.input-group-addon{
	    padding: 10px 12px 7.5px 12px;
	}
	/* #sel_other_users{
		position:absolute;
	} */
	.rewards-listing-btn .common-menus{
		/* margin-right: 16px; */
	}
	.rewards-listing-btn .common-menus .btn{
		/* padding: 2px 5px; */
	}

    .top-cols, .reward-top-section {
        display: none;
    }
    .stop-editing {
        pointer-events: none;
        opacity: 0.7;
    }
</style>

<script type="text/javascript">
    $(function(){

        // RESIZE MAIN FRAME
        $('html').addClass('no-scroll');
        ($.adjust_resize = function(){
            $(".box-body.clearfix").animate({
                minHeight: (($(window).height() - $(".box-body.clearfix").offset().top) ) - 17,
                maxHeight: (($(window).height() - $(".box-body.clearfix").offset().top) ) - 17
            }, 1, () => {
                // RESIZE LIST
                $('#rewards_accordion').animate({
                    maxHeight: (($(window).height() - $('#rewards_accordion').offset().top) ) - 37
                }, 1)
            });
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

        $('#modal_small').on('hidden.bs.modal', function(e){
            $(this).removeData('bs.modal');
            $(this).find('.modal-content').html('');
        })
        $.reload_data = false;
        $('#modal_box').on('hidden.bs.modal', function(e){
            $(this).removeData('bs.modal');
            $(this).find('.modal-content').html('');
            $('html,body').removeClass('modal-open');

            if($.reload_data) {
                var checked = $('#chk_logged_in_user').prop('checked');
                if (checked) {
                    var user_id = $.logged_user_id;
                    $.top_section(user_id)
                        .done(function(message) {
                            $.get_user_projects(user_id);
                        })
                    /*$.get_user_points(user_id).done(function(message) {
                        $.get_user_projects(user_id);
                    })*/
                } else {
                    var user_id = $('#sel_other_users').val();
                    if (user_id == '' || user_id == undefined) {
                        user_id = $.logged_user_id;
                    }
                    $.top_section(user_id)
                        .done(function(message) {
                            $.get_user_projects(user_id);
                        })
                    // $.get_user_points(user_id)
                    // $.get_user_projects(user_id);
                }
                $.show_reward_graphs();
                $.reload_data = false;
            }
        })
    })
</script>
<?php
$current_user_opt_status = user_opt_status($current_user_id);
 ?>
<div class="row reward-center">
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

        <div class="box-content">
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
                            <div class="modal modal-success fade" id="modal_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <div class="modal modal-success fade" id="modal_small" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xs">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <!-- /.modal -->
                            <!-- FILTER BOX -->
                            <div class="col-sm-7 col-md-7 col-lg-4">
                                <div class="input-group my-select select-user stop-editing <?php if(!$current_user_opt_status) { ?> not-editable <?php } ?>">
                                    <span class="input-group-addon">
                                        <input id="chk_logged_in_user" type="checkbox" checked="">&nbsp;<label style="position:relative; top :-1px;margin: 0; font-weight: 400;" for="chk_logged_in_user">My Rewards</label>
                                    </span>
                                    <label class="custom-dropdown" style="width:100%; float:left;">
                                        <?php
                                        $userList = [];
                                        $userSelectMsg = 'Select User';
										$onfocus =  "this.size=8";
                                        if(isset($allUsersList) && !empty($allUsersList)) {
                                            foreach ($allUsersList as $key => $value) {
                                                $user_opt_status = user_opt_status($key);
                                                if($user_opt_status) {
                                                    $userList[$key] = $value;
                                                }
                                            }
											if( !empty($userList) && count($userList) <= 7 ){
												$onfocus =  "this.size=''";
											} else {
												$onfocus =  "this.size=8";
											}
                                        }
                                        else{
                                            $userSelectMsg = 'No Users';
											$onfocus =  "this.size=''";
                                        }

											echo $this->Form->input('sel_other_users', array(
													'options' => $userList,
													'empty' => $userSelectMsg,
													'class' => 'form-control aqua',
													'id' => 'sel_other_users',
													'label' => false,
													'div' => false,
													'disabled' => 'disabled',
													 'onfocus'=>$onfocus,
													 'onfocus'=>"this.size=1",
													 'onblur'=>"this.size=1",
													 'onchange'=>"this.size=1;
													 this.blur();",
												));

                                        ?>
                                    </label>
                                </div>

                            </div>
                            <div class="col-sm-5 col-md-5 col-lg-8 text-right">
                                <a href="#" class="btn btn-sm btn-success icon-rc-wrap tipText stop-editing <?php if(!$current_user_opt_status) { ?> not-editable <?php } ?>" title="Reward Manager">
                                    <i class="icon-reward-center white"></i>
                                </a>
                                <!-- <a href="#" class="btn btn-sm btn-danger">Reset</a> -->
                            </div>
                            <!-- END FILTER BOX -->
                        </div>
                        <div class="box-body clearfix" id="box_body">
                            <div class="">
                                <div class="col-xs-12 top-cols-wrap reward-top-section">
                                    <div class="top-cols top-cols-1 user-project-section"></div>
                                    <div class="top-cols top-cols-2 user-opt-section"></div>
                                    <div class="top-cols top-cols-3 user-earned-section"></div>
                                    <div class="top-cols top-cols-4 user-redeem-section"></div>
                                    <div class="top-cols top-cols-5 user-remaining-section"></div>
                                </div>
                            </div>
                            <!-- <div class=" ">
                                <div class="col-xs-12 rewards-listing-btn" <?php if(!$current_user_opt_status) { ?> style="display: none;" <?php } ?>>
                                    <div class="pull-right common-menus">
                                        <?php
                                        $optData = get_user_opt_setting($current_user_id);
                                        $opt_reward_table = true;
                                        if(isset($optData) && !empty($optData)) {
                                            $opt_reward_table = (isset($optData['reward_table_opt_status']) && !empty($optData['reward_table_opt_status'])) ? true : false;
                                        }
                                        ?>
                                        <a href="#" class="btn btn-success btn-sm tipText all-project-ov-achieved <?php if(!$current_user_opt_status) { ?> not-editable <?php } ?>" title="All Projects - OV Achieved" <?php if(!$opt_reward_table) { ?> style="display: none;" <?php } ?>>
                                            <i class="icon-achieved-white"></i>
                                        </a>
                                        <a href="#" class="btn btn-primary btn-sm toggle-accordion <?php if(!$current_user_opt_status) { ?> not-editable <?php } ?>" title="Expand All">
                                            <i class="fa fa-expand"></i>
                                        </a>
                                    </div>
                                </div>
                            </div> -->
                            <div class="col-xs-12 rewards-listing-view">
                                <div class="panel-group user-projects-accordian" id="rewards_accordion">
                                    <!-- <div class="loading-bar"></div> -->
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
            </div>
        </div>
    </div>
</div>
