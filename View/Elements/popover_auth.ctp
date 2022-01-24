 
		 
			 
		<div class="input-group has-loader margin-tb">
			<i class="fa loader" id="icon"></i> 
			<input type="password" id="auth_password" name="auth_password" value="" class="form-control input_holder" style="padding: 5px; min-width: 240px;"  />
		</div>
		<input type="hidden" id="auth_email" name="auth_email" value="<?php //echo $row['id'] ?>" />
			 
		
		<div class="popover-footer text-center ">
			<div class="btn-group">
				<button type="submit" id="popover_submit" class="btn btn-sm btn-success" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'auth_check', 'admin' => FALSE ), TRUE); ?>">Submit</button>
				<button type="reset" id="popover_close" class="btn btn-sm btn-danger">Reset</button>
			</div>
		</div>
		
<style>
/* 
.start-loader {
	-animation: spin .7s infinite linear;
    -webkit-animation: spin2 .7s infinite linear;
}
		
.loader {
	display: inline-block;
	opacity: 0;
	max-width: 0;
	
	-webkit-transition: opacity 0.25s, max-width 0.45s; 
	-moz-transition: opacity 0.25s, max-width 0.45s;
	-o-transition: opacity 0.25s, max-width 0.45s;
	transition: opacity 0.25s, max-width 0.45s; 
}
	
.has-loader.active {
	cursor:progress;
}
	
.has-loader.active .loader {
	opacity: 1;
	max-width: 50px; 
}
	 */
	
@keyframes loader {
    to {transform: rotate(360deg);}
}
	
@-webkit-keyframes loader {
    to {-webkit-transform: rotate(360deg);}
}
	
.loader {
    min-width: 12px;
    min-height: 14px;
}
	
.loader:before {
    content: 'Loading…';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 16px;
    height: 16px;
    margin-top: -5px;
    margin-left: -8px; 
}
.loader:not(:required):before {
    content: '';
    border-radius: 50%;
    /* border: 2px solid rgba(0, 0, 0, .3);
    border-top-color: rgba(0, 0, 0, .6);  */
    border: 2px solid rgba(22, 159, 40, .0);
    border-top-color: rgba(22, 159, 40, 1); 
}

.has-loader.active .loader:not(:required):before { 
    animation: loader .6s linear infinite;
    -webkit-animation: loader .6s linear infinite;
}
.has-loader i#icon {
    left: auto;
    position: absolute;
    right: 7px;
    top: 8px;
    z-index: 10;
	opacity: 0
}
.has-loader.active i#icon {
	opacity: 1;
}
</style>