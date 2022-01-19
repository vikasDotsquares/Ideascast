<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>

<style>
	.no-scroll {
	    overflow: hidden;
	}
	.box-body.clearfix {
		overflow-x: auto;
    	overflow-y: auto;
    	border: 1px solid #dddddd;
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
	.project_group .panel-heading {
		padding: 0;
	}
	.accordion-group .panel .panel-heading .panel-title a:not(.view_share_map) {
		display: inline-block;
		font-size: 13px;
		font-weight: normal;
		width: 75%;
		padding: 10px 15px;
	}

	.panel.panel-default h4.panel-title { display:inline-block; width:100%; }
	.panel.panel-default .view_share_map, .panel.panel-default.expanded .view_share_map {
		border: 1px solid transparent;
		margin-top: 6px;
		padding: 3px 3px 2px;
	}

	/**********************************************/
	.partial_data.box-borders {
	    border-color: #ccc;
	}
	.partial_data {
	    padding: 0;
	    width: 100%;
	    border: none;
	}

	.overview-box {
	    float: left;
	    width: 100%;
	}

	.project_data {
	    padding: 0;
	    min-height: 177px;
	}

	.no-data {
	    color: #bbbbbb;
	    font-size: 30px;
	    left: 4px;
	    position: absolute;
	    text-align: center;
	    text-transform: uppercase;
	    top: 35%;
	    width: 98%;
	}
	/**********************************/


	@media (max-width:567px) {

	.panel.panel-default h4.panel-title { padding:0 10px; }
	.accordion-group .panel .panel-heading .panel-title a:not(.view_share_map) { width:100%; padding:10px 0; }
	.panel.panel-default .view_share_map, .panel.panel-default.expanded .view_share_map { float:left !important; margin:0; margin-right:5px; margin-bottom:10px; }

	}
	.create-group-title {
		min-width: 100px;
	}

</style>

<?php
	$current_user = $this->Session->read("Auth.User.id");
?>
<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left">
					<?php echo $page_heading; ?>
					<p class="text-muted date-time">
						<span>View Project sharing with My Groups </span>
					</p>

				</h1>

			</section>
		</div>

	<div class="box-content">

		<div class="row">
			<div class="col-xs-12">
				<div class="box border-top margin-top">
				  	<div style="padding :7px 0 6px 0; margin: 0; border-top-left-radius: 3px; background-color: #f1f3f4; border: 1px solid #ddd;  border-top-right-radius: 3px;border-top:none;border-left:none;border-right:none; " class="fliter">

				  		<div class="col-sm-4">
				  			<?php
				  			$all_list = $project_list = [];
				  			if(isset($permit_data['pp_data']) && !empty($permit_data['pp_data'])) {

								foreach($permit_data['pp_data'] as $key => $val ) {
									$ProjectGroup = $val['ProjectGroup'];
									$pdata = project_primary_id($ProjectGroup['user_project_id'], 1);

									if(isset($pdata['title']) && !empty($pdata['title'])){
										$all_list[$pdata['id']] = $pdata['title'];
									}
								}
							}
							if (isset($all_list) && !empty($all_list)) {
								$project_list = array_map(function($v){
								    return trim(htmlentities($v), ENT_QUOTES);
								}, $all_list);
								natcasesort($project_list);
							}
				  			 ?>
				  			<label style="width: 100%; vertical-align:middle;" class="custom-dropdown">
								<select class="form-control aqua view_detail">
								<?php if($project_list) { ?>
									<option value="">Projects with Groups</option>
									<?php
										foreach($project_list as $key => $val ) {
										?>
										<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
										<?php } ?>
									<?php }else{ ?>
									<option value="">No Project</option>
								<?php } ?>
								</select>
                            </label>
				  		</div>
				  		<div class="col-sm-6 pull-right">
							<div class="box-tools pull-right" >
								<a href="<?php echo SITEURL.'groups/index'; ?>"  title="Add Group" class="tipText pull-right" style="margin: 12px 0 0 0;
									" >
									<i class="workspace-icon"></i>
								</a>
								<form action="#" method="post" onsubmit="return false;" class="pull-left search-form" style=" width: 240px; float: left; margin: 3px 10px 0 0px;">
									<div class="input-group search-group">
										<input type="text" name="search_string" class="form-control search_string" placeholder="">
										<span class="input-group-btn">
											<button type="submit" name="search" id="search_btn" class="btn btn-flat btn-search  bg-gray"><i class="fa fa-search"></i></button>
										</span>
									</div>
								</form>
							</div>
						</div>
				</div>
						<div class="box-header no-padding" style="">
					<!-- MODAL BOX WINDOW -->
					<!--  // PASSWORD DELETE -->
							<div class="modal modal-danger fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content"></div>
                                    </div>
                                </div>
							 <div class="modal modal-success fade " id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								  <div class="modal-dialog">
										<div class="modal-content"></div>
								  </div>
							 </div>
					<!-- END MODAL BOX -->
						</div>

					<div class="box-body clearfix" >

						<div class="panel-group project_group project_group_div accordion-group" style="display:block">
							<?php  echo $this->element('../Shares/partials/group_search_result', ['type' => 'group', 'model' => 'ProjectGroup']); ?>
						</div>

					</div>

					</div>
				</div>
			</div>
		</div>

	</div>
</div>
<script type="text/javascript">
jQuery(function($) {


    $('body').delegate('.grp_title', 'keyup focus', function(event){
        var characters = 50;

        event.preventDefault();
        var $error_el = $(this).parents('.title-wrapper:first').find('.error');
        var $clear_el = $(this).parents('.title-wrapper:first').find('.clear_group_title');
        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
            $.input_char_count(this, characters, $error_el);
        }
        if($(this).val() != '' && $(this).val() !== undefined){
        	$clear_el.show();
        }
        else {
        	$clear_el.hide();
        }
    })

    $('body').delegate('.clear_group_title', 'click', function(event){
    	event.preventDefault();
    	$(this).parent().find('.grp_title').val('');
    	$(this).hide();
    })

    $('body').delegate('.update_group_title', 'click', function(event){
    	event.preventDefault();
    	var $title_el = $(this).parents('form#frmAddUsersToGroup:first').find('.grp_title')
    		title = $title_el.val(),
    		grp_id = $(this).parents('form#frmAddUsersToGroup:first').find('#group_id').val(),
    		$group_title = $(this).parents('.panel-main:first').find('span.group-name'),
    		$error_el = $(this).parents('form#frmAddUsersToGroup:first').find('.title-wrapper:first').find('.error');

		if(title != '' && title !== undefined){
			$.ajax({
				url: $js_config.base_url + 'groups/update_group_title',
				type: 'POST',
				dataType: 'JSON',
				data: {id: grp_id, title: title},
				success: function(response){
					if(response.success){
						$group_title.text(title);
						$error_el.text('');
					}
				}
			})
		}
		else{
			$(this).parents('form#frmAddUsersToGroup:first').find('.title-wrapper:first').find('.error').text('Please provide Group Title');
		}
    })

	$('body').delegate('.btn_add_users', 'click', function(event){
		event.preventDefault();
		var $t = $(this),
			$form = $t.parents("#frmAddUsersToGroup:first"),
			formdata = $form.serializeArray(),
			selectedUsers = $('.user_list', $form).val();

		$('.user_list').parent().find('.error-message.text-danger').text()

		if( !selectedUsers ) {
			$('.user_list').parents('.form-group:first').find('.error-message.text-danger').text('Please select at least one user.')
			return;
		}

		$form.submit()
	})


    $('.view_detail').on('change', function(event){
    	$('.search_string').val('');
    	$('.search-group .btn-search i').removeClass('fa-times').addClass('fa-search');
    	var searchTerm = $(this).val();

		if(searchTerm == '' || searchTerm === undefined) {
			$('.project_group .panel.panel-main').show();
			return;
		}

		var filter = $('.project_group .panel.panel-main').filter(function() {
		    return ($(this).attr('data-project') === searchTerm);
		});

		$('.project_group .panel.panel-main').hide();
		filter.show();

		/*$('.project-list').each(function(index, el) {
			if($(this).find('li.users-list:visible').length <= 0) {
				$(this).find('.no-users').show();
				// console.log('length', $(this).find('li.users-list:visible').length)
			}
		});*/
    })


	// PASSWORD DELETE
        $.current_delete = {};
        $('body').delegate('.delete-an-item', 'click', function(event) {
            event.preventDefault();
            $.current_delete = $(this);
        });

        $('#modal_delete').on('hidden.bs.modal', function () {
            $(this).removeData('bs.modal');
            $(this).find('.modal-content').html('');
            $.current_delete = {};
        });

	$('html, body').animate({
		scrollTop: 0
	}, 800)

	$('.search_string').attr('placeholder', 'Search Group...');

	$(document).on('click', function(e) {

		var $input = $("input[name=search_string]"),
			$parent = $input.parent(),
			$button = $parent.find('button'),
			$icon = $parent.find('i');

		if( $(e.target).is($input) || $(e.target).is($button) || $(e.target).is($parent) || $(e.target).is($icon) ) {
			$parent.css({'width': '350px'})
		}
		else {
			if( $input.val() == '' )
				$parent.css({'width': '350px'})


		}
	})

	$('.nav-tabs').on('shown.bs.tab', function (e) {
		var now_tab = e.target // activated tab

		// get the div's id
		var divid = $(now_tab).attr('href').substr(1);
		$("#"+divid).text('current tabs: ' + divid);

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
		var panel = $(this).parents(".panel:first");

		$(".project_group .panel").not(panel[0]).each(function() {

			if( $(this).find('.panel-collapse').hasClass('in') ) {
				$(this).find('.open_panel').trigger('click')
			}
		})

		$(".panel").removeClass('expanded', 300, 'swing');

		setTimeout($.proxy(function(){

			if( $(this).hasClass('collapsed')) {
				$(this).parents(".panel").removeClass('expanded', 300, 'swing');
			}
			else {
				$(this).parents(".panel").addClass('expanded', 300, 'swing');
			}
		}, this), 1 );
		event.preventDefault()

		 // $(this).on_off_panel()
	})

	if( $(".project_group .panel-heading[data-key] a.open_panel").length )
		$(".project_group .panel-heading[data-key] a.open_panel").trigger('click');


	$('body').delegate('.my-input', 'click', function(){

			$(this).toggleClass('min');

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
		$('.view_detail').val('');
		var searchTerms = $(this).val();




		if( searchTerms.length > 0) {
				$('#search_btn i').removeClass('fa-search').addClass('fa-times')
				$( '.project_group .panel').hide();
		}
		else {
			$('#search_btn i').removeClass('fa-times').addClass('fa-search')
			$( '.project_group .panel').show();
		}



		var tdtext = $( '.project_group .panel .panel-heading .panel-title a.open_panel .group-name').filter(function () {
        //return $(this).text() == searchTerms;
        return $(this).text().toLowerCase().indexOf(searchTerms.toLowerCase())>=0;
		});



		tdtext.each(function(){
			$(this).parents('.panel').show();

		})



		/* $( '.project_group .panel .panel-heading .panel-title a.open_panel').each(function() {

			var that = $(this);



			var hasMatch = searchTerms.length == 0 || that.is(':contains(' + searchTerms  + ')');
				if( hasMatch ) {
					that.parents('.panel:first').show()

				}
				else {

					that.parents('.panel:first').hide();

				}
		}); */
	});

	$('body').delegate('.add_users', 'click', function(event) {

		event.preventDefault();

		var runAjax = true;
		var that = $(this),
			data = $(this).data(),
			$parent_panel = $(this).parents('.panel:first'),
			$other_panel = $('.panel:not(.panel_add_users)').not($parent_panel);

		$('.open_panel[aria-expanded=true]', $other_panel).trigger('click')


		if( !$('.panel-collapse', $parent_panel).hasClass('in') )
			$('.open_panel', $parent_panel).trigger('click')

		// $('.ajax_overlay_preloader').show()

		if( runAjax ) {
			runAjax = false;

			setTimeout(function() {
				$( '.panel_add_users', $parent_panel ).load( data.remote, function( response, status, xhr ) {

					if ( status == "error" ) {
						var msg = "Sorry but there was an error: ";
						$( "#error" ).html( msg + xhr.status + " " + xhr.statusText );
					}

					$('.panel_add_users').slideDown('slow')
					// $('.ajax_overlay_preloader').fadeOut(300)

					/*$('html, body').animate({
						scrollTop: (  $parent_panel.offset().top - 80)
					}, 800, function(){})*/

				});
			}, 300)
		}
	})

	$('#popup_model_box').on('hidden.bs.modal', function(){
		$(this).removeData()
	})

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

})
</script>

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
	    /* color: #5F9323 !important; */
	}
</style>


