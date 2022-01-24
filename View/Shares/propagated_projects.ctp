<style type="text/css">
	.no-scroll {
	    overflow: hidden;
	}
	.box-body.clearfix {
		overflow-x: auto;
    	overflow-y: auto;
    	border: 1px solid #dddddd;
	}
</style>
<script type="text/javascript">
jQuery(function($) {

	$('html').addClass('no-scroll');
	$('.nav.nav-tabs').removeAttr('style');

	// RESIZE MAIN FRAME
    ($.adjust_resize = function(){
        $('.box-body.clearfix').animate({
            minHeight: (($(window).height() - $('.box-body.clearfix').offset().top) ) - 17,
            maxHeight: (($(window).height() - $('.box-body.clearfix').offset().top) ) - 17
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

	$('.remove_propagation').on('click', function (e) {
		e.preventDefault();
		var $this = $(this),
			data = $this.data(),
			$cbox = $('#confirm_box'),
			$yes = $cbox.find('#btn_yes'),
			$no = $cbox.find('#btn_no');

		$('#modal_body', $cbox).text(data.msg)
		$('#modal_header', $cbox).addClass('bg-red').html('<i class="fa fa-exclamation-triangle"></i> ' + data.header)

		$cbox.modal({keyboard: true})
			.one('click', '#btn_yes', function () {
				// Ajax request to sign-off/reopen
				// var post = {'data[Element][id]': id, 'data[Element][sign_off]': data.value},
				data_string = $.param({});

				$.ajax({
					type: 'POST',
					data: data_string,
					url: $this.attr('href'),
					global: false,
					dataType: 'JSON',
					success: function (response, statusText, jxhr) {
						if (response.success) {
							if ($.isFunction($.reload_chat)) {
								$.reload_chat();
								console.log('asfsfd')
							}
							$cbox.modal('hide')
							$this.parents('tr:first').remove()
						}
					}
				})

			});
	});

$('html, body').animate({
					scrollTop: 0
				}, 800)

	$('.nav-tabs').on('shown.bs.tab', function (e) {
		var now_tab = e.target // activated tab

		// get the div's id
		var divid = $(now_tab).attr('href').substr(1);
		$("#"+divid).text('current tabs: ' + divid);
		// $.getJSON('xxx.php').success(function(data){
			// $("#"+divid).text(data.msg);
		// });
	})

	$.fn.on_off_panel = function(v) {

		var $group = $(this).parent().parent();
		$group.find(".panel").not($(this).parent()).removeClass('expanded', 300, 'swing')

		var data = $(this).data(),
			$t = $(this);
			toggleBox = data.target;

		setTimeout($.proxy(function(){
			if($(this).hasClass('collapsed')) {
				$(this).parent().removeClass('expanded', 300, 'swing');
			}
			else {
				$(this).parent().addClass('expanded', 300, 'swing');
			}
		}, this), 500);

	}

	$('body').delegate('.panel-heading[data-toggle="collapse"]', 'click', function(event) {
		$(this).on_off_panel()
	})

	var heights = $(".inside").map(function() {
        return $(this).height();
    }).get(),

    maxHeight = Math.max.apply(null, heights);

    $(".inside").height(maxHeight);

	$('body').delegate('.my-input', 'click', function(){

			$(this).toggleClass('min');

	})


/*
	--------------------------------------------------------------------------
	List view control click event to get/set URL hash
	--------------------------------------------------------------------------
 */
	$("body").delegate('a.control', 'click', function (event) {

		event.preventDefault()
		var data = $(this).data(),
			getHash = data.active;

		$('.search_string').val('').trigger('keyup')

		if( getHash == '#user_view' ) {
			$('#project_accordion').hide()
			$('#user_accordion').show()
			$('.search_string').attr('placeholder', 'Search User...')


		}
		else if( getHash == '#project_view' ) {
			$('#user_accordion').hide()
			$('#project_accordion').show()
			$('.search_string').attr('placeholder', 'Search Project...')
		}

		if (history.pushState) {
			history.pushState(null, null, getHash);
		}
		else {
			location.hash = getHash;
		}

		var otherLink = $("#list_controls").find('a.control').not(this)

		if( !otherLink.hasClass('inactive') )
			otherLink.addClass('inactive')

		if( $(this).hasClass('inactive') )
			$(this).removeClass('inactive')

	})

	$('body').delegate('#search_btn', 'click', function(event){
		event.preventDefault();
		$('.search_string').val('').trigger('keyup')
		return false;
	})


	$.expr[":"].contains = $.expr.createPseudo(function(arg) {
		return function( elem ) {
			return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
		};
	});

	$('.search_string').keyup(function(e) {
		e.preventDefault();
		var searchTerms = $(this).val(),
			hashVal = document.location.hash,
			parent = (hashVal.indexOf('project') >= 0 ) ? "#project_accordion" : "#user_accordion";

		if( searchTerms.length > 0) {
				$('#search_btn i').removeClass('fa-search').addClass('fa-times')
		}
		else {
			$('#search_btn i').removeClass('fa-times').addClass('fa-search')
		}

		$(parent + ' .panel .panel-heading .panel-title a').each(function() {

			var data = $(this).data(),
				dataVal = data.value;

			var hasMatch = searchTerms.length == 0 || $(this).is(':contains(' + searchTerms  + ')');
			// console.log($(this).text())

			$(this).toggle(hasMatch);
				if( !hasMatch ) {
					$(this).parents('.panel:first').hide()
				}
				else {
					$(this).parents('.panel:first').show()
				}
		});
	});

/*
	--------------------------------------------------------------------------
	"SELF INVOKING" function to get/set URL hash
	@param 		none
	@return 	none
	--------------------------------------------------------------------------
 */
	var hash = document.location.hash;
	($.hashing = function() {
		if (hash) {
			hash = hash.substring(0, hash.length);
			var hashTag = $('#list_controls a[data-active="' + hash + '"]');
			if( hash == '#user_view' ) {
				$('#project_accordion').hide()
				$('#user_accordion').show()

				$('.search_string').attr('placeholder', 'Search User...')

				$('#user_accordion  .panel-collapse:first').addClass('in').parent().addClass('expanded')
			}
			else {
				$('#user_accordion').hide()
				$('#project_accordion').show()

				$('.search_string').attr('placeholder', 'Search Project...')

				$('#project_accordion  .panel-collapse:first').addClass('in').parent().addClass('expanded')
			}

			if (hashTag.length > 0) {
				hashTag.trigger('click')
			}
		}
		else {
			var hashTag = $('#list_controls a.control:first');
			if (hashTag.length > 0)
				hashTag.trigger('click')
		}
	})()

})
</script>

<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left">
					<?php echo $page_heading; ?>
					<p class="text-muted date-time">
						<span><?php echo $page_subheading; ?></span>
					</p>
				</h1>
				<div class="box-tools pull-right">

					<!-- <form action="#" method="post" onsubmit="return false;" class="pull-left search-form" style=" width: 240px; float: left; margin: 3px 10px 0 0px;">
						<div class="input-group">
							<input type="text" name="search_string" class="form-control search_string" placeholder="Search...">
							<span class="input-group-btn">
								<button type="submit" name="search" id="search_btn" class="btn btn-flat btn-search  bg-gray"><i class="fa fa-times"></i></button>
							</span>
						</div>
					</form> -->
					<div id="list_controls" class="btn-group list-controls">
						<!-- 	<a class="btn btn-success btn-sm tipText control"  title="List By: Project" id="project_view" href="#" data-active="#project_view" >
							<i class="fa fa-briefcase"></i>
						</a>
						<a class="btn btn-success btn-sm tipText control"  title="List By: User" id="user_view" href="#" data-active="#user_view">
							<i class="fa fa-user"></i>
						</a>  -->
					</div>
				</div>
			</section>
		</div>
	<div class="row" >
			<section class="content-header clearfix" style="margin:8px 15px 0 15px;  border-top-left-radius: 3px; background-color: #f5f5f5; border: 1px solid #ddd;  border-top-right-radius: 3px;" >
				<div class="col-md-6 nopadding-left">
					<?php
						if( isset($permit_data) && !empty($permit_data) ){
					?>
					<a href="<?php echo SITEURL;?>shares/my_sharing" class="btn btn-sm btn-success" style="margin: 6px 0 0 0;padding-left:22px; padding-right:22px;opacity: 0.6;"> Shared Projects (<?php echo !empty($permit_data) ? $permit_data : 0 ;?>)</a>&nbsp;
					<?php } else  {?>
					<a href="javascript:void(0);" class="btn btn-sm btn-success disabled" style="margin: 6px 0 0 0;padding-left:22px; padding-right:22px;opacity: 0.6;background-color:#cccccc;border-color:#dddddd; color:#000;"> Shared Projects (<?php echo !empty($permit_data) ? $permit_data : 0 ;?>)</a>&nbsp;
					<?php }
					if( isset($user_permissions) && count($user_permissions) > 0 ){
					?>
					<a href="javascript:void(0);" title=" " class="btn btn-sm btn-success" style="margin: 6px 0 0 0;cursor:default;"> Propagated Projects (<?php echo (count($user_permissions) > 0)? count($user_permissions) :0;?>)</a>
					<?php } else {?>
					<a href="javascript:void(0);" title=" " class="btn btn-sm btn-success disabled" style="margin: 6px 0 0 0;cursor:default;background-color:#cccccc;border-color:#dddddd; color:#000;"> Propagated Projects (<?php echo (count($user_permissions) > 0)? count($user_permissions) :0;?>)</a>
					<?php } ?>

				</div>
				<div class="col-md-6">
					<div class="box-tools pull-right" style="padding: 0 0px 49px 10px "></div>
				</div>
			</section>
		</div>
	<div class="box-content">

		<div class="row">
			 <div class="col-xs-12">
				  <div class="box border-top" style="margin:0px 0 0 !important;">
						<div class="box-header no-padding" style="">
					<!-- MODAL BOX WINDOW -->
							 <div class="modal modal-success fade " id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								  <div class="modal-dialog">
										<div class="modal-content"></div>
								  </div>
							 </div>

                            <div class="modal fade" id="confirm_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content border-radius-top">
                                        <div class="modal-header border-radius-top" id="modal_header"> </div>

                                        <div class="modal-body" id="modal_body"></div>

                                        <div class="modal-footer" id="modal_footer">
                                            <a class="btn btn-success btn-sm" id="btn_yes">Yes</a>
                                            <button type="button" id="btn_no" class="btn btn-danger btn-sm" data-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

					<!-- END MODAL BOX -->
						</div>

					<div class="box-body clearfix list-shares" >
						<div class="panel-group project_group accordion-group" id="project_accordion"  >
							<?php echo $this->element('../Shares/partials/propagated_project', ['user_permissions' => $user_permissions ] ); ?>
						</div>
					</div>

					</div>
				</div>
			</div>
		</div>

	</div>
</div>

<style>

.panel.panel-default .view_share_map {
    color: #fff;
}

.panel-title .sharedlinks {
    display: inline-block;
    font-size: 13px;
    font-weight: normal;
}

.accordion-group .panel .panel-heading .panel-title a:not(.view_share_map) {
    display: inline-block;
    font-size: 13px;
    font-weight: normal;
   width: auto;
}


.sharedlinks span:hover {
    text-decoration: underline;
}

.sharedlinks span {
    cursor: pointer;
}

.sharedlinks span.showgroupbytotalgrp:hover {
    text-decoration: none;
}

.sharedlinks span.showgroupbytotalgrp {
    cursor: default;
}

.col-exp-panel {
    float: right !important;
    margin: 9px 10px 0 0;
}
.strik {
    border: 1px solid #c00;
    left: 5px;
    position: absolute;
    top: 13px;
    transform: rotate(45deg);
    width: 20px;
	opacity: 0.7;
}
.row section.content-header h1 p.text-muted span {
    color: #7c7c7c;
    font-weight: normal;
    text-transform: none;
}
.panel.expanded .panel-heading {
    background-color: #5F9323;
    box-shadow: 0 3px 3px rgba(60, 141, 188, 0.9);
    color: #fff;
}
.panel.expanded .panel-heading .panel-title a:hover {
    color: #fff !important;
}
.panel.expanded .panel-heading:hover a {
    color: #fff !important;
}
.panel.expanded .panel-heading .panel-title a {
    color: #fff !important;
}
.panel.expanded .panel-heading .panel-title  {
    color: #fff !important;
}
.panel .panel-heading:hover a {
    color: #5F9323 !important;
}
</style>