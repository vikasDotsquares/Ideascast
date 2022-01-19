<style type="text/css">
	.wrapper.wrapper-full-page {
	    height: auto;
	    min-height: 100vh;
	}
	.card {
	    border-radius: 6px;
	    box-shadow: 0 2px 2px rgba(204, 197, 185, 0.5);
	    background-color: #FFFFFF;
	    color: #252422;
	    margin-bottom: 20px;
	    position: relative;
	}
	.lock-page .card-lock {
	    text-align: center;
	    width: 500px;
	    margin: 30px auto 0;
	    padding: 30px;
	    /*position: absolute;
	    left: 50%;
	    margin-left: -150px;*/
	    display: block;
	}
	.card-lock {
	    box-shadow: 0 25px 30px -13px rgba(40, 40, 40, 0.4);
	    border-radius: 6px;
	    padding-top: 25px;
	    padding-bottom: 25px;
	}
	.card-lock {
	    text-align: center;
	    padding: 30px;
	    display: block;
	    z-index: 2;
	}
	.card-lock .author {
	    border-radius: 50%;
	    width: 100px;
	    height: 100px;
	    overflow: hidden;
	    margin: 0 auto;
	}
	.card-lock h4 {
	    margin-top: 15px;
	    margin-bottom: 30px;
	}
	.form-group {
	    position: relative;
	    text-align: left;
	    margin-bottom: 15px;
	}
	span.error {
	    font-size: 11px;
	    color: #ff001f;
	}
	.form-control {
	    background-color: #F3F2EE;
	    border: 1px solid #e8e7e3;
	    border-radius: 4px;
	    color: #66615b;
	    font-size: 14px;
	    padding: 7px 18px;
	    height: 40px;
	    -webkit-box-shadow: none;
	    box-shadow: none;
	}
	.navbar.navbar-absolute {
	    position: absolute;
	    width: 100%;
	    z-index: 1030;
	        min-height: 75px;
	}
	.navbar-transparent {
	    padding-top: 15px;
	    background-color: transparent;
	    border-bottom: 1px solid transparent;
	}

	.wrapper-full-page .full-page-background {
	    background-image: url(../images/backgrounds/login_1.jpg);
	    position: absolute;
	    z-index: 1;
	    height: 100%;
	    width: 100%;
	    display: block;
	    top: 0;
	    left: 0;
	    background-size: cover;
	    background-position: center center;
	    opacity: 0.9;
	}
	/*.full-page-background:after {
	    background: #5e5e5e;
	    z-index: 3;
	    opacity: 1;
	}
	.full-page-background:after, .full-page-background:before {
	    display: block;
	    content: "";
	    position: absolute;
	    width: 100%;
	    height: 100%;
	    top: 0;
	    left: 0;
	    z-index: 2;
	}
	.full-page-background:after {
	    background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
		background-size: 400% 400%;
		animation: gradientBG 15s ease infinite;
	}
	.full-page-background:after, .full-page.has-image:after {
	    opacity: .7;
	}*/

	.login-page > .content, .lock-page > .content {
	    padding-top: 20vh;
	    margin: 0 auto;
	    display: flex;
	    justify-content: center;
	    align-items: center;
	}

	.full-page > .content {
	    min-height: calc(100vh - 70px);
	}
	.full-page > .content, .full-page > .footer {
	    position: relative;
	    z-index: 4;
	}

	@keyframes gradientBG {
		0% {
			background-position: 0% 50%;
		}
		50% {
			background-position: 100% 50%;
		}
		100% {
			background-position: 0% 50%;
		}
	}
	.btn-dark {
	    color: #fff;
	    background-color: #343a40;
	    border-color: #343a40;
		outline: 0;
	}
	.btn-dark:hover, .btn.btn-dark:focus {
	    color: #fff;
	    background-color: #23272b;
	    border-color: #1d2124;
		box-shadow: 0 0 0 0.2rem rgba(52,58,64,.5);
	}
	.form-control {
		transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
	    outline: 0;
	    box-shadow: none;
	    border: none;
	    border-bottom: 1px solid #ced4da;
	    border-radius: 0;
	    box-sizing: content-box;
	    background-color: transparent;
	    padding: .6rem 0 .4rem 0;
	    height: auto;
    	line-height: 1.5;
	}
	.form-control:focus {
	    box-shadow: 0 1px 0 0 #4285f4;
    	border-bottom: 1px solid #4285f4;
	}
	.form-group label {
	    position: absolute;
	    top: 0;
	    left: 0;
	    font-size: 14px;
	    transition: transform .2s ease-out,color .2s ease-out;
	    transform-origin: 0 100%;
	    transform: translateY(10px);
	    cursor: text;
	    color: #757575;
	    z-index: -3;
	    font-weight: normal;
	}
	.form-group label:after {
	    content: "";
	    position: absolute;
	    top: 65px;
	    display: block;
	    opacity: 0;
	    transition: .2s opacity ease-out,.2s color ease-out;
	}

	.form-group label.active {
	    font-size: 1.5rem;
	    transform: translateY(-14px) scale(.8);
	    color: #4285f4;
	}
	.copyrights {
	    list-style-type: none;
	    padding-left: 0;
	    margin-top: 20px;
	    margin-bottom: 10px;
	    display: -webkit-box;
	    display: -ms-flexbox;
	    display: flex;
	    -webkit-box-pack: center;
	    -ms-flex-pack: center;
	    justify-content: center;
	    position: absolute;
	    bottom: -40px;
	    color: #878787;
	    width: 100%;
	    left: 0;
	}
</style>
<?php
$userDetail = $this->Common->userDetail($this->Session->read('Auth.User.id'));
$profile = '';
if( isset($userDetail['UserDetail']['profile_pic']) && !empty($userDetail['UserDetail']['profile_pic']) ){
	$profile =  $userDetail['UserDetail']['profile_pic'];
}
if(!empty($profile) && file_exists(USER_PIC_PATH.$profile)) {
    $profiles = SITEURL.USER_PIC_PATH.$profile;
}else{
    $profiles = SITEURL.'img/image_placeholders/logo_placeholder11.gif';
}
$username = $this->Session->read('Auth.User.UserDetail.first_name') . ' ' . $this->Session->read('Auth.User.UserDetail.last_name');
 ?>
<div class="wrapper wrapper-full-page lock-page">
	<div class="content">
		<div class="card card-lock">
            <div class="author">
                <img class="avatar" src="<?php echo $profiles ?>" alt="<?php echo $username; ?>" width="100">
            </div>
            <h4><?php echo $username; ?></h4>
            <div class="form-group">
                <input type="password" class="form-control" autocomplete="off" id="password">
                <label>Enter Password</label>
                <span class="error"></span>
            </div>
            <button type="button" class="btn btn-block btn-dark btn-unlock">Unlock</button>
            <div class="copyrights">copyright © 2018 Ideascast. All rights reserved.</div>
        </div>
	</div>
	<div class="full-page-background"></div>
</div>

<script type="text/javascript">
	$(function(){
		$('.btn-unlock').on('click', function(event) {
			event.preventDefault();
			$.ajax({
				url: $js_config.base_url + 'users/unlock_account',
				type: 'POST',
				dataType: 'json',
				data: {'pass': $('#password').val()},
				success: function(response){
                    if(response.success){
                    	if($js_config.referer){
                    		// location.href = $js_config.referer;
                    		location.href = $js_config.base_url + 'dashboards/project_center';
                    	}
                    	else{
                        	location.href = $js_config.base_url + 'dashboards/project_center';
                    	}
                    }
                    else{
                    	$('.error').text('Invalid account password.')
                    }
               	}
			});

		});
		$('#password').on('keyup', function(event) {
			event.preventDefault();
			$('.error').text('');
			if(event.which == 13) {
				$('.btn-unlock').trigger('click');
			}
		})
		.on('focus', function(event) {
			event.preventDefault();
			$(this).parent().find('label').addClass('active');
		})
		.on('blur', function(event) {
			event.preventDefault();
			if($(this).val() == ''){
				$(this).parent().find('label').removeClass('active');
			}
		});
	})
</script>