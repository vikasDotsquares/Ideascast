<style>
.form-group.has-feedback .form-control-feedback:not(.absolute-feedback) {
		top: 0 !important;
}
</style>
<?php
if(isset($_SESSION['data']) && !empty($_SESSION['data'])){
$this->request->data = $_SESSION['data'];}
//pr($this->request->data); die;
?>


<div class="register-page clearfix">
	<div class="register-box thanks-nine">
	<div class="register-box-body">
		<h4 class="login-box-msg"><?php echo __("Create New Account");?> </h4>
	<?php
	echo $this->Session->flash('auth'); ?>
	<?php echo $this->Form->create('User'); ?>
		<div class="row">
			<div class="col-md-6">
			<label for="UserDetailFirstName">First Name<span class="text-red">*</span></label>
				<div class="form-group has-feedback">
				  <?php echo $this->Form->input('UserDetail.first_name', array('label' => false, 'div' => false, 'placeholder' => 'First Name', 'class' =>'form-control'));?>
				<span class="glyphicon glyphicon-user form-control-feedback"></span></div>
			</div>
			<div class="col-md-6">
			<label for="UserDetailLastName">Last Name<span class="text-red">*</span></label>
				<div class="form-group has-feedback">
				  <?php echo $this->Form->input('UserDetail.last_name', array('label' =>false, 'div' => false, 'placeholder' => 'Last Name', 'class' =>'form-control'));?>
				<span class="glyphicon glyphicon-user form-control-feedback"></span></div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
			<label for="UserDetailEmail">Email<span class="text-red">*</span></label>
				<div class="form-group has-feedback">

				  <?php echo $this->Form->input('User.email', array('label' => false, 'div' => false, 'placeholder' => 'Email', 'class' =>'form-control', 'type' => 'email'));?>
				<span class="glyphicon glyphicon-envelope form-control-feedback"></span></div>
			</div>
			<div class="col-md-6">
				<div class="form-group has-feedback">
				<label for="UserDetailContact">Contact Number <!--<span class="no_bold">(no spaces)</span>--><span class="text-red">*</span></label>
				  <?php echo $this->Form->input('UserDetail.contact', array('label' => false, 'div' => false, 'placeholder' => 'Contact Number', 'class' =>'form-control', 'type' => 'text'));?>
				<span class="glyphicon glyphicon-phone form-control-feedback absolute-feedback"></span></div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group has-feedback ">

				<label for="UserPassword">Password<span class="text-red">*</span></label>
				  <?php echo $this->Form->input('User.password', array('label'=>false,'type' => 'password', 'div' => false, 'placeholder' => 'Password', 'class' => 'form-control')); ?>

				<?php if(isset($this->request->data['error']['password']) && !empty($this->request->data['error']['password'])){ ?>
					<span class="text-red pass_error"><?php echo $this->request->data['error']['password']; ?></span>
				<?php } ?>


				<span  class="form-control-feedback toltipover absolute-feedback" ><i class="fa fa-question show_policy "></i></</span>


				<?php /* <span  data-content="Must be at least 8 characters Which includes 1 number character." title="" data-trigger="hover" data-toggle="popover" role="button" class="form-control-feedback  toltipover absolute-feedback" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-question"></i></</span> */ ?>

				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group has-feedback">
				<label for="UserCpassword">Confirm Password<span class="text-red">*</span></label>

				  <?php echo $this->Form->input('User.cpassword', array( 'type' => 'password', 'div' => false, 'required' => true, 'label' => false, 'placeholder' => 'Confirm Password', 'class' => 'form-control', 'size' => 20)); ?>

				<?php /* <span  data-content="Must be at least 8 characters Which includes 1 number character." title="" data-trigger="hover" data-toggle="popover" role="button" class="form-control-feedback  toltipover absolute-feedback" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-question"></i></</span> */ ?>

				<span  class="form-control-feedback toltipover absolute-feedback" ><i class="fa fa-question show_policy "></i></</span>

				</div>
			</div>
		</div>
		<!--<div class="row">
			<div class="col-md-6">
			<label for="UserDetailQuestion">Secret Questions<span class="text-red">*</span></label>
				<div class="form-group has-feedback">
				  <?php
					echo $this->Form->input('UserDetail.question', array('type'=>'select', 'div' => false, 'options'=>$questionArray, 'label'=>false, 'empty'=>'Select Secret Question', 'class' =>'form-control'));
				 ?>
				</div>
		   </div>
			<div class="col-md-6">
			<label for="UserDetailAnswer">Secret Answer<span class="text-red">*</span></label>
				<div class="form-group has-feedback">
				  <?php echo $this->Form->input('UserDetail.answer', array('label' => false, 'div' => false, 'placeholder' => 'Secret Answer', 'class' =>'form-control', 'size' => 20));?>
				</div>
			</div>
		</div> -->

					<div class="row">
								<div class="col-md-6">
								<div class="form-group has-feedback">
									<label for="UserCpassword">Country<span class="text-red">*</span></label>
									 <?php echo $this->Form->input('UserDetail.country_id', array('options' => $this->Common->getCountryList(),  'empty' => 'Select Country', 'label' => false, 'div' => false,  'onchange' => 'selectCity(this.options[this.selectedIndex].value)','class' => 'form-control')); ?>

								</div>
								</div>
								<div class="col-md-6">
									<div class="form-group has-feedback">
									  <?php
										echo $this->Form->input('UserDetail.department', array( 'type'=>'text', 'div' => false,    'class' =>'form-control','placeholder' => 'Department'));
									 ?>
									</div>
								</div>
							<!--<div class="col-md-6">
								<?php
									$states = array();
									if(isset($this->data['UserDetail']['country_id']) && !empty($this->data['UserDetail']['country_id'])){
										$states = $this->Common->getStateList($this->data['UserDetail']['country_id']);
									}
								?>

								<div class="form-group has-feedback">
								   <?php
									 echo $this->Form->input('UserDetail.state_id', array('options' => $states,'id' => 'state_dropdown', 'empty' => 'Select State',  'div' => false, 'class' => 'form-control')); ?>

								</div>
							</div> -->
						</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group has-feedback">
				  <?php
					echo $this->Form->input('UserDetail.job_title', array('type'=>'text', 'div' => false,  'class' =>'form-control','placeholder' => 'Job Title'));
				 ?>
				</div>
		   </div>
			<div class="col-md-6">
				<div class="form-group has-feedback">
				  <?php echo $this->Form->input('UserDetail.job_role', array( 'type'=>'text','div' => false, 'placeholder' => 'Job Role', 'class' =>'form-control' ));?>
				</div>
			</div>
		</div>



						<!--<div class="row">
							<div class="col-md-6">
								<div class="form-group has-feedback">

									<?php echo $this->Form->input('UserDetail.city', array('type' => 'text', 'div' => false, 'class' => 'form-control')); ?>

								</div>
							</div>
						</div>		-->

		<div class="row">
			<div class="col-md-6">
			<div class="form-group has-feedback member_org">
			  <!--<p>  <?php echo $this->Form->input("individual", array(
										'type' => 'radio',
										'options' => array('Individual '),
										'class' => 'testClass minimal',
										'div' => false,
										'id' => 'indi' ,
										'label' => false,
										'checked'=>true,
										'hiddenField' => false, // added for non-first elements
				)); ?></p>  -->

				<!--<p> <?php

				/* echo $this->Form->input("individual", array(
										'type' => 'checkbox',
										'options' => array('Belong to Organization/Institution '),
										'class' => 'testClass minimal',
										'div' => false,
										'id' => 'insti' ,
										'label' => false,
										'hiddenField' => false, // added for non-first elements
				));	 */


			   ?></p> <label>Belong to Organization/Institution</label> -->
			</div>
			</div>


		</div>

		 <div class="row" id="dats">
				<div class="col-md-6">
				<div class="form-group has-feedback">
					<label>Organization name</label>
					<?php echo $this->Form->input('UserDetail.self_org', array('type' => 'text', 'div' => false,'label' => false, 'class' => 'form-control')); ?>


				</div>
			</div>
				<div class="col-md-6">
				<div class="form-group has-feedback ">

				  <?php echo $this->Form->input('UserDetail.membership_code', array('type' => 'text', 'div' => false, 'placeholder' => 'Enter Code','required'=>false, 'class' => 'form-control')); ?>
					<p>Fill this if you have membership code.</p>
				</div>
			</div>
		</div>



	 <div class="row">
		<div class=" text-right">
			<?php
				echo $this->Form->submit(
				'Submit',
				array('class' => ' submit-btn', 'title' => 'Submit','div' => false)
				);
			?>
	   <?php echo $this->Form->end(); ?>
		<?php // echo $this->Form->end(__('Submit')); ?>
	 </div>
	  </div>
	  </div>
	  </div>
</div>

 <script type="text/javascript" >

$(document).ready(function(){

	// initilize popover tooltip message
	$('[data-toggle="popover"]').popover({container: 'body',html: true,placement: "left"});
});
</script>
<script type="text/javascript" >
 $(document).ready(function(){
     $('#UserDetailMembershipCode').hide();
	 $('#dats').hide();
	 $('#UserDetailMembershipCode').attr('disabled','disabled');
	 $('.testClass ').click(function() {
		var planId = $(this).attr('planid');
		var trId =  $(this).closest('tr').attr('id');
		$('.txt_'+trId).val(planId);
	});
		$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
			checkboxClass: 'icheckbox_minimal-blue',
			radioClass: 'iradio_minimal-blue'
        });
		        //Flat red color scheme for iCheck
       /*  $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
          checkboxClass: 'icheckbox_flat-green',
          radioClass: 'iradio_flat-green'
        }); */


		setTimeout(function(){
		$('.pass_error').fadeOut();
		},2000)
});
</script>
<?php //if( $_SERVER['SERVER_NAME'] == 'dotsquares.ideascast.com' || $_SERVER['SERVER_NAME'] == LOCALIP ) { ?>
<script>
 $(document).ready(function(){
		$('body').on('mouseenter', '.show_policy', function(event){
			event.preventDefault();
			var href = $js_config.base_url + 'users/list_program_policy';
			$t = $(this);
			$.ajax({
				url: href,
				type: "POST",
				data: $.param({listpolicy:'resetpssword'}),
				global: false,
				crossDomain:true,
				success: function (response) {  console.log(response);
                    $t.popover({
					placement : 'right',
					trigger : 'hover',
					crossDomain:true,
					html : true,
					template: '<div class="popover " role="tooltip"><div class="arrow"></div><h3 class="popover-title" style="display:none;"></h3><div class="popover-content register-popover pop-content"></div></div>',
					container: 'body',
					delay: { hide: 400}
					});
					$t.attr('data-content',response);
					$t.popover('show');
                }

			})
		})
});
</script>
<?php //} ?>
<script>

$(window).load(function(){

		if($('#insti').is(':checked')){
			$('#dats').show();
			$('#UserDetailMembershipCode').show();

			$('#UserDetailMembershipCode').removeAttr('disabled','disabled');


		}




	    $('#insti').next().click(function() { //alert(0);
		    $('#dats').show();
			$('#UserDetailMembershipCode').show();

			$('#UserDetailMembershipCode').removeAttr('disabled','disabled');
		});

	    $('#insti').next().click(function() { //alert(0);
		if(!$('#insti').is(':checked')){
		    $('#dats').hide();
			$('#UserDetailMembershipCode').hide();
			$('#UserDetailMembershipCode').attr('disabled','disabled');
			}
		});


})
 </script>
 <script type="text/javascript">
    function selectCity(country_id) {
        if (country_id != "-1") {
            loadData('state', country_id);
            $("#city_dropdown").html("<option value=''>Select city</option>");
        } else {
            $("#state_dropdown").html("<option value=''>Select state</option>");
            $("#city_dropdown").html("<option value=''>Select city</option>");
        }
    }

    function selectState(state_id) {
        if (state_id != "-1") {
            loadData('city', state_id);
        } else {
            $("#city_dropdown").html("<option value=''>Select city</option>");
        }
    }

    function loadData(loadType, loadId) {
        var dataString = 'loadType=' + loadType + '&loadId=' + loadId;
        $("#" + loadType + "_loader").show();
        $("#" + loadType + "_loader").fadeIn(400).html('Please wait... <?php echo $this->Html->image('loading1.gif'); ?>');
        $.ajax({
            type: "POST",
            url: "<?php echo Router::url(array('controller' => 'users', 'action' => 'get_state_city','admin'=>true)); ?>",
            data: dataString,
            cache: false,
            success: function (result) {
                //$("#" + loadType + "_loader").hide();
                if($("#" + loadType + "_dropdownEdit").length > 0 && $("#Recordedit").css('display') != 'none'){
					$("#" + loadType + "_dropdownEdit").html("<option value=''>Select " + loadType + "</option>");
					$("#" + loadType + "_dropdownEdit").append(result);
				}else{
					$("#" + loadType + "_dropdown").html("<option value=''>Select " + loadType + "</option>");
					$("#" + loadType + "_dropdown").append(result);
				}
            }
        });
    }
</script>
<style>.no_bold{ font-weight:normal}</style>