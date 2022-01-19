<!-- ADD NEW Industry User -->
<link href="<?php echo SITEURL;?>plugins/select2/dist/css/select2.min.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo SITEURL;?>plugins/select2/dist/js/select2.full.js"></script>
<style>
.select2-selection__choice__remove {
		float: right !important;
		padding: 0 0 0 5px;
}
.pass_error{
	font-size:11px;
}
</style>
<?php
if(isset($_SESSION['data']) && !empty($_SESSION['data'])){
$this->request->data = $_SESSION['data'];}
//pr($this->request->data); die;
?>
<?php echo $this->Session->flash( ); ?>
<div class="panel panel-primary changepaawrodsec">


	<div class="panel-heading">
        <h3 class="panel-title">Change Password</h3>
		<?php echo $this->Html->script('jquery.validate', array('inline' => true)); ?>
		<?php echo $this->Html->script('custom_validate', array('inline' => true)); ?>
		<div class="changepaawrod-btn">
			<button class="btn save-prof save_contact" type="button">Change</button>
			<?php $url = SITEURL . "projects/lists"; ?>
             <a href="<?php echo $url; ?>" id="" class="btn btn-primary cancel-prof" data-dismiss="modal"> Cancel</a>
		</div>
    </div>

   <?php
	echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'changepassword'), 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'UserCPassword')); ?>
    <?php echo $this->Form->input('User.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>

    <div class="panel-body form-horizontal change-password">
        <div class="panel-heading"></div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-4 control-label" for="UserClassification">Current Password:</label>
                        <div class="col-lg-7 col-sm-11">
							<div class="password-filed">
                            <?php echo $this->Form->input('User.current_password', array('type' => 'password', 'required' => false, 'autocomplete' => 'off', 'label' => false, 'div' => false, 'id'=> 'current_password', 'class' => 'form-control')); ?>
							<?php if(isset($this->request->data['error']['current_password']) && !empty($this->request->data['error']['current_password'])){ ?>
								<span class="text-red pass_error"><?php echo $this->request->data['error']['current_password']; ?></span>
							<?php } ?>
								</div>
                        </div>
                    </div>
                </div>
			</div>
			<div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-4 control-label" for="UserClassification">New Password:</label>
                        <div class="col-lg-7 col-sm-11 new-password">
							<div class="password-filed">
                            <?php echo $this->Form->input('User.password', array('type' => 'password', 'required' => false, 'autocomplete' => 'off', 'label' => false, 'div' => false, 'id'=>'new_password', 'class' => 'form-control readpolicyshows')); ?>
							<?php if(isset($this->request->data['error']['password']) && !empty($this->request->data['error']['password'])){ ?>
								<span class="text-red pass_error"><?php echo $this->request->data['error']['password']; ?></span>
							<?php } ?>
                        </div>
						</div>
                    </div>
                </div>
			</div>
			<div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-4 control-label" for="UserClassification">Confirm Password:</label>
                        <div class="col-lg-7 col-sm-11">
							<div class="password-filed">
                            <?php echo $this->Form->input('User.cpassword', array('type' => 'password', 'required' => false, 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
                        </div>
						</div>
                    </div>
                </div>

            </div>




        </div>
    </div>

   </form>
</div><!-- /.modal-content -->

<style>
	.modal-open .modal ,.fade.in{
	    /* overflow-x: hidden !important;
	    overflow-y: auto !important; */
	}
	.panel-primary.changepaawrodsec{
	    border-color: #3c8dbc;
		margin-top: 10px;
	}
	.panel-primary.changepaawrodsec>.panel-heading {
	    background-color: #3c8dbc;
	    border-color: #3c8dbc;
	    display: flex;
	    justify-content: space-between;
	    align-items: center;
	    padding-top: 8px;
	    padding-bottom: 8px;
	}
	.changepaawrodsec .panel-title {
	    font-weight: 400;
	    font-size: 18px;
	}
	.error {
		font-weight:normal;
		color: #dd4b39;
	    font-size: 11px;
		margin: 0;
		vertical-align: top;
	}
	.form-group .col-lg-1 {
	       padding: 0;
	}
	.fa-info {
		background: #00aff0 none repeat scroll 0 0;
		border-radius: 50%;
		color: #fff;
		font-size: 13px;
		height: 22px;
		line-height: 24px;
		width: 22px;
	}
	#User label {
		font-size : 13px ;
	}

.changepaawrodsec .form-group {
    margin-bottom: 20px;
}
.password-filed {
    position: relative;
    vertical-align: top;
}
.password-filed label.error {
    position: absolute;
    bottom: -13px;
}

</style>
<?php //if( $_SERVER['SERVER_NAME'] == 'dotsquares.ideascast.com' || $_SERVER['SERVER_NAME'] == LOCALIP ) { ?>
<script>
$(function(){

	$('#UserCPassword').validate({
        rules: {
            'data[User][current_password]' : {
                minlength: 5,
                required: true
            },
			'data[User][password]' : {
                required: true
            },
			'data[User][cpassword]' : {
                required: true,
				equalTo: '#new_password'
            }
        },
		messages: {
				'data[User][current_password]': "Current Password is required",
				'data[User][password]': {
					required: "New Password is required",
					minlength: "Your password must be at least 5 characters long"
				},
				'data[User][cpassword]': {
					required: "Confirm Password is required",
					minlength: "Your password must be at least 5 characters long",
					equalTo: "New Password and Confirm Password must match"
				}
		  }
    }); // end form data

	$('.save_contact').on('click', function(){

        if( $("#UserCPassword").valid() ){
			$("#UserCPassword").submit();
		}

	});


	;($.password_policies = () => {
		var href = $js_config.base_url + 'users/list_program_policy';
		$.ajax({
			url: href,
			type: "POST",
			data: $.param({listpolicy: 'policy'}),
			success: function (response) {
				$('.readpolicyshows').popover({
					content: response,
			        placement: "right",
			        container: 'body',
			        trigger: 'hover',
			        html: true,
			        delay: { show: "50", hide: "400" },
			        template: '<div class="popover " role="tooltip"><div class="arrow"></div><h3 class="popover-title" style="display:none;"></h3><div class="popover-content chngpass-popover pop-content"></div></div>'
			    })
			    .on('click', function() {
			        var _this = this;
			        $(this).popover('show');
			        $('.popover').on('mouseleave', function() {
			            $(_this).popover('hide');
			        });
			    })
			    .on('mouseleave', function() {
			        var _this = this;
			        setTimeout(function() {
			            if (!$('.popover:hover').length) {
			                $(_this).popover('hide');
			            }
			        }, 300);
			    });
			}
		})
	})();




});
</script>
<?php //} ?>
