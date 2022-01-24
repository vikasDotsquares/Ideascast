
<?php echo $this->Html->css('projects/list-grid') ?>

<?php echo $this->Html->script('projects/plugins/colored_tooltip', array('inline' => true)); ?>


<script type="text/javascript">
jQuery(function($) {
	$('body').delegate('.ajax-pagination a', 'click', function(e) {
        e.preventDefault()

		var $this = $(this),
		$parent = $this.parents('#list_grid_container').filter(':first'),
		post = { 'project_id': '<?php echo $project_id; ?>' },
		pageUrl = $this.attr('href');

		$.ajax({
			type:'POST',
			data: $.param(post),
			url: pageUrl,
			global: false,
			beforeSend: function( response, status, jxhr ) {
				$parent.html('<div class="progress-bar"><div class="progress"></div></div>')
				//$parent.html('<div id="ajax_overlay" style="position: relative; background: none; display: block; z-index: 0;"><div class="ajax_overlay_loader" style="padding-top: 100px;"></div></div>');
			},
			success: function( response, status, jxhr ) {
				setTimeout(function(){
					$parent.html(response);
				}, 800)
			}
		})

        return false;
    });
})
</script>

<div class="row">
	<div class="col-xs-12">

		<div class="row">
			<section class="content-header clearfix">
					<h1 class="box-title pull-left"><?php echo $page_heading; ?>
						<p class="text-muted date-time">
							<span>View owned projects</span>
						</p>
					</h1>
					<div class="box-tools pull-right">

					</div>
			</section>
		</div>


    <div class="box-content">

            <div class="row ">
                <div class="col-xs-12">


				<?php

				if( isset($projects) && !empty($projects)) {
				?>
                    <div class="box border-top margin-top">
                        <div class="box-header">
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->

                        </div>
						<div class="box-body clearfix list-acknowledge" style="min-height: 600px;">


<!--
<div class="row">
	<div class="col-sm-4">
		<div class="panel panel-black">
			<div class="panel-heading">
				header
			</div>
			<div class="panel-body">
				body
			</div>
			<div class="panel-footer clearfix" style=" ">
				<div class="btn-group pull-left">
					<a class="btn btn-xs bg-white"><i class="fa fa-comment"></i></a>
					<a class="btn btn-xs bg-white"><i class="fa fa-comments"></i></a>
					<a class="btn btn-xs bg-white"><i class="fa fa-comments-o"></i></a>

				</div>
			</div>
		</div>
	</div>
</div>
		-->


<div id="list_grid_container" class="">

	<!-- LIST AND GRID VIEW START	-->
	<ul class="grid clearfix" id="ul_list_grid">
		<?php

		$row_counter = 0;
		$project_counter = ( isset($projects) && !empty($projects) ) ? count($projects) : 0;
		foreach( $projects as $key => $val ) {

			$item = $val['Project'];

			$open_project_link =  SITEURL.'projects/index/'.$item['id']; ?>
		<li>
			<div class="panel <?php echo $item['color_code'] ?>">

				<div class="panel-heading">
					<h4 class="panel-title tipText" title="<?php tipText($item['title']); ?>">
						<a href="<?php echo $open_project_link; ?>">
							<?php
								echo $this->Text->truncate($item['title'],27,array('ending' => '...','exact' => false,'html' => false));
							?>
						</a>

						<div class="btn-group list-btn-group pull-right">
							<a href="#" class="btn btn-default btn-xs color_bucket tipText" title="<?php tipText('Color Options' ); ?>" ><i class="fa fa-paint-brush"></i></a>
							<div class="display_none color_box">
								<div class="colors btn-group">
									<a href="#" data-color="panel-red" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
									<a href="#" data-color="panel-blue" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
									<a href="#" data-color="panel-maroon" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Maroon"><i class="fa fa-square text-maroon"></i></a>
									<a href="#" data-color="panel-aqua" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
									<a href="#" data-color="panel-yellow" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
									<a href="#" data-color="panel-green" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
									<a href="#" data-color="panel-teal" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
									<a href="#" data-color="panel-purple" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
									<a href="#" data-color="panel-navy" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
								</div>
							</div>

							<a class="btn btn-default btn-xs tipText" title="<?php tipText('Edit Project Details' ); ?>" href="<?php echo SITEURL.'projects/manage_project/'.$item['id']; ?>" >
								<i class="fa fa-pencil"></i>
							</a>

							<a class="btn btn-default btn-xs tipText" title="<?php tipText('Open Project' ); ?>" href="<?php echo $open_project_link; ?>" >
								<i class="fa fa-folder-open"></i>
							</a>

							<a class="btn btn-default btn-xs tipText" title="<?php tipText('Calendar' ); ?>" href="<?php echo SITEURL; ?>users/event_calender/<?php echo $item['id'] ?>" >
								<i class="fa fa-calendar"></i>
							</a>

							<a class="btn btn-default btn-xs tipText" title="<?php tipText('Resources' ); ?>" href="<?php echo SITEURL; ?>users/projects/<?php echo $item['id'] ?>" >
								<i class="fa fa-file"></i>
							</a>
						</div>
					</h4>
				</div>

				<div class="panel-body">
					<div class="grid-dates"  style="">
						<span>Created: <?php
						//echo date('d M Y', $item['created']);
						echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$item['created']),$format = 'd M Y');
						?></span>
						<span>Updated: <?php
						//echo date('d M Y', $item['modified']);
						echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$item['modified']),$format = 'd M Y');
						?></span>
					</div>
					<div class="text-content" style="">
					<?php
						echo $this->Text->truncate(strip_tags($item['description'], '<br />'),470,  array('ending' => '...','exact' => false,'html' => true));
					?>
					</div>
				</div>

				<div class="panel-footer clearfix">

					<div class="btn-group grid-btn-group pull-left">

						<a href="#" class="btn btn-default btn-xs color_bucket tipText" title="<?php tipText('Color Options' ); ?>" ><i class="fa fa-paint-brush"></i></a>

						<div class="color_box color_box_bottom" style="display:none">
							<div class="colors btn-group">
								<a href="#" data-color="panel-red" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
								<a href="#" data-color="panel-blue" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
								<a href="#" data-color="panel-maroon" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Marron"><i class="fa fa-square text-maroon"></i></a>
								<a href="#" data-color="panel-aqua" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
								<a href="#" data-color="panel-yellow" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
								<a href="#" data-color="panel-green" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
								<a href="#" data-color="panel-teal" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
								<a href="#" data-color="panel-purple" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
								<a href="#" data-color="panel-navy" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
							</div>
						</div>

						<a class="btn btn-default btn-xs tipText" title="<?php tipText('Edit Project Details' ); ?>" href="<?php echo SITEURL.'projects/manage_project/'.$item['id']; ?>" >
							<i class="fa fa-pencil"></i>
						</a>

						<a class="btn btn-default btn-xs tipText" title="<?php tipText('Open Project' ); ?>" href="<?php echo $open_project_link; ?>" >
							<i class="fa fa-folder-open"></i>
						</a>

						<a class="btn btn-default btn-xs tipText" title="<?php tipText('Calendar' ); ?>" href="<?php echo SITEURL; ?>users/event_calender/<?php echo $item['id'] ?>" >
							<i class="fa fa-calendar"></i>
						</a>

						<a class="btn btn-default btn-xs tipText" title="<?php tipText('Resources' ); ?>" href="<?php echo SITEURL; ?>users/projects/<?php echo $item['id'] ?>" >
							<i class="fa fa-file"></i>
						</a>

					</div>

					<div class="pull-right">

						<a href="<?php echo SITEURL . 'projects/reports/' . $item['id'] ?>" title="Project Report" class="btn btn-default pull-right btn-xs text-bold more" >More <i class="fa fa-fw fa-angle-double-right"></i></a>

					</div>

				</div>

			</div>
		</li>
		<?php } ?>
	</ul>
</div>

<div class="row">
	<div class="ajax-pagination">
		<?php  echo $this->element('jeera_paging');  ?>
	</div>
</div>
					    </div>
                    </div>
				<?php }else {
					echo $this->element('../Projects/partials/error_data', array(
							'error_data' => [
								'message' => "You have not created any project yet.",
								'html' => "Click <a class='' href='".Router::Url(array('controller' => 'projects', 'action' => 'manage_project', 'admin' => FALSE ), TRUE)."'>here</a> to create project now."
							]
					));
				}
				?>
                </div>
            </div>
        </div>
	</div>
</div>


<?php echo $this->Html->script('templates/create.workspace', array('inline' => true)) ?>
<?php echo $this->Html->script('templates/list-grid', array('inline' => true)) ?>
<script type="text/javascript" >
$(function() {

	$(window).scroll(function() {
		var $active_element = $('.color_box').filter(function() {
			return $(this).is(":visible") == true;
		});
		if( $active_element.length ) {

			var s =  $active_element.isScrolledIntoView();
			var p =  $active_element.parent().isScrolledIntoView();
			if ( ( s.top + $active_element.height() ) > $(window).height() ) {
				$active_element.removeClass('color_box_bottom').addClass('color_box_top')
			}
			else if ( ( p.top ) < 100 ) {
				$active_element.removeClass('color_box_top').addClass('color_box_bottom')
			}
		}
	});

	$('#popup_modal').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });

	$('.color_bucket').each(function () {
		$(this).data('color_box', $(this).parent().find('.color_box'))
		$(this).parent().find('.color_box').data('color_bucket', $(this))
	})

	$.getViewportOffset = function($e) {
	  	var $window = $(window),
			scrollLeft = $window.scrollLeft(),
			scrollTop = $window.scrollTop() + 200,
			offset = $e.offset(),
			rect1 = { x1: scrollLeft, y1: scrollTop, x2: scrollLeft + $window.width(), y2: scrollTop + $window.height() },
			rect2 = { x1: offset.left, y1: offset.top, x2: offset.left + $e.width(), y2: offset.top + $e.height() };

			return {
				left: (offset.left) - scrollLeft,
				top: (offset.top) - scrollTop,
				insideViewport: rect1.x1 < rect2.x2 && rect1.x2 > rect2.x1 && rect1.y1 < rect2.y2 && rect1.y2 > rect2.y1
			};
		}

	$.fn.isScrolledIntoView = function() {
		var offset = $(this).offset();

		return {
			left: offset.left - $(window).scrollLeft(),
			top: offset.top -  $(window).scrollTop()
		};
	};

	$.fn.visible = function(partial) {

		var $t            = $(this),
			$w            = $(window),
			viewTop       = $w.scrollTop(),
			viewBottom    = viewTop + $w.height(),
			elTop         = $t.offset().top,
			elVisibility  = (viewBottom - elTop) / $t.height();

		return ( elVisibility >= partial);
	}

	$('.color_bucket').on('click', function (event) {
		event.preventDefault();
		var $color_box = $(this).parent().find('div.color_box'),
			vars = {
				offset: $color_box.offset(),
				top: $color_box.offset().top,
				left: $color_box.offset().left,
				w: $color_box.width(),
				h: $color_box.height(),
				sidebar_width: $('aside.main-sidebar').width(),
				panel: $(".color_bucket:first").parents(".panel:first"),
				panel_offset: $(".color_bucket:first").parents(".panel:first").offset(),
				wWidth: $(window).width(),
				wHeight: $(window).height(),
				dHeight: $(document).height(),
				scroll: $(window).scrollTop()
			}

			$color_box.slideToggle(200)
			if( $color_box.offset().left < 230 ) { // set right
				$color_box.removeClass('color_box_left').addClass('color_box_right')
			}
			if( vars.panel_offset > ($(window).width() - 300) ) {// set left
				$color_box.removeClass('color_box_right').addClass('color_box_left')
			}

			var s =  $color_box.isScrolledIntoView();
			var p =  $color_box.parent().isScrolledIntoView();
			if ( ( p.top + $color_box.height() + $color_box.parent().height() ) > ( $(window).height() - 80 ) ) { // set on top
				$color_box.removeClass('color_box_bottom').addClass('color_box_top')
			}
			else if ( ( p.top ) < 100 ) { // set to bottom
				$color_box.removeClass('color_box_top').addClass('color_box_bottom')
			}
			console.log(( p.top + $color_box.height() + $color_box.parent().height() ))

	});

	$(".el_color_box").on('click', function( event ) {
		event.preventDefault();

		var $cb = $(this)
		var $hd = $cb.parents('.panel:first')
		var cls = $hd.attr('class')

		var foundClass = (cls.match (/(^|\s)panel-\S+/g) || []).join('')
		if( foundClass != '' ) {
			$hd.removeClass(foundClass)
		}
		var applyClass = $cb.data('color')

		$hd.addClass(applyClass);

		$(this).setPanelColorClass();


		// SEND AJAX HERE TO CHANGE THE COLOR OF THE ELEMENT
	})

	$.fn.setPanelColorClass = function() {

		var url = $(this).data('remote');
		var color_code = $(this).data('color');
		var data = $.param({'color_code': color_code});

		$.ajax({
			type:'POST',
			data: data,
			url: url,
			global: true,
			success: function( response, status, jxhr ) {
				if( status == 'success' ) {
					console.log('success')
				}
				else {
					console.log('error')
				}

			},
		});
	}


	$('body').on('click', function (e) {
		$('.color_bucket').each(function () {
			//the 'is' for buttons that trigger popups
			//the 'has' for icons within a button that triggers a popup
			if ( !$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.color_box').has(e.target).length === 0) {
				var color_box = $(this).data('color_box')
				color_box.hide()
			}
		});


	});


})
$(window).load( function() {
	setTimeout(function() {
		$.setLimitedText( 140 )
	}, 500)
})

$(function() {


})

</script>
