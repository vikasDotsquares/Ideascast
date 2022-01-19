<style>
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


#list_controls .control {
   /* box-shadow: 0 1px 0 rgba(2, 2, 2, 0.5) inset, 0 0 3px rgba(0, 0, 0, 0.1) inset;
    font-size: 14px;
    padding: 7px 11px 6px 12px;*/

	box-shadow: none;
}

.declineres{
		text-transform:none;
	}

</style>


<script type="text/javascript">
	jQuery(function($) {

	$('html, body').animate({
		scrollTop: 0
	}, 800)


	$('body').on('click', function(e) {

		var $input = $("input[name=search_string]"),
			$parent = $input.parent(),
			$button = $parent.find('button'),
			$icon = $parent.find('i');

		if( $(e.target).is($input) || $(e.target).is($button) || $(e.target).is($parent) || $(e.target).is($icon) ) {
			$parent.css({'width': '350px'})
		}
		else {
			if( $input.val() == '' )
				//$parent.css({'width': '80px'})
				$parent.css({'width': '350px'})
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

	$('body').delegate('a.open_panel', 'click', function(event) {

		$(".panel").removeClass('expanded', 300, 'swing');
		setTimeout($.proxy(function(){
			if( $(this).hasClass('collapsed')) {

				$(this).parents(".panel").removeClass('expanded', 300, 'swing');
			}
			else {
				$(this).parents(".panel").addClass('expanded', 300, 'swing');
			}
		}, this), 500);
		event.preventDefault()



		// $(this).on_off_panel()
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

		$(parent + ' .panel .panel-heading .panel-title a').each(function() {
			var data = $(this).data(),
				dataVal = data.value;

			var hasMatch = searchTerms.length == 0 || $(this).is(':contains(' + searchTerms  + ')');
			// console.log($(this).text())

			//$(this).toggle(hasMatch);
				 if( !hasMatch ) {
				 	 $(this).hide()

				 }
				 else {
					//$(this).parents('.panel:first').show()
					$(this).show();
					$(this).parents('.panel').show();
				 }

				 $('#project_accordion .panel .panel-heading .panel-title a.open_panel').each(function(){

					if($(this).css('display')=='none'){
					console.log($(this));
						$(this).parents('.panel').hide();
					}
				 })


				 $('#user_accordion .panel .panel-heading .panel-title a.open_panel').each(function(){

					if($(this).css('display')=='none'){
					console.log($(this));
						$(this).parents('.panel').hide();
					}
				 })

				 $('a.view_share_map').each(function(){
					$(this).show();
				 })
		});
	});


	/*         $.expr[":"].contains = $.expr.createPseudo(function (arg) {
                return function (elem) {
                    return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
                };
            });



            $('.search_string').keyup(function (e) {
			e.preventDefault();
            var searchTerms = $(this).val(),
			hashVal = document.location.hash,
			parent = (hashVal.indexOf('project') >= 0 ) ? "#project_accordion" : "#user_accordion";

                $(parent + ' .panel .panel-heading .panel-title a').each(function () {
                    var hasMatch = searchTerms.length == 0 || $(this).is(':contains(' + searchTerms + ')');

                    $(this).toggle(hasMatch);
                      if( !hasMatch ) {
					 $(this).parents('.panel:first').hide()
					 }
					 else {
						$(this).parents('.panel:first').show()
					  }
					console.log(hasMatch);


                });
            }); */


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

				$('#project_accordion  .panel-collapse:not(.close_panel):first').addClass('in').parent().addClass('expanded')
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
					'easeOutBounce'
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
						<span>View People interested in working with you </span>
						<span> </span>
					</p>

				</h1>

			</section>
		</div>
		<?php echo $this->Session->flash(); ?>


			<div class="row" >
			<section class="content-header clearfix" style="margin:8px 15px 0 15px;  border-top-left-radius: 3px;    background-color: #f1f3f4;     border: 1px solid #ddd;  border-top-right-radius: 3px;" >

				<div class="box-tools pull-right" style="padding:5px 0px 7px 10px">

					<form action="#" method="post" onsubmit="return false;" class="pull-left search-form" style=" width: 240px; float: left; margin: 3px 10px 0 0px;">
						<div class="input-group search-group">
							<input type="text" name="search_string" class="form-control search_string" placeholder="">
							<span class="input-group-btn">
								<button type="submit" name="search" id="search_btn" class="btn btn-flat btn-search  bg-gray"><i class="fa fa-times"></i></button>
							</span>
						</div>
					</form>


					<div id="list_controls" class="btn-group list-controls">
						<a class="btn btn-success btn-sm tipText control"  title="List By: Project" id="project_view" href="#" data-active="#project_view" >
							<i class="fa fa-briefcase"></i>
						</a>
						<a class="btn btn-success btn-sm tipText control"  title="List By: User" id="user_view" href="#" data-active="#user_view">
							<i class="fa fa-user"></i>
						</a>
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
		<?php  echo $this->element('../Boards/partial/project_search_result' ); ?>

	</div>

	<div class="panel-group user_group accordion-group" id="user_accordion" style="display:none">
			<?php  echo $this->element('../Boards/partial/user_search_result' ); ?>
	</div>

					</div>

					</div>
				</div>
			</div>
		</div>

	</div>
</div>
<style>
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

<!-- MODAL BOX WINDOW -->
<div class="modal modal-success fade" id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>
<div class="modal modal-success fade" id="popup_model_box_decline" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>


<script type="text/javascript" >
		$("body").delegate(".decline_list", 'click', function (event) {
		event.preventDefault();
		var $that = $(this);
			//$panel = $that.parents('.panel:first'),
		var board_id=	$that.attr('data-id');

		BootstrapDialog.show({
			title: 'Confirmation',
			message: 'Are you sure you want to decline this interest received?',
			type: BootstrapDialog.TYPE_DANGER,
			draggable: true,
			buttons: [
				{
					icon: 'fa fa-check',
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: true,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url: $js_config.base_url + 'boards/project_decline',
								type: "POST",
								data: $.param({id: board_id}),
								dataType: "JSON",
								global: false,
								success: function (response) {
									//$('#projectId').trigger('change');
									location.reload();
								}
							})
						)
					}
				},
				{
					label: ' No',
					icon: 'fa fa-times',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
			]
		});



		/* $(".project_accordion, .user_accordion").tooltip({
			placement:'top',
			template:'<div class="tooltip declineres" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>',
			container:'body',

		}) */

	});
</script>