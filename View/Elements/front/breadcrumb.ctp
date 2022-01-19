<?php
  $url = (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) ? htmlspecialchars( $_SERVER['HTTP_REFERER'] ,ENT_QUOTES) : null;
	if($this->params['action'] == 'tododetails'){
		$url = SITEURL.'todos/requests';
	}
  ?>
<div class="breadcrumb-wrap">
<span class="btn-back tipText" title="Go Back" ><i class="fa fa-chevron-left"></i></span>
<input type="hidden" value="<?php echo (isset($url) && !empty($url) )? $url:""; ?>" id="hidden_url" name="hidden_url" >
<ol class="breadcrumb-list breadcrumb">
    <!-- <li><a href="<?php //echo SITEURL?>"> Home page</a></li>	-->
	<?php
		if( isset($crumb) && !empty($crumb) ) {
			echo breadcrumbs($crumb);
		}
	?>
</ol>
</div>
<script type="text/javascript" >
$(function(){

	$('.bredd').tooltip({
  template: '<div class="tooltip CUSTOM-CLASS" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>'
  ,'container':'body','placement': 'bottom',
	})


	$(".btn-back.tipText").tooltip({'placement': 'bottom'})
	$(".breadcrumb").find('span.tipText').tooltip({'placement': 'bottom'})

	var currentHash = function() {
		return location.hash;//.replace(/^#/, '')
	}

	var hashes = [];
	var last_hash;
	var hash = location.hash;

	var hashchange = function() {
		last_hash = hash;
		hash = location.hash;
		if( last_hash !== hash ) {
			if( $.inArray(last_hash, hashes) == -1 ) {
				hashes.push(last_hash)
			}
			// console.log(hashes)
			// console.log('hash changed from ' + last_hash + ' to ' + hash)
			return true;
		}
	}

	var clicked = 0;
	$(".btn-back").on("click", function(event) {
		event.preventDefault()
		var $input = $('#hidden_url'),
			url = $input.val();
			if( url.indexOf("login") <= 0  ) {
				if( url != '' ) {
					location.href = url
				}
			}

	})

});
</script>
