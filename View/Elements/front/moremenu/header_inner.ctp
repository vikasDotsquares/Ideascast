<?php
if(checkPasswordExp($this->params['action']) == false && $this->params['action'] != "changepassword" && $this->Session->read('Auth.User.role_id') == 2 ){
		header('location:'.SITEURL.'users/changepassword');
		exit;
}  ?>
<style>
    .modal-body.people {
        max-height: 317px;
        overflow: auto;
    }
    .home_icon {
        font-size: 30px !important;
        padding: 22px 17px 23px 16px !important;
    }
    .home_icon:hover {
        color: #76b532 !important;
    }
    .todolist_link > i.bg-gray {
        border-radius: 3px;
        font-size: 13px;
        line-height: 0.9;
        padding: 2px 3px;
        position: absolute;
        right: 3px;
        text-align: center;
        top: 13px;
    }

    .dropdown-menu {
        color: #333 !important;
    }
    ul.dropdown-menu li.dropdown-submenu:hover > ul.dropdown-menu {
         display: block;
        margin-top:0px;
    }
    .dropdown-submenu {
        position:relative;
    }
    li.dropdown-submenu > .dropdown-menu {
        left: 100%;
        top: 0;
    }
    .border-alt {
        border: 1px solid #fff !important;
        border-radius: 2px;
        font-size: 10px;
        margin: -20px 0 0 !important;
        padding: 1px;
        position: relative;
        top: -2px;
    }
    .border-black {
        border: 1px solid #333 !important;
        border-radius: 2px;
        font-size: 10px;
        margin: -20px 0 0 !important;
        padding: 1px;
        position: relative;
        top: -2px;
    }

    .border-alt-big {
        border: 2px solid #777 !important;
        border-radius: 2px;
        padding: 1px;
        width: auto;
        font-size: 11px;
    }

    .open ul.dropdown-menu > li.men  a {
        position: relative;
        padding-right: 33px;
    }
    .open ul.dropdown-menu > li.men  a > span {
        position: absolute;
        right: 10px;
        top: 10px;
    }

	.drop-icon1 {
		background-image: url("../images/icons/down_arrow.png") !important;
		background-position: center bottom;
		background-repeat: no-repeat;
		background-size: 12px auto;
	}

    .theme_default li.task-reminder a:hover i.icon_reminder {
        filter: brightness(0) invert(1);
    }

</style>


<script type="text/javascript" >

    $(function () {

        $.footer_center = function() {
            var a = $('body').width() - 230;
            $('.cnts').width($('body').width());
            $('.cnts').css('padding-left','250px');
            $('.cnts').css('margin-left','0px');
            //$('.footer p').css('padding-right','230px');
            $('.footer .footer-content').css('padding-right','70px');
            $('.footer .footer-content').css('margin-right','0px');
        }


        /*$("body").delegate(".todoul > a", 'click', function(event) {
            event.stopPropagation();
        });*/

		myDate = new Date('12/22/2008 12:00 AM');
		document.cookie = 'cookieName=mysite; expires=' + myDate.toString + ';';

		$('li.mega-dropdown a').on('click', function (event) {
			$("#about_ideascost").slideToggle('slow');
			event.stopPropagation();
		});

		$('body').on('click', function (e) {
			if( !$('li.mega-dropdown').parents('li.dropdown:first').hasClass('open') ) {
				$("#about_ideascost").hide();
			}
		});

		$('#modal_medium').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('')
		});

		$('#modal_people,#modal_notifications').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('')
		});

		$('#popup_modal').on('hidden.bs.modal', function(){
			$(this).removeData('bs.modal')
			$(this).find('.modal-content').html('')
		})

		$('body').delegate('#save_page', 'click', function(event) {
			event.preventDefault();
			var runAjax = true;
			if( runAjax ) {
				$.ajax({
					type:'POST',
					url: $js_config.base_url + 'settings/start_page',
					data: $("#frmStartPage").serialize(),
					global: true,
					dataType: 'JSON',
					success: function(response) {
						runAjax = false;
						if( response.success ) {
							$('#message_box').html(response.msg).slideDown(500)
							setTimeout(function(){
								$('#message_box').slideUp(500).html('')
								$('#popup_modal').modal('hide');
							}, 3000)
						}
						else {

						}
					}

				});
			}
		})
        //$('.todolist_link').tooltip({'placement': 'bottom','container':'body'});

        $('.todolist_link').tooltip({
            template: '<div class="tooltip CUSTOM-CLASS" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>'
            , 'container': 'body', 'placement': 'bottom',
        })

        $('.nav.navbar-nav .tipText').tooltip({
            template: '<div class="tooltip CUSTOM-CLASS" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>'
            ,
			'container': 'body',
			'placement': 'bottom',
        })


        $('.todolist_link').on('mouseleave', function (e) {
            var $tooltip = $(this).data('bs.tooltip'),
                    $tip = $tooltip.$tip;

            $tip.hide()

        })

        $('.todoul').on('mouseenter touchend', function (e) {

            var w = $(window),
                wWidth = w.width(),
                dd = $(this).find('.dropdown-menu'),
                ddWidth = dd.width()
                ddLeft = dd.offset().left,
                total = ddWidth + ddLeft;
            if(total > wWidth) {
                alert('todolist_link')
                dd.css({
                    left: 'auto',
                    right: '100%'
                })
            }
        })


    })


    $(window).load(function () {


    // console.log(localStorage.getItem('open_tog'))
        /*
         if(localStorage.getItem('open_tog')!='open'){
         $('.sidebar-toggle').click(function(){
         localStorage.setItem('open_tog', 'open');

         })
         }else if(localStorage.getItem('open_tog')=='open'){

         $('body').addClass('sidebar-collapse');

         $('.sidebar-toggle').click(function(){
         localStorage.setItem('open_tog', 'close');

         })

         }else if(localStorage.getItem('open_tog')=='null'){
         localStorage.setItem('open_tog', 'close');
         } */

        if ($('body').hasClass('sidebar-collapse')) {
            $('.cnts').removeAttr('style');
            $('.footer .footer-content').removeAttr('style');
        } else {
            $.footer_center();
            /*var a = $('body').width() - 230;
            $('.cnts').width($('body').width());
            $('.cnts').css('margin-left', '230px');
            //$('.footer p').css('margin-right','230px');
            $('.footer .footer-content').css('margin-right', '230px');*/

        }

        $('.sidebar-toggle').click(function () {

            /* PROJECT COST PAGE STICKY HEADER */
            setTimeout(function() {
                if (typeof $.fixDiv !== 'undefined' && $.isFunction($.fixDiv))
                    $.fixDiv();
            }, 700);
            /* PROJECT COST PAGE STICKY HEADER */

            if ($('.share_propagation').length) {
                $('.share_propagation').each(function () {
                    var bsPopover = $(this).data('bs.popover');
                    if (bsPopover) {
                        $(this).trigger('click')
                    }
                })
            }

            if ($('body').hasClass('sidebar-collapse')) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo SITEURL . 'settings/updatebody/1/' . $this->Session->read('Auth.User.id') ?>',
                    global: false,
                    success: function (data) {
                        if (typeof $.rearrange_elements !== 'undefined' && $.isFunction($.rearrange_elements)) {
                            $.rearrange_elements()

                        }
                        $(".getelements").trigger("click");



                        //var a = $('body').width() - 230;
                        // $('.cnts').removeAttr('style');
                        // $('.footer .footer-content').removeAttr('style');

                    }

                });
            }
            else {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo SITEURL . 'settings/updatebody/0/' . $this->Session->read('Auth.User.id') ?>',
                    global: false,
                    success: function (data) {
                        if (typeof $.rearrange_elements !== 'undefined' && $.isFunction($.rearrange_elements)) {
                            $.rearrange_elements()

                        }
                        $(".getelements").trigger("click");
                        $.footer_center();
                        /*var a = $('body').width() - 230;
                        $('.cnts').width($('body').width());
                        $('.cnts').css('margin-left', '230px');
                        //$('.footer p').css('margin-right','230px');
                        $('.footer .footer-content').css('margin-right', '230px');*/
                    }

                });

            }

        })





        $('#closeOpenChat').click(function () {

			var pid = $('.ChatHeader select#project').val();
			var uid = '<?php echo $this->Session->read('Auth.User.id'); ?>';


		        $.ajax({
                    type: 'POST',
                     url: '<?php echo SITEURL . "settings/updateChatStats/";?>'+pid+'/'+uid+'/'+'0',
                    global: true,
                    success: function (data) {

                    }

                });

		})
		$('#btnChatWindow').click(function () {

			var pid = $('.ChatHeader select#project').val();
			var uid = '<?php echo $this->Session->read('Auth.User.id'); ?>';

            if(!$('#openChat').hasClass('open')) {
		        $.ajax({
                    type: 'POST',
                    url: '<?php echo SITEURL . "settings/updateChatStats/";?>'+pid+'/'+uid+'/'+'1',
                    global: true,
                    success: function (data) {

                    }

                });
            }

		});





        $('.ws_to').click(function () {
            if ($(".ws_to a i").attr('class') == "fa fa-long-arrow-up") {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo SITEURL . 'settings/updatewsp/1/' . $this->Session->read('Auth.User.id') ?>',
                    global: true,
                    success: function (data) {
                        $(".ws_tog").show();
                        if (typeof $.rearrange_elements !== 'undefined' && $.isFunction($.rearrange_elements)) {
                            $.rearrange_elements()
                        }
                    }

                });
            } else {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo SITEURL . 'settings/updatewsp/0/' . $this->Session->read('Auth.User.id') ?>',
                    global: true,
                    success: function (data) {
                        $(".ws_tog").hide();
                        if (typeof $.rearrange_elements !== 'undefined' && $.isFunction($.rearrange_elements)) {
                            $.rearrange_elements()
                        }
                    }

                });

            }

        })

        $('#requests a').click(function (event) {
            if ($("i", $(this)).attr('class') == "fa fa-long-arrow-up") {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo SITEURL . 'settings/update_request/1/' . $this->Session->read('Auth.User.id') ?>',
                    global: true,
                    success: function (data) {
                        $(".request_list").show();
                        if (typeof $.rearrange_elements !== 'undefined' && $.isFunction($.rearrange_elements)) {
                            $.rearrange_elements()
                        }
                    }

                });
            } else {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo SITEURL . 'settings/update_request/0/' . $this->Session->read('Auth.User.id') ?>',
                    global: true,
                    success: function (data) {
                        $(".request_list").hide();
                        if (typeof $.rearrange_elements !== 'undefined' && $.isFunction($.rearrange_elements)) {
                            $.rearrange_elements()
                        }
                    }

                });

            }

        })


        $("body").delegate(".toggle_header_menus", "click", function (event) {
            event.preventDefault();
            $('.navbar-custom-menu').toggleClass('in')
        })


        $("body").delegate(".dropdown-submenu", "mouseenter", function (event) {
            event.preventDefault();
            if ($(this).children('ul.dropdown-menu:first').length > 0) {
                var $menu = $(this).children('ul.dropdown-menu:first'),
                        mw = $menu.width(),
                        offset = $menu.offset(),
                        left = offset.left,
                        w = $(window).width(),
                        out = (left + mw);

                if (out > w) {
                    $menu.css({'left': 'auto', 'right': '100%'})
                }

            }
        })


        $('body').delegate('#notify_bell', 'mouseenter', function(event) {
            $(this).tooltip({ container: 'body', placement: 'auto' })
            $(this).tooltip('show')
        })
        $('body').delegate('#notify_bell', 'mouseleave', function(event) {
            $(this).tooltip('hide')
        })
    })



    var findTimezone = function() {
      var tz = jstz.determine();
      return tz.name();
    }

    var timezone = jstz.determine();
    var Cname = timezone.name();
    var offsetTime = new Date().getTimezoneOffset();
    setTimeout(function(){
    $.ajax({
    	type: 'POST',
    	url: '<?php echo SITEURL . 'settings/timezone/';?>',
    	data: $.param({'Cname':Cname, 'offset': offsetTime}),
    	success: function (data) {
    	}
    });
     },500)

    setTimeout(function(){
            $.ajax({
            	type: 'POST',
            	url: '<?php echo SITEURL . 'settings/timezone/';?>',
            	data: $.param({'Cname':Cname, 'offset': offsetTime}),
            	success: function (data) {
            	}
            });
     },3500)


</script>

<?php //checkPasswordExp($this->params['action']); ?>
<style>
    .skin-blue .main-header .logo {
        background-color: transparent !important;
    }
</style>
<!-- Modal Confirm -->
<div class="modal modal-success fade" id="modal_people" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-people">
		<div class="modal-content"></div>
	</div>
</div>

<header class="main-header <?php echo $user_theme; ?>" data-theme="<?php echo $user_theme; ?>">
<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
    <!-- Logo -->
    <a href="<?php echo SITEURL; ?>" class="logo">
		<span class="logo-text">
			OpusView<sup>TM</sup>
		</span>
        <?php
        $menuprofile = $this->Session->read('Auth.User.UserDetail.menu_pic');
        $menuprofiles = SITEURL . USER_PIC_PATH . $menuprofile;

        if (!empty($menuprofile) && file_exists(USER_PIC_PATH . $menuprofile)) {
            $menuprofiles = SITEURL . USER_PIC_PATH . $menuprofile;
        } else {
            $menuprofiles = SITEURL . 'images/logo_white.svg';
        }
        ?>
        <?php
        if ($user_theme == 'theme_default') {
            $logo_file = 'logo_white.svg';
        } else {
            $logo_file = 'logo_white.svg';
        }
        ?>

        <?php  /* <img class="logo"  src="<?php echo SITEURL . 'images/' . $logo_file ?>" alt="logo" style="background-color: transparent !important;" /> */ ?>

    </a>
    <!--
                    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
    -->

    <a class="btn btn-default toggle_header_menus" href="#">
        <span class="fa fa-bars"></span>
    </a>
<?php //pr($this->Session->read('Auth.User'),1); ?>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
				<?php
					 if( $this->Session->read('Auth.User.role_id') == 1 ){
				?>
					<li class="dropdown"><a  title="Project" href="javascript:void(0)"  class="dropdown-toggle tipText drop-icon" data-toggle="dropdown" ><i  class="fa fa-cubes"></i></a>
						<ul class="dropdown-menu">
							<li  ><a  href="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'create_workspace', 0 ), TRUE); ?>" class="" ><i class="fa fa-th"></i> Template Library </a></li>
						</ul>
					</li>
				<?php } else {

					if( $this->Session->read('Auth.User.role_id') != 3 ){
				?>
					<!-- <li><a href="#" class="tipText" title="Team Talk"><i  class="fa fa-microphone"></i></a></li> -->
					<!--<li><a href="<?php echo SITEURL ?>" class="home_icon"><i class="fa fa-home home-ic"></i></a></li>-->
					<!--<li><a href="<?php echo $this->Html->url(SITEURL . 'plans/add_feature', true); ?>">Add Features</a></li>-->
					<!--<li><a href="#" class="" >MARKETPLACE </a></li> -->
                <?php
                $dolist_count = 0;

                $counters = $this->Group->dolist_counters();
                if (isset($counters['total']) && !empty($counters['total'])) {
                    $dolist_count += $counters['total'];

                }

                $projects = $this->Group->header_dolist();
				if(isset($projects) && !empty($projects)){
                $projects = array_unique($projects);
				}



                if (isset($projects) && !empty($projects)) {
                    foreach ($projects as $toid => $prid) {

                        $projectData = project_detail($prid);
                        $pcounters = $this->Group->dolist_counters($prid);
                        if (isset($pcounters['total']) && !empty($pcounters['total'])) {
                            // $dolist_count += $pcounters['total'];
							$dolist_count += $pcounters['today_count'] + $pcounters['tom_count'] + $pcounters['up_count'] + $pcounters['ns_count'] + $pcounters['over_count'];
                        }
                    }
                }
                ?>

			   <li class="dropdown custom-drp-menu"><a  title="Work" href="javascript:void(0)"  class="dropdown-toggle tipText drop-icon" data-toggle="dropdown" ><i  class="fa fa-cubes"></i></a>
			    <ul class="dropdown-menu" id="main-dropmenu">
					<li  ><a href="<?php echo SITEURL . 'dashboards/program_center'; ?>"  ><span><i class="fa program_center_icon_logo"></i></span> Program Center </a><span></li>
					<li  ><a   href="<?php echo SITEURL . 'projects/objectives'; ?>"  ><span><i class="fa fa-dashboard"></i></span> Dashboards </a></li>

					<li  ><a href="<?php echo SITEURL . 'dashboards/project_center'; ?>"  ><span><i class="fa fa-dot-circle-o"></i></span> Project Center </a><span></li>
					<li class="task-reminder"><a href="<?php echo SITEURL . 'dashboards/task_reminder'; ?>"><span><i class="icon_reminder black"></i></span> Reminders </a></li>

					<li class="task-center"><a href="<?php echo TASK_CENTERS; ?>"><span><i class="ico-task-center"></i></span> Task Center </a><span></li>
					<li  ><a   href="<?php echo SITEURL . 'boards'; ?>"    ><span><i class="fa fa-newspaper-o"></i></span> Project Board </a></li>

					<li  ><a   href="<?php echo SITEURL . 'costs'; ?>"    ><span><i class="fa-manage-cost"></i></span> Cost Center </a></li>
					<li><a href="<?php echo SITEURL . 'studios'; ?>"><span><i class="fa fa-sitemap fa-rotate-270"></i></span> Studio </a></li>


					<li><a href="<?php echo Router::Url(array('controller' => 'risks','action' => 'index'), TRUE); ?>" class="" ><span><i class="fa fa-exclamation" aria-hidden="true"></i></span> Risk Center </a></li>
					<li  ><a  href="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'create_workspace', 0 ), TRUE); ?>" class="" ><span><i class="fa fa-th"></i></span> Template Library </a></li>

					<?php
						$sketchCount = $this->Sketch->getUnviewed();
						$sketchList = $this->Sketch->getListUnviewed();
					?>
					<li class="<?php echo isset($sketchCount) && $sketchCount > 0 ? 'dropdown-submenu':'';?>">
					 <a  href="<?php echo SITEURL.'skts/index' ?>"   class="" >
						<span><i class="fa fa-pencil-square-o"></i></span> Sketches
						<?php
						if(isset($sketchCount) && $sketchCount > 0){
							echo "<span>(".$sketchCount.")</span>";
						}

						?>
						<?php echo isset($sketchCount) && $sketchCount > 0 ? '<b class="fa fa-caret-right"></b>':'';?>
					 </a>
						<?php if(isset($sketchCount) && $sketchCount > 0){?>
						<ul class="dropdown-menu" style="right: 100%; max-height: 300px; overflow-x: hidden;overflow-y: auto;">
							<?php
							if(isset($sketchList) && !empty($sketchList)){
								foreach($sketchList as $valSketch){
									?>
									<li class="sketch-id-header-<?php echo $valSketch['ProjectSketch']['id'];?>">
										<a href="<?php echo SITEURL;?>skts/edit_sketch/project_id:<?php echo $valSketch['ProjectSketch']['project_id'];?>/sketch_id:<?php echo $valSketch['ProjectSketch']['id'];?>">
											<?php echo ucfirst($valSketch['ProjectSketch']['sketch_title']);?>
										</a>
									</li>
									<?php
								}
							}
							?>
						</ul>
					  <?php } ?>
					</li>

                        <?php
                            /* <li class="task-reminder"><a href="<?php echo SITEURL . 'dashboards/task_reminder'; ?>"><span><i class="icon_reminder black"></i></span> Reminders </a></li>
    						<li  ><a   href="<?php echo SITEURL . 'projects/objectives'; ?>"  ><span><i class="fa fa-dashboard"></i></span> Dashboards </a></li>

                            <li><a href="<?php echo SITEURL . 'studios'; ?>"><span><i class="fa fa-sitemap fa-rotate-270"></i></span> Studio </a></li>
    						<li  ><a  href="<?php echo SITEURL . 'categories/manage_categories'; ?>"  ><span><i class="glyphicon glyphicon-signal fa-rotate-ac-90"></i></span> Categories </a></li>


                            <li  ><a   href="<?php echo SITEURL . 'boards'; ?>"    ><span><i class="fa fa-newspaper-o"></i></span> Project Board </a></li>
    						<?php
    							$sketchCount = $this->Sketch->getUnviewed();
    							$sketchList = $this->Sketch->getListUnviewed();
    						?>
                            <li class="<?php echo isset($sketchCount) && $sketchCount > 0 ? 'dropdown-submenu':'';?>">
                             <a  href="<?php echo SITEURL.'skts/index' ?>"   class="" >
                                <span><i class="fa fa-pencil-square-o"></i></span> Sketches
                                <?php
                                if(isset($sketchCount) && $sketchCount > 0){
                                    echo "<span>(".$sketchCount.")</span>";
                                }

                                ?>
                                <?php echo isset($sketchCount) && $sketchCount > 0 ? '<b class="fa fa-caret-right"></b>':'';?>
                             </a>
                                <?php if(isset($sketchCount) && $sketchCount > 0){?>
                                <ul class="dropdown-menu" style="right: 100%; max-height: 300px; overflow-x: hidden;overflow-y: auto;">
                                    <?php
                                    if(isset($sketchList) && !empty($sketchList)){
                                        foreach($sketchList as $valSketch){
                                            ?>
                                            <li class="sketch-id-header-<?php echo $valSketch['ProjectSketch']['id'];?>">
                                                <a href="<?php echo SITEURL;?>skts/edit_sketch/project_id:<?php echo $valSketch['ProjectSketch']['project_id'];?>/sketch_id:<?php echo $valSketch['ProjectSketch']['id'];?>">
                                                    <?php echo ucfirst($valSketch['ProjectSketch']['sketch_title']);?>
                                                </a>
                                            </li>
                                            <?php
                                        }
                                    }
                                    ?>
                                </ul>
                              <?php } ?>
                         </li>


                            <li  ><a href="<?php echo SITEURL . 'dashboards/project_center'; ?>"  ><span><i class="fa fa-dot-circle-o"></i></span> Project Center </a><span></li>
    						<li  ><a href="javascript:"  ><span><i class="fa program_center_icon_logo"></i></span> Program Center </a><span></li>

                            <li class="task-center"><a href="<?php echo SITEURL . 'dashboards/task_center'; ?>"><span><i class="ico-task-center"></i></span> Task Center </a><span></li>
                            <li  ><a  href="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'create_workspace', 0 ), TRUE); ?>" class="" ><span><i class="fa fa-th"></i></span> Template Library </a></li>

    						<li><a href="javascript:" class="" ><span><i class="fa fa-exclamation" aria-hidden="true"></i></span> Risk Center </a></li>
                        */
                        ?>
				</ul>

			   </li>


                <li>
                    <a href="<?php echo SITEURL; ?>todos/index" style="text-transform:none !important;" class=" todolist_link <?php if ($dolist_count > 0) { ?> dropdown-toggle  drop-icon<?php } ?>" title="To-do Lists" data-original-title="To-do Lists" <?php if ($dolist_count > 0) { ?> data-toggle="dropdown" <?php } ?>>
                        <i style="font-size: 10px !important" class="fa fa-list-ul border-alt"></i>
                        <i class="bg-gray "><?php echo $dolist_count; ?></i>
                    </a>


                    <ul class="dropdown-menu">
                        <li class="dropdown-submenu todoul">
                            <?php
                            $counters = $this->Group->dolist_counters();

                            if (isset($counters['total']) && !empty($counters['total'])) {


                                // pr($d);
                                ?>
                                <a href="<?php echo SITEURL; ?>todos/index" class="">
                                    Unspecified Project (<?php echo $counters['total']; ?>)
                                    <b class="fa fa-caret-right"></b>
                                </a>

                                <ul class="dropdown-menu" style="left: auto; right: 100%;">
                                    <?php
                                    $countActive = $counters['active_count'];
                                    $countToday = $counters['today_count'];
                                    $countTomorrow = $counters['tom_count'];
                                    $countUpcoming = $counters['up_count'];
                                    $countNotset = $counters['ns_count'];
                                    $countOverdue = $counters['over_count'];
                                    $countCompleted = $counters['com_count'];
                                    $archive = $counters['archive'];
                                    ?>
                                    <li class="men">
                                        <a class="text-red" href="<?php echo SITEURL; ?>todos/index/day:active">
                                            Active <span class="badge bg-red"><?php //echo $countActive; ?></span>
                                        </a>
                                    </li>
                                    <li class="men">
                                        <a href="<?php echo SITEURL; ?>todos/index/day:today">
                                            Today <span class="badge bg-red"><?php echo $countToday; ?></span>
                                        </a>
                                    </li>
                                    <li class="men">
                                        <a href="<?php echo SITEURL; ?>todos/index/day:tomorrow">
                                            Tomorrow <span class="badge bg-red"><?php echo $countTomorrow; ?></span>
                                        </a>
                                    </li>
                                    <li class="men">
                                        <a href="<?php echo SITEURL; ?>todos/index/day:upcoming">
                                            Upcoming <span class="badge bg-red"><?php echo $countUpcoming; ?></span>
                                        </a>
                                    </li>

                                    <li class="men">
                                        <a href="<?php echo SITEURL; ?>todos/index/day:notset">
                                            Not Set <span class="badge bg-red"><?php echo $countNotset; ?></span>
                                        </a>
                                    </li>
                                  <!--  <li class="men">
                                        <a href="<?php echo SITEURL; ?>todos/index/day:completed">
                                            Completed <span class="badge bg-red"><?php echo $countCompleted; ?></span>
                                        </a>
                                    </li>-->
                                    <li class="men">
                                        <a href="<?php echo SITEURL; ?>todos/index/day:overdue">
                                            Overdue <span class="badge bg-red"><?php echo $countOverdue; ?></span>
                                        </a>
                                    </li>
									<?php /*  ?>
									<li class="men">
										<a class="text-red" href="javascript:void(0)">
											Not Active
										</a>
									</li>

									<li class="men">
                                        <a href="<?php echo SITEURL; ?>todos/index/day:completed">
                                            Completed <span class="badge bg-red"><?php echo $countCompleted; ?></span>
                                        </a>
                                    </li>
											<li class="men">
                                                <a href="<?php echo SITEURL; ?>todos/index/day:completed">
                                                    Archived <span class="badge bg-red"><?php echo $archive; ?></span>
                                                </a>
                                            </li>
									<?php  */ ?>
                                </ul>
                            <?php } ?>
                        </li>
                        <?php
                        $projects = $this->Group->header_dolist();
						if(isset($projects) && !empty($projects)){
						$projects = array_unique($projects);
						}
                        ?>


<?php
if (isset($projects) && !empty($projects)) {
    foreach ($projects as $toid => $prid) {
        $projectData = project_detail($prid);

        $counters = $this->Group->dolist_counters($prid);
		// pr($counters );
        if ((isset($counters['total']) && !empty($counters['total'])) && !empty($projectData)) {
            // pr($d);
            ?>
                                    <li class="dropdown-submenu todoul">
                                        <a href="<?php echo SITEURL; ?>todos/index/project:<?php echo $prid; ?>" class="">
            <?php echo strip_tags($projectData['title']); ?> (<?php echo $counters['total']; ?>)
                                            <b class="fa fa-caret-right"></b>
                                        </a>

                                        <ul class="dropdown-menu" style="left: auto; right: 100%;">
            <?php
            $countActive = $counters['active_count'];
            $countToday = $counters['today_count'];
            $countTomorrow = $counters['tom_count'];
            $countUpcoming = $counters['up_count'];
            $countNotset = $counters['ns_count'];
            $countOverdue = $counters['over_count'];
            $countCompleted = $counters['com_count'];
            $archive = $counters['archive'];
            ?>
                                            <li class="men">
                                                <a class="text-red" href="<?php echo SITEURL; ?>todos/index/project:<?php echo $prid; ?>/day:active">
                                                    Active <span class="badge bg-red"><?php //echo $countActive; ?></span>
                                                </a>
                                            </li>
                                            <li class="men">
                                                <a href="<?php echo SITEURL; ?>todos/index/project:<?php echo $prid; ?>/day:today">
                                                    Today <span class="badge bg-red"><?php echo $countToday; ?></span>
                                                </a>
                                            </li>
                                            <li class="men">
                                                <a href="<?php echo SITEURL; ?>todos/index/project:<?php echo $prid; ?>/day:tomorrow">
                                                    Tomorrow <span class="badge bg-red"><?php echo $countTomorrow; ?></span>
                                                </a>
                                            </li>
                                            <li class="men">
                                                <a href="<?php echo SITEURL; ?>todos/index/project:<?php echo $prid; ?>/day:upcoming">
                                                    Upcoming <span class="badge bg-red"><?php echo $countUpcoming; ?></span>
                                                </a>
                                            </li>

                                            <li class="men">
                                                <a href="<?php echo SITEURL; ?>todos/index/project:<?php echo $prid; ?>/day:notset">
                                                    Not Set <span class="badge bg-red"><?php echo $countNotset; ?></span>
                                                </a>
                                            </li>

                                            <li class="men">
                                                <a href="<?php echo SITEURL; ?>todos/index/project:<?php echo $prid; ?>/day:overdue">
                                                    Overdue <span class="badge bg-red"><?php echo $countOverdue; ?></span>
                                                </a>
                                            </li>
											<?php /*  ?>
											<li class="men">
                                                <a class="text-red" href="javascript:void(0)<?php //echo SITEURL; ?>todos/index/project:<?php echo $prid; ?>/day:completed">
                                                    Not Active
                                                </a>
                                            </li>

											<li class="men">
                                                <a href="<?php echo SITEURL; ?>todos/index/project:<?php echo $prid; ?>/day:completed">
                                                    Completed <span class="badge bg-red"><?php echo $countCompleted; ?></span>
                                                </a>
                                            </li>

											<li class="men">
                                                <a href="<?php echo SITEURL; ?>todos/index/project:<?php echo $prid; ?>/day:completed">
                                                    Archived <span class="badge bg-red"><?php echo $archive; ?></span>
                                                </a>
                                            </li>
											<?php  */ ?>
                                        </ul>
        <?php }
    }
    ?>
                            </li>
                                <?php }
                            ?>


                    </ul>


<?php //}  ?>
                </li>



               <!-- <li><a href="<?php echo SITEURL ?>boards" class="tipText" title="Project Board"><i  class="fa fa-newspaper-o"></i></a></li>-->



                <li>
					<li class=""><a href="javascript:void(0)" id="chat_icon" class="tipText  dropdown-toggle dropdown-submenu" data-toggle="dropdown" title="Communication" ><i  class="fa fa-comment"></i><i class="bg-gray counter" style="border-radius: 3px; font-size: 13px; line-height: 0.9; padding: 2px 3px; position: absolute; right: 3px; text-align: center; top: 13px; display: none;">6</i>	</a></li>
				</li>

<?php
$user_notifications = get_notifications($this->Session->read('Auth.User.id'));
$notify_flag = false;
$unread_flag = 0;
if(isset($user_notifications) && !empty($user_notifications)){
    $notify_flag = count($user_notifications);
    $unread_counter = arraySearch($user_notifications, 'viewed', '0');
    if(isset($unread_counter) && !empty($unread_counter)){
        $unread_flag = count($unread_counter);
    }
}

?>
   <li class="dropdown" id="notify-dropmenu">
        <a href="javascript:void(0)" class="dropdown-toggle" title="Notifications" <?php if($notify_flag){ ?> data-toggle="dropdown" <?php } ?> id="notify_bell" >
            <i  class="fa fa-bell"></i>
            <i class="bg-gray bell-count"><?php echo $unread_flag; ?></i>
        </a>
        <ul class="dropdown-menu" id="notify-sub-drop">
			  <div class="clear-all-notify">
                <span class="prj-notify-text text-bold">Project Notifications</span>
                <span class="pull-right">
                    <strong>All</strong>
                    <a href="#" class="close-top"><i class="fa fa-close"></i></a>
                </span>
            </div>
            <div class="notify-scrollone">
                <span class="aaaaaaaa"></span>
            <?php if($notify_flag){
            foreach ($user_notifications as $key => $value) {
                $data = $value['UserNotification'];
            ?>
            <li <?php if($data['viewed'] == 0){ ?> class="unread" <?php } ?> data-id="<?php echo $data['id']; ?>">
                <?php  // add project id link
                /*if (($data['type'] != '' && $data['type'] == 'risk') && (isset($data['project_id']) && !empty($data['project_id']) && dbExists('Project', $data['project_id']))) {
                ?>
                <a href="<?php echo Router::Url( array( "controller" => "risks", "action" => "index", $data['project_id'], 'admin' => FALSE ), true ); ?>" class="notify-link">
                <?php
                }
                else if (($data['type'] != '' && $data['type'] == 'reminder') && (isset($data['project_id']) && !empty($data['project_id']) && dbExists('Project', $data['project_id']))) {
                ?>
                <a href="<?php echo Router::Url( array( "controller" => "dashboards", "action" => "task_reminder", "project" => $data['project_id'], 'admin' => FALSE ), true ); ?>" class="notify-link">
                <?php
                }
                else {
                    if (isset($data['project_id']) && !empty($data['project_id']) && dbExists('Project', $data['project_id'])) {
                ?>
                    <a href="<?php echo Router::Url( array( "controller" => "projects", "action" => "index", $data['project_id'], 'admin' => FALSE ), true ); ?>" class="notify-link">
                <?php
                    }
                }*/

                if(isset($data['project_id']) && !empty($data['project_id']) && dbExists('Project', $data['project_id'])){ ?>
                    <a href="<?php echo Router::Url( array( "controller" => "projects", "action" => "index", $data['project_id'], 'admin' => FALSE ), true ); ?>" class="notify-link">
                <?php } ?>
                <?php if(isset($data['subject']) && !empty($data['subject'])){ ?>
                <strong class="title"><?php echo $data['subject']; ?></strong>
                <?php } ?>
                <?php if(isset($data['heading']) && !empty($data['heading'])){ ?>
                <span class="notify-info notify-heading"><?php echo $data['heading']; ?></span>
                <?php } ?>
                <?php if(isset($data['sub_heading']) && !empty($data['sub_heading'])){ ?>
                <span class="notify-info notify-heading"><?php echo $data['sub_heading']; ?></span>
                <?php } ?>
                <span class="notify-info">
                    <?php if(isset($data['creator_name']) && !empty($data['creator_name'])){
                    $creator_name = $data['creator_name']; ?>
                        <?php if($data['created_id'] == $this->Session->read('Auth.User.id') && $data['receiver_id'] == $this->Session->read('Auth.User.id')){
                            $creator_name = 'Me';
                        } ?>
                        <span>By: <?php echo $creator_name; ?>, </span>
                    <?php } ?>
                    <?php if(isset($data['date_time']) && !empty($data['date_time'])){ ?><span> <?php echo date('d M Y, h:i A', strtotime($data['date_time'])); ?></span><?php } ?>
                </span>
                <?php // add project id link
                if(isset($data['project_id']) && !empty($data['project_id']) && dbExists('Project', $data['project_id'])){ ?>
                    </a>
                <?php } ?>
                <a class="read-bottom <?php if($data['viewed'] > 0){ ?> viewed <?php } ?> " href="#">
                   <i class="fa fa-check"></i>
                </a>
                <a class="close-bottom" href="#">
                   <i class="fa fa-close"></i>
                </a>
            </li>
            <?php }
            } ?>

            </div>
        </ul>

    </li>

                <li><a href="<?php echo SITEURL."searches/" ; ?>" class="tipText" title="Smart Search"><i class="fa fa-search"></i></a></li>


<?php
 if ( (isset($project_id) && !empty($project_id)))
$dataOwner = $this->ViewModel->projectPermitType($project_id  , $this->Session->read('Auth.User.id') );
 if ( (isset($project_id) && !empty($project_id) && $this->params['controller'] != 'costs')  && $dataOwner == 1 ) { ?>
                    <li class="dropdown">
                       <a href="#" class="dropdown-toggle tipText drop-icon" data-toggle="dropdown" title="Export"><i class=" fa fa-book"></i></a>
                        <!-- Drop-Down Menu-->
                        <ul class="dropdown-menu">
                                <!--<li><a data-toggle="modal" data-modal-width="600" class="tipText" title="Select work space" href="<?php echo SITEURL ?>projects/exportwsp/<?php echo $project_id; ?>/doc" data-target="#modal_medium" rel="tooltip" ><i class="fa fa-file-word-o text-black"></i> Word</a></li>

                                <li><a data-toggle="modal" data-modal-width="600" class="tipText" title="Select work space" href="<?php echo SITEURL ?>projects/exportwsp/<?php echo $project_id; ?>/ppt" data-target="#modal_medium" rel="tooltip" ><i class="fa fa-file-powerpoint-o text-black"></i> Power Point</a></li>

                                <li><a data-toggle="modal" data-modal-width="600" class="tipText" title="Select work space" href="<?php echo SITEURL ?>projects/exportwsp/<?php echo $project_id; ?>" data-target="#modal_medium" rel="tooltip" ><i class="fa fa-file-pdf-o text-black"></i> PDF</a></li>-->


                            <li>
                                <a data-toggle="modal" data-target="#modal_medium" data-modal-width="600" class="tipTexts msword_doc" href="<?php echo SITEURL;?>export_datas/index/project_id:<?php echo $project_id;?>" rel="tooltip" >
                                    <i class="fa fa-file-word-o text-black"></i>MS Word
                                </a>
<!--                                <a class="tipTexts msword_doc"  >
                                    <i class="fa fa-file-word-o text-black"></i>MS Word
                                </a>-->
                            </li>
                            <?php if($this->params['controller'] == 'costs'){ ?>
                            <li>
								<a data-toggle="modal" id="exportdatasw" data-target="#modal_medium" data-modal-width="600" class="tipTexts msword_xls" href="<?php echo SITEURL;?>costs/export_xls/project_id:<?php echo $project_id;?>" rel="tooltip" >
                                    <i class="fa fa-file-excel-o text-black"></i>MS Excel
                                </a>

                            </li>
                            <?php } ?>

                           <!-- <li><a   data-modal-width="600" class="tipTexts" data-target="#modal_medium" rel="tooltip" ><i class="fa fa-file-text text-blue"></i>Google Docs</a></li>-->

                            <!--<li><a   data-modal-width="600" class="tipText" title="Select work space"   data-target="#modal_medium" rel="tooltip" ><i class="fa fa-file-pdf-o text-black"></i> PDF</a></li>-->
                        </ul>
                    </li>
<?php } else if ( (isset($project_id) && !empty($project_id))){
 if($dataOwner == 1){ ?>
					<li class="dropdown" id="exportdatafiles" style="display:none;">
                       <a href="#" class="dropdown-toggle tipText drop-icon" data-toggle="dropdown" title="Export"><i class=" fa fa-book"></i></a>
                        <ul class="dropdown-menu">
							<?php if( isset($project_id) && !empty($project_id) ){ ?>
							<li>
                                <a data-toggle="modal" data-target="#modal_medium" data-modal-width="600" class="tipTexts msword_doc" href="<?php echo SITEURL;?>export_datas/index/project_id:<?php echo isset($project_id) ? $project_id : '' ;?>" rel="tooltip" >
                                    <i class="fa fa-file-word-o text-black"></i>MS Word
                                </a>
                            </li>
                            <?php }
							if( isset($this->params['controller']) && $this->params['controller'] == 'costs'){ ?>
                            <li>
								<a data-toggle="modal" id="exportdatasw" data-target="#modal_medium" data-modal-width="600" class="tipTexts msword_xls" href="<?php echo SITEURL;?>costs/export_xls/project_id:<?php echo $project_id;?>" rel="tooltip" >
                                    <i class="fa fa-file-excel-o text-black"></i>MS Excel
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
<?php } }else{ ?>
<li class="dropdown" id="exportdatafiles" style="display:none;">
                       <a href="#" class="dropdown-toggle tipText drop-icon" data-toggle="dropdown" title="Export"><i class=" fa fa-book"></i></a>
                        <ul class="dropdown-menu">
							<?php if( isset($project_id) && !empty($project_id) ){ ?>
							<li>
                                <a data-toggle="modal" data-target="#modal_medium" data-modal-width="600" class="tipTexts msword_doc" href="<?php echo SITEURL;?>export_datas/index/project_id:<?php echo isset($project_id) ? $project_id : '' ;?>" rel="tooltip" >
                                    <i class="fa fa-file-word-o text-black"></i>MS Word
                                </a>
                            </li>
                            <?php }
							if( isset($this->params['controller']) && $this->params['controller'] == 'costs'){ ?>
                            <li>
								<a data-toggle="modal" id="exportdatasw" data-target="#modal_medium" data-modal-width="600" class="tipTexts msword_xls" href="<?php echo SITEURL;?>costs/export_xls/project_id:<?php echo $project_id;?>" rel="tooltip" >
                                    <i class="fa fa-file-excel-o text-black"></i>MS Excel
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>

<?php }



 }
?>



 <li><a href="<?php echo SITEURL.'docs/user/index.htm';  ?>" target="_blank" class="tipText" title="Online Help"><i class="fa  fa-question-circle-o"></i></a></li>

 <li class="dropdown"><a  data-toggle='dropdown' class="dropdown-toggle drop-icon" data-placement="bottom"  href="javascript:void(0)"  ><i class="fa fa-ellipsis-v"></i></a>

<ul class="dropdown-menu" style="min-width: 235px;">

	<li class=""><a href="<?php echo $this->Html->url( SITEURL.'contactus', true ); ?>" target="_blank">Contact Us</a></li>

	<li class="mega-dropdown show_hide_about"><a href="javascript:void(0);" class="dropdown-toggle">About IdeasCast OpusView</a></li>


	<li style="" id="about_ideascost">
		<div class="">
			<div class="i-about-top">
				<span class="i-jeera-image"></span>
				<span class="i-about-jeera">
					<b class="i-jeera-name"><img src="<?php echo SITEURL; ?>images/about-green-logo.png" alt="about-green-logo"></b>
					<i class="i-jeera-tagline">Business Social Software<!--Enterprise social software--></i>
				</span>
			</div>
				<div class="i-about-mid">Version: <?php echo $this->Common->jeeraversion(); ?></div>
				<div class="i-about-bottom"> &copy; <?php echo date('Y')?> IdeasCast Limited. All rights reserved.</div>
		</div>
	</li>

	<li class=""><a href="mailto:support@Ideascast.com?subject=Support">Support</a></li>


 </ul>
</li>

                <li class="dropdown" style="display:none;">
                    <a href="#" class="dropdown-toggle tipText" data-toggle="dropdown" data-placement="bottom" title="User Setting"><i  class=" fa fa-fw fa-gear"></i></a>

                    <!-- Drop-Down Menu-->
                    <ul class="dropdown-menu">

                       <!-- <li>
							<?php $editURL = SITEURL . 'shares/show_profile/'.$this->Session->read('Auth.User.id'); ?>
                            <a href="<?php echo $editURL; ?>" id="trigger_edit_profile" data-target="#popup_modal"  data-toggle="modal"><i class="fa fa-fw fa-user"></i> Profile</a>
                        </li>-->

<?php
//$dat = $this->Common->getUpload(92);  pr($dat);
$parameter = $this->Session->read('Auth.User.id');
$output = $this->requestAction('users/getUpload/' . $parameter);

$output['UserPlan']['is_active'] = 1;

if (isset($output['UserPlan']['is_active']) && $output['UserPlan']['is_active'] == '1') {  //die;
    ?>
                            <li><a id="trigger_uploads" data-toggle="modal" data-target="#popup_modal" href="<?php echo SITEURL . 'users/profile'; ?>"><i class="fa fa-fw fa-camera-retro"></i> Images </a>
                            </li>

<?php } else { ?>
                            <li>
                                <a data-content="To add this feature go to the ADD FEATURE page." title="" data-trigger="hover" data-toggle="popover" role="button" class="toltipover" data-placement="top" tabindex="0" data-original-title=""  href="javascript:void(0)"><i class="fa fa-fw fa-upload"></i> Images </a>
                            </li>
<?php } ?>

                        <li><a href="" data-remote="<?php echo SITEURL . 'settings/themes'; ?>" data-toggle="modal" data-backdrop="true" data-target="#popup_modal"><i class="fa fa-fw fa-adjust"></i> Themes </a></li>

                        <li><a href="" data-remote="<?php echo SITEURL . 'settings/start_page'; ?>" data-toggle="modal" data-backdrop="true" data-target="#popup_modal"><i class="custom_homeicon"></i> Start Page </a></li>


                    </ul>
                </li>

                <!-- <li><a class="tipText" title="Sign Out" href="<?php echo SITEURL . 'users/logout'; ?>"><i data-placement="bottom"  class=" fa fa-fw fa-power-off"></i> </a> </li> -->


<?php
}
 /*
  if(AuthComponent::user('id')): ?>

  <li><a class="btn btn-sm btn-warning goto-ideascost" href="<?php echo SITEURL?>"><i class="fa fa-caret-right"></i> Go to Ideascast</a> </li>

  <?php else: ?>
  <li><a class="btn btn-sm btn-warning goto-ideascost" href="<?php echo SITEURL?>/projects/lists"><i class="fa fa-caret-right"></i> Go to Home</a> </li>
  <?php endif; */ ?>
            </ul>
        </div>
    </nav>
</header>

<!-- ---- Record Edit Box ---- -->
<div class="modal fade" id="Recordedit" tabindex="-1" role="dialog"  aria-hidden="true"></div>

<!-- Modal -->
<div class="modal modal-success fade" id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content"></div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->

<!-- Modal -->
<div class="modal modal-success fade" id="modal_notifications" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->

<div class="modal fade" id="Recordview" tabindex="-1" role="dialog" aria-hidden="true"></div>

<!------ Record Delete Alert Message ------>
<div class="modal fade" id="deleteBox" tabindex="-1" role="dialog" aria-hidden="true">
<?php echo $this->Form->create('', array('type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'RecordDeleteForm')); ?>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Are you sure, you would like to delete this record?</h4>
            </div>
            <input type="hidden" id="recordDeleteID" name='data[id]' />
            <div class="modal-footer clearfix bordertopnone">
                    <button type="submit" class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Delete</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><!--<i class="fa fa-times"></i>--> Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</form>
</div><!-- /.modal -->
<!------ Record Delete Alert Message ------>


<!------ Record Status Update Confirmation Message ------>
<div class="modal fade" id="StatusBox" tabindex="-1" role="dialog" aria-hidden="true">
<?php echo $this->Form->create('', array('type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'RecordStatusFormId')); ?>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Are you sure, you would like to <span id="statusname"></span> this record?</h4>
            </div>
            <input type="hidden" id="recordID" name='data[id]' />
            <input type="hidden" id="recordStatus" name='data[status]' />
            <div class="modal-footer clearfix bordertopnone">
                <button type="submit" class="btn btn-success"><i class="fa fa-fw fa-check"></i> Yes</button>
                <button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> No</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</form>
</div><!-- /.modal -->
<!------ Record Status Update Confirmation Message ------>

<!-- Modal Small -->
<div class="modal modal-success fade " id="smallModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content"></div>
    </div>
</div>
<!-- /.modal -->


<div class="modal modal-warning fade" id="modal_info">
    <div class="modal-dialog">
        <div class="modal-content border-radius">
            <div class="modal-header" style="padding: 5px 10px">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Information </h4>
            </div>
            <div class="modal-body"> </div>
            <div class="modal-footer" style="padding: 5px 10px">
                <button type="button" class="btn btn-danger" data-dismiss="modal">OK</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal modal-success fade" id="popup_model_box">
    <div class="modal-dialog">
        <div class="modal-content border-radius">
            <div class="modal-header" style="padding: 5px 10px">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Information </h4>
            </div>
            <div class="modal-body"> </div>
            <div class="modal-footer" style="padding: 5px 10px">
                <button type="button" class="btn btn-danger" data-dismiss="modal">OK</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script type="text/javascript" >
    $(function () {


        $.fn.data_loader = function () {

            var dfd = new jQuery.Deferred();

            var $this = $(this);

            $this.show().width('100%');
            setTimeout(function () {
                $this.animate({
                    opacity: 0
                }, 1500, function () {
                    $this.hide().css({width: 0, opacity: 1})
                    dfd.resolve();
                    return true;
                })
            }, 1000)
            return dfd.promise();


        }

        $('#popup_modal').on('hidden.bs.modal', function () {
            $(this).removeData('bs.modal');
        });

        $('#smallModal').on('hidden.bs.modal', function () {
            $(this).removeData('bs.modal');
        });

    })
</script>
<script type="text/javascript" >

// Submit form to delete records
    $(document).on('submit', '#RecordDeleteForm', function (e) {
        var postData = $(this).serializeArray();
        var formURL = $(this).attr("action");
        $.ajax({
            url: formURL,
            type: "POST",
            data: postData,
            async: false,
            success: function (response) {
                $('.modal').modal('hide').data('bs.modal', null);
                location.reload();
            }
        });
        e.preventDefault(); //STOP default action
    });

// Submit form to update status
    $(document).on('submit', '#RecordStatusFormId', function (e) {
        var postData = $(this).serializeArray();
        var formURL = $(this).attr("action");
        $.ajax({
            url: formURL,
            type: "POST",
            data: postData,
            async: false,
            success: function (response) {
                $('.modal').modal('hide').data('bs.modal', null);
                location.reload();
            }
        });
        e.preventDefault(); //STOP default action
    });


// Open Admin Profile Form
    $(document).on('click', '#trigger_edit_profile', function (e) {
        var formURL = $(this).attr('href') // Extract info from data-* attributes
        $.ajax({
            url: formURL,
            async: false,
            success: function (response) {
                if ($.trim(response) != 'success') {
                    $('#popup_modal').html(response);
                } else {
                    location.reload(); // Saved successfully
                }
            }
        });
        e.preventDefault();
    })


    $(document).on('click', '#trigger_uploads', function (e) {
        $('#popup_modal').modal('hide');
        var formURL = $(this).attr('href') // Extract info from data-* attributes
        $.ajax({
            url: formURL,
            async: false,
            success: function (response) {
                if ($.trim(response) != 'success') {
                    $('#popup_modal').html(response);
                } else {
                    location.reload(); // Saved successfully
                }
            }
        });
        e.preventDefault();
    })


    $(document).on('click', '.rht', function (e) {
        $('#popup_modal').modal('hide');
        var formURL = $(this).attr('href') // Extract info from data-* attributes
        $.ajax({
            url: formURL,
            async: false,
            success: function (response) {
                if ($.trim(response) != 'success') {
                    $('#popup_modal').html(response);
                } else {
                    location.reload(); // Saved successfully
                }
            }
        });
        e.preventDefault();
    })




</script>
<script type="text/javascript" >
    $(document).ready(function () {

        // initilize popover tooltip message
        $('[data-toggle="popover"]').popover({container: 'body', html: true/* , placement: "auto" */});

		 $(".getelements").trigger("click");

		/* $('navbar-nav .tipText').tooltip({
         container: 'body',html: true,placement:"bottom"
         }); */

    });

</script>
<script type="text/javascript" > var SITEURL = '<?php echo SITEURL; ?>'</script>
<?php

//setcookie("CAKEPHP", session_id(), time()+3600,'/');  /* expire in 1 hour */
//setcookie("CURRENT_USER", $this->Session->read('Auth.User.id'), time()+3600,'/');  /* expire in 1 hour */
//setcookie("CURRENT_EXPIRE", time()+3600, time()+3600,'/');  /* expire in 1 hour */

 ?>

<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="modal_medium" class="modal modal-success fade">
    <div class="modal-dialog modal-md">
        <div class="modal-content">



        </div>
    </div>
</div>
