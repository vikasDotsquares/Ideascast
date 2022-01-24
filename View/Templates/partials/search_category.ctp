<style>
#sel_template_tab .trails:nth-child(2n),#sel_template_tab .trails:nth-child(3n) {
	cursor:default !important;
	text-decoration:none !important;
}
</style>
<?php  //pr($template_categories_count);
// echo $reviewrating; 
/* $showFullWidth = true;
if( $this->Session->read('Auth.User.role_id') == 1 ) {
$showFullWidth = true;
}
if($showFullWidth == true ){
	$columnWidth = 4;
	$ulWidht = 9;
} else {
	$columnWidth = 3;
	$ulWidht = 12;
} */


if( isset($this->request->params['pass'][1]) && $this->request->params['pass'][1] > 0){
	$showFullWidth = true;
	if( $this->Session->read('Auth.User.role_id') == 1 ) {
		$showFullWidth = true;
		}
		if($showFullWidth == true ){
			$columnWidth = 4;
			$ulWidht = 9;
		} else {
			$columnWidth = 3;
			$ulWidht = 12;
	}	
} else {
	$columnWidth = 3;
	$ulWidht = 12;
}
 
if(isset($template_categories) && !empty($template_categories)){ ?>

 <ul id="new_templates" class="clearfix templates_list col-sm-<?php echo $ulWidht;?>">
<?php
	foreach( $template_categories as $key => $val ) {
		$item = $val['TemplateCategory'];
		// pr($item);
		$icon_name = explode('.', $item['cat_icon']);
		$icon_file = ( !empty($icon_name) && count($icon_name) > 1 ) ? 'template-'.$icon_name[0] : 'icon_folder';
		 
		//$cat_templates = category_templates($item['id']);
		if(isset($val[0]['total']) && !empty($val[0]['total'])){
		$cat_templates = $val[0]['total'];
		}else{
		$cat_templates = category_templates($item['id']);
		}
		
		 
		if(isset($template_categories_count) && !empty($template_categories_count) ){
			//$cat_templates = $template_categories_count;
		 	$cat_templates = $template_categories_count[$key][0]['total'];
		}
		$ratings='';
		$revreating='';
		if(isset($reviewrating) && !empty($reviewrating)){
			foreach($reviewrating as $listrating){
				$ratings .= $listrating.',';
			}	
		}
		$revreating = substr($ratings, 0, -1);//substr($ratings,-1); 
		//echo $revreating; 
		if( !empty($projects_id) ){
			$project_id = $projects_id;			
		} else {
			$project_id = 0;
		}
	?>
		
		<li class="utemp_cat_list <?php echo $actual; ?>" style="cursor:pointer" data-revrating="<?php echo $revreating;?>" data-remote="<?php echo Router::Url(array('controller' => 'templates', 'action' =>  'create_workspace', $project_id, $item['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $item['id']; ?>">
			<div class="cat-list-inside"> 
			<div class="icon-wrapper">
				<div class="icon-inner"><span class="cat-icon <?php echo $icon_file; ?>"></span></div>
			</div>
			<div class="cat-title" data-ctitle="<?php echo $item['title'];?>">
				<?php echo $item['title'].'<br />(' . $cat_templates . ')';?>
			</div>
			</div>
		</li>
		
<?php } 
	echo "</ul>";   
	
	} else { ?>
		<div class="select_msg col-sm-<?php echo $ulWidht;?>" style="top: 48%;" >NO KNOWLEDGE TEMPLATES FOUND</div>
<?php }  ?>


<script>
	$(function(){
	$('a[href="#"],a[href=""]').attr('href', 'javascript:;');
	
 
 

	$.jsPaginations = function(args) {
		$js_config.search_limit = 4;
		var chkTotalTrRows = $('#new_templates').children('li').length,
			rows_per_page = $js_config.search_limit,
			total_rows;

		/* if( args && args.hasOwnProperty('parent') && args.parent !== '' ) {
			var $parent = args.parent;
			chkTotalTrRows = $($parent).children('li').length;
		} */
		if( chkTotalTrRows > 0 ) {
			total_rows = chkTotalTrRows;
		}
		
		var cur_page = (args) ? args.cur_page : 1;
		var start = (rows_per_page * (cur_page - 1));
		var end = start + rows_per_page;
		if( args && args.hasOwnProperty('parent') && args.parent !== '' ) {
			var $parent = args.parent; 
			// if( total_rows <= $js_config.search_limit ) {
				// $($parent).children().css('display', 'none').slice(start, end).css('display', 'block')
			// }
			// else {
				// $($parent + " > li").hide();
				// $($parent + ' > li:gt('+start+'):lt('+end+')').show();
			// } 
			$($parent).children().css('display', 'none').slice(start, end).css('display', 'block')
			
		} 
		
		var pagination_data = {
			"total_rows": total_rows,
			"rows_per_page": $js_config.search_limit,
			"adjacents" : $js_config.search_adjacents,
			"cur_page": parseInt(cur_page),
			"show_first": 1,
			"show_last": 1,
			"show_prev": 1,
			"show_next": 1,
			"btn_class": 'btn btn-default btn-sm',
		};
		
		//$('.paginate_links').html('<div class="loaders"></div>');	

			 
		$.ajax({
			url: $js_config.base_url + 'templates/get_pagination',
			type: 'POST',
			data: pagination_data,
			dataType: "JSON",
			success: function(response) {
				// Success
				if(response.success) {
					$('.paginate_links').html(response.output);
				}
			}
		});
	}

	
});
	function clickAnchor(t) {
		
		var element = $(t);
		var cur_page = element.attr("data-value");
console.log('cur_page', cur_page)
		var args = {'cur_page': cur_page, parent:'#new_templates'}

		/*var pagination_data = {
			"total_rows": $('#new_templates').children('li').size(),
			"rows_per_page": $js_config.search_limit,
			"adjacents" : $js_config.search_adjacents,
			"cur_page": parseInt(cur_page),
			"show_first": 1,
			"show_last": 1,
			"show_prev": 1,
			"show_next": 1,
			"btn_class": 'btn btn-default btn-sm',
		};
		
		 
		 var selectedTab = $('#search_accordion').find('.chat-shad');
		if( selectedTab.length > 0 ) {
			if( selectedTab.hasClass('view-all') ) {
				var link = selectedTab.find('a'),
					dataid = link.data('id');
				pagination_data.total_rows = $(".search-items-all-" + dataid).find(".result-item").length;
				args.parent = ".search-items-all-" + dataid;
			}
			else {
				var link = selectedTab.find('a'),
					dataid = link.attr('href');
				pagination_data.total_rows = $(".search-div-main" + dataid).find(".result-item").length;
				args.parent = ".search-div-main" + dataid;
			}

		} */
		$.jsPagination(args);
	}

	
</script>