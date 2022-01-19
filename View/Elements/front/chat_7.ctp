<textarea id="chat_received_messages" style="display: none;"></textarea>
<?php if($this->Session->read('Auth.User.role_id') == 2 ){ ?>
	<?php if(SOCKET_MESSAGES){ ?>
		<?php
		echo $this->Html->script(SOCKETURL . '/socket.io/socket.io.js');
		echo $this->Html->css(array('projects/animate'));
		echo $this->Html->script('projects/socket/socket_connection', array('inline' => true));
		?>
	<?php } ?>
	<style type="text/css">
		.create-project-block {
			text-align: center;
			display: block;
			font-weight: normal;
		}
		.signout-overlay {
		    display: none;
		    position: fixed;
		    top: 0;
		    left: 0;
		    width: 100%;
		    height: 100%;
		    background: rgba(255, 255, 255,.3);
		    z-index: 99999999;
		}
		.login-to-chat {
			display: none;
			height: 0;
			width: 0;
			visibility: hidden;
		}
		.open-chat-wrap {
		    position: fixed;
		    right: 2em;
		    bottom: 1.5em;
		    list-style: none;
		    white-space: nowrap;
		    border-radius: 50%;
		    display: flex;
		    justify-content: center;
		    align-items: center;
		    cursor: default;
		    z-index: 999;
		    width: 46px;
		    height: 46px;
		}
	</style>
	<script type="text/javascript">
		$(function(){
			$.chat_enabled = '<?php echo chat_enabled(); ?>';
		})
	</script>
	<?php if(chat_enabled()){
		echo $this->Html->css(array('projects/chat_7'));
			if($this->Session->read('chat_loggedin')){
				CakeSession::delete('chat_loggedin');
			}
		} ?>

		<div class="iframe-logoff logoff" style="display: none;"></div>
	<?php } else { ?>
	 <script type="text/javascript">
	 	$(function(){
	 		$('body').on('click', '.user-signoff', function(event) {
		        event.preventDefault();
				if(!$.isMobile){
					$('.logoff').html('<iframe src="' + $js_config.CHATURL + '/logout" id="iframe_logout"></iframe>');
				}
				location.href = $js_config.base_url + "logout";
		    });
	 	})
	 </script>
	<?php } ?>

<?php if($this->Session->read('Auth.User.role_id') == 2 ){ ?>
<script type="text/javascript">
	$(function(){
		/* LOGOUT FROM CHAT IF USER SIGNED-OFF FROM THE MAIN SITE */
	    $('body').on('click', '.user-signoff', function(event) {
	        event.preventDefault();
			
			$('.signout-overlay').show();

			if(!$.isMobile){
				$.socket.emit("php:socket:leave", {'id': $js_config.USER.id});

				var url = $js_config.CHATURL + '/opuscast';
				if($.current_project_id != 0 && $.current_project_id != '' && $.current_project_id !== undefined){
					url = $js_config.CHATURL + '/opuscast' + '?tab=contact' + '&projectid=' + $.current_project_id;
				}

				w = window.open(url, 'newChatWindow',
								'left='+$(window).width()+',top='+$(window).height()+',width=1,height=1,menubar=no,toolbar=no,location=no,status=no,resizable=no,scrollbars=no,noopener=no,noreferrer=no,directories=no');
				if(w){
					w.close();
				}
				$('.logoff').html('<iframe src="' + $js_config.CHATURL + '/logout" id="iframe_logout"></iframe>');
				$('#iframe_logout').load(function() {
				})
			}
			location.href = $js_config.base_url + "logout";
	    });
	})
</script>
<?php } ?>
