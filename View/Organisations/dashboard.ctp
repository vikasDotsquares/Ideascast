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
            var itemid = $(this).parent().attr("itemid");
            var classes = $('#'+itemid).attr("class");
            var color_code  = $(this).attr("data-color");
            //alert(classes);
            //console.log("data-remote : "+dataremote+" itemid : "+itemid+" data-color : "+color_code +" classes : "+classes);
            var foundClass = (classes.match(/(^|\s)bg-\S+/g) || []).join('');
            if (foundClass != '') {
                    $('#'+itemid).removeClass(foundClass)
            }
            $('#'+itemid).addClass(color_code)
			
            $.ajax({
                type: "POST",
                url: dataremote,                
				data: $.param({ 'slug': itemid, 'color_code' : color_code}),               
				global: true,               
                success:function(response){
					console.log(response['success']);
					if( response.success == true ){
					$(".color_box_bottom").hide();		
					}
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
        <section class="content frnt-dashboard">

            <!-- Small boxes (Stat box) -->
            <?php 
			if ($this->Session->read('Auth.User.role_id') == 3) { ?>
                <div class="row">
					
					<div class="col-lg-4 col-xs-8">
                        <!-- small box -->
                        <?php
						$orgaccount = '';
						 $orgaccount = $this->Common->get_color_code('orgaccount'); 
						//pr($organisations); die;
						?>
                        <div class="small-box <?php echo $orgaccount;?>" id="orgaccount">
                            <div class="inner">                                
                                <h3>&nbsp;</h3>
								<p>Main Account</p>
                                <p><a class="typestatus" href="mailto:<?php echo $this->Session->read('Auth.User.email'); ?>"><?php echo $this->Session->read('Auth.User.email'); ?></a></p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-building"></i>
                            </div>
                            <div class="small-box-footer" style="text-align: left; padding: 10px">
								<?php $orgUsers = SITEURL."organisations/orgownerdetail/".$this->Session->read('Auth.User.id'); ?>							
                                <a href="javascript:;" id="trigger_edit_profile" data-target='#popup_modal_start' data-remote="<?php echo $orgUsers; ?>" data-toggle="modal" class="more-info">More info <i class="fa fa-arrow-circle-right"></i></a>
                                <div class="color_picker pull-right">
                                    <a href="#color_pick" style="" class=" btn btn-default btn-xs toggle_color tipText" title="Color Options" >
                                        <i class="fa fa-paint-brush"></i>
                                    </a>
                                    <div id="color_pick" class="color_box color_box_bottom" >
                                        <div class="colors" itemid="orgaccount">
                                            <a href="#" data-color="bg-red" data-remote="<?php echo Router::Url(array("controller" => "organisationss", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
                                            <a href="#" data-color="bg-blue" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="bg-maroon" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="bg-aqua" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="bg-yellow" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="bg-green" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="bg-teal" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="bg-purple" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="bg-navy" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div><!-- ./col -->
					
                    <div class="col-lg-4 col-xs-8">
                        
                        <?php 
							$orgusers = '';
							$orgusers = $this->Common->get_color_code('orgusers'); 
						?>
                        <div class="small-box <?php echo $orgusers;?>" id="orgusers">
                            <div class="inner">
                                <h3><?php echo $this->Common->totalDataU('User'); ?></h3>
                                
								<p>Main Account Users</p>                
								
                                <p><a class="typestatus" href="<?php echo SITEURL; ?>organisations/manage_users/sort:User.status/direction:asc">Inactive (<?php echo $this->Common->totalInactiveU('User'); ?>)</a>  <a class="typestatus" href="<?php echo SITEURL; ?>organisations/manage_users/sort:User.status/direction:desc" >/ Active (<?php echo $this->Common->totalActiveU('User'); ?>)</p>
								
                            </div>
                            <div class="icon">
                                <i class="fa fa-group"></i>
                            </div>
                            <div class="small-box-footer" style="text-align: left; padding: 10px">
                                <a href="<?php echo SITEURL; ?>organisations/manage_users" class="more-info">More info <i class="fa fa-arrow-circle-right"></i></a>
                                <div class="color_picker pull-right">
                                    <a href="#color_pick" style=" " class=" btn btn-default btn-xs toggle_color tipText" title="Color Options" >
                                        <i class="fa fa-paint-brush"></i>
                                    </a>
                                    <div id="color_pick" class="color_box color_box_bottom" >
                                        <div class="colors" itemid="orgusers">
                                            <a href="#" data-color="bg-red" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
                                            <a href="#" data-color="bg-blue" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="bg-maroon" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="bg-aqua" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="bg-yellow" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="bg-green" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="bg-teal" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="bg-purple" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="bg-navy" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
						
                    </div>
					
					<?php /* <div class="col-lg-4 col-xs-8">
                        
                        <?php 
							$orgskill = '';
							$orgskill = $this->Common->get_color_code('orgskill'); 
						?>
                        <div class="small-box <?php echo $orgskill;?>" id="orgskill">
                            <div class="inner">
                                <h3><?php echo $this->Common->totalDataS('Skill'); ?></h3>
                                <p>Skills</p>                
									
                               
							    <p><a class="typestatus" href="<?php echo SITEURL; ?>skills/index/sort:Skill.status/direction:asc">Inactive (<?php echo $this->Common->totalInactiveSkill('Skill'); ?>)</a>  <a class="typestatus" href="<?php echo SITEURL; ?>skills/index/sort:Skill.status/direction:desc" >/ Active (<?php echo $this->Common->totalActiveSkill('Skill'); ?>)</p>
							   
                            </div>
							
                            <div class="icon">
                                <i class="fa fa-cogs"></i>
                            </div>
                            <div class="small-box-footer" style="text-align: left; padding: 10px">
                                <a href="<?php echo SITEURL; ?>skills" class="more-info">More info <i class="fa fa-arrow-circle-right"></i></a>
                                <div class="color_picker pull-right">
                                    <a href="#color_pick" style=" " class=" btn btn-default btn-xs toggle_color tipText" title="Color Options" >
                                        <i class="fa fa-paint-brush"></i>
                                    </a>
                                    <div id="color_pick" class="color_box color_box_bottom" >
                                        <div class="colors" itemid="orgskill">
                                            <a href="#" data-color="bg-red" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
                                            <a href="#" data-color="bg-blue" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="bg-maroon" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="bg-aqua" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="bg-yellow" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="bg-green" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="bg-teal" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="bg-purple" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="bg-navy" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
						</div>
                    </div> */ ?>
					<?php  /* 
					$flagORg = $this->Common->domain_list(); 
					if($flagORg == true){ ?>
					
					<div class="col-lg-4 col-xs-8">
                        
                        <?php 
							$orgdomain = '';
							$orgdomain = $this->Common->get_color_code('orgdomain'); 
						?>
                        <div class="small-box <?php echo $orgdomain;?>" id="orgdomain">
                            <div class="inner">
                                <h3><?php echo $domainCount;?></h3>
								<p>Linked Domains</p>                
                                <p>&nbsp;</p>								
                            </div>
                            <div class="icon">
                                <i class="fa fa-list"></i>
                            </div>
                            <div class="small-box-footer" style="text-align: left; padding: 10px">
                                <a href="<?php echo SITEURL; ?>organisations/domain_list" class="more-info">More info <i class="fa fa-arrow-circle-right"></i></a>
                                <div class="color_picker pull-right">
                                    <a href="#color_pick" style=" " class=" btn btn-default btn-xs toggle_color tipText" title="Color Options" >
                                        <i class="fa fa-paint-brush"></i>
                                    </a>
                                    <div id="color_pick" class="color_box color_box_bottom" >
                                        <div class="colors" itemid="orgdomain">
                                            <a href="#" data-color="bg-red" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
                                            <a href="#" data-color="bg-blue" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="bg-maroon" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="bg-aqua" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="bg-yellow" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="bg-green" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="bg-teal" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="bg-purple" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="bg-navy" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
						
                    </div>
					<?php } */
 					 
					$flagORgEmailDomain = $this->Common->countOrgEmailDomain( $this->Session->read('Auth.User.id') ); 
					
					if($flagORgEmailDomain == true){ ?>
					
					<div class="col-lg-4 col-xs-8">
                        
                        <?php 
							$orgEmaildomain = '';
							$orgEmaildomain = $this->Common->get_color_code('orgEmaildomain'); 
						?>
                        <div class="small-box <?php echo $orgEmaildomain;?>" id="orgEmaildomain">
                            <div class="inner">
                                <h3><?php echo $flagORgEmailDomain;?></h3>
								<p>Email Domains</p>                
                                <p><a class="typestatus" href="<?php echo SITEURL; ?>organisations/domain_settings/sort:ManageDomain.create_account/direction:asc">Inactive (<?php echo $this->Common->totalInactiveEmail('ManageDomain'); ?>)</a>  <a class="typestatus" href="<?php echo SITEURL; ?>organisations/domain_settings/sort:ManageDomain.create_account/direction:desc" >/ Active (<?php echo $this->Common->totalActiveEmail('ManageDomain'); ?>)</p>								
                            </div>
                            <div class="icon">
                                <i class="fa fa-envelope"></i>
                            </div>
                            <div class="small-box-footer" style="text-align: left; padding: 10px">
                                <a href="<?php echo SITEURL; ?>organisations/domain_settings" class="more-info">More info <i class="fa fa-arrow-circle-right"></i></a>
                                <div class="color_picker pull-right">
                                    <a href="#color_pick" style=" " class=" btn btn-default btn-xs toggle_color tipText" title="Color Options" >
                                        <i class="fa fa-paint-brush"></i>
                                    </a>
                                    <div id="color_pick" class="color_box color_box_bottom" >
                                        <div class="colors" itemid="orgEmaildomain">
                                            <a href="#" data-color="bg-red" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
                                            <a href="#" data-color="bg-blue" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="bg-maroon" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="bg-aqua" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="bg-yellow" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="bg-green" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="bg-teal" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="bg-purple" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="bg-navy" data-remote="<?php echo Router::Url(array("controller" => "organisations", "action" => "update_color", 'admin' => false), true); ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
						
                    </div>
			<?php  } 
			} ?>

        </section><!-- /.content -->
    </div><!-- /.right-side -->
</div><!-- /.right-side -->
</div><!-- /.right-side -->

<style>
    .frnt-dashboard .color_picker {
        display: inline-block;
        position: relative;
    }
    .frnt-dashboard .color_picker .color_box {
        background: #fff none repeat scroll 0 0;
        border: 1px solid #ccc;
        border-radius: 5px;
        display: block;
        right: 0;
        padding: 1px 4px 5px 5px;
        position: absolute;
        text-align: left;
        top: 25px;
        max-width: 89px;
        z-index: 631;
    }
    .frnt-dashboard .color_picker .colors a {
        margin-top: 3px;
    }
    .frnt-dashboard .color_picker .color_box {
        display: none;
    }
    .frnt-dashboard .small-box .more-info {
        color: #fff;
    }
	.frnt-dashboard .small-box .more-info:hover {
        color: #fff !important;
    }
	.frnt-dashboard .small-box .typestatus {
        color: #fff;
    }
	.frnt-dashboard .small-box .typestatus:hover, {
        color: #fff !important;
    }
	
	.more-info:hover {
		color: #ffffff;
	}
</style>