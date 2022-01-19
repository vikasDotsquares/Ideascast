<?php
echo $this->Html->css('projects/task_reminder');
echo $this->Html->css('projects/tags');
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
echo $this->Html->css('projects/tokenfield/bootstrap-tokenfield');
echo $this->Html->script('projects/plugins/tokenfield/bootstrap-tokenfield', array('inline' => true));
// echo $this->Html->css('projects/bs.checkbox');
//echo $this->Html->script('projects/manage_project', array('inline' => true));
//echo $this->Html->css('projects/manage_project');


$resourcer = $this->Session->read('Auth.User.UserDetail.resourcer');
$resourcer_permit = (isset($resourcer) && !empty($resourcer)) ? $resourcer : false;

?>
<style>
	.no-data-show {
	    pointer-events: none;
	}
	.no-scroll {
	    overflow: hidden;
	}
	.content {
	    padding: 0 15px 0 15px;
	}
	.ui-autocomplete{
		z-index: 9999;
	}
	.sel_tag_container .form-group .loader-icon {
		position: absolute;
		right: -18px;
		top: 59%;
		display: none;
	}
	.multiselect-container.dropdown-menu > li:not(.multiselect-group) {
		margin-bottom: -4px !important;
	}
	.multiselect-container.dropdown-menu li:not(.multiselect-group) a label.checkbox {
		padding: 5px 20px 5px 40px !important;
	}

	.sel_tag_container .form-group {
		position: relative;
	}
	.resp_msg {
	    font-size: 12px;
	    display: block;
	    color: #41a041;
	    position: absolute;
	    bottom: -19px;
	}
	.selectedmytags-input .tokenfield .token .token-label {
		text-transform:none;
	}
	.ui-menu .ui-menu-item {
		text-transform:none;
	}
	.tag-dd-menu a.disabled{
		pointer-events: none;
		/*background-color: #d9d9d9;*/
	}
	.tagmatch-all .fancy_label{position: relative; top: -2px;  }

	.margint6{
		margin-top: 6px;
	}

</style>
<script type="text/javascript">
	$(function(){
		$.popUserId = 0;
		$('.pophover').popover({
	        placement : 'bottom',
	        trigger : 'hover',
	        html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
	    });

        $('html').addClass('no-scroll');

	    ($.adjust_resize = function(){
	        $(".paging-wrapper").animate({
	            minHeight: (($(window).height() - $(".paging-wrapper").offset().top) ) - 17,
	            maxHeight: (($(window).height() - $(".paging-wrapper").offset().top) ) - 17
	        }, 1)
        })();

	    // WHEN DOM STOP LOADING CHECK AGAIN FOR MAIN FRAME RESIZING
	    var interval = setInterval(function() {
	        if (document.readyState === 'complete') {
	            $.adjust_resize();
	            clearInterval(interval);
	        }
	    }, 1);

	    // RESIZE FRAME ON WINDOW RESIZE EVENT
	    $(window).resize(function() {
	        $.adjust_resize();
	    })






	    /*function debounce(func){
            var timer;
            return function(event){
                if(timer) clearTimeout(timer);
                timer = setTimeout(func, 100, event);
            };
        }

        var interval = setInterval(function() {
            if (document.readyState === 'complete') {
                // $('html').removeClass('modal-open');
                clearInterval(interval);
            }
        }, 1000);

		window.addEventListener("resize", debounce(function(e){
            var $cd = $(".paging-wrapper");
            var cmin_height = (($(window).height() - $cd.offset().top) - 24);
            if(cmin_height > 100){
                $cd.animate({'min-height': cmin_height, 'height': cmin_height}, 30);
            }
            else{
                $cd.animate({'min-height': 100}, 30);
            }
        }));


        ;($.resize_wrapper = function(){
            setTimeout(function(){
                var $cd = $(".paging-wrapper");
                var cmin_height = (($(window).height() - $cd.offset().top) - 24);
                if(cmin_height > 100){
                    $cd.animate({'min-height': cmin_height, 'height': cmin_height}, 30);
                }
                else{
                    $cd.animate({'min-height': 100}, 30);
                }

            }, 1)
        })();*/
        $.resourcer_permit = <?php echo $resourcer_permit; ?>
	})
</script>

<div class="row">
    <div class="col-xs-12">

			<section class="main-heading-wrap">
            <div class="main-heading-sec">
                <h1><?php echo $page_heading; ?></h1>
                <div class="subtitles"><span><?php echo $page_subheading; ?> </span></div>
            </div>

            <div class="header-right-side-icon">
				<span class="ico-nudge ico-project-summary tipText tag-nudge-icon" title="Send Nudge" data-toggle="modal" data-target="#modal_nudge" data-remote=<?php echo Router::Url( array( "controller" => "boards", "action" => "send_nudge_board", 'type' => 'tags', 'admin' => FALSE ), true ); ?>></span>
			</div>
        </section>

     	<div class="box-content">
			<div class="row ">
				<div class="col-xs-12">
					<div class="box noborder margint6 postion-rel">
						<div class="tag-right-icon">
							<?php if($resourcer_permit){ ?>
								<a class="tipText to-planning" href="#" style="display: none;" data-type="search" title="Go To Planning"><i class="planningblack18"></i></a>
							<?php } ?>
							<a class="tipText to-engagement" href="#" style="display: none;" data-type="search" title="Go To People"><i class="peopleblack18"></i></a>
						</div>
						<div class="box-header filters tag-page-header" style="">
							<div class="modal modal-success fade" id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog ">
									<div class="modal-content"></div>
								</div>
							</div>
							<!-- /.modal -->
							<div class="tag-header-sec">
								<div class="selectedmytags-input">
										<input type="hidden" name="selectedMyTags" id="selectedMyTags">
										<input type="hidden" name="showingUsersForTags" id="showingUsersForTags">
										<div id="mytagbox">
											<input type="text" name="my_tags_ids" onpaste="return false;" id="my_tags" class="form-control tokenfield " data-placeholder="Tag name..." />
										</div>
								</div>
								<div class="show-match-right">
									<div class="show-dropdown">
										<input type="button" name="show_user_btn" id="show_user_btn" title="Show Matches" class="btn btn-success tipText" disabled="true" value="Show" />
                                        <div class="dropdown">
										<button title="More Actions" class="btn btn-success dropdown-toggle tipText"  type="button" id="menu1" data-toggle="dropdown">
										<span class="caret"></span></button>
										<ul class="dropdown-menu tag-dd-menu" role="menu" aria-labelledby="menu1">
                                            <li><a href="" class="show_all_tagged_people"><i class="tagicon showpeopletags"></i> Show All People with Tags </a></li>
                                            <li><a href="" class="reset_selection_people"><i class="tagicon resettags"></i> Reset Selections and People Listed </a></li>
											<li><a href="" data-remote="<?php echo Router::Url( array( "controller" => "tags", "action" => "tags_setting", 'type' => 'add_remove_tags', 'admin' => FALSE ), true ); ?>" data-toggle="modal" data-target="#dropdown_action_modal" data-action="add_remove_tags" class="add_remove_tags disabled"><i class="tagicon applytags"></i> Add/Remove Tags from People Listed </a></li>
											<li><a href="" data-remote="<?php echo Router::Url( array( "controller" => "tags", "action" => "tags_setting", 'type' => 'clear_all_tags', 'admin' => FALSE ), true ); ?>" data-toggle="modal" data-target="#dropdown_action_modal" data-action="clear_all_tags" class="clear_all_tags disabled"><i class="tagicon alltags"></i>Clear All Tags from People Listed</a></li>
											<li><a href="" data-remote="<?php echo Router::Url( array( "controller" => "tags", "action" => "tags_setting", 'type' => 'delete_my_tags', 'admin' => FALSE ), true ); ?>" data-toggle="modal" data-target="#dropdown_action_modal" data-action="delete_my_tags" class="delete_my_tags"><i class="tagicon deletetags"></i>Delete All My Tags</a></li>
											<!--<li><a href="javascript:void(0)" class="clear_all_tags"><i class="tagicon alltags"></i>Clear All Tags from People Listed</a></li>
											<li><a href="javascript:void(0)" class="delete_my_tags"><i class="tagicon deletetags"></i>Delete All My Tags</a></li>-->
										</ul>
									</div>
									</div>
									<div class="tagmatch-all">
										<label>
											<input type="checkbox" id="match_all_checkbox" name="match_all_checkbox" checked="checked" class="" value="0" >
											<!--<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>&nbsp;-->
											<label class="fancy_label text-black" for="match_all_checkbox">Require All</label>
										</label>
									</div>
									<!--<div class="tagmatch-all">
										<input type="checkbox" id="match_all_checkbox" name="match_all_checkbox" checked="checked" class="fancy_input" value="0" >
										<label class="fancy_label text-black" for="match_all_checkbox">Require All</label>
									</div>-->
								</div>
							</div>
						</div>
						<div class="tag-body clearfix" >
							<div class="tag-data-container data-container">
								<?php
									echo $this->element('../Tags/partials/tags', ['reminder_elements' => [], 'filter' => 'today']);
								?>
							</div>
						</div><!-- /.box-body -->
					</div><!-- /.box -->
     		    </div>
		   </div>
		</div>
    </div>
</div>
<div class="modal modal-danger fade size-normal" id="dropdown_action_modal" role="dialog" aria-hidden="true" aria-labelledby="add_remove_tags_title" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		</div>
	</div>
</div>
<input type="hidden" id="paging_page" value="0" />
<input type="hidden" id="paging_max_page" value="" />
<input type="hidden" id="paging_type" value="tag" />

<script>
$(function(){
	$('#sel_tags').multiselect({
		enableUserIcon: false,
		multiple: true,
		enableHTML: true,
		enableFiltering: true,
		includeSelectAllOption: true,
		nonSelectedText: 'Select Tags',
		numberDisplayed: 2,
		filterPlaceholder: 'Search My Tags',
		enableCaseInsensitiveFiltering: true,
		buttonWidth: '100%',
		maxHeight: 543,
		onSelectAll: function() {
            //$('#isSelectAllTags').val(1);
        },
		onDeselectAll: function() {
			//$('#isSelectAllTags').val(0);
		},
		onChange: function(element, checked) {
			/*$('#isSelectAllTags').val(0);
			var brands = $('#sel_tags option:selected');
			var selected = [];
			$(brands).each(function(index, brand){
				selected.push([$(this).val()]);
			});
			console.log($('#sel_tags').val());console.log('B');
			console.log(selected);console.log('C');
			var selectedss = this.$select.val();console.log(selectedss);*/
		}
	})
	$('.btn-add-tags').on('click', function(event){
		event.preventDefault();
		callAddRemoveFunc('add');
	})
	$('.btn-remove-tags').on('click', function(event){
		event.preventDefault();
		callAddRemoveFunc('remove');
	})
	$('#dropdown_action_modal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget);
		var action = button.data('action');

		$("#dropdown_action_modal").data('modalTask', action);

		var modal = $(this);
	})
	/*$('.show_all_people').on('click', function(event){
		event.preventDefault();
		//
	})*/
	$('.reset_selection_people').on('click', function(event){
		event.preventDefault();

		$('.peoplecount-info .people-count').html(0);
		$('#selectedMyTags').val('');
		$('#showingUsersForTags').val('');
		$('.to-engagement, .to-planning').hide();
		$.resetSorting();
		clearTextFromTagInput('my_tags');

		$.getJSON(
			$js_config.base_url + 'tags/get_saved_tags',
			{ },
			function(result){
				$.availableTags = result;
				$("input#my_tags").tokenfield('destroy');
				// setTimeout(function(){
					$('#my_tags').val('');
					$.add_tokenfield('reset');
					$("input#my_tags").tokenfield('setTokens', []);
				// }, 1)
				$('#tagData').html('<div class="no-row-wrapper" style="position:unset">NO PEOPLE</div>');

				$('.buttons-container .people-section a').addClass('disabled');
				$('.buttons-container .task-section a').addClass('disabled');
				$('.tag-dd-menu a.clear_all_tags, .tag-dd-menu a.add_remove_tags').addClass('disabled');
				$("#show_user_btn").attr('disabled', true);
				$('#match_all_checkbox').prop('checked', true);
				$.nudge_icon();
			}
		);
	})
	$("#dropdown_action_modal").on('hidden.bs.modal', function (event) {
		$(this).removeData('bs.modal');
		$(this).find('.modal-content').html('');
		var modalTask = $(this).data('modalTask');
		if(modalTask == 'clear_all_tags') {
			$('.RecordDeleteClass ').addClass('disabled');
		} else if(modalTask == 'delete_my_tags') {
			$('#paging_page').val(0);

			$('#showingUsersForTags').val('');
			var match_all = 0;
			if ($('#match_all_checkbox').is(":checked")){
				match_all = 1;
			}
			$.resetSorting();
			clearTextFromTagInput('my_tags');
			$.ajax({
				url: $js_config.base_url + 'tags/search_users',
				data: { q: '', match_all: match_all },
				type: 'post',
				//dataType: 'JSON',
				success: function(response){
					if( response != null ) {
						$('#tagData').html(response);
					}
				}
			})
		} else if(modalTask == 'add_remove_tags') {
			var userListType = $('#paging_type').val();
			$('#isSelectAllTags').val(0);
			$('#selectedMultiTags').val('');
			$('#resp_msg').html('');
			$("#sel_tags").multiselect('deselectAll', false);
			$("#sel_tags").multiselect('refresh');
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('');
			if(userListType == 'tag') {
				$('#show_user_btn').trigger('click');
			} else if(userListType == 'people'){
				$('.show_all_tagged_people').trigger('click');
			}
			$.getJSON(
				$js_config.base_url + 'tags/get_saved_tags',
				{ },
				function(result){
					$.availableTags = result;
					$("input#my_tags").tokenfield('destroy');
					$.add_tokenfield();
					$("input#my_tags").tokenfield('setTokens', $('#showingUsersForTags').val())
					//$('#show_user_btn').trigger('click');
					$("#show_user_btn").focus();
					//$("#show_user_btn").attr('disabled', true);
				}
			);
		}
	})

	$('#modal_small').on('hidden.bs.modal', function () {
		$(this).removeData('bs.modal');
	    $(this).find('modal-content').html('');

		$.getJSON(
			$js_config.base_url + 'tags/get_saved_tags',
			{ },
			function(result){console.log('B');
				$.availableTags = result;
				var userListType = $('#paging_type').val();
				//if(userListType == 'tag') {
					if($.popUserTagCnt > 0) {
						$.callbackGetUserTags($.popUserId);
						//$('#show_user_btn').trigger('click');
					} else {
						$('.tag_container.tags_'+$.popUserId).html('');
						$('.action_'+$.popUserId+' a.RecordDeleteClass').addClass('disabled')
					}
			}
		);
	});

	$("#popup_modal").on('hidden.bs.modal', function () {
		$(this).removeData('bs.modal');
	    $(this).find('modal-content').html('');

		$.getJSON(
		    $js_config.base_url + 'tags/get_saved_tags',
		    { },
		    function(result){
		    	$.availableTags = result;
				//$("input#my_tags").tokenfield('destroy');
		    	//$.add_tokenfield();
				var userListType = $('#paging_type').val();
				//if(userListType == 'tag') {
					if($.popUserTagCnt > 0) {
						$.callbackGetUserTags($.popUserId);
						//$('#show_user_btn').trigger('click');
					} else {
						$('.tag_container.tags_'+$.popUserId).html('');
						$('.action_'+$.popUserId+' a.RecordDeleteClass').addClass('disabled')
					}
		    }
		);
	})
})
$.availableTags = <?php echo json_encode($tags); ?>;
function callAddRemoveFunc(action) {

}
</script>
<?php echo $this->Html->script('projects/users_tags', array('inline' => true)); ?>
<?php echo $this->Html->script('jquery.tokeninput', array('inline' => true)); ?>