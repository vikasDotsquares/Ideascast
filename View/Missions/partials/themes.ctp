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
.color-preview.default {
	/* fallback */ 
	background-color: #5e5e5e;
	background: url(../images/navbar.jpg);
	background-repeat: repeat-x;
	/* Safari 4-5, Chrome 1-9 */
	background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#5e5e5e), to(#202020));
	/* Safari 5.1, Chrome 10+ */
	background: -webkit-linear-gradient(top,  #5e5e5e, #202020);
	/* Firefox 3.6+ */
	background: -moz-linear-gradient(top,  #5e5e5e, #202020);
	/* IE 10 */
	background: -ms-linear-gradient(top,  #5e5e5e, #202020);
	/* Opera 11.10+ */
	background: -o-linear-gradient(top,  #5e5e5e, #202020);
}
.color-preview i {
	display: none;
	padding-top: 3px;
}
#message_box {
	font-size: 12px; text-align: center; margin: 0px 0px 10px; background: rgb(95, 147, 35) none repeat scroll 0% 0%; padding: 10px 5px; color: #ffffff; font-weight: 600; display: none;
}

</style>

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h3 class="modal-title" id="myModalLabel" style="display: inline"> Choose Theme </h3>
	</div>

	<div class="modal-body">
		<h3 class="" id="message_box" ></h3>
		<?php
			echo $this->Form->create('setTheme', array('url' => ['controller' => 'settings', 'action' => 'themes'], 'class' => 'form-horizontal', 'id' => 'frmSetTheme'));
		?>
			<div class="form-inner">
				
				<div class="form-group">
					<div class="control-label col-sm-3" style=""></div>
					<div class="col-sm-7 color-preview iridium"  data-theme="theme_iridium">
						Iridium
						<i class="fa fa-check pull-right" ></i>
					</div>
				</div>
				<div class="form-group">
					<div class="control-label col-sm-3" style=""> </div>
					<div class="col-sm-7 color-preview battleship_gray"  data-theme="theme_battleship_gray">
						Battleship Gray
						<i class="fa fa-check pull-right" ></i>
					</div>
				</div>
				<div class="form-group">
					<div class="control-label col-sm-3" style=""> </div>
					<div class="col-sm-7 color-preview black" data-theme="theme_black">
						Black
						<i class="fa fa-check pull-right" ></i>
					</div>
				</div>

				<div class="form-group">
					<div class="control-label col-sm-3" style=""></div>
					<div class="col-sm-7 color-preview default" data-theme="theme_default">
						Default
						<i class="fa fa-check pull-right" ></i>
					</div>
				</div>

			</div>
		<?php  echo $this->Form->end(); ?>

	</div>

	<div class="modal-footer">
		<button class="btn btn-success" id="save_setting" >Save</button>
		<button class="btn btn-danger" id="close_modal" data-dismiss="modal">Close</button>
	</div>

<script type="text/javascript" >
$(function(){

		$("[data-theme=<?php echo $user_theme ?>]").find('i').show()
  	
		$('body').delegate('#save_setting', 'click', function(event) {
				event.preventDefault();

				var $selected = $('.color-preview').find('i:visible'),
					selected_theme = $.current_theme = ($selected.length) ? $selected.parent('.color-preview').data('theme') : 'theme_default',
					runAjax = true;
					
				var $img = $('header.main-header a.logo img'),
					logo = 'logo_white.png';
					
				/* if( selected_theme == 'theme_default' ) {
					logo = 'logo.png';
				} */
				
				if( $img.length ) {
						$img[0].src = $js_config.base_url + 'images/' + logo; 
				}
				// save to db
				// if(runAjax) {
						$.ajax({
							type:'POST',
							url: $js_config.base_url + 'settings/themes/',
							data: $.param({'selected_theme': selected_theme}),
							global: true,
							dataType: 'JSON',
							success: function(response) {
								if( response.success ) { 
									// console.log($js_config.base_url + 'images/logo_white.png')
									$('#message_box').html(response.msg).slideDown(500)
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

				$others.find('i').hide()
				$(this).find('i').show()

				var data = $(this).data(),
					theme = data.theme,
					ms_theme = $('.main-sidebar').data('theme'),
					mh_theme = $('.main-header').data('theme');

					$('.main-header').removeClass(mh_theme).addClass(theme)
					$('.main-sidebar').removeClass(ms_theme).addClass(theme)

					$('.main-header').data({'theme': theme})
					$('.main-sidebar').data({'theme': theme})
					
				var $img = $('header.main-header a.logo img'),
					logo = 'logo_white.png'
				if( theme == 'theme_default' ) {
					logo = 'logo.png';
				}
					if( $img.length ) {
						$img[0].src = $js_config.base_url + 'images/' + logo; 
					}
		})
		
	/*  */
})
	 
</script>