<style>
	.color-preview {
		color: #ffffff;
		padding: 5px 10px;
		text-align: left;
		cursor: pointer;
	}


	.color-preview.battleship_gray {
		background: #848482 none repeat scroll 0 0 !important;
	}
	.color-preview.black {
		background: #000000 none repeat scroll 0 0 !important;
	}
	.color-preview.chilli_pepper {
		background: #C11B17 none repeat scroll 0 0 !important;
	}
	.color-preview.dark_orchid {
		background: #7D1B7E none repeat scroll 0 0 !important;
	}
	.color-preview.indigo {
		background: #4b0082 none repeat scroll 0 0 !important;
	}
	.color-preview.iridium {
		background: #3D3C3A none repeat scroll 0 0 !important;
	}
	.color-preview.navy_blue {
		background: #000080 none repeat scroll 0 0 !important;
	}
	.color-preview.plum_velvet {
		background: #7D0552 none repeat scroll 0 0 !important;
	}
	.color-preview.seaweed_green {
		background: #437C17 none repeat scroll 0 0 !important;
	}

	.color-preview.cinnamon {
		background: #C58917 none repeat scroll 0 0 !important;
	}
	.color-preview.slate_blue {
		background: #737CA1 none repeat scroll 0 0 !important;
	}
	.color-preview.default {
		/* fallback */
		background-color: #616161;
		/*background: url(../images/navbar.jpg);
		background-repeat: repeat-x;
		background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#5e5e5e), to(#202020));
		background: -webkit-linear-gradient(top,  #5e5e5e, #202020);
		background: -moz-linear-gradient(top,  #5e5e5e, #202020);
		background: -ms-linear-gradient(top,  #5e5e5e, #202020);
		background: -o-linear-gradient(top,  #5e5e5e, #202020);*/
	}
	.color-preview i {
		display: none;
		padding-top: 3px;
	}

	/*============== Secondary theme ================================*/
	.secondary-color-preview {
		color: #ffffff;
		padding: 5px 10px;
		text-align: left;
		cursor: pointer;
	}

	.secondary-color-preview.battleship_gray {
		background: #848482 none repeat scroll 0 0 !important;
	}
	.secondary-color-preview.black {
		background: #000000 none repeat scroll 0 0 !important;
	}
	.secondary-color-preview.chilli_pepper {
		background: #C11B17 none repeat scroll 0 0 !important;
	}
	.secondary-color-preview.dark_orchid {
		background: #7D1B7E none repeat scroll 0 0 !important;
	}
	.secondary-color-preview.indigo {
		background: #4b0082 none repeat scroll 0 0 !important;
	}
	.secondary-color-preview.iridium {
		background: #3D3C3A none repeat scroll 0 0 !important;
	}
	.secondary-color-preview.navy_blue {
		background: #000080 none repeat scroll 0 0 !important;
	}
	.secondary-color-preview.plum_velvet {
		background: #7D0552 none repeat scroll 0 0 !important;
	}
	.secondary-color-preview.seaweed_green {
		background: #437C17 none repeat scroll 0 0 !important;
	}

	.secondary-color-preview.cinnamon {
		background: #C58917 none repeat scroll 0 0 !important;
	}
	.secondary-color-preview.slate_blue {
		background: #737CA1 none repeat scroll 0 0 !important;
	}
	.secondary-color-preview.default {
		/* fallback */
		background-color: #616161;
		/*background: url(../images/navbar.jpg);
		background-repeat: repeat-x;
		background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#5e5e5e), to(#202020));
		background: -webkit-linear-gradient(top,  #5e5e5e, #202020);
		background: -moz-linear-gradient(top,  #5e5e5e, #202020);
		background: -ms-linear-gradient(top,  #5e5e5e, #202020);
		background: -o-linear-gradient(top,  #5e5e5e, #202020);*/
	}

	.secondary-color-preview i {
		display: none;
		padding-top: 3px;
	}
	#message_box {
		font-size: 12px; text-align: center; margin: 0px 0px 10px; background: rgb(95, 147, 35) none repeat scroll 0% 0%; padding: 10px 5px; color: #ffffff; font-weight: 600; display: none;
	}


	.theme_column .form-group {
		margin-left: 0;
		margin-right: 0;
	}


	/*#theme-menu {
		border-bottom: 1px solid #437c16;
		margin-top: -4px;
		margin-bottom: 12px;
		padding-bottom: 1px;
	}

	#theme-menu > li > a {
		padding: 4px 10px;
		border: 1px solid transparent;
	}

	#theme-menu li:first-child {
		border-right: 2px solid #4cae4c;
	}
	#theme-menu > li > a:hover {
		background-color: transparent;
		color: #333;
		text-decoration: none;
		border: 1px solid transparent;
	}
	#theme-menu li.active {
		background-color: transparent;
	}

	#theme-menu > li.active > a:hover {
		color: #ccc !important;
	}
	#theme-menu li a, #theme-menu li a:hover, #theme-menu li.active a, #theme-menu li.active a:hover {
		border: 1px solid transparent;
	}
	#theme-menu li a {
		color: #5F9323;
		font-weight: 600;
	}
	#theme-menu li a:hover {
		color: #5F9323;
	}
	#theme-menu li.active a {
		color: #ccc;
		font-weight: normal;
	}*/


</style>

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h3 class="modal-title" id="myModalLabel" style="display: inline"> Choose Theme </h3>
	</div>

	<div class="modal-body allpopuptabs clearfix">
		<h3 class="" id="message_box" ></h3>

		<ul class="nav nav-tabs" id="theme-menu">
		  <li class="nav-item active">
			<a class="nav-link " data-tval="primary" data-toggle="tab" href="#primary">Primary</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link"  data-tval="secondary" data-toggle="tab" href="#secondary">Secondary</a>
		  </li>
		</ul>

		<?php
			echo $this->Form->create('setTheme', array('url' => ['controller' => 'settings', 'action' => 'themes'], 'class' => 'form-horizontal', 'id' => 'frmSetTheme'));
		?>
		<!-- Tab panes -->
		<div class="tab-content">

		  <div class="tab-pane container active" id="primary">
				<div class="col-sm-6 theme_column">
					<div class="form-inner">
						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 color-preview indigo" data-theme="theme_indigo">
								Indigo
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>
						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 color-preview dark_orchid"  data-theme="theme_dark_orchid">
								Dark Orchid
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>
						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 color-preview plum_velvet"  data-theme="theme_plum_velvet">
								Plum Velvet <!--(Initiative)<!-- -->
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>
						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 color-preview chilli_pepper"  data-theme="theme_chilli_pepper">
								Chilli Pepper <!--(Action)-->
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>
						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 color-preview seaweed_green"  data-theme="theme_seaweed_green">
								Seaweed Green <!--(Energize)-->
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>

						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 color-preview cinnamon"  data-theme="theme_cinnamon">
								Cinnamon <!--(Innovate)-->
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-6 theme_column">
					<div class="form-inner">
						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 color-preview navy_blue"  data-theme="theme_navy_blue">
								Navy Blue <!--(Focus)-->
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>

						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 color-preview slate_blue"  data-theme="theme_slate_blue">
								Slate Blue
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>

						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 color-preview iridium"  data-theme="theme_iridium">
								Iridium
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>
						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""> </div> -->
							<div class="col-sm-12 color-preview battleship_gray"  data-theme="theme_battleship_gray">
								Battleship Gray
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>
						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""> </div> -->
							<div class="col-sm-12 color-preview black" data-theme="theme_black">
								Black <!--(Strength)-->
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>

						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 color-preview default" data-theme="theme_default">
								Default
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>
					</div>
				</div>
		  </div>
		  <div class="tab-pane container fade" id="secondary">
				<div class="col-sm-6 theme_column">
					<div class="form-inner">
						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 secondary-color-preview indigo" data-stheme="theme_indigo">
								Indigo
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>
						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 secondary-color-preview dark_orchid"  data-stheme="theme_dark_orchid">
								Dark Orchid
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>
						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 secondary-color-preview plum_velvet"  data-stheme="theme_plum_velvet">
								Plum Velvet <!--(Initiative)<!-- -->
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>
						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 secondary-color-preview chilli_pepper"  data-stheme="theme_chilli_pepper">
								Chilli Pepper <!--(Action)-->
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>
						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 secondary-color-preview seaweed_green"  data-stheme="theme_seaweed_green">
								Seaweed Green <!--(Energize)-->
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>

						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 secondary-color-preview cinnamon"  data-stheme="theme_cinnamon">
								Cinnamon <!--(Innovate)-->
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>


					</div>
				</div>
				<div class="col-sm-6 theme_column">
					<div class="form-inner">
						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 secondary-color-preview navy_blue"  data-stheme="theme_navy_blue">
								Navy Blue <!--(Focus)-->
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>

						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 secondary-color-preview slate_blue"  data-stheme="theme_slate_blue">
								Slate Blue
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>

						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 secondary-color-preview iridium"  data-stheme="theme_iridium">
								Iridium
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>
						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""> </div> -->
							<div class="col-sm-12 secondary-color-preview battleship_gray"  data-stheme="theme_battleship_gray">
								Battleship Gray
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>
						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""> </div> -->
							<div class="col-sm-12 secondary-color-preview black" data-stheme="theme_black">
								Black <!--(Strength)-->
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>

						<div class="form-group">
							<!-- <div class="control-label col-sm-3" style=""></div> -->
							<div class="col-sm-12 secondary-color-preview default" data-stheme="theme_default">
								Default
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>
					</div>
				</div>
		  </div>
		</div>
		<input type="hidden" id="theme_name" value="primary" >
		<?php  echo $this->Form->end(); ?>

	</div>

	<div class="modal-footer">
		<button class="btn btn-success" id="save_setting" >Save</button>
		<button class="btn btn-danger" id="close_modal" data-dismiss="modal">Cancel</button>
	</div>

<script type="text/javascript" >
$(function(){

		$('body').delegate('.nav-link', 'click', function(event) {
			$that = $(this);
			$("#theme_name").val( $that.data('tval') );
		});


		$(".color-preview[data-theme=<?php echo $user_theme ?>]").find('i').show();
		$(".secondary-color-preview[data-stheme=<?php echo $secondary_user_theme ?>]").find('i').show();

		$(".color-preview[data-theme=<?php echo $user_theme ?>]").addClass('theme-selected');
		$(".secondary-color-preview[data-stheme=<?php echo $secondary_user_theme ?>]").addClass('sec-theme-selected');

		$('#save_setting').click(function(event) {
				event.preventDefault();

				var theme_type = 'primary';
				if( $("#theme_name").val() != null ){
					theme_type = $("#theme_name").val();
				}

				var $selected = $('.theme-selected'),
					selected_theme = $.current_theme = ($selected.length) ? $selected.data('theme') : 'theme_default';


				var $selected_secondary = $('.sec-theme-selected'),
					secondary_selected_theme = ($selected_secondary.length) ? $selected_secondary.data('stheme') : 'theme_seaweed_green';

				runAjax = true;
				var $img = $('header.main-header a.logo img'),
					logo = 'logo_white.png';
				if( $img.length ) {
					$img[0].src = $js_config.base_url + 'images/' + logo;
				}

				// save to db
				// if(runAjax) {
						$.ajax({
							type:'POST',
							url: $js_config.base_url + 'settings/themes/',
							data: $.param({'selected_theme': selected_theme,'secondary_theme':secondary_selected_theme}),
							global: false,
							dataType: 'JSON',
							success: function(response) {
								if( response.success ) {
								    $.socket.emit("theme:setting:change", {userID: $js_config.USER.id, theme: selected_theme});

									$('#modal_medium').modal('hide');
									// console.log($js_config.base_url + 'images/logo_white.png')
									//$('#message_box').html(response.msg).slideDown(500)
									setTimeout(function(){
										$('#message_box').slideUp(500).html('')

									}, 3000)
								}
								else {

								}
							}

						});

				// }

		})


		$('body').delegate('.color-preview', 'click', function(event) {
				event.preventDefault();

				var $others = $('.color-preview').not(this)
				$others.removeClass('theme-selected');
				$(this).addClass('theme-selected');
				$others.find('i').hide();
				$(this).find('i').show();

				var data = $(this).data(),
					theme = data.theme,
					ms_theme = $('.main-sidebar').data('theme'),
					mh_theme = $('.main-header').data('theme'),
					ch_theme = $('.open-chat-win').data('theme');

					$('.main-header').removeClass(mh_theme).addClass(theme)
					$('.main-sidebar').removeClass(ms_theme).addClass(theme)
            		$('.open-chat-win').removeClass(ch_theme).addClass(theme)

					$('.main-header').data({'theme': theme})
					$('.main-sidebar').data({'theme': theme})
            		$('.open-chat-win').data({'theme': theme})

				var $img = $('header.main-header a.logo img'),
					logo = 'logo_white.png'
				/* if( theme == 'theme_default' ) {
					logo = 'logo.png';
				} */
					if( $img.length ) {
						$img[0].src = $js_config.base_url + 'images/' + logo;
					}
		})

		$('body').delegate('.secondary-color-preview', 'click', function(event) {
				event.preventDefault();

				var $others = $('.secondary-color-preview').not(this);
				$others.find('i').hide();
				$(this).find('i').show();

				$others.removeClass('sec-theme-selected');
				$(this).addClass('sec-theme-selected');


		})

	/*  */
})

</script>