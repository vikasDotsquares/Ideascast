<?php
    $level_data = $this->Permission->confidence_level($element_id);
    $confidence_level = 'Not Set';
    $level_value = 0;
    $level_class = 'dark-gray';
    $level_arrow = 'notsetgrey';
    if(isset($level_data) && !empty($level_data)){
        $level_data = $level_data[0];
        $confidence_level = $level_data[0]['confidence_level'];
        $level_class = $level_data[0]['confidence_class'];
        $level_arrow = $level_data[0]['confidence_arrow'];
        $level_value = $level_data['el']['level'];
    }


$current_org = $this->Permission->current_org();

$element_project = element_project($element_id);
$project_level = ProjectLevel($element_project, $this->Session->read('Auth.User.id'));

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="myModalLabel">Confidence Level</h3>
</div>
<!-- POPUP MODAL BODY -->
<div class="modal-body clearfix confidence-popup">
    <div class="common-tab-sec view-skills-tab">
        <ul class="nav nav-tabs tab-list">
		    <?php
			$activeA  = $activeB = $activetabA = $activetabB = '';
			if(isset($sign_off) && !empty($sign_off)){

				$activeA  = 'active';
				$activeB  = '';
				$activeB  = '';
				$activetabA  = 'active in';
			}else{
				$activeA  = '';
				$activeB  = 'active';
				$activetabB  = 'active in';
			}
			if(!isset($sign_off) || empty($sign_off)){
			?>
            <li class="   <?php  echo $activeB; ?>"> <a data-toggle="tab" class="active slevels" href="#setlevel" aria-expanded="true">Set Level</a> </li>
			<?php } ?>
            <li class="shistoryMain  <?php  echo $activeA; ?> "> <a data-toggle="tab" class="shistory" href="#history" aria-expanded="true">History</a> </li>
        </ul>
        <div class="tab-content">
            <div id="setlevel" class="tab-pane fade <?php  echo $activetabB; ?>">
                <div class="row">
                    <div class="col-sm-12"><span class="same-error"></span></div>
                    <div class="form-group">
                        <label for="UserUser" class="col-lg-2 control-label">Level: </label>
                        <div class="col-lg-10 level-set">
                            <div id="slider"></div>
                        </div>
                    </div>
                    <div class="form-group valuegroup">
                        <label for="UserUser" class="col-lg-2 control-label">Value: </label>
                        <div class="col-lg-10 value-info">
                            <span class="range"><?php //echo $level_value.'%'; ?></span> <i class="level-icon level-ts <?php echo $level_arrow; ?>"></i> <span class="c-level"><?php echo $confidence_level; ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="UserUser" class="col-lg-2 control-label">Comment: </label>
                        <div class="col-lg-10">
                            <input class="form-control" placeholder="50 characters" type="text" value="" id="comments" name="comments" autocomplete="off">
                            <span class="errors"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="history" class="tab-pane fade <?php  echo $activetabA; ?>">
				<div class="confidence-history">
                    <?php if(isset($history) && !empty($history)){
                        foreach ($history as $key => $value) { ?>
                        <?php //pr($value);
                        $full_name = $value[0]['full_name'];
                        $profile_pic = $value['ud']['profile_pic'];
                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
                            $profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
                        } else {
                            $profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }
						//pr($value);
                        ?>
                            <div class="style-people-com">
                                <span class="style-popple-icon-out">
                                    <a class="style-popple-icon "   data-remote="<?php echo SITEURL; ?>/shares/show_profile/<?php echo $value['ud']['user_id']; ?>" id="trigger_edit_profile" data-target="#popup_modal" data-toggle="modal">
            							<img src="<?php echo $profilesPic; ?>" class="user-image tipText" title="<?php echo $full_name; ?>" align="left" width="40" height="40">
                                        <?php if($current_org['organization_id'] != $value['ud']['organization_id']){ ?>
                                            <i class="communitygray18 tipText community-g" title="Not In Your Organization" style="cursor: default;"></i>
                                        <?php } ?>
            						</a>
                                </span>
                                <div class="style-people-info">
                                    <a data-target="#" data-toggle="modal">
                                        <span class="style-people-name" style="cursor: default;">
                                            <span><?php echo $value['el']['level']; ?>%</span>
                                            <span><?php echo htmlentities($value['el']['comment'], ENT_QUOTES, "UTF-8"); ?></span>
                                        </span>
                                        <span class="style-people-title"><?php echo $this->Wiki->_displayDate(date('Y-m-d H:i:s', strtotime($value['el']['created'])), $format = 'd M, Y h:i A') ?></span>
                                    </a>
                                    <?php if($project_level && (!isset($sign_off) || empty($sign_off))) { ?>
            						  <a href="#" class="cf-delete tipText" data-element="<?php echo $element_id; ?>" data-id="<?php echo $value['el']['id']; ?>" title="Delete"><i class="deleteblack"></i> </a>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php }else{ ?>
                        <div class="no-history">No History</div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer confidencefooter">
    <a class="btn btn-success save_confidence" >Set</a>
    <a class="btn btn-danger " data-dismiss="modal">Close</a>
</div>

<style type="text/css">
    #slider .ui-slider-handle {
        outline: 0;
        background: #767676;
        border-radius: 50%;
        top: -7px;
        width: 18px;
        height: 18px;
        color: #fff;
        font-weight: normal;
        font-size: 12px;
        line-height: 18px;
        border: none;
		left: 1%;
	    /* margin-left: 0px; */
    }

    #slider.ui-slider-horizontal {
      top: 14px;
      left:0;
      height:4px;
      width: 100%;
      border: none;
      background: #ccc;
    }
    .no-history {
        color: #bbbbbb;
        display: block;
        font-size: 30px;
        height: 50px;
        margin: 47px 0;
        text-align: center;
        vertical-align: middle;
        width: 100%;
        text-transform: uppercase;
    }
    .errors, .same-error {
        color: #dd4b39;
        font-size: 11px;
    }
</style>


<script>
    $(function(){
        var level_val = '<?php echo $level_value; ?>';

		$(".confidence-history").slimScroll({height: 245, alwaysVisible: true});

		if($('.shistoryMain').hasClass('active')){
			$('.save_confidence').hide();
		}

		$('.shistory').click(function(){
			$('.save_confidence').hide();
			setTimeout(function(){
				$(".confidence-history").slimScroll({height: 245, alwaysVisible: true});
			},300)
		})

		$('.slevels').click(function(){
			$('.save_confidence').show();
		})

        function update_tip(event, ui) {
            // Allow time for exact positioning
            setTimeout(function () {
                $(ui.handle).attr('title', ui.value).tooltip('fixTitle').tooltip('show');
            }, 0);
        }
        function range_setting(range) {

            $( ".range" ).html(range + '%' );
            if(range > 0 && range <= 24) {
                $('.level-icon').removeClass('mediumlowgrey lowgrey highgrey mediumhighgrey notsetgrey').addClass('lowgrey');
                $('.c-level').text('Low');
                $('.ui-slider-range-min').css('background', '#e5030d');
            }
            else if(range >= 25 && range <= 49) {
                $('.level-icon').removeClass('mediumlowgrey lowgrey highgrey mediumhighgrey notsetgrey').addClass('mediumlowgrey');
                $('.c-level').text('Medium Low');
                $('.ui-slider-range-min').css('background', '#e76915');
            }
            else if(range >= 50 && range <= 74) {
                $('.level-icon').removeClass('mediumlowgrey lowgrey highgrey mediumhighgrey notsetgrey').addClass('mediumhighgrey');
                $('.c-level').text('Medium High');
                $('.ui-slider-range-min').css('background', '#e3a809');
            }
            else if(range >= 75 && range <= 100) {
                $('.level-icon').removeClass('mediumlowgrey lowgrey highgrey mediumhighgrey notsetgrey').addClass('highgrey');
                $('.c-level').text('High');
                $('.ui-slider-range-min').css('background', '#5f9322');
            }
            else{
                $('.level-icon').removeClass('mediumlowgrey lowgrey highgrey mediumhighgrey')
                $('.c-level').text('');
                $('.ui-slider-range-min').css('background', '#ccc');
                // $('#slider.ui-slider-horizontal').css('background', '#ccc');
				 $( ".range" ).html('Not Set');
            }
        }

        var slider_move = false;
        var $slider = $( "#slider" ).slider({
            range: "min",
            value: '<?php echo $level_value; ?>',
            min: 1,
            max: 100,
            step: 1,
            slide: function( event, ui ) {
                var range =  ui.value
                slider_move = true;
                $(this).find('.ui-slider-handle').tooltip({
                    animation: false,
                    placement: 'top',
                    trigger: 'manual',
                    container: $(this).find('.ui-slider-handle')[0],
                    title: range
                }).tooltip('show');
                update_tip(event, ui);
                range_setting(range);
				$('.same-error').html('')

                $(this).find('.ui-slider-handle').css('background', '#767676')
            },
            create: function(event, ui) {
                var value = '<?php echo $level_value; ?>';
                // console.log(value)
				//setTimeout(function(){

				 range_setting('<?php echo $level_value; ?>');

				//},500)
                  $(this).slider("option", "value", <?php echo $level_value; ?>);
				 if(value > 0){
				  slider_move = true;
				 }
            },
            stop: function(event, ui) {
                $(this).find('.ui-slider-handle').tooltip('destroy');
            }
        });

        $('body').delegate('#comments', 'keyup focus', function(event){
            var characters = 50;

            event.preventDefault();
            var $error_el = $(this).parent().find('.errors');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })

        $('.style-popple-icon').off('click').on('click', function(event) {

			$('#element_level').modal('hide');
		})

        $('.save_confidence').off('click').on('click', function(event) {
            event.preventDefault();
            var level = $slider.slider('value'),
                comments = $('#comments').val()
				oldlevel = '<?php echo $level_value; ?>';

			//if(oldlevel.length < 1){
            if((!level || !slider_move) ) {
                $('#slider').find('.ui-slider-handle').css('background', '#c00')
            }
			//}
            if(comments.length <= 0 || comments == '' || comments === undefined) {
                $('#comments').parent().find('.errors').text("Comment is required.")
            }

            if(slider_move && level && comments.length > 0){
                var data = {
                    element_id: $js_config.currentElementId,
                    workspace_id: $js_config.currentWorkspaceId,
                    project_id: $js_config.currentProjectId,
                    level: level,
                    comment: comments
                }
                $.ajax({
                    url: $js_config.base_url + 'entities/save_confidence',
                    type: 'POST',
                    dataType: 'JSON',
                    data: data,
                    success: function(response){
                        if(response.success){
                            $.save_confidence = true;
                            $('#element_level').modal('hide');
                        }
                        else{
                            $('.same-error').html(response.msg);
                        }
                    }
                })
            }
        });

        $('.cf-delete').off('click').on('click', function(event) {
            event.preventDefault();
            var $this = $(this);
            var element_id = $(this).data('element');
            var id = $(this).data('id');
            $.ajax({
                url: $js_config.base_url + 'entities/delete_confidence',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    id: id,
                    element_id: element_id,
                    workspace_id: $js_config.currentWorkspaceId,
                    project_id: $js_config.currentProjectId,},
                success: function(response){
                    if(response.success){
                        $.save_confidence = true;
                        $this.parents('.style-people-com:first').slideUp(200, ()=>{
                            $this.parents('.style-people-com:first').remove();
                            setTimeout(()=> {
                                console.log($('.confidence-history .style-people-com').length)
                                if($('.confidence-history .style-people-com').length <= 0){
                                    $('.confidence-history').html('<div class="no-history">No History</div>')
                                }
                            }, 1)

                        })
                    }
                }
            })
        })
    })
</script>


