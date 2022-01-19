
<?php $location_types = $this->User->location_types(); ?>
<?php $current_location = $this->User->current_location(); ?>
<?php $user_unavailability = $this->User->user_unavailability($this->Session->read('Auth.User.id'));
      $UserPrompt = UserPrompt();
	  if($UserPrompt==1){
		  $pclass ="location_prompt_checked";
	  }else{
		  $pclass ="";
	  }

	  $is_session_prompt = $this->Session->read('Auth.User.is_prompt');
?>

<?php $work_availability = $this->Scratch->work_availability(); ?>

<li class="dropdown location-list">
    <a  class="location-info-h" href="#" >
        <span class="location-text tipText current-location" title="Workplace" data-placement="bottom"><?php echo $current_location; ?></span>
        <span class="location-text-work current-status tipText" title="Work Status" data-remote="<?php echo Router::url(array('controller' => 'settings', 'action' => 'availability', 'admin' => false)); ?>" data-target='#availability_modal'  data-toggle='modal'><?php echo $user_unavailability; ?></span>
    </a>
	<a class="work-availability tipText" title="Your Available Work Hours Today" href="<?php echo Router::url(array('controller' => 'settings', 'action' => 'notification', 'tab' => 'availability', 'admin' => false)); ?>"> <?php echo $work_availability; ?> hr day </a>
    <a  class="location-arrow-link" data-toggle='dropdown' href="javascript:void(0)">
        <span class="nav-icon-all dropdown-toggle" ><i class="icon-size-nav1 location-arrow"></i></span>
    </a>
    <?php if(isset($location_types) && !empty($location_types)){ ?>
    <ul class="dropdown-menu loc-dd">
        <?php foreach ($location_types as $key => $value) {
            $type_id = $value['UserLocationType']['id'];
            $type_text = $value['UserLocationType']['location'];
            $type_icon = $value['UserLocationType']['icon'];
         ?>

            <li class="dropdown-submenu">
                <a href="#" data-id="<?php echo $type_id; ?>" data-text="<?php echo $type_text; ?>" class="location-types"><span class="location-nav-icon"><i class="location-all-icon location-home-icon" style="background-image: url('<?php echo SITEURL; ?>images/icons/location_types/<?php echo $type_icon; ?>');"></i></span> <?php echo $type_text; ?></a>
            </li>
        <?php } ?>
			<li class="dropdown-submenu">
                <a href="#" data-prompt="<?php echo $UserPrompt; ?>"   class="location_prompt <?php echo $pclass; ?>"><span class="location-nav-icon"><i class="location-all-icon  "  ></i></span>  Prompt</a>
            </li>
    </ul>
    <?php } ?>
</li>
<script type="text/javascript">
    $(function(){

		$.cookie('is_prompt', 1, {path: '/'});
		var promptParam = $.cookie('is_prompt');
		var is_prompt_off = $.cookie('is_prompt_off');
		var is_session_prompt ='<?php echo $is_session_prompt; ?>';
		if(is_session_prompt == 1 && promptParam ==1 && is_prompt_off !=1){
			setTimeout(function(){
			$('.location-arrow-link').trigger('click');
			$.cookie('is_prompt', 2, {path: '/'});
			$.cookie('is_prompt_off', 1, {path: '/'});
			},250)
		}

        $.update_user_location = function(data){
            var dfd = $.Deferred();
            $.ajax({
                url: $js_config.base_url + 'settings/update_user_location',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(response){
                    dfd.resolve(response);
                }
            })

            return dfd.promise()
        }

		$.update_prompt = function(data){
            var dfd = $.Deferred();
            $.ajax({
                url: $js_config.base_url + 'settings/userPrompt',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(response){
                    dfd.resolve(response);
                }
            })

            return dfd.promise()
        }
          $('.location-types').on('click', function(event) {
            event.preventDefault();
            var data = $(this).data();
            var params = {'id': data.id};
            $.update_user_location(params).done(function(response){
                if(response){
                    $('.location-list .current-location').text(data.text);
                }
            });
        });

		$('.location_prompt').on('click', function(event) {
            event.preventDefault();
			$(this).toggleClass('location_prompt_checked');
			if($('.location_prompt').data('prompt') == 1){
				$('.location_prompt').data('prompt',0)
			}else{
				$('.location_prompt').data('prompt',1)
			}
			var data = $(this).data();
            var params = {'is_prompt': data.prompt};
            $.update_prompt(params).done(function(response){
                if(response){
					if($('.location_prompt').data('prompt') == 1){
						$('.location_prompt').data('prompt',0)
					}else{
						$('.location_prompt').data('prompt',1)
					}
                    //$('.location-list .current-location').text(data.text);
                }
            });
        });



        $.availability_save = false;
        $.get_availability_status = function(data){
            var dfd = $.Deferred();
            $.ajax({
                url: $js_config.base_url + 'settings/get_availability_status',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(response){
                    dfd.resolve(response);
                }
            })

            return dfd.promise()
        }

        $('#availability_modal').on('hidden.bs.modal', function () {
            $(this).removeData('bs.modal');
            $(this).find('.modal-content').html('');
            // if($.availability_save){
                $.availability_save = false;
                $.get_availability_status({user_id: $js_config.USER.id}).done(function(response){
                    if(response.success){
                        var status = response.content;
                        console.log('status',status)
                        $('.current-status').html(status);
                    }
                })
            // }
        })

        $(".location-info-h").click(function(e) {
            // e.preventDefault();
            // e.stopPropagation();
            console.log('1111111111111')
        });

        /*$('body').on('ckick', '.work-availability', function (event) {
            // e.preventDefault();
            // event.stopImmediatePropagation();
            console.log('asdfdsf')
            location.href = $js_config.base_url + 'settings/notification/tab:availability'
        })*/


    })
</script>