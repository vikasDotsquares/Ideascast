
<?php 
$message = '';
$flash_type = '';

if( CakeSession::check('fmessage') ) {
	$message = CakeSession::read('fmessage.message');
	$flash_type = CakeSession::read('fmessage.type');
	
	CakeSession::delete('fmessage');
} 
?>


<?php if(isset($flash_type) && !empty($flash_type)) {  ?>
	
	<?php if( $flash_type == 'success' ) { ?>
	
		<div class="success-alert message-alert">
		   <a href="#" class="close-alert">x</a>
		   <p><?php echo $message; ?></p>
		</div>
	<?php } ?>
	
	<?php if( $flash_type == 'error' ) { ?>
		<div class="error-alert message-alert">
			<a href="#" class="close-alert">x</a>
			<p><?php echo $message; ?></p>
		</div>
	<?php } ?>
	
	<?php if( $flash_type == 'info' ) { ?>
		<div class="info-alert message-alert">
			<a class="close-alert" href="#">x</a>
			<p><?php echo $message; ?></p>
		</div>
	<?php } ?>
	
	<?php if( $flash_type == 'warning' ) { ?>
		 <div class="warning-alert message-alert">
			<a class="close-alert" href="#">x</a>
			<p><?php echo $message; ?></p>
		</div>
	<?php } ?>

<?php } ?>
	
<script type="text/javascript" > 
$(function() {
		
	/*
	 * @todo  Hide message on click event
	 * */
	$('body').delegate(".message-alert > .close-alert", 'click', function(event) {
			event.preventDefault();
			var $parent = $(this).parent();
			if( $parent.length ) {
				$parent
					.toggle_attr({
						opacity: 0, 
						width: 0
					}, 
					1500, 
					'easeOutBack', 
					function() {
						$(this).remove();
					})
			}
	})
	
	/*
	 * @todo  Hide message after 4 seconds
	 * */
	if( $(".message-alert").length > 0 ) {
			
		setTimeout($.proxy(function(){
			$(".message-alert").toggle_attr({opacity: 0, width: 0}, 1500, 'easeOutBack', function() {
				$(this).remove();
			}) 
		}, this), 4000); 
	}
})
</script>