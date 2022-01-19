<?php
echo $this->Html->script(array('projects/plugins/loadsh/lodash'));
echo $this->Html->css(array('projects/dropdown'));

 ?>

<?php if(!chat_enabled()){ ?>

<style>
	.btn-conversation .pills {
		background: #6c6c6c none repeat scroll 0 0;
		border: 2px solid #fff;
		border-radius: 50%;
		display: inline-block;
		font-size: 12px;
		font-weight: 600;
		padding: 0 6px;
		position: absolute;
		right: 0;
		top: -10px;
	}
	a.btn-no-project {
	    background: #76b531 none repeat scroll 0 0;
	    border: 0.2rem solid #fff;
	    border-radius: 50%;
	    bottom: 1em;
	    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.2);
	    color: #fff;
	    height: 50px;
	    padding: 12px 0 0;
	    position: fixed;
	    right: 2em;
	    width: 50px;
	    z-index: 999999;
	    font-size: 16px;
	    transition: all 0.3s ease-in-out 0s;
	    float: right;
	}
</style>

<!--  ############################ here start chat div  ############################ -->
        <?php

        echo $this->Html->script('projects/chat_common');
        echo $this->Html->css(array('projects/chat_common'));

 		if( !empty($project_lists) && count($project_lists) > 0 ){
 ?>
        <div class="pull-right">
            <a id="btnChatWindow" href="#openChat" class="btn btn-success btn-conversation event-none">
	        	<div class="lds-ellipsis">
					<div></div>
					<div></div>
					<div></div>
					<!-- <div></div> -->
				</div>
                <i class="fa fa-comment"></i>
                <span>Live</span>
				<label class="pills"  style="display:none;"></label>
            </a>
        </div>
        <?php } else {
			if( $this->Session->read('Auth.User.role_id') == 2 ){
		?>
        <div class="pull-right">
            <a id="btnChatWindowDisabled" data-toggle="popover" data-trigger="hover" data-placement="left" data-title="Chat not available" data-content="No projects" href="#openChat" class="btn btn-success btn-no-project">
                <i class="fa fa-comment"></i>
            </a>
        </div>
			<?php }
		}?>

  <?php /* for local stop the chat loading */ ?>
        <div id="openChat" class="openChat" >

            <div class="ChatHeader">
                <div class="btn-group">
                <button type="button" id="fullscree	nOpenChat" class="btn btn-fullscreen"><i class="fa fa-expand" style=""></i></button>
                <button type="button" id="closeOpenChat" class="btn btn-close"><i class="fa fa-times"></i></button>
                </div>

                <div class="modal-title" style="display:none;">
                <div class="input-group">
                  <label class="input-group-addon" for="txa_title">Chat:</label>
                  <?php
				  // $new_project_lists = null;
				   // foreach($project_lists as $key => $value) {

				   // }
					$project_lists = array_filter($project_lists);
                    $project_id = (isset($project_id) && !empty($project_id)) ? $project_id : null;
                    //pr($project_lists);die;
					echo $this->Form->input('ExportData.project',
                            array(
                                'label' => false,
                                'type' => 'select',
                                'div' => false,
                                'options'=>$project_lists,
                                "multiple"=>false,
                                'selected'=>$project_id,
                                'class' => 'form-control title_limit title aqua trigger_chat_select_box',
                                'id' => 'project',
								'empty'=>false,
                                )
                            );
                    ?>
                </div>

                </div>
            </div>
            <?php
			$current_user_data = $this->Session->read('Auth.User');
			if( $_SERVER['SERVER_NAME'] != "prod.ideascast.com" ){
			?>
			<form method="post" action="<?php echo CHATURL; ?>/signin" target="ifr_iframe" id="ifr_form" style="display: none" >

				<input type="hidden" name="userid" value="<?php echo $current_user_data['id']; ?>" id="ifr_userid" />
				<input type="hidden" name="email" value="<?php echo $current_user_data['email']; ?>" id="ifr_email" />
				<input type="hidden" name="password"   id="ifr_password" />
				<input type="hidden" name="redirect" id="ifr_redirect" />
				<input type="hidden" name="member" id="ifr_member_id" />
				<input type="hidden" name="tab" id="ifr_tab" />
				<select class="form-control" name="organisation_id" id="organisation_id">
					<option>Select Organisation</option>
					<option selected="" value="1">Sample</option>
                </select>
				<?php
					$url_project_id = 0;
					if (isset($project_id) && !empty($project_id)) {
						$url_project_id = $project_id;
					}
					else if(isset($_sidebarProjectId) && !empty($_sidebarProjectId)) {
						$url_project_id = $_sidebarProjectId;
					}
					else {
							$keys = array_keys($project_lists);
							if( isset($keys[0]) ){
								$url_project_id = $keys[0];
							}
					}
				?>
				<input type="hidden" name="project_id" value="<?php echo $url_project_id; ?>" id="ifr_project_id" />
                <input type="hidden" name="login" id="ifr_login" />
				<input type="submit" name="sub" value="" id="ifr_submit" />
			</form>
            <iframe name="ifr_iframe" id="ifr_iframe" allow="microphone; camera" src="<?php echo CHATURL; ?>/signin" ></iframe>
			<?php } ?>
        </div>

        <!--  ############################ here start chat div end ###################### -->


<script type="text/javascript">
	$(function(){


		$("#btnChatWindowDisabled").popover({
		        placement : 'left',
		        trigger : 'hover',
		        html : true,
			  	container: 'body'
    	});

		$('body').delegate('.chat_start_section', 'click', function (event) {

			event.preventDefault();

			var uid = $(this).data('member'),
				pid = $(this).data('project'),
				uemail = $(this).data('email');
			setTimeout(function(){
			$('#btnChatWindow').trigger('click');
			if( $js_config.CHAT_CLOUD == 'yes' ){

			$.ajax({
			    url: $js_config.CHATURL+"/api/login",
			    data: {
					email: $js_config.USER.email,
			        member: uemail,
			        projectId: pid,
					login: 'startchat',
			        tab: 'startchat'
			    },
			    success: function(res){
			    	console.log(res);
			    },
			    dataType: 'jsonp',
			    type: 'post'
			});

			}else{
				$.ajax({
				    url: $js_config.CHATURL+"/signin",
				    data: {
				        email: $js_config.USER.email,
				        project: pid,
				        member: uemail,
				        login: 'startchat',
				        tab: 'startchat'
				    },
				    success: function(res){
				    	console.log(res);
				    },
				    dataType: 'jsonp',
				    type: 'post'
				});
			}

			}, 1000)
		})

		$('body').delegate('.my_chat_start', 'click', function (event) {
			event.preventDefault();
			$('#btnChatWindow').trigger('click');
			var uid = $(this).data('id'),
				pid = $(this).data('pid');
			console.log(pid);

				//setTimeout(function(){
			if( $js_config.CHAT_CLOUD == 'yes' ){

			$.ajax({
			    url: $js_config.CHATURL+"/api/login",
			    data: {
					email: $js_config.USER.email,
			        member: uemail,
			        projectId: pid,
					login: 'startchat',
			        tab: 'startchat'
			    },
			    success: function(res){
			    	console.log(res);
			    },
			    dataType: 'jsonp',
			    type: 'post'
			});

			}else{
			$.ajax({
			    url: $js_config.CHATURL+"/signin",
			    data: {
			        email: $js_config.USER.email,
			        project: pid,
			        member: uemail,
			        login: 'startchat',
			        tab: 'startchat'
			    },
			    success: function(res){
			    	console.log(res);
			    },
			    dataType: 'jsonp',
			    type: 'post'
			});
			}
				//},1500)


		})
	})
</script>
<?php } ?>