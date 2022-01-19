<!-- Right side column. Contains the navbar and content of the page -->
<div class="row">
<script type="text/javascript" >
    $(function () {

        $('.toggle_color').click(function (event) {
            event.preventDefault();
            var target = $(this).next('.color_box');
            $(target).slideToggle(200);
            $(".wrapper").css('overflow', 'visible')
        });
        
        $('body').delegate('.el_color_box', 'click', function (event) {
            event.preventDefault()
            var dataremote = $(this).attr("data-remote");
            var boxtitle = $(this).parent().data("boxtitle");
            var itemid = $(this).parent().attr("itemid");
            var classes = $('#'+itemid).attr("class");
            var color_code  = $(this).attr("data-color");
			
            //alert(classes);
            console.log("data-remote : "+dataremote+" itemid : "+itemid+" data-color : "+color_code +" classes : "+classes+" title:"+boxtitle);
			
            var foundClass = (classes.match(/(^|\s)bg-\S+/g) || []).join('')
            if (foundClass != '') {
                    $('#'+itemid).removeClass(foundClass)
            }
            $('#'+itemid).addClass(color_code)
			
            $.ajax({
                type: "POST",
                url: dataremote,                
				data: $.param({ 'slug': itemid, 'color_code' : color_code,'title':boxtitle}),               
				global: true,               
                success:function(response){ 					
					//location.reload();
				}
            });
            return;
        })
        
    });

</script>
    <div class="col-xs-12">
        <div class="box ">
            <!-- Content Header (Page header) -->

            <ol class="breadcrumb">
                <li><a href="<?php echo SITEURL; ?>dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <!--<li class="active">Dashboard</li>-->
            </ol>
        </div>
        <?php echo $this->Session->flash(); ?>
        <!-- Main content -->
        <section class="content">

            <!-- Small boxes (Stat box) -->
            <?php if ($this->Session->read('Auth.Admin.User.role_id') == 1) { ?>
                <div class="row">
					
					<div class="col-lg-4 col-xs-8">
                        <!-- small box -->
                        <?php $organisation = $this->requestAction(array("controller" => "dashboards", "action" => "get_color_code", "organisation")); ?>
                        <div class="small-box <?php echo $organisation;?>" id="organisation">
                            <div class="inner">
                                <h3>
								<?php 
									echo $this->Common->totalOrgData('User'); 
								?></h3>
                                <p>Organizations</p>
                                <p><a href="<?php echo SITEURL; ?>sitepanel/organisations/index/sort:User.status/direction:asc">Inactive (<?php echo $this->Common->totalOrgInactiveU('User'); ?>)</a>  <a href="<?php echo SITEURL; ?>sitepanel/organisations/index/sort:User.status/direction:desc" >/ Active (<?php echo $this->Common->totalOrgActiveU('User'); ?>)</p>
                            </div>


                            <div class="icon">
                                <i class="fa fa-building"></i>
                            </div>
                            <div class="small-box-footer" style="text-align: left; padding: 10px">
                                <a href="<?php echo SITEURL; ?>sitepanel/organisations" class="more-info">More info <i class="fa fa-arrow-circle-right"></i></a>
                                <div class="color_picker pull-right">
                                    <a href="#color_pick" style="" class=" btn btn-default btn-xs toggle_color tipText" title="Color Options" >
                                        <i class="fa fa-paint-brush"></i>
                                    </a>
                                    <div id="color_pick" class="color_box color_box_bottom" >
                                        <div class="colors" itemid="organisation">
                                            <a href="#" data-color="bg-red" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
                                            <a href="#" data-color="bg-blue" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="bg-maroon" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="bg-aqua" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="bg-yellow" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="bg-green" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="bg-teal" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="bg-purple" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="bg-navy" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div><!-- ./col -->
					
                    <div class="col-lg-4 col-xs-8">
                        <!-- small box -->
                        <?php $users = $this->requestAction(array("controller"=>"dashboards","action"=>"get_color_code","users"));?>
                        <div class="small-box <?php echo $users;?>" id="users">
                            <div class="inner">
                                <h3><?php echo $this->Common->totalDataU('User'); ?></h3>
                                <p>Internal IdeasCast Users</p>                

                                <p><a href="<?php echo SITEURL; ?>sitepanel/users/index/sort:User.status/direction:asc">Inactive (<?php echo $this->Common->totalInactiveU('User'); ?>)</a>  <a href="<?php echo SITEURL; ?>sitepanel/users/index/sort:User.status/direction:desc" >/ Active (<?php echo $this->Common->totalActiveU('User'); ?>)</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-group"></i>
                            </div>
                            <div class="small-box-footer" style="text-align: left; padding: 10px">
                                <a href="<?php echo SITEURL; ?>sitepanel/users" class="more-info">More info <i class="fa fa-arrow-circle-right"></i></a>
                                <div class="color_picker pull-right">
                                    <a href="#color_pick" style=" " class=" btn btn-default btn-xs toggle_color tipText" title="Color Options" >
                                        <i class="fa fa-paint-brush"></i>
                                    </a>
                                    <div id="color_pick" class="color_box color_box_bottom" >
                                        <div class="colors" itemid="users">
                                            <a href="#" data-color="bg-red" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
                                            <a href="#" data-color="bg-blue" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="bg-maroon" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="bg-aqua" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="bg-yellow" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="bg-green" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="bg-teal" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="bg-purple" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="bg-navy" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- ./col -->
					
					<div class="col-lg-4 col-xs-8">
                        <!-- small box -->
                        <?php $pages = $this->requestAction(array("controller"=>"dashboards","action"=>"get_color_code","pages"));?>
                        <div class="small-box <?php echo $pages;?>" id="pages">
                            <div class="inner">
                                <h3><?php echo $this->Common->totalData('Page'); ?></h3>
                                <p>Pages</p>
                                <p><a href="<?php echo SITEURL; ?>sitepanel/pages/index/sort:Page.status/direction:asc">Inactive (<?php echo $this->Common->totalInactive('Page'); ?>)</a>  <a href="<?php echo SITEURL; ?>sitepanel/pages/index/sort:Page.status/direction:desc" >/ Active (<?php echo $this->Common->totalActive('Page'); ?>)</p>

                            </div>



                            <div class="icon">
                                <i class="fa  fa-file-text"></i>
                            </div>
                            <div class="small-box-footer" style="text-align: left; padding: 10px">
                                <a href="<?php echo SITEURL; ?>sitepanel/pages" class="more-info">More info <i class="fa fa-arrow-circle-right"></i></a>
                                <div class="color_picker pull-right">
                                    <a href="#color_pick" style=" " class=" btn btn-default btn-xs toggle_color tipText" title="Color Options" >
                                        <i class="fa fa-paint-brush"></i>
                                    </a>
                                    <div id="color_pick" class="color_box color_box_bottom" >
                                        <div class="colors" itemid="pages">
                                            <a href="#" data-color="bg-red" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
                                            <a href="#" data-color="bg-blue" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="bg-maroon" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="bg-aqua" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="bg-yellow" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="bg-green" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="bg-teal" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="bg-purple" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="bg-navy" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div><!-- ./col -->
					
                    <?php /* ?><div class="col-lg-4 col-xs-8">
                        <!-- small box -->
                        <?php $blogpost = $this->requestAction(array("controller" => "dashboards", "action" => "get_color_code", "blog")); ?>
                        <div class="small-box <?php echo $blogpost;?>" id="blog">
                            <div class="inner">
                                <h3><?php echo $this->Common->totalData('Post'); ?></h3>
                                <p>Blogs</p>
                                <p>&nbsp;</p>
                            </div>


                            <div class="icon">
                                <i class="fa fa-rss"></i>
                            </div>
                            <div class="small-box-footer" style="text-align: left; padding: 10px">
                                <a href="<?php echo SITEURL; ?>sitepanel/posts" class="more-info">More info <i class="fa fa-arrow-circle-right"></i></a>
                                <div class="color_picker pull-right">
                                    <a href="#color_pick" style="" class=" btn btn-default btn-xs toggle_color tipText" title="Color Options" >
                                        <i class="fa fa-paint-brush"></i>
                                    </a>
                                    <div id="color_pick" class="color_box color_box_bottom" >
                                        <div class="colors" itemid="blog">
                                            <a href="#" data-color="bg-red" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
                                            <a href="#" data-color="bg-blue" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="bg-maroon" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="bg-aqua" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="bg-yellow" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="bg-green" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="bg-teal" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="bg-purple" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="bg-navy" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div><!-- ./col --><?php */ ?>
					
					
					<div class="col-lg-4 col-xs-8" style="display:none;">
                        <!-- small box -->
                        <?php $announcements = $this->requestAction(array("controller"=>"dashboards","action"=>"get_color_code","announcements"));?>
                        <div class="small-box <?php echo $announcements;?>" id="announcements">
                            <div class="inner">
                                <h3><?php echo $this->Common->totalData('Announcement'); ?></h3>
                                <p>Announcements</p>
                                <p><a href="<?php echo SITEURL; ?>sitepanel/announcements/index/sort:Announcement.status/direction:asc">Inactive (<?php echo $this->Common->totalInactive('Announcement'); ?>)</a>  <a href="<?php echo SITEURL; ?>sitepanel/announcements/index/sort:Announcement.status/direction:desc" >/ Active (<?php echo $this->Common->totalActive('Announcement'); ?>)</p>

                            </div> 
                            <div class="icon">
                                <i class="fa  fa-file-text"></i>
                            </div>
                            <div class="small-box-footer" style="text-align: left; padding: 10px">
                                <a href="<?php echo SITEURL; ?>sitepanel/announcements" class="more-info">More info <i class="fa fa-arrow-circle-right"></i></a>
                                <div class="color_picker pull-right">
                                    <a href="#color_pick" style=" " class=" btn btn-default btn-xs toggle_color tipText" title="Color Options" >
                                        <i class="fa fa-paint-brush"></i>
                                    </a>
                                    <div id="color_pick" class="color_box color_box_bottom" >
                                        <div class="colors" data-boxtitle="Announcements" itemid="announcements">
                                            <a href="#" data-color="bg-red" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
                                            <a href="#" data-color="bg-blue" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="bg-maroon" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="bg-aqua" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="bg-yellow" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="bg-green" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="bg-teal" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="bg-purple" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="bg-navy" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div><!-- ./col -->
					
					
                   <?php /* <div class="col-lg-4 col-xs-8">-->
                        <!-- small box -->
                      <!--  <?php $orders = $this->requestAction(array("controller"=>"dashboards","action"=>"get_color_code","orders"));?>
                        <div class="small-box <?php echo $orders;?>" id="orders">
                            <div class="inner">
                                <h3><?php echo $this->Common->totalOrders(); ?></h3>
                                <p>Orders</p>
                                <!--<p><a href="javascript:void(0)">Inactive (0)</a>  <a href="<?php echo SITEURL; ?>sitepanel/users/all_transaction">/ Active (<?php echo $this->Common->totalOrders(); ?>)</p>-->
                            <!--    <p>&nbsp;</p>
                            </div>
                            <div class="icon">
                                <i class="ion-stats-bars"></i>
                            </div>
                            <div class="small-box-footer" style="text-align: left; padding: 10px">
                            <a href="<?php echo SITEURL; ?>sitepanel/users/all_transaction" class="more-info">More info 
                                <i class="fa fa-arrow-circle-right"></i></a>
                                <div class="color_picker pull-right">
                                    <a href="#color_pick" style=" " class=" btn btn-default btn-xs toggle_color tipText" title="Color Options" >
                                        <i class="fa fa-paint-brush"></i>
                                    </a>
                                    <div id="color_pick" class="color_box color_box_bottom" >
                                        <div class="colors" itemid="orders">
                                            <b href="#" data-color="bg-red" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></b>
                                            <a href="#" data-color="bg-blue" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="bg-maroon" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="bg-aqua" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="bg-yellow" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="bg-green" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="bg-teal" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="bg-purple" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="bg-navy" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>--><!-- ./col -->


                <!--    <div class="col-lg-4 col-xs-8">
                        
                        <?php $projects = $this->requestAction(array("controller"=>"dashboards","action"=>"get_color_code","projects"));?>
                        <div class="small-box <?php echo $projects;?>" id="projects">
                            <div class="inner">
                                <h3><?php echo $this->Common->totalDataP('UserProject'); ?></h3>
                                <p>Projects</p>
                                <p><a href="javascript:void(0)">Inactive (<?php echo $this->Common->totalInactive('UserProject'); ?>)</a>  <a href="javascript:void(0)" >/ Active (<?php echo $this->Common->totalActive('UserProject'); ?>)</p>

                            </div>
                            <div class="icon">
                                <img src="<?php echo SITEURL ?>images/project-icon01.png" alt="project">
 
                            </div>
                            <div class="small-box-footer" style="text-align: left; padding: 10px">
                            <a href="javascript:void(0)" class="more-info">More info <i class="fa fa-arrow-circle-right"></i></a>
                            <div class="color_picker pull-right">
                                    <a href="#color_pick" style=" " class=" btn btn-default btn-xs toggle_color tipText" title="Color Options" >
                                        <i class="fa fa-paint-brush"></i>
                                    </a>
                                    <div id="color_pick" class="color_box color_box_bottom" >
                                        <div class="colors" itemid="projects">
                                            <a href="#" data-color="bg-red" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
                                            <a href="#" data-color="bg-blue" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="bg-maroon" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="bg-aqua" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="bg-yellow" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="bg-green" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="bg-teal" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="bg-purple" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="bg-navy" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
					<!-- ./col -->
					
					<!-- <div class="col-lg-4 col-xs-8">
                      
                        <?php $authors = $this->requestAction(array("controller"=>"dashboards","action"=>"get_color_code","authors"));?>
                        <div class="small-box <?php echo $authors;?>" id="authors">
                            <div class="inner">                  
                                <h3><?php echo $this->Common->totalDataThird('User'); ?></h3>
                                <p>Authors</p>
                                <p><a href="<?php echo SITEURL; ?>sitepanel/users/thirdparty_users/index/sort:User.status/direction:asc">Inactive (<?php echo $this->Common->totalInactiveT('User'); ?>)</a>  <a href="<?php echo SITEURL; ?>sitepanel/users/thirdparty_users/index/sort:User.status/direction:desc" >/ Active (<?php echo $this->Common->totalActiveT('User'); ?>)</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="small-box-footer" style="text-align: left; padding: 10px">
                                <a href="<?php echo SITEURL; ?>sitepanel/users/thirdparty_users" class="more-info">More info 
                                    <i class="fa fa-arrow-circle-right"></i>
                                </a>
                                <div class="color_picker pull-right">
                                    <a href="#color_pick" style=" " class=" btn btn-default btn-xs toggle_color tipText" title="Color Options" >
                                        <i class="fa fa-paint-brush"></i>
                                    </a>
                                    <div id="color_pick" class="color_box color_box_bottom" >
                                        <div class="colors" itemid="authors">
                                            <a href="#" data-color="bg-red" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
                                            <a href="#" data-color="bg-blue" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="bg-maroon" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="bg-aqua" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="bg-yellow" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="bg-green" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="bg-teal" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="bg-purple" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="bg-navy" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
				 
					
					
                    <!--<div class="col-lg-4 col-xs-8">-->
                        <!-- small box -->
                      <!--   <?php $institutions = $this->requestAction(array("controller"=>"dashboards","action"=>"get_color_code","institutions"));?>
                        <div class="small-box <?php echo $institutions;?>" id="institutions">
                            <div class="inner">                  
                                <h3><?php echo $this->Common->totalDataInst('User'); ?></h3>
                                <p>Institutions</p>
                                <p><a href="<?php echo SITEURL; ?>sitepanel/users/institution_users/sort:User.status/direction:asc">Inactive (<?php echo $this->Common->totalInactiveI('User'); ?>)</a>  <a href="<?php echo SITEURL; ?>sitepanel/users/institution_users/sort:User.status/direction:desc" >/ Active (<?php echo $this->Common->totalActiveI('User'); ?>)</p>				  
                            </div>
                            <div class="icon">
                                <i class="fa fa fa-building-o"></i>
                            </div>
                            <div class="small-box-footer" style="text-align: left; padding: 10px">
                                <a href="<?php echo SITEURL; ?>sitepanel/users/institution_users" class="more-info">More info <i class="fa fa-arrow-circle-right"></i></a>
                                <div class="color_picker pull-right">
                                    <a href="#color_pick" style=" " class=" btn btn-default btn-xs toggle_color tipText" title="Color Options" >
                                        <i class="fa fa-paint-brush"></i>
                                    </a>
                                    <div id="color_pick" class="color_box color_box_bottom" >
                                        <div class="colors" itemid="institutions">
                                            <a href="#" data-color="bg-red" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
                                            <a href="#" data-color="bg-blue" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="bg-maroon" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="bg-aqua" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="bg-yellow" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="bg-green" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="bg-teal" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="bg-purple" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="bg-navy" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                        </div>
                    </div><!-- ./col -->
                    <!--<div class="col-lg-4 col-xs-8" style="display:block">-->
                        <!-- small box -->
                       <!-- <?php $currencies = $this->requestAction(array("controller"=>"dashboards","action"=>"get_color_code","currencies"));?>
                        <div class="small-box <?php echo $currencies;?>" id="currencies">
                            <div class="inner">
                                <h3><?php echo $this->Common->totalData('Currency'); ?></h3>
                                <p>Currencies</p>
                              <!-- <p><a href="<?php echo SITEURL; ?>sitepanel/currencies/index/sort:Currency.status/direction:asc">Inactive (<?php echo $this->Common->totalInactive('Currency'); ?>)</a>  <a href="<?php echo SITEURL; ?>sitepanel/currencies/index/sort:Currency.status/direction:desc" >/ Active (<?php echo $this->Common->totalActive('Currency'); ?>)</p>	-->
                                <?php $currencyData = $this->Common->currencyData(); ?>
                              <!--<p>Active (<?php //echo $currencyData['Currency']['sign'];   ?><?php echo $this->Common->currencySymbol(); ?>)</p>	-->
                             <!--   <p>&nbsp;</p>
                            </div>
                            <div class="icon">
                                <i class="fa  fa-dollar"></i>
                            </div>
                            <div class="small-box-footer" style="text-align: left; padding: 10px">
                                <a href="<?php echo SITEURL; ?>sitepanel/currencies" class="more-info">More info <i class="fa fa-arrow-circle-right"></i></a>
                                <div class="color_picker pull-right">
                                    <a href="#color_pick" style=" " class=" btn btn-default btn-xs toggle_color tipText" title="Color Options" >
                                        <i class="fa fa-paint-brush"></i>
                                    </a>
                                    <div id="color_pick" class="color_box color_box_bottom" >
                                        <div class="colors" itemid="currencies">
                                            <a href="#" data-color="bg-red" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
                                            <a href="#" data-color="bg-blue" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="bg-maroon" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="bg-aqua" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="bg-yellow" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="bg-green" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="bg-teal" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="bg-purple" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="bg-navy" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                    </div>--><!-- ./col -->

                    

                    <!--<div class="col-lg-4 col-xs-8">-->
                        <!-- small box -->
                       <!-- <?php $plans = $this->requestAction(array("controller"=>"dashboards","action"=>"get_color_code","plans"));?>
                        <div class="small-box <?php echo $plans;?>" id="plans">
                            <div class="inner">
                                <h3><?php echo $this->Common->totalData('Plan'); ?></h3>
                                <p>Plans</p>
                                <p><a href="<?php echo SITEURL; ?>sitepanel/plans/index/sort:Plan.status/direction:asc">Inactive (<?php echo $this->Common->totalInactive('Plan'); ?>)</a>  <a href="<?php echo SITEURL; ?>sitepanel/plans/index/sort:Plan.status/direction:desc" >/ Active (<?php echo $this->Common->totalActive('Plan'); ?>)</p>	

                            </div>


                            <div class="icon">
                                <i class="fa fa-user-secret"></i>
                            </div>
                            <div class="small-box-footer" style="text-align: left; padding: 10px">
                                <a href="<?php echo SITEURL; ?>sitepanel/plans" class="more-info">More info <i class="fa fa-arrow-circle-right"></i></a>
                                <div class="color_picker pull-right">
                                    <a href="#color_pick" style=" " class=" btn btn-default btn-xs toggle_color tipText" title="Color Options" >
                                        <i class="fa fa-paint-brush"></i>
                                    </a>
                                    <div id="color_pick" class="color_box color_box_bottom" >
                                        <div class="colors" itemid="plans">
                                            <a href="#" data-color="bg-red" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
                                            <a href="#" data-color="bg-blue" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="bg-maroon" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="bg-aqua" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="bg-yellow" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="bg-green" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="bg-teal" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="bg-purple" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="bg-navy" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>--><!-- ./col -->	

                   <!-- <div class="col-lg-4 col-xs-8">-->
                        <!-- small box -->
                       <!-- <?php $coupons = $this->requestAction(array("controller"=>"dashboards","action"=>"get_color_code","coupons")); 
                        //echo  $coupons;?>
                        <div class="small-box <?php echo $coupons;?>" id="coupons">
                            <div class="inner">
                                <h3><?php echo $this->Common->totalData('Coupon'); ?></h3>
                                <p>Coupons</p>
                                <p><a href="<?php echo SITEURL; ?>sitepanel/coupons/index/sort:Coupon.status/direction:asc">Inactive (<?php echo $this->Common->totalInactive('Coupon'); ?>)</a>  <a href="<?php echo SITEURL; ?>sitepanel/coupons/index/sort:Coupon.status/direction:desc" >/ Active (<?php echo $this->Common->totalActive('Coupon'); ?>)</p>

                            </div>


                            <div class="icon">
                                <i class="fa fa-ticket"></i>
                            </div>
                            <div class="small-box-footer" style="text-align: left; padding: 10px">
                                <a href="<?php echo SITEURL; ?>sitepanel/coupons" class="more-info">More info <i class="fa fa-arrow-circle-right"></i></a>
                                <div class="color_picker pull-right">
                                    <a href="#color_pick" style=" " class=" btn btn-default btn-xs toggle_color tipText" title="Color Options" >
                                        <i class="fa fa-paint-brush"></i>
                                    </a>
                                    <div id="color_pick" class="color_box color_box_bottom" >
                                        <div class="colors" itemid="coupons">
                                            <a href="#" data-color="bg-red" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
                                            <a href="#" data-color="bg-blue" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="bg-maroon" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="bg-aqua" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="bg-yellow" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="bg-green" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="bg-teal" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="bg-purple" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="bg-navy" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>--><!-- ./col -->			
 

                    


						



                   <!-- <div class="col-lg-4 col-xs-8">
                      
                        <?php $project_center = $this->requestAction(array("controller"=>"dashboards","action"=>"get_color_code","project_center"));?>
                        <div class="small-box <?php echo $project_center;?>"  id="project_center">
                            <div class="inner">
                                <h3>0<?php //echo $this->Common->totalData('Country');  ?></h3>
                                <p>Project Center</p>
                                <p>&nbsp;</p>

                            </div>


                            <div class="icon">
                                <i class="fa fa-bullseye"></i>
                            </div>

                            <div class="small-box-footer" style="text-align: left; padding: 10px">
                                <a style="color: #fff" href="#" class="more-info">More info <i class="fa fa-arrow-circle-right"></i></a>

                                <div class="color_picker pull-right">
                                    <a href="#" style=" " class=" btn btn-default btn-xs toggle_color tipText" title="Color Options" >
                                        <i class="fa fa-paint-brush"></i>
                                    </a>
                                    <div id="color_pick" class="color_box color_box_bottom" >
                                        <div class="colors" itemid="project_center">
                                            <a href="#" data-color="bg-red" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
                                            <a href="#" data-color="bg-blue" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="bg-maroon" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="bg-aqua" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="bg-yellow" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="bg-green" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="bg-teal" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="bg-purple" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="bg-navy" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "update_color", 'admin' => true), true); ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>-->
					
					<!-- ./col -->*/ ?>						


                </div><!-- /.row -->
            <?php } ?>

        </section><!-- /.content -->
    </div><!-- /.right-side -->
</div><!-- /.right-side -->
</div><!-- /.right-side -->

<style>
    .color_picker {
        display: inline-block;
        position: relative;
    }
    .color_box {
        background: #fff none repeat scroll 0 0;
        border: 1px solid #ccc;
        border-radius: 5px;
        display: block;
        right: 0;
        padding: 1px 4px 3px 5px;
        position: absolute;
        text-align: left;
        top: 25px;
        width: 86px;
        z-index: 631;
    }
    .colors a {
        margin-top: 3px;
    }
    .color_box {
        display: none;
    }
    .more-info,.more-info:hover {
        color: #fff;
    }
</style>