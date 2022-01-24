<?php
?>
<style>
	.no-scroll {
	    overflow: hidden;
	}
	.box-body.list-shares {
		overflow-x: auto;
    	overflow-y: auto;
    	border: 1px solid #dddddd;
	}
	.fa-user-victor {
	    background: url(../images/icons/user-victor-black.png) no-repeat center;
	    height: 18px;
	    width: 18px;
	    background-size: 100%;
	    margin-bottom: -4px;
	    display: inline-block;
	}
	.search-form { border: none; }
	.input-group.search-group {
		border: 1px solid #ccc;
		border-collapse: separate;
		display: table;
		float: right;
		position: relative;
		transition: all 0.6s ease-in-out 0s;
		width: 350px;
	}
	    .sharing-task-search{ width: 350px; float: left; margin: 3px 0px 0 0px;}

	    .sharing-task-search .input-group.search-group{
	        width: 100%;
	        max-width: 350px;
	    }
	 @media (min-width:992px) and (max-width:1100px) {
	.sharing-task-search{ width: 250px;}
	}

</style>
<script type="text/javascript">
		jQuery(function($) {
			$('#modal_small').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('');
		});

		$.current_delete = {};
        $('body').delegate('.delete-an-item', 'click', function(event) {
            console.log(' delete')
            event.preventDefault();
            $.current_delete = $(this);
            console.log('$.current_delete', $.current_delete)
        });

		$('#modal_delete').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('');
			$.current_delete = {};
		});


	  $('html, body').animate({
		scrollTop: 0
	  }, 800,'easeOutBounce')


	$('body').on('click', function(e) {

		var $input = $("input[name=search_string]"),
			$parent = $input.parent(),
			$button = $parent.find('button'),
			$icon = $parent.find('i');

		if( $(e.target).is($input) || $(e.target).is($button) || $(e.target).is($parent) || $(e.target).is($icon) ) {
			//$parent.css({'width': '350px'})
		}
		else {
			if( $input.val() == '' ){}
				//$parent.css({'width': '80px'})
				// $parent.css({'width': '350px'})
		}
	})

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

	$('body').delegate('a .open_panel', 'click', function(event) {

		$('.panel-collapse').not($(this).parents('.panel').find('.panel-collapse')).collapse('hide');
		$(".panel").removeClass('expanded', 300, 'swing');
		//$(".panel").removeClass('opened', 300, 'swing');
		setTimeout($.proxy(function(){
			if( $(this).hasClass('collapsed')) {
				$(this).parents(".panel").removeClass('expanded', 300, 'swing');
				//$(this).parents(".panel").removeClass('opened', 300, 'swing');
			}
			else {
				$(this).parents(".panel").addClass('expanded', 300, 'swing');
				//$(this).parents(".panel").addClass('opened', 300, 'swing');
			}
		}, this), 100);
		event.preventDefault()



		//$(this).on_off_panel()
	})

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
			if( $('.search_string').val() == '' )
				$('.search_string').attr('placeholder', 'Search User...');
		}
		else if( getHash == '#project_view' ) {
			$('#user_accordion').hide()
			$('#project_accordion').show()

			if( $('.search_string').val() == '' )
				$('.search_string').attr('placeholder', 'Search Project...');
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

		var totalSearch = false;

		$(parent + ' .panel .panel-heading .panel-title a .open_panel').each(function() {
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
					totalSearch = true;
				}
		});

		if( totalSearch == false ){
			$(".abshowhoga").show();
		} else {
			$(".abshowhoga").hide();
		}


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
			if( hash == '#project_view' ) {
				$('#project_accordion').hide()
				$('#user_accordion').show()

				$('.search_string').attr('placeholder', 'Search User...')

				//$('#user_accordion .panel-collapse:first').addClass('in').parent().addClass('expanded')

			}
			else {
				$('#user_accordion').hide()
				$('#project_accordion').show()

				$('.search_string').attr('placeholder', 'Search Project...')

				//$('#project_accordion .panel-collapse:not(.close_panel):first').addClass('in').parent().addClass('expanded')

				// $('#project_accordion .panel-collapse:first').addClass('in').parent().addClass('expanded')

			}

			if (hashTag.length > 0) {
				hashTag.trigger('click')
			}
		}
		else {
			var hashTag = $('#list_controls a.control[data-active="#user_view"]');
			if (hashTag.length > 0)
				hashTag.trigger('click')
		}

	})()

	/* SHOW/HIDE SELECTED PROJECT COMMING FROM projects/lists;
		* SHOW ONLY PANEL WHOSE URL-ID IS MATCHED, REST ARE CLOSED BY DEFAULT */
		// HIDE THE PANEL
		$('#project_accordion .panel .panel-heading[data-key]').each(function() {
				var $panel = $(this).parents('.panel:first'),
					$panel_heading = $panel.find('.panel-heading');
				if( $panel_heading.data('key') > 0 ) {
					//$panel_heading.trigger('click')
					$('html, body').animate({
						 scrollTop: ($panel_heading.offset().top) -80
					}, 1500,
				 	//'easeOutBounce'
					)
				}

		})
})
</script>

<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left">
					<?php echo $page_heading; ?>
					<p class="text-muted date-time">
						<span><?php echo $page_subheading; ?> </span>
					</p>
				</h1>
			</section>
		</div>


			<div class="row" >
			<section class="content-header clearfix" style="margin:8px 15px 0 15px;  border-top-left-radius: 3px;    background-color: #f1f3f4;     border: 1px solid #ddd;  border-top-right-radius: 3px;" >
				<div class="col-md-7 col-lg-6 nopadding-left">
					<?php
					if( isset($permit_data['pp_data']) && !empty($permit_data['pp_data']) && count($permit_data['pp_data']) > 0){
					?>
					<a href="javascript:void(0);" class="btn btn-sm btn-success" style="margin: 6px 0 0 0;padding-left:22px; padding-right:22px;cursor:default; "> Shared Projects (<?php echo ( isset($permit_data['pp_data']) && !empty($permit_data['pp_data']) && count($permit_data['pp_data']) > 0) ? count($permit_data['pp_data']) : 0 ;?>)</a>&nbsp;
					<?php } else {?>

					<a href="javascript:void(0);" class="btn btn-sm btn-success disabled" style="margin: 6px 0 0 0;padding-left:22px; padding-right:22px;cursor:default;background-color:#cccccc;border-color:#dddddd; color:#000;"> Shared Projects (<?php echo ( isset($permit_data['pp_data']) && !empty($permit_data['pp_data']) && count($permit_data['pp_data']) > 0) ? count($permit_data['pp_data']) : 0 ;?>)</a>&nbsp;

					<?php }
					$share_cont = ( isset($permit_data['pp_data']) && !empty($permit_data['pp_data']) && count($permit_data['pp_data']) > 0) ? count($permit_data['pp_data']) : 0 ;
					if( isset($user_permissions) && $user_permissions > 0 ){

					if($share_cont == 0){
						$css = "margin: 6px 0 0 0; opacity: 1;";
					}else{
						$css = "margin: 6px 0 0 0; opacity: 0.6;";
					}
						?>
					<a href="<?php echo SITEURL;?>shares/propagated_projects" class="btn btn-sm btn-success" style="<?php echo $css; ?>"> Propagated Projects (<?php echo ($user_permissions > 0)? $user_permissions :0;?>)</a>
					<?php } else { ?>
						<a class="btn btn-sm btn-success disabled" style="margin: 6px 0 0 0; opacity: 0.6; background-color:#cccccc;border-color:#dddddd; color:#000;"> Propagated Projects (<?php echo ($user_permissions > 0)? $user_permissions :0;?>)</a>
					<?php }

					if( isset($permit_data['pp_data']) && !empty($permit_data['pp_data']) && count($permit_data['pp_data']) > 0 ){
					?>
					<div id="list_controls" class="btn-group list-controls">

							<a class="btn btn-success btn-sm tipText control"  title="List By: User" id="user_views" href="#" data-active="#user_view" >
								<i class="fa fa-user"></i>
							</a>
							<a class="btn btn-success btn-sm tipText control"  title="List By: Project" id="project_views" href="#" data-active="#project_view" >
								<i class="fa fa-briefcase"></i>
							</a>

						</div>
					<?php } /* else { ?>
					<div id="list_controls" class="btn-group list-controls">

							<a class="btn btn-success btn-sm tipText control disabled"  title="List By: User" href="#" style="background-color:#cccccc;border-color:#dddddd; color:#000;">
								<i class="fa fa-user"></i>
							</a>
							<a class="btn btn-success btn-sm tipText control disabled"  title="List By: Project" href="#"  style="background-color:#cccccc;border-color:#dddddd; color:#000;" >
								<i class="fa fa-briefcase"></i>
							</a>

						</div>
					<?php } */ ?>

				</div>
				<div class="col-md-5  col-lg-6 nopadding-right">
					<div class="box-tools pull-right" style="padding: 0 0px 10px 10px ">
						<div class="modal modal-success fade" id="modal_small" style="display: none;">
							<div class="modal-dialog">
								<div class="modal-content border-radius"></div><!-- /.modal-content -->
							</div><!-- /.modal-dialog -->
						</div>
						<div class="modal modal-danger fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content"></div>
							</div>
						</div>
						<form action="#" method="post" onsubmit="return false;" class="pull-left search-form sharing-task-search" >
							<div class="input-group search-group">
								<input type="text" name="search_string" class="form-control search_string" placeholder="Search User...">
								<span class="input-group-btn">
									<button type="submit" name="search" id="search_btn" class="btn btn-flat btn-search  bg-gray"><i class="fa fa-times"></i></button>
								</span>
							</div>
						</form>



					</div>
				</div>
			</section>
		</div>

	<div class="box-content">

		<div class="row">
			 <div class="col-xs-12">
				  <div class="box border-top  ">
						<div class="box-header no-padding" style="">
					<!-- MODAL BOX WINDOW -->
							 <div class="modal modal-success fade " id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								  <div class="modal-dialog">
										<div class="modal-content"></div>
								  </div>
							 </div>
					<!-- END MODAL BOX -->
						</div>

					<div class="box-body clearfix list-shares" >

						<!--
						<div class="toggle-wrapper">
							<input type="checkbox" id="buttonThree" />
							<label for="buttonThree">
								<i></i>
							</label>
						</div>
						-->

<div class="panel-group project_group accordion-group" id="project_accordion" style="display:none">
		<?php  echo $this->element('../Shares/partials/project_search_result', ['type' => 'project', 'model' => 'ProjectPermission']); ?>

</div>

<div class="panel-group user_group accordion-group" id="user_accordion" style="display:none">
	<?php  echo $this->element('../Shares/partials/user_search_result', ['type' => 'project', 'model' => 'ProjectPermission']); ?>

</div>

					</div>

					</div>
				</div>
			</div>
		</div>

	</div>
</div>
<style>
	.peoplethumb .btn {
		padding: 2px 8px;
		text-align: center;
		display: inline-block;
		vertical-align: middle;
	}
	.row section.content-header h1 p.text-muted span {
	    color: #7c7c7c;
	    font-weight: normal;
	    text-transform: none;
	}
	.pointer { cursor:pointer; }
	.panel.expanded .panel-heading {
	    background-color: #5F9323;
	    box-shadow: 0 3px 3px rgba(60, 141, 188, 0.9);
	    color: #fff;
	}
	.panel.expanded .panel-heading .panel-title a.hover-grn:hover {
	    color: #fff !important;
	}
	.panel.expanded .panel-heading:hover a {
	   /*  color: #fff !important; */
	}
	.panel.expanded .panel-heading .panel-title a.hover-grn {
	    color: #fff !important;
	}
	.panel.expanded .panel-heading .panel-title  {
	    color: #fff !important;
	}
	.panel .panel-heading:hover a {
	    /* color: #5F9323 !important; */
	}
	.sort-search {
		text-align: right;
	}
</style>
<script type="text/javascript">
	$(()=>{

		$('html').addClass('no-scroll');
		$('.nav.nav-tabs').removeAttr('style');

		// RESIZE MAIN FRAME
	    ($.adjust_resize = function(){
	        $('.box-body.list-shares').animate({
	            minHeight: (($(window).height() - $('.box-body.list-shares').offset().top) ) - 17,
	            maxHeight: (($(window).height() - $('.box-body.list-shares').offset().top) ) - 17
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
	})
</script>