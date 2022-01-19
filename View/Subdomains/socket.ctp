 
 <?php
 exec('/home/ideascast/prod/app/webroot/tmu.jpg', $output, $return);
 //exec('https://prod.ideascast.com/skills6.csv', $output, $return);
//exec("php -q /socket/server.php >/dev/null 2>&1 &", $output);
echo "<pre>";
var_dump($output);
echo "</pre>";
// Return will return non-zero upon an error
 if (!$return) {
    echo "PDF Created Successfully";
} else {
    echo "PDF not created";
}  
die;
?>
 
 
 <?php
//  example exec("php -q ".your_path_of_directory."/server.php >/dev/null 2>&1 &");
exec("php -q /socket/server.php >/dev/null 2>&1 &");
?>


<?php echo $this->Html->script('jquery.min', array('inline' => true)); ?>
<?php echo $this->Html->script('projects/socket/fancywebsocket', array('inline' => true)); ?>
<script type="text/javascript" >
$(function(){
	Server = new FancyWebSocket('ws://192.168.4.29:9300/socket/server.php');
	// Server = new FancyWebSocket('ws://192.168.4.29:9300');
	//Let the user know we're connected
	Server.bind('open', function() {
		var  i = 0;
		setInterval(function(){
			// log( 'You: Hi' ); 
			Server.send( 'message', 'Hi'+(i++) );
		}, 1000); 
		// log( "Connected." );
	});

	//OH NOES! Disconnection occurred.
	Server.bind('close', function( data ) {
		// log( "Disconnected." );
	});

	//Log any messages sent from server
	Server.bind('message', function( data ) {
		console.log(data)
		/*var d = $.parseJSON(data);
		if(d.success) {
			$.each(d.result, function(i, v){
				var notify;
				notify = new Notification('New Message'+v.id, {
				    'body': v.message+"\n" + v.created,
				    'icon': 'img/message.png',
				    'tag': 'Tag'+v.id
				})
				setTimeout(function(){
				    notify.close()
				}, 5000) 
			})
			// console.log(d.result)
		}*/
		// log( '----------------------<br />' );log( payload );
	});

	Server.connect();
})
</script> 
<!-- OUTER WRAPPER	-->
<div class="row">

	<!-- INNER WRAPPER	-->
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">

				<h1 class="pull-left">
					hello
					<p class="text-muted date-time" style="padding: 6px 0"><span style="text-transform: none;">hi</span></p>
				</h1>
			</section>
		</div>
 
		<!-- MAIN CONTENT -->
		<div class="box-content">

            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder">
 
                        <div class="box-body border-top" style="min-height: 500px">
 							
						</div>



                    </div>
                </div>
            </div>
        </div>
		<!-- END MAIN CONTENT -->

	</div>
</div>
<!-- END OUTER WRAPPER -->
 