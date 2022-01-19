<div class="login-form">
<?php //echo $this->Session->flash('auth'); ?>

<?php 
 
if ($loggedIn) { ?>
	<h2 class="login-title"><?php echo __('User Section');?> </h2>
	<ul class="form-list">
	<li>
	  <?php  
	  $userData = $this->session->read('Auth.User');
	  echo '<strong>Name:</strong> '.$userData['UserDetail']['first_name'].' ',$userData['UserDetail']['last_name'];?>
	</li>
	<li>
	  <?php  echo '<strong>Email:</strong> '.$userData['email'];?>
	</li>
	<li class="clearfix">
	  <?php 
		echo $this->Html->link('Logout', SITEURL.'users/logout', array('class' => 'btn btn-warning pull-right'));

		//echo $this->Html->link($this->Form->button('Logout'), array('controller' => 'users', 'action' => 'logout',1), array('escape'=>false,'title' => "Logout", 'class' => 'btn btn-warning pull-right'));
	?>
	</li>
	</ul>
<?php }else{ ?>
  <h2 class="login-title"><?php echo __('Login Here');?> </h2>
  
  <?php echo $this->Form->create('User', array(
  'url' => array(
    'controller' => 'Users', 
    'action' => 'login', 
    )
  )); ?>
   <?php  echo $this->Session->flash();?>
  <ul class="form-list">
	<li>
	  <?php echo $this->Form->input('User.email', array('label' => false ,'type' => 'email', 'placeholder' => 'Email Address', 'class' =>'form-control'));?>
	</li>
	<li>
	  <?php echo $this->Form->input('User.password', array('label' => false ,'type' => 'password', 'placeholder' => 'Password', 'class' =>'form-control'));?>
	</li>
	<li class="clearfix">
	  <?php echo $this->Form->input('remember_me', array('label' => 'Remember me', 'type' => 'checkbox', 'placeholder' => 'username', 'class' =>'checkbox'));?>
	  
	<?php 
		echo $this->Form->submit(
			'Login', 
			array('class' => 'btn btn-warning pull-right', 'title' => 'Login')
		);
	?>
	<?php /*  <button type="submit" class="btn btn-warning pull-right">Login</button> */ ?>
	</li>
  </ul>
  <?php echo $this->Form->end(); ?>
  <div class="form-footer clearfix"> 
  <?php echo $this->Html->link("Forgot Password ?",array("controller"=>"users","action"=>"forgetpwd")); ?>
   
  <?php echo $this->Html->link('Create new Account', array('controller'=>'users','action'=>'register')); ?>
   </div>
<?php }?> 
</div>