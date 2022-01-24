<script type="text/javascript" >
$(function() {
	if( $js_config.logs ) {
		$(".cake-log-container").slideDown(500);
	}
})
</script>

<span class="cake-log-container">
	<style>
	.cake-log-container {
		display: none;	
	}
	.cake-sql-log {
		background-color: #e1dfe0 !important;
		color: #000000 !important;
	} 
	.cake-sql-log caption, .caption {
		background-color: #404040 !important;
		box-shadow: 0 0 16px #000000;
		color: #ffffff !important;
		font-size: 20px;
		text-align: center;
	}
	.caption {
			height: 10px;
			min-height: 10px;
			width: 100%;
			min-width: 100%;
			margin: 2px 0 0 0;
	}
	</style>

	<?php echo $this->element('sql_dump'); ?>
	<p class="caption"></p>
</span>