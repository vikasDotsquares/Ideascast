<script type="text/javascript" >
$(function() {
	$("input#UserEmail").focus();
})
</script>
<style>
h4.login-box-msg {
	
	font-family:"Open sans","Helvetica Neue",Helvetica,Arial,sans-serif;
	font-size:16px;	
	
}	
</style>
<div class="login-page clearfix">
<div class="login-box">
<div class="login-box-body">
	<h4 class="login-box-msg">Administrator Account</h4>
	<?php echo $this->Form->create('User',array('id' => 'test', 'class'=>'form-validate')); ?>
	<?php echo $this->Session->flash(); ?>
	
	<?php 
		// Check Remeber username and password
		$remember = '';$email = '';$password = '';
		if(isset($_COOKIE["username"]) && !empty($_COOKIE["username"]) && isset($_COOKIE["password"]) && !empty($_COOKIE["password"])){
			$email = $_COOKIE["username"];
			$password = $_COOKIE["password"];
			$remember = 'checked = "checked"';
		}
	?>
			
			
		 <div class="form-group has-feedback">
				<input type="email" id="UserEmail" name="data[User][email]" required="required" value="<?php echo $email; ?>" class="form-control" placeholder="Username or Email address"/>
			</div>
			<div class="form-group has-feedback">
				<input type="password" name="data[User][password]"  required="required" value="<?php echo $password; ?>" class="form-control" placeholder="Password"/>
			</div>          

		
			<div class="row">	
			<div class="col-xs-8">
			<div class="checkbox icheck">
				<input type="checkbox" name="data[User][remember]" id="remember" <?php echo $remember; ?> name="remember_me"/>
			</div> 
			</div>
			  <div class="col-xs-4 ">
			<?php 
				echo $this->Form->submit(
					'Login', 
					array('class' => 'btn btn-warning pull-right btn-flat', 'title' => 'Login')
				);
			?>
			<?php /*  <button type="submit" class="btn btn-warning pull-right">Login</button> */ ?>
			</div>
			</div>
		
	</form>
</div></div></div>