
<?php echo $this->Html->css('projects/list-grid') ?>
<?php echo $this->Html->css('projects/animate'); ?>
<?php echo $this->Html->css('projects/templates'); ?>



<?php echo $this->Html->css('star-rating'); ?>
<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multi', array('inline' => true));
echo $this->Html->script('projects/template_library', array('inline' => true));

echo $this->Html->script('star-ratings', array('inline' => true));
 echo $this->Html->script('projects/plugins/marks/jquery.mark.min', array('inline' => true));




$showFullWidth = false;
if( $this->Session->read('Auth.User.role_id') == 1 || ( isset($this->request->params['pass'][1]) && $this->request->params['pass'][1] > 0) ) {
	$showFullWidth = true;
}

if($showFullWidth == true ){
	$columnWidth = 4;
	$ulWidht = 9;
} else {
	$columnWidth = 3;
	$ulWidht = 12;
}



?>
<!-- Modal Boxes // PASSWORD DELETE-->
<div class="modal modal-danger fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>
<div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>
<div class="modal modal-success fade " id="popup_modal_element" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>
<div class="modal modal-success fade " id="modal_manage_templates" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog add-update-modal">
		<div class="modal-content"></div>
	</div>
</div>
<div class="modal modal-success fade " id="modal_multi_templates" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog ">
		<div class="modal-content"></div>
	</div>
</div>
<style type="text/css">
	<?php if( isset($this->request->params['pass'][1]) && $this->request->params['pass'][1] > 0  ){ ?>

	  #new_templates li{ display:none;}

	<?php } ?>
	.inner-view .date-time {

	    font-weight: normal !important;

	}

	.cont_new{ display:none;}

	#popup_modal_element .modal-body {
	    background: #fff none repeat scroll 0 0 !important;
	    color: #333 !important;
	    max-height: 428px;
	    overflow: auto;
	}

	.popover .user-popover p {
		font-size: 11px;
		margin-bottom: 2px;
	}
	.popover .user-popover p:first-child {
		font-weight: 600;
		font-size: 14px;
	}
	.popover .user-popover p:last-child {
		margin-bottom: 5px;
	}


	.popover p:nth-child(2) {
		font-size: 12px;
	}


	.popover .popover-content {
		font-size: 12px;
		font-weight:normal;
		word-break: break-word;

	}
	.useremal a{
		color: #00A6DD !important;
	}
	.popover .popover-content .more_infor{
		border-bottom:solid 1px #444444;
	}
	.popover .popover-content .thrdauthor{
		font-weight:normal !important;
	}

	.mark, mark {
	    color: #f00;
	    padding: 0;
	    background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
	    color: #f00;
		text-transform: none;
	}
	.box-header .box-title{
		text-transform: none !important;
	}
	.table-wrapper {
		border: 1px solid #a9a9a9;
		border-radius: 4px;
		padding: 1px;
		width: 100%;
		display:table;
	}
	.table-wrapper .table {
		margin: 0;
		height:68px;
	}
	.table-wrapper .table td {
		border: 1px solid #fff;
		/* border-radius: 5px; */
		padding: 0px 0;
		vertical-align: middle;
		display:table-cell;
	}

	#thirdpartyusers{
		display:none;
	}

	#manage_templates {
		border-color: #605ca8 !important;
	}
	.btn-multi-select {
	    background-color: #e06c0a;
    	border-color: #E0680A;
    	color: #fff;
	}
	.btn-multi-select:hover {
	    background-color: #d56505;
    	border-color: #d56507;
    	color: #fff;
	}
	.wsp_template{
		background:rgba(103, 160, 40, 0.2);
	}
	#successFlashMsg{
		overflow: hidden;
	}
	#successFlashMsg p{
		margin: 4px 0 8px;
	}
	.template_info {
		background: #3c8dbc none repeat scroll 0 0;
		border-radius: 50%;
		color: #ffffff;
		font-size: 10px;
		height: 22px;
		line-height: 20px;
		padding: 0 8px;
		width: 22px;
		margin-top: 5px;
	}

	#sel_template_tab .trails:nth-child(2n) {
		cursor:default !important;
		text-decoration:none !important;
	}

	.no-scroll {
		overflow: hidden;
	}

	.box-body{
	overflow-x: auto;
    overflow-y: overlay;
	}


</style>
<script type="text/javascript">

<?php
if(isset($this->params['pass']['1'])) {
?>
//$('#ajax_overlay').show();
<?php } ?>
	jQuery(function($) {
		$('#modal_manage_templates, #modal_multi_templates').on('hidden.bs.modal', function(e){
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('');
		})

		$('#manage_templates').toggleClass('btn-success btn-default');

		$('body').delegate('.template_type', 'click', function(event){
			//$('.multi_template').hide()
		    if($(this).data('type') == 1) {
		    	$('#manage_templates').show().removeClass('btn-default disabled').addClass('btn-success');
		    }
		    else {
		    	$('#manage_templates').hide().removeClass('btn-success').addClass('btn-default disabled');
		    }


		    /*setTimeout(function(){
		    	if($('.templates_list').length <= 0) {
		    		$('.multi_template').hide()
		    	}
		    	else{
		    		$('.multi_template').show()
		    	}
		    }, 1500)*/

		});

		$('body').delegate('.multi_template', 'click', function(event){
			var type = $('.jeera-dashboard-but .template_type.active').data('type');
		    $.ajax({
	    		url: $js_config.base_url + 'templates/multi_templates',
	    		type: 'POST',
	    		data: {
	    			project_id: 		$js_config.project_id,
	    			template_cat_id: 	$js_config.template_category_id,
	    			type: 				type,
	    		},
	    		global: false,
	    		success: function(response){
	    			$('#modal_multi_templates').modal({
	                    keyboard: false
	                })

	                $('#modal_multi_templates .modal-content').html(response).show();
	    		}
	    	})
		});

		$('a[href="#"],a[href=""]').attr('href', 'javascript:;');

		// PAGE DEPENDENT CONFIGURATIONS -------------------
		$js_config.project_id = '<?php echo $project_id ?>';

		$js_config.template_select_url  = '<?php echo Router::Url(array('controller' => 'templates', 'action' => 'template_select', 'admin' => FALSE ), TRUE); ?>';

		$js_config.get_workspace_url = '<?php echo Router::Url(array('controller' => 'templates', 'action' => 'get_workspace', 'admin' => FALSE ), TRUE); ?>';


		$js_config.add_ws_url = '<?php echo Router::Url(array( "controller" => "templates", "action" => "create_workspace", $project_id ), true); ?>/';

		$js_config.step_2_element_url = '<?php echo $this->Html->url(array( "controller" => "projects", "action" => "manage_elements" ), true); ?>/';




		// END CONFIGURATIONS -------------------

		$("#ul_list_grid > li > div.box-success").on('click', function(event) {
			var $this = $(this);
			$("#ul_list_grid > li > div.selected").removeClass("selected")
			$this.addClass("selected")
		})

		$.fn.setTemplateGrid = function(event) {
				var $w = $(window),
					$ul = $("#ul_list_grid"),
					outerWidth = $ul.outerWidth(),
					maxNum = 4,
					liWidth = (( outerWidth * maxNum ) / 100) ;
				liWidth = liWidth.toFixed(2) ;


			// $("#list_grid_container li").each( function(i, v) {
				// console.log($(v).outerWidth())
				// $(v).css('width', ( liWidth+"%"))
			// })
		}
		$(window).on('resize', function(event) {
				// $(this).setTemplateGrid()
		})
		// $(this).setTemplateGrid()


		/* ******** TABS ANIMATION *************** */
		var start_tab_anim = false;
		if(start_tab_anim) {
			var b = 'fadeInLeft';
			var c;
			var a;
			d($('#myTempletTabs a'), $('#tab-content'));
			function d(e, f, g) {
				e.click(function (i) {
					i.preventDefault();
					$(this).tab('show');
					var h = $(this).data('easein');
					if (c) {
						c.removeClass(a);
					}
					if (h) {
						f.find('div.active').addClass('animated ' + h);
						a = h;
					} else {
						if (g) {
							f.find('div.active').addClass('animated ' + g);
							a = g;
						} else {
							f.find('div.active').addClass('animated ' + b);
							a = b;
						}
					}
					c = f.find('div.active');
				});
			}
			$('#myTempletTabs a[data-toggle=tab]').click(function (f) {
				f.preventDefault();

				if ($(this).data('easein') != undefined) {
					$(this).next().removeClass($(this).data('easein')).addClass('animated ' + $(this).data('easein'));
				} else {
					$(this).next().addClass('animated ' + b);
				}
			});
		}

		/* ******** TABS ANIMATION *************** */

		window.chr = 0;


		$('body').delegate('.ajax-pagination a', 'click', function(e) {
	        e.preventDefault()

			var $this = $(this),
				$parent = $this.parents('#template_list_container').filter(':first'),
				post = { 'project_id': '<?php echo $project_id; ?>' },
				pageUrl = $this.attr('href');

			$.ajax({
				type:'POST',
				data: $.param(post),
				url: pageUrl,
				global: false,
				success: function( response, status, jxhr ) {
					setTimeout(function(){
						$parent.html(response);
					}, 800)
				}
			})

	        return false;
	    });

		$('body').delegate('.pagination_temp_filter_template a', 'click', function(e) {
	        e.preventDefault()
			var tmpType = $(this).attr('type');

			var $this = $(this),
				$parent = $this.parents('#template_tab').find('#new_templates:first'),
				post = { 'project_id': '<?php echo $project_id; ?>', 'type': tmpType, 'category_id': $(this).attr('template_category_id'), 'columnwidht': 3 },
				pageUrl = $this.attr('href');

				if(tmpType == 3){

					$('.col-md-3.pull-right').show();
					//$('#new_templates').removeClass('col-sm-12');
					$('#new_templates').removeClass('col-md-9');
					$('#new_templates').addClass('col-md-9');

					$('#new_templates li').removeClass('col-lg-3');
					$('#new_templates li').removeClass('col-lg-4');
					$('#new_templates li').addClass('col-lg-4');

					$('.select_msg_main').removeClass('col-sm-12');
					$('.select_msg_main').removeClass('col-sm-9');
					$('.select_msg_main').addClass('col-sm-9');

					var chk = $('.col-md-3.pull-right')

					if(!chk.hasClass('tparty')){
						if($('.col-md-3.pull-right').length > 0){
							$('.tparty').remove();
						}
					}

				}else{

					$('.col-md-3.pull-right').hide();
					$('#new_templates').removeClass('col-md-9');
					//$('#new_templates').removeClass('col-sm-12');
					//$('#new_templates').addClass('col-sm-12');

					$('#new_templates li').removeClass('col-lg-3');
					$('#new_templates li').removeClass('col-lg-4');
					$('#new_templates li').addClass('col-lg-3');

					$('.select_msg_main').removeClass('col-sm-12');
					$('.select_msg_main').removeClass('col-sm-9');
					$('.select_msg_main').addClass('col-sm-12');
					$('.tparty').html('');

				}

			$('.pagination.pagination-large.pull-right').remove();
			$('.pagination_temp_filter_template').remove();

			$.ajax({
				type:'POST',
				data: $.param(post),
				dataType: 'JSON',
				url: pageUrl,
				global: false,
				success: function( response, status, jxhr ) {
					setTimeout(function(){
						$parent.html(response);
					}, 400);


				}
			})

	        return false;
	    });


		$('body').delegate('.pagination_temp_rating_filter a', 'click', function(e) {
	        e.preventDefault()

			var tmpType = $(this).attr('type');

			tmpType = (tmpType == '' || tmpType != undefined)? $(this).attr('searchdatatype'):tmpType;

			var starRating = $( $(".search_rating[data-active=1]") ).map(function() {
				return $(this).data('rating');
			  }).get().join();
			//console.log(starRating);

			var $this = $(this),
				$parent = $this.parents('#template_tab').find('#new_templates:first'),
				post = { 'project_id': '<?php echo $project_id; ?>', 'searchdatatype': $(this).attr('searchdatatype'), 'template_category_id': $(this).attr('template_category_id'), 'columnwidht': 3,'starRating':starRating },
				pageUrl = $this.attr('href');

				// console.log($parent);
				// return false;

				if(tmpType == 3){

					$('.col-md-3.pull-right').show();
					//$('#new_templates').removeClass('col-sm-12');
					//$('#new_templates').removeClass('col-sm-9');
					 $('#new_templates').removeClass('col-md-12');
					 $('#new_templates').removeClass('col-md-9');
					$('#new_templates').addClass('col-md-9');

					$('#new_templates li').removeClass('col-lg-3');
					$('#new_templates li').removeClass('col-lg-4');
					$('#new_templates li').addClass('col-lg-4');

					$('.select_msg_main').removeClass('col-sm-12');
					$('.select_msg_main').removeClass('col-sm-9');
					$('.select_msg_main').addClass('col-sm-9');

					var chk = $('.col-md-3.pull-right')

					if(!chk.hasClass('tparty')){
						if($('.col-md-3.pull-right').length > 0){
							$('.tparty').remove();
						}
					}

				}else{

					$('.col-md-3.pull-right').hide();
					$('#new_templates').removeClass('col-md-9');
					$('#new_templates').removeClass('col-md-12');
					$('#new_templates').addClass('col-md-12');

					$('#new_templates li').removeClass('col-lg-3');
					$('#new_templates li').removeClass('col-lg-4');
					$('#new_templates li').addClass('col-lg-3');

					$('.select_msg_main').removeClass('col-sm-12');
					$('.select_msg_main').removeClass('col-sm-9');
					$('.select_msg_main').addClass('col-sm-12');
					$('.tparty').html('');
					$('.tparty').hide();

				}

			$('.pagination.pagination-large.pull-right').remove();
			$('.pagination_temp_filter_template').remove();

			$.ajax({
				type:'POST',
				data: $.param(post),
				url: pageUrl,
				global: false,
				success: function( response, status, jxhr ) {
					setTimeout(function(){
						$parent.html('').html(response);
					}, 400);


				}
			})

	        return false;
	    });


		$('body').delegate('#modelFormAddWorkspace', "submit", function(e){

			e.preventDefault();

			var $form = $(this),
				runAjax = true;

			var mb = $('#popup_modal');
			var add_ws_url = $js_config.add_ws_url;

			var step_element_url = $js_config.step_2_element_url;

			// Get current project_id
			var project_id = $("input[name='data[Workspace][project_id]']").val();
				project_id = project_id || $js_config.project_id

			step_element_url += project_id

			if( runAjax && runAjax == true ) {
				$.ajax({
					url: add_ws_url,
					type:'POST',
					data: $form.serialize(),
					dataType: 'json',
					success: function( response, status, jxhr ) {
						runAjax = false;
						// REMOVE ALL ERROR SPAN
						$form.find('span.error-message.text-danger').text("")

						if( response.success ) {

							if( !$.isEmptyObject(response.content) ) {
								var insert_ws_id = response.content.id;
								if( insert_ws_id ) {
									$('#popup_modal').modal('hide')
									setTimeout(function() {
										var redirect = '';
										if( response.content.url == false ) {
											step_element_url += '/' + insert_ws_id
											redirect = step_element_url
											window.location.replace(redirect);
										}
										else {
											redirect = response.content.id
											window.location.reload();
										}

									}, 1000)
								}
							}
						}
						else {
							if( ! $.isEmptyObject( response.content ) ) {

								$.each( response.content, function( ele, msg) {

									var $element = $form.find('[name="data[Workspace]['+ele+']"]')
									var $parent = $element.parents(".parent-wrap:first");

									if( $parent.find('span.error-message.text-danger').length ) {
										$parent.find('span.error-message.text-danger').text(msg)
									}
								})

							}
							if( ! $.isEmptyObject(response.date_error ) ) {
								$("#date-error-message").html('<div id="successFlashMsg" class="box box-solid bg-red" style="overflow: hidden;  "><div class="box-body"><p>'+response.date_error+'</p></div></div>')
								setTimeout(function(){
									$("#date-error-message").fadeOut("500");
								},2000)
							}
						}
					}
				});
				// end ajax
			}
		})


		/*
		 * @access  public
		 * @todo  	Set data of each color bucket and color boxes
		 * @return  None
		 * */
		$('body').delegate('.like_comment', 'click', function(event){
			event.preventDefault();

			var $that = $(this),
			data = $that.data(),
			href = data.remote,
			$label = $that.find('span.label'),
			number = parseInt($label.text(), 10);

			//console.log(this)

			if( href != '' ) {
				$.ajax({
					url: href,
					type: "POST",
					data: $.param({}),
					dataType: "JSON",
					global: false,
					success: function (response) {
						if(response.content != '' ) {
							$label.text(number + 1)
						}
						$that.removeClass('like_comment')

					}
				})
			}

		})

		/*
		 * @access  public
		 * @todo  	Bind click event on delete project button to delete that
		 * @return  None
		 * */
		$("body").delegate(".trash_template123", 'click', function (event) {
			var $that = $(this),
				$parent_list = $that.parents('li.utemp_list:first'),
				data = $parent_list.data(),
				template_id = data.id;

			BootstrapDialog.show({
				title: 'Confirmation',
				message: 'Are you sure you want to delete this Library Template?',
				type: BootstrapDialog.TYPE_DANGER,
				draggable: true,
				buttons: [
				{
					//icon: '',
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: true,
					action: function (dialogRef) {
						$.when(
						$.ajax({
							url: $js_config.base_url + 'templates/trash_template',
							type: "POST",
							data: $.param({template_id: template_id }),
							dataType: "JSON",
							global: false,
							success: function (response) {
								if( response.success ) {
									$parent_list.slideUp(500, function(){
										$parent_list.remove();
										/* if( $('.templates_list li').length <= 0 ) {
											$('.templates_list').html('<div class="select_msg" >  NO TEMPLATES AVAILABLE </div>')
										} */
									});
									location.reload();
								}
							}
						})
						).then(function( data, textStatus, jqXHR ) {
							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							dialogRef.getModalBody().html('<div class="loader"></div>');
							setTimeout(function () {
								dialogRef.close();
							}, 300);
						})
					}
				},
				{
					label: ' No',
					//icon: '',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
				]
			});

		});

		$('.template_pophover').popover({
	        placement : 'top',
	        trigger : 'hover',
	        html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
	    })



		$('body').delegate('.template_type', 'click', function(e){
			e.preventDefault();

			$('.popover').remove();
			$('.popover').remove();
			$('.select_msg').remove();
			$('.select_msg_main').remove();

			$('.paginate_links').remove();

			$('.sbox-rating').css('pointer-events','unset');
			$('.sbox-rating').removeClass('disable');
			$('.sbox').css('pointer-events','unset');
			$('.sbox').removeClass('disable');
			$('#temp_search').val('');
			$('.search_clear').hide();
			$('.search_submit').show();

			$('.template_type .cont').show();
			$('.template_type .cont_new').hide();


				var $that = $(this),
				data = $that.data(),
				type = data.type;
				showcolwidth = '<?php echo $columnWidth;?>';
				if(type > 1 && type <=3 ){
					$('#btn_create_template').hide();
				} else {
					$('#btn_create_template').show();
				}


				var chk = $('.col-md-3.pull-right');
				if(chk.hasClass('tparty')){
					if($('.col-md-3.pull-right').length > 0){
						$('.tparty').remove();
					}
				}


				/* =============== Close Right Sidebar options ===========*/
				var collapsArraytop=[];
				$('div[id^="collapseOne-"]').each(function(){
					collapsArraytop.push(this.id);
				});
				$.each( collapsArraytop, function( i, val ) {
					$('#'+val).css('display','none');
					$('.third-party-user').addClass('closed');
				});
				/* ====================================================== */

				// $('.template_type').removeAttr('style');
				// $that.css('box-shadow', '3px 2px 4px 0px #333');

				$('.template_type').removeClass('active');
				$that.addClass('active');

				$('.search_rating').attr({'data-serachtype':$(this).data('type'),'data-active':0});
				$('.search_rating i').addClass('text-gray');

				$('.tparty').remove();


				$('.pagination.pagination-large.pull-right').remove();
				$('.pagination_temp_filter_template').remove();

			$.ajax({
				url: $js_config.base_url + 'templates/filter_templates',
				type: "POST",
				data: { project_id: $js_config.project_id, category_id: $js_config.template_category_id, type: type,columnwidht:showcolwidth },
				dataType: "JSON",
				global: false,
				success: function (response) {
					// console.log(response.length)
					$('.col-md-3.pull-right').hide();
					setTimeout(function(){
						if($("#new_templates").length > 0){
							$("#new_templates").remove();
						}
						$('.tparty,.select_msg').remove();
						$(response).insertAfter("#template_tab .row");

						if(type == 3){
							$('.col-md-3.pull-right').show();
							//$('#new_templates').removeClass('col-sm-12');
							$('#new_templates').removeClass('col-md-9');
							$('#new_templates').addClass('col-md-9');

							$('#new_templates li').removeClass('col-lg-3');
							$('#new_templates li').removeClass('col-lg-4');
							$('#new_templates li').addClass('col-lg-4');

							$('.select_msg_main').removeClass('col-sm-12');
							$('.select_msg_main').removeClass('col-sm-9');
							$('.select_msg_main').addClass('col-sm-9');

							var chk = $('.col-md-3.pull-right')

							if(!chk.hasClass('tparty')){
								if($('.col-md-3.pull-right').length > 0){
									$('.tparty').remove();
								}
							}

						}else{
							//console.log("i am here...");
							$('.col-md-3.pull-right').hide();
							$('#new_templates').removeClass('col-md-9');
							//$('#new_templates').removeClass('col-sm-12');
							$('#new_templates').addClass('col-md-12');

							$('#new_templates li').removeClass('col-lg-3');
							$('#new_templates li').removeClass('col-lg-4');
							$('#new_templates li').addClass('col-lg-3');

							$('.select_msg_main').removeClass('col-sm-12');
							$('.select_msg_main').removeClass('col-sm-9');
							$('.select_msg_main').addClass('col-sm-12');

							//$('.tparty').hide();
							var chk = $('.col-md-3.pull-right')

							if(chk.hasClass('tparty')){
								if($('.col-md-3.pull-right').length > 0){
									$('.tparty').remove();
								}
							}

							if($('.utemp_list').length <= 0) {

							}
						}

					}, 100);

					setTimeout(function(){
					if($(".paginate_links").length > 1){
							 $(".paginate_links").remove();

						}
					},400)


					setTimeout(function(){
						e.preventDefault();
						e.stopPropagation();

						$('.paginate_links').html('');
						$.jsPagination({cur_page: 1, parent: '#new_templates' });

						//  Group Select Functionality

						if($js_config.project_id > 0) {
							var typeVal = $('.template_type.active').data('type');
							if($('.templates_list .utemp_list').length <= 1) {
					    		 //$('.multi_template').hide()
					    		 $('.multi_template').addClass('disabled')
					    	}
					    	else{

								 $('.multi_template').removeClass('disabled')
					    		setTimeout(function(){
					    		var titleVal = '';
					    		//console.log($('.template_type.active').data('type'))
					    		if(typeVal == 1) {
					    			titleVal = 'Select Knowledge Templates';
					    			$('.multi_template').attr('data-original-title', titleVal)
					    		}
					    		else if(typeVal == 2) {
					    			titleVal = 'Select Multiple OpusView Templates';
					    			$('.multi_template').attr('data-original-title', titleVal)
					    		}
					    		else if(typeVal == 3) {
					    			titleVal = 'Select Multiple Third Party Templates';
					    			$('.multi_template').attr('data-original-title', titleVal)
					    		}


					    		$('.multi_template').show()
					    	}, 1000)
					    	}
				    	}
				    	else {
				    		//$('.multi_template').hide()
				    	}
					},1000);
				}
			})

			$('.template_type i.fa').addClass('hide');
			$('i.fa', $(this)).removeClass('hide');

		})

		$('body').delegate('#cat_trail', 'click', function(e){
			e.preventDefault();
			var $this = $(this);
			if( $(this).data('remote') )
				window.location = $(this).data('remote');
			return
			$.ajax({
				url: $js_config.base_url + 'templates/template_categories',
				type: "POST",
				data: {project_id: $(this).data('project')},
				dataType: "JSON",
				global: false,
				success: function (response) {
					//console.log("==pawan==");
					$('#template_tab').html(response);
					$this.parent('#sel_template_tab').html('Library');
					window.history.pushState('', '', $js_config.base_url + 'templates/create_workspace/' + $this.data('project'));
				}
			})
		})

		<?php if(isset($template_category_id) && !empty($template_category_id)){ ?>
			$('#myTempletTabs a[data-target="#template_tab"]').tab('show');
			$('#btn_create_template,.sbox').show();
		<?php } ?>

		<?php if ( !isset($project_id) || empty($project_id)) { ?>
			$('#myTempletTabs a[data-target="#new_tab"]').removeAttr('data-toggle').removeAttr('data-target').css({'color': '#cccccc','pointer-events': 'none'});
			$('#myTempletTabs a[data-target="#new_tab"]').removeClass('disabled');
			$('#myTempletTabs a[data-target="#template_tab"]').tab('show');
			$('#btn_create_template,.sbox').show();
		<?php } ?>

		$('body').delegate('.utemp_cat_list_actual', 'click', function(e){
			e.preventDefault();
			//alert(0);
			//var ratingString = $(this).data('revrating');
			//var temp_rev_rating = $(this).data('revrating').split(',');

			/* console.log(temp_rev_rating.length);
			    return false; */
	        /*
			if( temp_rev_rating.length > 0 ){
				console.log(temp_rev_rating.length);
			} else {

			} */

			window.location = $(this).data('remote');
			$('#ajax_overlay').hide();
		})

		<?php if(isset($this->params['pass']['1']) && $this->params['pass']['1'] ==0) { ?>
			setTimeout(function(){

				$('#myTempletTabs li:nth(1)').trigger('click');
				$('#myTempletTabs a[data-target="#template_tab"]').tab('show');
				$('#ajax_overlay').hide();
				$('.sbox-rating').show();
				//console.log($('#myTempletTabs li:nth(1)'));
			},1500)
		<?php } ?>

		$('#myTempletTabs a').on('shown.bs.tab', function(event){
			var x = $(event.target);         // active tab
			var y = $(event.relatedTarget).text();  // previous tab
			if( x.is('#sel_template_tab') ) {
				$('#btn_create_template,.sbox').show();
			}
			else {
				$('#btn_create_template,.sbox').hide();
			}
		});

		$('body').delegate('.arrow-down-click a', 'click', function(e){
			e.preventDefault();
			var $parent = $(this).parents('.middle-section'),
				$bottom_section = $parent.find('.bottom-section');

			$bottom_section.slideToggle('slow', function(){ $(this).css('box-shadow','0px 0px 10px 2px #67a028') });
			$(this).toggleClass('opened closed');

		})


		function isScrolledIntoView(elem)
		{
			var docViewTop = $(window).scrollTop();
			var docViewBottom = docViewTop + $(window).height();

			var elemTop = $(elem).offset().top;
			var elemBottom = elemTop + $(elem).height();

			return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
		}

		/*******************************************************************/
		/*					START WORKSPACE TEMPLATE					   */
		/*******************************************************************/

		$('#modal_manage_templates').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('')
			//location.reload();
		});

		// get project workspace
		$('body').delegate('#all_owner_projects', 'change', function(event){
		event.preventDefault();

			var projectid = $(this).val();
			var href = $js_config.base_url + 'templates/get_workspaces';
			if( projectid > 0 ){
				$.ajax({
					url: href,
					type: "POST",
					crossDomain: false,
					data: $.param({project_id:projectid}),
					global: false,
					success: function (response) {
						$(".ownerprojectworkspace").html(response);
					}
				})
			}
		});

		// submit form
		$('body').delegate('.submitwptemplate', 'click', function(event){
			event.preventDefault();
			$(this).prop("disabled", true);
			var project_id = $("#all_owner_projects").val();
			var workspace_id = $("#all_owner_project_workspace").val();
			var template_title = $("#template_title").val();
			var template_description = $("#template_description").val();
			var template_document = 'off';
			var wsp_imported_for = 'off';
			if ($('#include_documents').is(":checked"))
			{
				template_document = 'on';
			}
			if ($('#wsp_imported_for').is(":checked"))
			{
				wsp_imported_for = 'on';
			}

			var destination_id = "";
			var destination_idArr = new Array();
			var i=1;
			$( "#destination_id option:selected" ).each(function() {
			  destination_id += $( this ).val() + ",";
			  destination_idArr[i] = $( this ).val();
			  i++;
			});
			destination_id = removeLastComma(destination_id);

			//console.log(destination_idArr.length); return false;

			var href = $js_config.base_url + 'templates/workspace_template';
			// console.log(project_id+"="+workspace_id+"="+destination_id);

			if( project_id > 0 && workspace_id > 0 && destination_id !="" ){

				$.ajax({
					url: href,
					type: "POST",
					dataType:"JSON",
					crossDomain: false,
					data: $.param({project_id:project_id,workspace_id:workspace_id,template_title:template_title,template_description:template_description,destination_id:destination_id,include_documents:template_document,wsp_imported:wsp_imported_for}),
					global: false,
					success: function (response) {

						if( response.success ){

							if( destination_idArr.length > 2 ){
								location.href = $js_config.base_url + 'templates/create_workspace/0/';
							} else {
								location.href = $js_config.base_url + 'templates/create_workspace/0/'+destination_id;
							}
						} else {
							$(this).prop("disabled", false);
							if( response.content.title != '' ){
								$(".tmptTitle").text(response.content.title);
							}
							if( response.content.description != '' ){
								$(".tmptDescription").text(response.content.description);
							}

						}
					}
				})

			}

		});

		function removeLastComma(str) {
		   return str.replace(/,(\s+)?$/, '');
		}

		/*******************************************************************/
		/*					END WORKSPACE TEMPLATE					       */
		/*******************************************************************/
		// PASSWORD DELETE
		$.current_delete = {};
		$('body').delegate('.delete-an-item', 'click', function(event) {
			event.preventDefault();
			$.current_delete = $(this);
		});

		$('#modal_delete').on('hidden.bs.modal', function () {
	        $(this).removeData('bs.modal');
	        $(this).find('.modal-content').html('');
	        $.current_delete = {};
	    });

		$("#sel_new_tab").on("click", function(){
			//$(this).parents().find('.templateheading').text("Add Knowledge Templates to your Project");
			//$(this).parents().find('#page_heading').text("Add Knowledge Template");
			$(".standardinfo").show();
			$(".libraryinfo").hide();

		})
		$("#sel_template_tab").on("click", function(){
			//$(this).parents().find('.templateheading').text("Add a Template Workspace to your Project");

			<?php if( isset($this->request->params['pass'][0]) && $this->request->params['pass'][0] > 0){ ?>

			if($("#sel_template_tab").find('span').length < 2){
			$(this).parents().find('.templateheading').text("Add Knowledge Templates to your Project");
			$(this).parents().find('#page_heading').text("Add Knowledge Templates");
			$(this).parents().find('.breadcrumb li:last').text('Add Knowledge Templates');
			}
			<?php }else {?>

			if($("#sel_template_tab").find('span').length < 2){
			$(this).parents().find('.templateheading').text("View and select Knowledge Templates for your Project work");
			$(this).parents().find('#page_heading').text("Knowledge Library");
			$(this).parents().find('.breadcrumb li:last').text('Knowledge Library');
			}

			<?php }?>

			$(".standardinfo").hide();
			$(".libraryinfo").show();


		})

		$('.prophover').popover({
			placement : 'right',
			trigger : 'hover',
			html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
		})

	})
</script>

<?php
$info = "View and select Knowledge Templates for your Project work";
if( isset($this->request->params['pass'][1]) && $this->request->params['pass'][1] > 0){

		$info = "Add Knowledge Templates to your Project";
?>
<script>
$(function(){

	$('.content-header.clearfix .templateheading').text("Add Knowledge Templates to your Project");
	$('#page_heading').text("Add Knowledge Templates");
	$('.breadcrumb li:last').text('Add Knowledge Templates');
})
</script>


<?php
} else if(( isset($this->request->params['pass'][0]) && $this->request->params['pass'][0] > 0) && !isset($this->request->params['pass'][1])){


		$info = "Add Workspace to your Project";

}else if( isset($this->request->params['pass'][1]) && $this->request->params['pass'][1] == 0){

?>

<script>
$(function(){
	$('#sel_template_tab').trigger('click');
})
</script>
<?php

}else{

	$info = "View and select Knowledge Templates for your Project work";
}


?>

<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left" style="min-height: 50px;"><span id="page_heading"><?php echo $data['page_heading']; ?></span>
					<?php if ( isset($project_id) && !empty($project_id)) { ?>
					<p class="text-muted date-time templateheading" id="project_text">
						<?php /* echo $project_title; ?>
						<span>Created: <?php //echo date('d M Y h:i:s', strtotime($project_detail['UserProject']['created']));
						echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($project_detail['UserProject']['created'])),$format = 'd M Y h:i:s');
						?></span>
						<span>Updated: <?php //echo date('d M Y h:i:s', strtotime($project_detail['UserProject']['modified']));
						echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($project_detail['UserProject']['modified'])),$format = 'd M Y h:i:s');
						?></span> */

						echo $info ;
						?>




					</p>
					<?php } else { ?>
					<p class="text-muted date-time lib" id="">
					  <?php echo $info ; ?>
					 </p>
					<?php } ?>
				</h1>
			<?php echo $this->Session->flash(); ?>
			</section>
		</div>


		<div class="box-content">

            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">
                        <div class="box-header template-library-button" style="background: #f1f3f4 none repeat scroll 0px 0px; cursor: move; border-bottom: none; border: 1px solid #dddddd; padding: 8px 10px; min-height: 48px;">


							<?php if(isset($this->params['pass'][0]) && $this->params['pass'][0] <= 0 ){  ?>

								<a class="pull-left libraryinfo" style="color:#555;" ><i class="fa fa-info template_info prophover" data-placement="left" data-content="Knowledge Templates are pre-configured Workspaces to quickly let you set up your projects. You can create and select Knowledge Templates containing Tasks and Documents" data-original-title="" title=""></i></a>

							<?php } else { ?>

								<a class="pull-left standardinfo" style="color:#555;" ><i class="fa fa-info template_info prophover" data-placement="top" data-content="Workspaces contain Areas to allow you to organize related Tasks and to visualize the required work." data-original-title="" title=""></i></a>
								<a class="pull-left libraryinfo" style="color:#555; display:none;" ><i class="fa fa-info template_info prophover" data-placement="top" data-content="Templates are pre-configured Workspaces to quickly let you set up your projects. You can create and select Templates containing Tasks and Documents." data-original-title="" title=""></i></a>

							<?php } ?>
							<!-- END MODAL BOX -->
							<?php
							$listdomainusers = $this->Common->userDetail($this->Session->read('Auth.User.id'));
						  
							
							if(isset($this->params['pass'][0]) && $this->params['pass'][0] == 0 ){

							if( $this->Session->read('Auth.User.role_id') != 1){
								
							if($listdomainusers['UserDetail']['create_template'] == 1){  								 
							 
							?>
							  <a id="btn_create_template1" class="kl-mt tipText" data-target="#modal_manage_templates" data-toggle="modal" title="Convert Workspace to Knowledge Template" data-remote="<?php echo Router::Url(array( "controller" => "templates", "action" => "convertwstemplate",  'admin' => FALSE ), true); ?>"> <i class="convertworkspace"></i></a>
							<?php 
							}else{ ?>
								
							 <a id="btn_create_templates1"  class="kl-mt tipText disable"    title="Access Denied"  > <i class="convertworkspace"></i></a>	
							<?php }  
								}
							}   ?>

							<?php   if(isset($this->params['pass']['1']) && !empty($this->params['pass']['1'])){  
							
							if($listdomainusers['UserDetail']['create_template'] == 1){  
							?>
							  <a id="btn_create_template" title="Add Knowledge Template"  class="kl-mt pull-right tipText" href="<?php echo Router::Url(array( "controller" => "templates", "action" => "create_templates",$this->params['pass']['0'],$this->params['pass']['1'],  'admin' => FALSE ), true); ?>"><i class="workspace-icon"></i> </a>
							<?php  }else{ ?>
							  <a id="btn_create_templates" title="Access Denied"  class="kl-mt pull-right tipText disable"  ><i class="workspace-icon"></i> </a>	
								
							<?php }  
							
							
							} else { 
							if($listdomainusers['UserDetail']['create_template'] == 1){  
							?>
							  <a id="btn_create_template" title="Add Knowledge Template" class="kl-mt pull-right tipText" href="<?php echo Router::Url(array( "controller" => "templates", "action" => "create_templates",  'admin' => FALSE ), true); ?>"><i class="workspace-icon"></i> </a>

							<?php }else{ ?>
							  <a id="btn_create_templates" title="Access Denied" class="kl-mt pull-right tipText disable"  ><i class="workspace-icon"></i> </a>
								
							<?php } } ?>

                        </div>
	<?php

//pr($this->request->params['pass']);
$showActive = " ";
$showActiveT = "";
if( isset($this->request->params['pass'][1]) && $this->request->params['pass'][1] > 0){
		 $showActive = "active";
		 $showActiveT = "";


} else if(( isset($this->request->params['pass'][0]) && $this->request->params['pass'][0] > 0) && !isset($this->request->params['pass'][1])){


		$showActiveT = "active";
		$showActive='';

}

?>

						<div class="box-body borderworkspace" >
							<div class="col-sm-12">
								<ul class="nav nav-tabs" id="myTempletTabs" style="padding: 0 0 10px 0; border-bottom: medium none; cursor: pointer !important; border-bottom: 2px solid #67a028;">
									<li class="<?php echo $showActiveT; ?> "><a id="sel_new_tab" class="disabled"   data-target="#new_tab" data-toggle="tab" data-easein="fadeInLeft">New</a></li>
									<li class="<?php echo $showActive; ?>">
									    <?php echo $cursor = ''; ?>
										<?php
											if(isset($template_category_id) && !empty($template_category_id)){
												
												$cursor = 'style = "cursor:pointer !important;" ';
											}
											?>
										<a id="sel_template_tab" <?php //echo $cursor ; ?> data-target="#template_tab" data-toggle="tab" data-easein="fadeInLeft">
											
											<span id="cat_trail"  <?php  echo $cursor ; if(isset($template_category_id) && !empty($template_category_id)){ ?>data-remote="<?php echo Router::Url(array('controller' => 'templates', 'action' =>  'create_workspace', $project_id,0, 'admin' => FALSE ), TRUE); ?>" class="trails" <?php } ?> data-project="<?php echo $project_id; ?>">Knowledge Templates</span>
											<?php
											if(isset($template_category_id) && !empty($template_category_id)){
												$cat_data = getByDbId('TemplateCategory', $template_category_id, ['title']);
											?>
												 > <span class="trails"><?php echo $cat_data['TemplateCategory']['title']; ?></span>
											<?php } ?>
										</a>
									</li>
								</ul>
								<?php //if( isset($template_category_id) && !empty($template_category_id)){ ?>
								<div class="sbox-rating tipText" title="Filter Knowledge Templates">
									<div class="rating-group">
										<span class="rating-box">
											<button class="btn btn-white btn-sm search_rating" data-serachtype="1" data-active='0' data-rating="1" type="button">1 <i class="fa fa-star text-gray"></i></button>
											<button class="btn btn-white btn-sm search_rating" data-serachtype="1" data-active='0' data-rating="2" type="button">2 <i class="fa fa-star text-gray"></i></button>
											<button class="btn btn-white btn-sm search_rating" data-serachtype="1" data-active='0' data-rating="3" type="button">3 <i class="fa fa-star text-gray"></i></button>
											<button class="btn btn-white btn-sm search_rating" data-serachtype="1" data-active='0' data-rating="4" type="button">4 <i class="fa fa-star text-gray"></i></button>
											<button class="btn btn-white btn-sm search_rating" data-serachtype="1" data-active='0' data-rating="5" type="button">5 <i class="fa fa-star text-gray"></i></button>
											<button class="btn  btn-sm search_rating_close " type="button"><i class="clearblackicon"></i></button>
										</span>
									</div>
								</div>
								<?php //} ?>
								<div class="sbox">
									<div class="input-group">
										<input id="temp_search" type="text" class="form-control" placeholder="Search Title for...">
										<span class="input-group-btn">
											<button class="btn search_clear" style="display:none;" type="button"><i class="clearblackicon"></i></button>
											<button class="btn search_submit" type="button"><i class="search-skill"></i></button>
										</span>
									</div>
								</div>
							</div>

							<div class="tab-content" id="tab-content">

								<div class="tab-pane  <?php echo $showActiveT; ?> " id="new_tab">
									<div id="template_list_container" class="clearfix" >
									<?php
									if( isset($data['templates']) && !empty($data['templates'])) {

									?>
										<!-- LIST AND GRID VIEW START	-->
										<ul id="template_list" class="clearfix col-sm-<?php echo $ulWidht;?>">
										<?php
											foreach( $templates as $key => $val ) {
												$item = $val['Template'];
										?>
												<li class="col-lg-<?php echo $columnWidth;?> col-md-4 col-sm-6">
													<div class="box box-success" data-remote="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'popups', 'workspace', $project_id, $item['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $item['id']; ?>" data-toggle="modal" data-target="#popup_modal1">

														<div class="box-header"> <h3 class="box-title "><?php echo $item['title'] ?> </h3> </div>
														<div class="box-body clearfix <?php if( isset($item['wsp_imported']) && $item['wsp_imported'] == 1 ){?> wsp_template<?php } ?>">
															<a title="Select" href="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'popups', 'workspace', $project_id, $item['id'], 'admin' => FALSE ), TRUE); ?>" class="btn btn-jeera btn-sm select-btn btn_select_workspace tipText" id="btn_select_workspace" data-id="<?php echo $item['id']; ?>" data-toggle="modal" data-target="#modal_manage_templates"> <i class="fa fa-check"></i> Select </a>
																<div class="pull-right"> <?php echo $this->Html->image('layouts/'.$item['layout_preview'], ['class' => 'thumb']); ?></div>
														</div>
													</div>
												</li>
											<?php } ?>
										</ul>

										<div class="ajax-pagination col-sm-<?php echo $ulWidht;?>">
											<?php  echo $this->element('jeera_paging');  ?>
										</div>
									<?php } ?>
									</div>
								</div>
								<div class="tab-pane utemp_cat_tab <?php echo $showActive; ?>" id="template_tab">
									<div class="row">
										<?php if( isset($template_category_id) && !empty($template_category_id)){ ?>
										<div class="col-sm-12 col-md-7 margin-bottom jeera-dashboard-but">
											<?php
 											if( $this->Session->read('Auth.User.role_id') != 1 ){  ?>
												<a id="internal_templates" data-type="1" class="btn btn-success btn-sm template_type" style="display:none" href="">Internal (<span class="cont"><?php echo template_type_count($template_category_id); ?></span><span class="cont_new"><?php echo template_type_count($template_category_id); ?></span>) <i class="fa fa-long-arrow-down"></i></a>
											<?php } /* ?>
												<a id="jeera_templates" data-type="2" class="btn btn-success btn-sm template_type" href="">OpusView (<span class="cont_new"><?php echo template_type_count($template_category_id, 2); ?></span><span class="cont"><?php echo template_type_count($template_category_id, 2); ?></span>) <i class="fa fa-long-arrow-down hide"></i></a>
												<a id="outer_templates" data-type="3" class="btn btn-success btn-sm template_type" href="">Third Party (<span class="cont"><?php echo template_type_count($template_category_id, 3); ?></span><span class="cont_new"><?php echo template_type_count($template_category_id, 3); ?></span>) <i class="fa fa-long-arrow-down hide"></i></a>


										<?php */ if(template_type_count($template_category_id) <= 0 || (isset($project_id) && !empty($project_id)) ){ ?>
										 <?php }else{ ?>
										 	<a id="manage_templates" class="btn bg-purple btn-sm tipText" title="Copy/Move Knowledge Templates" data-toggle="modal" data-remote="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'manage_templates', $template_category_id, 'admin' => FALSE ), TRUE); ?>" data-target="#modal_manage_templates" href=""  >Manage</a>
										 <?php } ?>

										<a id=" " class="btn btn-sm btn-multi-select tipText multi_template " title="" data-toggle="modals" data-remote="<?php //echo Router::Url(array('controller' => 'templates', 'action' => 'multi_templates', $project_id, $template_category_id, 'admin' => FALSE ), TRUE); ?>" data-target="#modal_multi_templatess" href="" <?php if(template_type_count($template_category_id) <= 0 || (!isset($project_id) || empty($project_id)) ){ ?> style="display: none;" <?php } ?> data-type="1">Group Select</a>


										</div>
										<?php if( isset($showFullWidth) && $showFullWidth == true ) {
											if( $this->Session->read('Auth.User.role_id') == 1 ){
										?>
										<div class="col-sm-12 col-md-5 margin-bottom jeera-dashboard-but">
											<div class="pull-right" >
											<a id="jeera_dashboard" class="btn btn-success btn-sm" href="<?php echo SITEURL.'templates/jeera_dashboard';?>">OpusView Dashboard</a>
											<a id="third-party-dashboard" class="btn btn-success btn-sm" href="<?php echo SITEURL.'templates/thirdparty_dashboard';?>">Third Party Dashboard</a>
											</div>
										</div>
											<?php }

										} /* else {
												if( $this->Session->read('Auth.User.role_id') == 1 ){
										?>
												<div class="col-sm-12 col-md-6 margin-bottom jeera-dashboard-but">
													<div class="pull-right" >
													<a id="jeera_dashboard" class="btn btn-success btn-sm" href="javascript:void(0);">Jeera Dashboard</a>
													<a id="third-party-dashboard" class="btn btn-success btn-sm" href="javascript:void(0);">Third Party Dashboard</a>
													</div>
												</div>
										<?php  }
											} */
										} ?>
									</div>
									<?php if( (isset($template_categories) && !empty($template_categories)) && ( !isset($template_category_id) || empty($template_category_id)) ) {

									?>

										<!-- LIST AND GRID VIEW START	-->
										<ul id="new_templates" class=" voikas clearfix templates_list col-sm-12">
											<?php foreach( $template_categories as $key => $val ) {
												// pr($val);
												$item = $val['TemplateCategory'];
												$cat_templates = category_templates($item['id']);
												$icon_name = explode('.', $item['cat_icon']);
												$icon_file = (count($icon_name) > 1) ? 'template-'.$icon_name[0] : 'icon_folder';
												// pr($icon_file);

											?>

												<li class="utemp_cat_list utemp_cat_list_actual" data-remote="<?php echo Router::Url(array('controller' => 'templates', 'action' =>  'create_workspace', $project_id, $item['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $item['id']; ?>">
													<div class="cat-list-inside">
													<div class="icon-wrapper">
														<div class="icon-inner"><span class="cat-icon <?php echo $icon_file; ?>"></span></div>
													</div>
													<div class="cat-title">
														<?php
															echo $item['title'].'<br />(' . $cat_templates . ')';
														?>
													</div>
													</div>
												</li>
											<?php } ?>
										</ul>

										<div class="ajax-paginations col-sm-<?php echo $ulWidht;?>">
											<?php  // echo $this->element('jeera_paging');  ?>
										</div>
									<?php } ?>

									<?php if( (isset($user_templates) && !empty($user_templates)) && (isset($template_category_id) && !empty($template_category_id)) ) { ?>
									<ul id="new_templates" class="  clearfix templates_list col-md-<?php echo $ulWidht;?>">
										<!-- LIST AND GRID VIEW START	-->
										 <?php
											$average = 0;
											foreach( $user_templates as $key => $val ) {
												$item = $val['TemplateRelation'];
												// pr($item);
											?>
												<li class="col-lg-<?php echo $columnWidth; ?> col-md-6 col-sm-12 utemp_list" data-id="<?php echo $item['id']; ?>"  >

													<div class="box box-success">
														<div class="box-body clearfix <?php if( isset($item['wsp_imported']) && $item['wsp_imported'] == 1 ){?> wsp_template<?php } ?>">
                                                        <div class="box-header"> <h3 class="box-title truncate"><?php echo htmlentities($item['title'], ENT_QUOTES) ?> </h3> </div>
															<div class="top-section">
																<div class="tamplate-thumb-left text-center">
																	<?php
																		$userDetail = $this->ViewModel->get_user( $item['user_id'], null, 1 );
																		$profilesPic = SITEURL.'images/placeholders/user/user_1.png';


																		$html = '';

																		$html = CHATHTML($item['user_id']);
																		$style = '';

																		/* if( $owner['UserProject']['user_id'] == $key ) {
																			$style = 'border: 2px solid #333';
																		} */

																		//$userDetail = $this->ViewModel->get_user( $key, null, 1 );
																		$user_image = SITEURL . 'images/placeholders/user/user_1.png';
																		$user_name = 'N/A';
																		$job_title = 'N/A';



																		if(isset($userDetail) && !empty($userDetail)) {
																			$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
																			$profile_pic = $userDetail['UserDetail']['profile_pic'];
																			$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);
																			$job_title = htmlentities($job_title,ENT_QUOTES);

																			if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
																				$user_image = SITEURL . USER_PIC_PATH . $profile_pic;

																		 } ?>
																			<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $item['user_id'])); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>"  >
																				<img src="<?php echo $user_image; ?>" class="user-image" style="<?php echo $style; ?>" >
																			</a>
																	<?php } ?>


																	<?php 	if(isset($userDetail) && !empty($userDetail)) {
																			$profile_pic = $userDetail['UserDetail']['profile_pic'];

																			if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
																				$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
																			}
																		}
																	?>
																	<div class="template_creator-image">
																	<?php
																		//pr($item);
																		if($userDetail['User']['role_id'] == 1){
																			$dataRemoteUrl = Router::Url(array('controller' => 'templates', 'action' => 'show_admin_profile', $item['thirdparty_id'] ), TRUE);
																		} else {
																			$dataRemoteUrl = Router::Url(array('controller' => 'shares', 'action' => 'show_profile', $item['user_id'] ), TRUE);
																		}
																	?>
																	<?php /* <img class="template_creator" alt="Logo Image" style=""  src="<?php echo $profilesPic ?>" alt="Profile Image" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo $dataRemoteUrl; ?>" /> */ ?>
                                                                    </div>
																	<?php if ( !isset($project_id) || empty($project_id)) { ?>

																	<a class="btn btn-jeera btn-sm select-btn disable tipText  " title="Select from Project" id="btn_select_user_templates" > <i class="fa fa-check"></i> Select </a>
																	<?php }else { ?>
																	<a class="btn btn-jeera btn-sm select-btn btn_select_user_template " id="btn_select_user_template" > <i class="fa fa-check"></i> Select </a>
																	<?php }  ?>
																</div>
																<div class="tamplate-thumb-right block-thumb">
																    <?php //pr($item); ?>
																	<div class="sec-bar-tamlate" >
																	    <?php
																		 //pr($item['template_id']);
																		echo $this->element('../Templates/partials/area_template', ['template_id' => $item['template_id'],'allow'=>0,'selection' => null ] ); ?>
																		<?php //echo $this->Html->image('layouts/'.$val['Template']['layout_preview'], ['class' => 'thumb']); ?>
																	</div>
																	<div class="bost-block" >
																		<a id="" class="btn btn-xs template_pophover rv-margin-rd" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?php echo nl2br($item['description']); ?>" href="#"><i class="fa fa-info template_info"></i></a>

																		<?php $review_count = template_reviews($item['id'], 1); ?>
																		<?php $sum_template_reviews = sum_template_reviews($item['id']);
																		$average = 0;
																		if( (isset($sum_template_reviews[0][0]['total']) && !empty($sum_template_reviews[0][0]['total'])) && (isset($review_count) && !empty($review_count)) ) {
																			$average = $sum_template_reviews[0][0]['total'] / $review_count;

																			$whole = floor($average);      // 1
																			$fraction = $average - $whole; // .25

																			if($fraction > 0.5 || $fraction < 0.5){
																			$average = round($average);

																			}else{
																			$average = $average;
																			}

																		}
																	?>

																	<span class="rv-span rv-margin" ><i title="<?php if( isset($review_count) && !empty($review_count) ){ ?> Reviews<?php }else {?>Review<?php } ?>" class="review-icon tipText pull-right <?php if( isset($review_count) && !empty($review_count) ){ ?>review-black<?php }else{ ?>review-gray<?php } ?>" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'add_review', $item['id'] ), TRUE); ?>"></i>
																	</span>


																	<?php /* ?><span class="rv-span rv-margin" ><i title="<?php if( isset($review_count) && !empty($review_count) ){ ?> Annotations<?php }else {?>Annotate<?php } ?>" class="review-icon tipText pull-right <?php   if( isset($review_count) && !empty($review_count) ){ ?>review-black<?php }else{   ?>review-gray<?php  } ?>" data-toggle="modal" data-target="#popup_modal" data-remote="<?php  echo Router::Url(array('controller' => 'templates', 'action' => 'add_review', $item['id'] ), TRUE); ?>"></i>
																	</span>

																	<span class="rv-span rv-margin" ><i title="<?php if( isset($review_count) && !empty($review_count) ){ ?> Annotations<?php }else {?>Annotate<?php } ?>" class="review-icon tipText pull-right <?php if( isset($review_count) && !empty($review_count) ){ ?>review-black<?php }else{ ?>review-gray<?php } ?>" data-toggle="modals" data-target="#popup_modals" data-remotes="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'add_review', $item['id'] ), TRUE); ?>"></i>
																	</span><?php */ ?>


																	<?php if( $item['user_id'] == $this->Session->read('Auth.User.id')) { ?>
																			<a id="" class="btn btn-xs btn-default rv-margin tipText" href="<?php echo Router::Url(array( "controller" => "templates", "action" => "update_template", $item['id'], $item['template_category_id'], $project_id, 'admin' => FALSE ), true); ?>" data-title="Edit Template"><i class="fa fa-pencil"></i></a>

																		<?php } ?>

																	</div>




																<div class=" rv-count">
																<div class="rv-cont-inside"><a data-original-title="Likes" data-remote="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'like_comment', $item['id'] ), TRUE); ?>" class="btn btn-xs btn-default  tipText <?php if( $this->Session->read('Auth.User.id') != $item['user_id'] && !template_commented($item['id'], $this->Session->read('Auth.User.id'))) { ?>like_comment<?php } ?>">
																			<i class="fa fa-thumbs-o-up"></i>
																			<span class="label bg-purple"><?php echo (isset($val['TemplateLike']) && !empty($val['TemplateLike'])) ? count($val['TemplateLike']) : 0; ?> </span>
																		</a>

																<?php /* <a id="" class="btn btn-default rv-like btn-xs tipText" title="Elements in Template"   ><i class="icon_elm"></i> <?php echo template_elements($item['id']); ?></a> */

																$verify = template_elements($item['id']);
																?>



																	<a id="" class="btn btn-default rv-like btn-xs tipText" title="Tasks in Template"  <?php if( !empty($verify) ) { ?> data-toggle="modal" data-target="#popup_modal_element" data-remote="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'view_elements', $item['id'] ), TRUE); ?>" <?php } ?>><i class="icon_elm"></i><?php echo template_elements($item['id']); ?></a>



																</div>
                                                                	<?php if( $item['user_id'] == $this->Session->read('Auth.User.id')) { ?>

																			<a id="" class="btn btn-xs btn-danger trash_template rv-margin tipText" data-title="Delete Template" ><i class="fa fa-trash"></i></a>
																		<?php } ?>

																</div>



																</div>




															</div>
															<div class="middle-section">
																<div class="btn-bar">
																	<div class="pull-left">







																	</div>

																	<div class="pull-right">
																		<?php /*  <a id="" class="btn btn-default btn-xs tipText" title="Elements in Template"  <?php if( !empty(template_elements($item['id'])) ) { ?> data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'view_elements', $item['id'] ), TRUE); ?>" <?php } ?>><i class="icon_elm"></i> <?php echo template_elements($item['id']); ?></a>  */?>




																	</div>

																</div>


																<?php  ?>
																<div class="review-bar">
																	<span class="rev-text">Reviews (<b class="review-count"><?php echo $review_count; ?></b>)</span>


																	<?php $item_id = $item['id']; ?>
																	<span class="star-rating">


																	  <input id="star5" name="rating_<?php echo $item_id ?>" value="5" type="radio" <?php if($average == 5){ ?>checked="checked" <?php } ?>>
																		<label class="full lbl" for="star5" title="Awesome - 5 stars"></label>

																		<input id="star4half" name="rating_<?php echo $item_id ?>" value="4 and a half" type="radio" <?php if($average == 4.5){ ?>checked="checked" <?php } ?>>
																		<label class="half lbl" for="star4half" title="Pretty good - 4.5 stars"></label>

																		<input id="star4" name="rating_<?php echo $item_id ?>" value="4" type="radio" <?php if($average == 4){ ?>checked="checked" <?php } ?>>
																		<label class="full lbl" for="star4" title="Pretty good - 4 stars"></label>

																		<input id="star3half" name="rating_<?php echo $item_id ?>" value="3 and a half" type="radio" <?php if($average == 3.5){ ?>checked="checked" <?php } ?>>
																		<label class="half lbl" for="star3half" title="Meh - 3.5 stars"></label>

																		<input id="star3" name="rating_<?php echo $item_id ?>" value="3" type="radio" <?php if($average == 3){ ?>checked="checked" <?php } ?>>
																		<label class="full lbl" for="star3" title="Meh - 3 stars"></label>

																		<input id="star2half" name="rating_<?php echo $item_id ?>" value="2 and a half" type="radio" <?php if($average == 2.5){ ?>checked="checked" <?php } ?>>
																		<label class="half lbl" for="star2half" title="Kinda bad - 2.5 stars"></label>

																		<input id="star2" name="rating_<?php echo $item_id ?>" value="2" type="radio" <?php if($average == 2){ ?>checked="checked" <?php } ?>>
																		<label class="full lbl" for="star2" title="Kinda bad - 2 stars"></label>

																		<input id="star1half" name="rating_<?php echo $item_id ?>" value="1 and a half" type="radio" <?php if($average == 1.5){ ?>checked="checked" <?php } ?>>
																		<label class="half lbl" for="star1half" title="Meh - 1.5 stars"></label>

																		<input id="star1" name="rating_<?php echo $item_id ?>" value="1" type="radio"  <?php if($average == 1){ ?>checked="checked" <?php } ?> >
																		<label class="full lbl" for="star1" title="Sucks big time - 1 star"></label>

																		<input id="starhalf" name="rating_<?php echo $item_id ?>" value="half"type="radio" <?php if($average == 0.5){ ?>checked="checked" <?php } ?>>
																		<label class="half lbl" for="starhalf" title="Sucks big time - 0.5 stars"></label>
																	</span>
																</div>
																<?php  ?>
                                                                <div class="arrow-down-click"><a href="#" class="closed"><i class="fa " aria-hidden="true"></i></a></div>

                                                                <div class="bottom-section">
																<div class="dates-bar">
																	<span class="col-sm-6">Created:<br />
																	<?php echo $this->Wiki->_displayDate( date('Y-m-d h:i:s', strtotime($item['created'])), 'd M Y'); ?>
																	</span>
																	<span class="col-sm-6">Updated:<br />
																	<?php echo $this->Wiki->_displayDate( date('Y-m-d h:i:s', strtotime($item['modified'])), 'd M Y'); ?>
																	</span>
																</div>
																	<?php
																	if( isset($val['AreaRelation']) && !empty($val['AreaRelation'])) {
																		foreach( $val['AreaRelation'] as $arkey => $arval ) {
																	?>
																		<span class="area_wrap">
																			<h5 class="area_title"><?php echo htmlentities($arval['title'],ENT_QUOTES, "UTF-8"); ?></h5>
																			<span class="area_desc">
																				<?php echo nl2br(htmlentities($arval['description'],ENT_QUOTES, "UTF-8")); ?>
																			</span>
																		</span>
																	<?php }
																	} ?>
																</div>


															</div>

														</div>

													</div>

												</li>
											<?php } ?>
										</ul>
										<div class="paginate_links"></div>
									<?php } /* else if( (isset($template_category_id) && !empty($template_category_id)) ) { ?>
										<div class="select_msg col-sm-<?php echo $ulWidht;?>" > NO KNOWLEDGE TEMPLATES AVAILABLE </div>
									<?php } */ ?>





								</div>

							</div>

						</div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>

<script type="text/javascript">
$(function(){

	$('#temp_search').keyup(function(event){

		var keycode =  event.which ;
		if(keycode == 13){
			if( $(this).val() ){
				$('.search_submit').trigger('click');
			} else {
				$(".search_clear").trigger('click');
			}
		}

		if($(this).val() == ''){
			$(".search_clear").trigger('click');
		}

	})


/* 	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    }); */

	$('.template_pophover').popover({
        placement : 'top',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });





        function toggleIcon(e) {
            $(e.target)
                .prev('.panel-heading')
                .find(".more-less")
                .toggleClass('glyphicon-plus glyphicon-minus');
        }




	$('body').delegate('.third-party-user', 'click', function(e) {
        e.preventDefault();

		var $this = $(this);
		collapseid = "#collapseOne-"+$this.data('partyuserid');
		collapseidString = "collapseOne-"+$this.data('partyuserid');

		$('.search_rating').attr({'data-serachtype':3,'data-active':0});
		$('.search_rating i').addClass('text-gray');

		$(".template_type[data-type=3]").addClass('active');
		$(".template_type[data-type=2]").removeClass('active');
		$(".template_type[data-type=2] .fa-long-arrow-down").addClass('hide');
		$(".template_type[data-type=1]").removeClass('active');
		$(".template_type[data-type=1] .fa-long-arrow-down").addClass('hide');


		var collapsArray=[];
		$('div[id^="collapseOne-"]').each(function(){
			collapsArray.push(this.id);
		});

		$(this).toggleClass('opened closed');
		$('.third-party-user').not(this).removeClass('opened').addClass('closed');

		//console.log( 'pawan' );
		//console.log( $(this).hasClass('closed'));
		//console.log( 'kkkkkkkk' );


		var template_category_id = '';
		<?php if( isset($this->request->params['pass'][1]) && $this->request->params['pass'][1] > 0){ ?>
			template_category_id = '<?php echo $this->request->params['pass'][1];?>';
		<?php } ?>

		$('.paginate_links').remove();
		if( $(this).hasClass('closed') == true ){

			post = { 'thirdparty_id': '','project_id':'<?php echo $project_id; ?>','columnwidth':'<?php echo $columnWidth;?>','template_category_id':template_category_id },
			pageUrl = $js_config.base_url + 'templates/filter_user_templates';

		} else {
			post = { 'thirdparty_id': $this.data('partyuserid'),'project_id':'<?php echo $project_id; ?>','columnwidth':'<?php echo $columnWidth;?>','template_category_id':template_category_id },
			pageUrl = $js_config.base_url + 'templates/filter_user_templates';
		}

		$('.select_msg').remove();
		$('.select_msg_main').remove();

		$( '<div class="paginate_links"></div>' ).insertAfter( ".tparty" );

		$.ajax({
			type:'POST',
			data: $.param(post),
			url: pageUrl,
			async:false,
			global: false,
			success: function( response, status, jxhr ) {
				setTimeout(function(){
					if($("#new_templates").length > 0){
						$("#new_templates").remove();
					}

					if($("#new_templates").find('.tparty')){
					$(response).insertAfter("#template_tab .tparty");
					}else{
					$(response).insertAfter("#template_tab .row");
					}

					$('.col-md-3.pull-right').show();
					//$('#new_templates').removeClass('col-sm-12');
					$('#new_templates').removeClass('col-md-9');
					$('#new_templates').addClass('col-md-9');

					$('#new_templates li').removeClass('col-lg-3');
					$('#new_templates li').removeClass('col-lg-4');
					$('#new_templates li').addClass('col-lg-4');

					var cc = $('#new_templates li').length;
					$('.template_type.active .cont').hide();
					$('.template_type.active .cont_new').html(cc).show();

				}, 500);

				$.each( collapsArray, function( i, val ) {
					if( val == collapseidString ){
						$('#'+val).toggle("slow").removeClass('collapse');

					} else {
						$('#'+val).toggle("slow").addClass('collapse');
						$('#'+val).css('display','none');

					}
				});

				$('.template_type').removeAttr('style');
				$("#outer_templates i").removeClass('hide');
				$("#outer_templates").css('box-shadow', '3px 2px 4px 0px #333');

				setTimeout(function(){
					$.jsPagination({cur_page: 1, parent: '#new_templates' });
				},1000);

			}
		})

        return false;
    });


	$('body').delegate('.ajax-page a', 'click', function(e) {
        e.preventDefault();

		var $this = $(this),
			$parent = $this.parents('#template_tab').filter(':first'),
			post = { 'project_id': '<?php echo $project_id; ?>' },
			pageUrl = $this.attr('href');

		$.ajax({
			type:'POST',
			data: $.param(post),
			url: pageUrl,
			global: false,
			success: function( response, status, jxhr ) {
				setTimeout(function(){
					$parent.html(response);
				}, 800)
			}
		})

        return false;
    });


	$.opened_model = $();
	$('#popup_modal').on('show.bs.modal', function(event) {
		$.opened_model = $(event.relatedTarget);
	});



	$('#popup_modal_element').on('show.bs.modal', function(event) {
		$.opened_model = $(event.relatedTarget);
	});

	$('#popup_modal_element').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
		$(this).find('.modal-content').html("");
	})



    $('#popup_modal').on('hidden.bs.modal', function () {
//console.log('hide');
		$(this).removeData('bs.modal');
		$(this).find('.modal-content').html("");


		if( $.opened_model.is('i.review-icon') ) {

			var id = $.opened_model.parents('.utemp_list:first').data('id');
			$.ajax({
				url: $js_config.base_url + 'templates/get_reviews_count/' + id,
				type: "POST",
				data: $.param({}),
				dataType: "JSON",
				global: false,
				success: function (response) {
					$.opened_model.parents('.box-body:first').find('.review-count').text(response);
					if( $.parseJSON(response) > 0 ) {
						$.opened_model.addClass('review-black').removeClass('review-gray');
					}
					else {
						$.opened_model.addClass('review-gray').removeClass('review-black');
					}

				}
			})

			$.ajax({
				url: $js_config.base_url + 'templates/review_stars/' + id,
				type: "POST",
				data: $.param({}),
				dataType: "JSON",
				global: false,
				success: function (response) {
					$.opened_model.parents('.box-body:first').find('.star-rating').html(response);
				}
			})
		}

	});

/* ******** SET WORKSPACE TO SELECTED PROJECT ON CLICK EVENT OF TEMPLATE SELECT BUTTON *************** */
	$('body').delegate('.btn_select_user_template', 'click', function(e) {
        e.preventDefault()

		var $this = $(this),
			$parent = $this.parents('.utemp_list:first'),
			data = $parent.data(),
			template_id = data.id;

		if( template_id > 0 && template_id != "" && template_id !== undefined ) {

			BootstrapDialog.show({
                title: 'Confirmation',
                message: 'Are you sure you want to select this Knowledge Template for your Project?',
                type: BootstrapDialog.TYPE_SUCCESS,
                draggable: true,
                buttons: [{
                        label: ' Publish to Project',
                        cssClass: 'btn-success',
                        autospin: false,
                        action: function(dialogRef) {
                            var $button = this;
                            $button.disable();
                            $button.spin();
                            dialogRef.setClosable(false);
                            $.when(
                                $.ajax({
									type:'POST',
									dataType:'JSON',
									data: { template_id: template_id, project_id: $js_config.project_id },
									url: $js_config.base_url + 'templates/select_user_template/' + template_id,
									global: false,
									success: function( response ) {
										if( response.success ) {
											if (response.content) {
                                                console.log(response.content.socket)
                                                // send web notification
                                                $.socket.emit('socket:notification', response.content.socket, function(userdata) {});
                                            }
											window.location = $js_config.base_url + 'projects/manage_elements/' + $js_config.project_id + '/' +  response.content;
										}
									}
								})
                            ).then(function(data, textStatus, jqXHR) {

                            })
                        }
                    },
                    {
                        label: ' Cancel',
                        cssClass: 'btn-danger',
                        action: function(dialogRef) {
                            dialogRef.close();
                        }
                    }
                ]
            });
		}

    });


	$('body').delegate('#submit_review', 'click', function(event) {
		event.preventDefault();

		var $form = $('#modelFormTemplateReview'),
			data = $form.serialize(),
			data_array = $form.serializeArray();

		$.when(
			$.ajax({
				url: $js_config.base_url + 'templates/save_review',
				type: "POST",
				data: data,
				dataType: "JSON",
				global: false,
				success: function (response) {
					if(response.success) {
						$form.find('#TemplateReviewId').val('');
						$form.find('#TemplateReviewComments').val('');
						$form.find('#TemplateReviewComments').next().html('');
						$form.find('#clear_annotate').hide()


						$form.find('#input-rating').rating('reset');
						$form.find('#used_unused').prop('checked', false);
					}
					else {
						if( ! $.isEmptyObject( response.content ) ) {

							$.each( response.content, function( ele, msg) {

								var $element = $form.find('[name="data[TemplateReview]['+ele+']"]')
								var $parent = $element.parent();

								if( $parent.find('span.error-message.text-danger').length  ) {
									$parent.find('span.error-message.text-danger').text(msg)
								}
							})

						}
					}

				}
			})
		).then(function( data, textStatus, jqXHR ) {
			if(data.success) {
			setTimeout(function(){
				$.ajax({
					url: $js_config.base_url + 'templates/get_reviews/' + data.content,
					type: "POST",
					data: $.param({}),
					dataType: "JSON",
					global: false,
					success: function (responses) {
						$('#annotate-list', $('body')).html(responses)
					}
				})
			},700)

			}
		})
	})



/* $(function(){
	var lastX = 0;
	var currentX = 0;
	var page = 1;
	$(window).scroll(function () {
		currentX = $(window).scrollTop();
		console.log(currentX)
		if (currentX - lastX > 90 * page) {
			lastX = currentX;
			page++;
			$.post( $js_config.base_url+'templates/get_more/4/page:' + page, {project_id: 4}, function(data) {
				$('#template_list').append(data);
			});
		}
	});
}) */


$('#temp_search').val('');

$('.sbox-rating').hide();

$('body').delegate('li #sel_new_tab', 'click', function (event) {

	event.preventDefault();
	event.stopPropagation();

	$('.sbox-rating').hide();

	window.location.href= $js_config.base_url+'templates/create_workspace/'+$js_config.project_id;
})

$('body').delegate('#sel_template_tab', 'click', function (event) {

	$('.sbox-rating').show();
})


if($('#sel_template_tab').parent('li').hasClass('active')){
	$('.sbox-rating').show();
}else{
	$('.sbox-rating').hide();
}




<?php  if( isset($this->request->params['pass'][2]) && $this->request->params['pass'][2] > 0  ) {  ?>
$('body').delegate('.trails', 'click', function (event) {
        event.preventDefault();


		 $('#sel_template_tab').html('<span id="cat_trail" data-project="0">Library</span>');
		 window.location.href= $js_config.base_url + 'templates/create_workspace/0/0';
	 return
	})




<?php } ?>


<?php if( isset($this->request->params['pass'][1]) && $this->request->params['pass'][1] > 0 && !isset($this->request->params['pass'][2]) ){

if( $this->Session->read('Auth.User.role_id') == 1 ){  ?>
	$(".template_type[data-type='2']").trigger( "click" );
<?php  } else { ?>
	$(".template_type[data-type='1']").trigger( "click" );
<?php } ?>

$('.sbox-rating').show();




	$('body').delegate('.search_submit', 'click', function (event) {
		event.preventDefault();

		var keyword = $("#temp_search").val();

		$('.sbox-rating').css('pointer-events','none');
		$('.sbox-rating').addClass('disable');

  		keyword = keyword.trim();
		 if(keyword.length > 0){
			$("#temp_search").css('border','solid 1px #d2d6de');
		}else{
		 $("#temp_search").css('border','solid 1px #f00');
		 setTimeout(function(){
			$("#temp_search").css('border','solid 1px #d2d6de');

			$('.sbox-rating').css('pointer-events','unset');
			$('.sbox-rating').removeClass('disable');
		 },2000)
		//$('<span class="error text-red">Please enter value.</span>').insertAfter("#temp_search");
		$allListElements = $('#tab-content #new_templates li');

		var $matchingListElements = $allListElements.filter(function (i, li) {

			var listItemText = $(li).find('.box-header .box-title').text().toUpperCase(), searchText = keyword.toUpperCase();
			return ~listItemText.indexOf(searchText);
		});

		$matchingListElements.show();
		var searchText = keyword.toUpperCase();
			$('#tab-content #new_templates li').unmark();
			$('#tab-content #new_templates li .box-header .box-title').mark(searchText);
			return;
		}

		$('.search_clear').show();
		$('.search_submit').hide();

		$allListElements = $('#tab-content #new_templates li');

		var $matchingListElements = $allListElements.filter(function (i, li) {

			var listItemText = $(li).find('.box-header .box-title').text().toUpperCase(), searchText = keyword.toUpperCase();


			return ~listItemText.indexOf(searchText);


		});




		 $matchingListElements.show();
		var searchText = keyword.toUpperCase();
		$('#tab-content #new_templates li').unmark();
		$('#tab-content #new_templates li .box-header .box-title').mark(searchText);


		//var cc = $('#tab-content #new_templates li:visible').length;
		var cc =  $('#tab-content #new_templates li').find('h3 mark:first').length;

		$('.template_type.active .cont').hide();

		   $('#tab-content #new_templates li').hide();
		   $('#tab-content #new_templates li').find('h3 mark:first').parents('li').show();

		//$('#tab-content #new_templates li').hide();

		$('.template_type.active .cont_new').html(cc).show();
		var seachdata_type = $('.template_type.active').data('type');

		if( cc < 1 && seachdata_type == 3 ){
			$('.select_msg').remove()
			var html = '<div class="col-sm-9 select_msg_main"> <div class="select_msg col-sm-9"  > NO KNOWLEDGE TEMPLATES AVAILABLE </div></div>';
			$(html).insertAfter("#template_tab .row");
		} else if( cc < 1 && seachdata_type != 3 ){
			$('.select_msg').remove()
			var html = '<div class="col-sm-12 select_msg_main"> <div class="select_msg col-sm-12"  > NO KNOWLEDGE TEMPLATES AVAILABLE </div></div>';
			$(html).insertAfter("#template_tab .row");
		} else {
			// $('.select_msg').remove();
			// $('.select_msg_main').remove();
		}

				//$('#new_templates li').hide();

		 if(cc > 12){
			//	$allListElements.hide();
			  $('.paginate_links').show();
			  setTimeout(function(){
				$.jsPaginationSearch({cur_page: 1, parent: '#new_templates' });
			},1000);
		 }else{
			$('.paginate_links').hide();
		}



	});

	$('body').delegate('.search_clear', 'click', function (event) {

		$('#temp_search').val('');
		$('.search_submit').trigger('click');
		$('.search_clear').hide();
		$('.search_submit').show();
		$("#temp_search").css('border','solid 1px #d2d6de');

		$('.template_type.active .cont').show();
		$('.template_type.active .cont_new').hide();

		$('.sbox-rating').css('pointer-events','unset');
		$('.sbox-rating').removeClass('disable');

			if($('.utemp_list').length > 0 && $('.utemp_list:visible').length > 0){
				$('.select_msg').remove();
				$('.select_msg_main').remove();
			}
			//$('.paginate_links').remove();

					var seachdata_type = $('.template_type.active').data('type');
					if(seachdata_type == 3){
						$('.col-md-3.pull-right').show();
					//	$('#new_templates').removeClass('col-sm-12');
						$('#new_templates').removeClass('col-md-9');
						$('#new_templates').addClass('col-md-9');

						$('#new_templates li').removeClass('col-lg-3');
						$('#new_templates li').removeClass('col-lg-4');
						$('#new_templates li').addClass('col-lg-4');

						$('.select_msg_main').removeClass('col-sm-12');
						$('.select_msg_main').removeClass('col-sm-9');
						$('.select_msg_main').addClass('col-sm-9');
					} else {
						$('.col-md-3.pull-right').hide();
						$('#new_templates').removeClass('col-md-9');
						//$('#new_templates').removeClass('col-sm-12');
						//$('#new_templates').addClass('col-sm-12');

						$('#new_templates li').removeClass('col-lg-3');
						$('#new_templates li').removeClass('col-lg-4');
						$('#new_templates li').addClass('col-lg-3');

						$('.select_msg_main').removeClass('col-sm-12');
						$('.select_msg_main').removeClass('col-sm-9');
						$('.select_msg_main').addClass('col-sm-12');
					}

					var cc = $('#tab-content #new_templates li:visible').length;

					if(cc > 12){
				//	$allListElements.hide();
						  $('.paginate_links').show();
						  setTimeout(function(){
							$.jsPagination({cur_page: 1, parent: '#new_templates' });
						},1000);
					}else{
						$('.paginate_links').hide();
					}

			/* setTimeout(function(){ //alert(0);
				$.jsPagination({cur_page: 1, parent: '#new_templates' });
			},1000); */

			//console.log("paging not showing");


	});

	$('body').delegate('.search_rating', 'click', function (event) {
        event.preventDefault();
		//var seachdata_type = 0;

		$('.sbox').css('pointer-events','none');
		$('.sbox').addClass('disable');
		$('.paginate_links').remove();


		//$(this).find('i').removeClass('text-gray');
		var template_category_id ='<?php echo (isset($this->params['pass']['1'])) ? $this->params['pass']['1'] : 0; ?>';
		var project_id ='<?php echo (isset($this->params['pass']['0'])) ? $this->params['pass']['0'] : 0; ?>';

		$('.pagination.pagination-large.pull-right').remove();
		$('.pagination_temp_filter_template').remove();

		/* if( $(this).data('serachtype') != 0 ){
			var seachdata_type = $(this).data('serachtype');
		}  */

		var seachdata_type = $('.template_type.active').data('type');

		$(this).find('i').toggleClass('text-gray','');
		$(this).attr('data-active', $(this).attr('data-active') == '0' ? '1' : '0');
		pageUrl = $js_config.base_url + 'templates/search_template';

			var starRating = $( $(".search_rating[data-active=1]") ).map(function() {
			return $(this).data('rating');
		  }).get().join();


		$('.select_msg').remove();
		$('.select_msg_main').remove();

		//console.log("222222222222222");
		//return false;
		if( seachdata_type == 3  ){

			$('.third-party-user').removeClass('opened').addClass('closed');

			$('.saveasdiv_ajax .panel-collapse').addClass('collapse').removeAttr('style');
		}


		if( starRating == ""  ){
			$('.sbox').css('pointer-events','unset');
			$('.sbox').removeClass('disable')
		}


		if( starRating == "" && seachdata_type == 1  ){

			$(".template_type[data-type="+seachdata_type+"]").trigger( "click" );
			$(this).find('i').toggleClass('text-gray','');
			//return false;


		} else {

			$.ajax({
				type:'POST',
				data: $.param({'starRating':starRating,'template_category_id':template_category_id,'columnWidth':'<?php echo $columnWidth; ?>','project_id':project_id,'searchdatatype':seachdata_type}),
				url: pageUrl,
				global: false,
				success: function( response, status, jxhr ) {
						setTimeout(function(){
							if($("#new_templates").length > 0){
								$("#new_templates").remove();
							}

							if($("#template_tab").find('.tparty').length > 0){
							$(response).insertAfter("#template_tab .tparty");
							}else{
							$(response).insertAfter("#template_tab .row");
							}

						var seachdata_type = $('.template_type.active').data('type');
						if(seachdata_type == 3){
						$('.col-md-3.pull-right').show();
					//	$('#new_templates').removeClass('col-sm-12');
						$('#new_templates').removeClass('col-md-9');
						$('#new_templates').addClass('col-md-9');

						var pageingDiv = $('.paginate_links').html();

						//$('.paginate_links').remove();

						$('.paginate_links').remove()

						$('#template_tab').append('<div class="paginate_links"></div>');

						$('#new_templates li').removeClass('col-lg-3');
						$('#new_templates li').removeClass('col-lg-4');
						$('#new_templates li').addClass('col-lg-4');

						$('.select_msg_main').removeClass('col-sm-12');
						$('.select_msg_main').removeClass('col-sm-9');
						$('.select_msg_main').addClass('col-sm-9');
					}else{
						$('.col-md-3.pull-right').hide();
						$('#new_templates').removeClass('col-md-9');
						//$('#new_templates').removeClass('col-sm-12');
						//$('#new_templates').addClass('col-sm-12');

						$('#new_templates li').removeClass('col-lg-3');
						$('#new_templates li').removeClass('col-lg-4');
						$('#new_templates li').addClass('col-lg-3');

						$('.select_msg_main').removeClass('col-sm-12');
						$('.select_msg_main').removeClass('col-sm-9');
						$('.select_msg_main').addClass('col-sm-12');
					}
							$('#new_templates li').hide()
							var cc = $('#new_templates li').length;
							$('.template_type.active .cont').hide();
							$('.template_type.active .cont_new').html(cc).show();
							//$(".template_type[data-type="+seachdata_type+"]").trigger( "click" );
					}, 300);
					setTimeout(function(){
						$.jsPagination({cur_page: 1, parent: '#new_templates' });
					},400);
				}

			})
		}
    });

	$('body').delegate('.search_rating_close', 'click', function (event) {
	 event.preventDefault();

	 	$('.sbox').css('pointer-events','unset');
		$('.sbox').removeClass('disable')

		var template_category_id ='<?php echo (isset($this->params['pass']['1'])) ? $this->params['pass']['1'] : 0; ?>';
		var project_id ='<?php echo (isset($this->params['pass']['0'])) ? $this->params['pass']['0'] : 0; ?>';

		var searchdatatype = $('.template_type.active').data('type');

		if( searchdatatype == 3  ){

			$('.third-party-user').removeClass('opened').addClass('closed');
			$('.saveasdiv_ajax .panel-collapse').addClass('collapse').removeAttr('style');
		}


		$('.search_rating').find('i').addClass('text-gray');


		$('.search_rating').attr('data-active', 0);

				pageUrl = $js_config.base_url + 'templates/search_template';
		$('.select_msg').remove();
		$('.select_msg_main').remove();

		$('.pagination.pagination-large.pull-right').remove();
		$('.pagination_temp_filter_template').remove();
		 $('.paginate_links').remove();


		$.ajax({
			type:'POST',
			data: $.param({'starRating':0,'template_category_id':template_category_id,'searchdatatype':searchdatatype,'columnWidth':'<?php echo $columnWidth; ?>','project_id':project_id}),
			url: pageUrl,
			global: false,
			success: function( response, status, jxhr ) {
					setTimeout(function(){
						if($("#new_templates").length > 0){
							$("#new_templates").remove();

						}
						if($("#template_tab").find('.tparty').length > 0){
						$(response).insertAfter("#template_tab .tparty");
						}else{
						$(response).insertAfter("#template_tab .row");
						}


						var seachdata_type = $('.template_type.active').data('type');
						if(seachdata_type == 3){
						$('.col-md-3.pull-right').show();
						//$('#new_templates').removeClass('col-sm-12');
						$('#new_templates').removeClass('col-md-9');
						$('#new_templates').addClass('col-md-9');

						$('#new_templates li').removeClass('col-lg-3');
						$('#new_templates li').removeClass('col-lg-4');
						$('#new_templates li').addClass('col-lg-4');

						$('.select_msg_main').removeClass('col-sm-12');
						$('.select_msg_main').removeClass('col-sm-9');
						$('.select_msg_main').addClass('col-sm-9');
					}else{
						$('.col-md-3.pull-right').hide();
						$('#new_templates').removeClass('col-md-9');
						//$('#new_templates').removeClass('col-sm-12');
					//	$('#new_templates').addClass('col-sm-12');

						$('#new_templates li').removeClass('col-lg-3');
						$('#new_templates li').removeClass('col-lg-4');
						$('#new_templates li').addClass('col-lg-3');

						$('.select_msg_main').removeClass('col-sm-12');
						$('.select_msg_main').removeClass('col-sm-9');
						$('.select_msg_main').addClass('col-sm-12');
					}
						var cc = $('#new_templates li').length;
						$('.template_type.active .cont').hide();
						$('.template_type.active .cont_new').html(cc).show();

					}, 400);

					setTimeout(function(){
						$.jsPagination({cur_page: 1, parent: '#new_templates' });
					},1000);


			}
		})

	})

<?php } else { ?>



<?php  if( isset($this->request->params['pass'][2]) && $this->request->params['pass'][2] > 0  ) {  ?>
$('body').delegate('.search_submit', 'click', function (event) {

		event.preventDefault();
		var keyword = $("#temp_search").val();

		$('.sbox-rating').css('pointer-events','none');
		$('.sbox-rating').addClass('disable');

  		keyword = keyword.trim();
		 if(keyword.length > 0){
			$("#temp_search").css('border','solid 1px #d2d6de');
		}else{
		 $("#temp_search").css('border','solid 1px #f00');
		 setTimeout(function(){
			$("#temp_search").css('border','solid 1px #d2d6de');

			$('.sbox-rating').css('pointer-events','unset');
			$('.sbox-rating').removeClass('disable');
		 },2000)
		//$('<span class="error text-red">Please enter value.</span>').insertAfter("#temp_search");
		$allListElements = $('#tab-content #new_templates li');

		var $matchingListElements = $allListElements.filter(function (i, li) {

			var listItemText = $(li).find('.box-header .box-title').text().toUpperCase(), searchText = keyword.toUpperCase();
			return ~listItemText.indexOf(searchText);
		});

		$allListElements.hide();


		 $matchingListElements.show();
		var searchText = keyword.toUpperCase();
			$('#tab-content #new_templates li').unmark();
			$('#tab-content #new_templates li .box-header .box-title').mark(searchText);
			return;
		}

		$('.search_clear').show();
		$('.search_submit').hide();

		$allListElements = $('#tab-content #new_templates li');

		var $matchingListElements = $allListElements.filter(function (i, li) {

			var listItemText = $(li).find('.box-header .box-title').text().toUpperCase(), searchText = keyword.toUpperCase();


			return ~listItemText.indexOf(searchText);


		});

		$allListElements.hide();


		 $matchingListElements.show();
		var searchText = keyword.toUpperCase();
		$('#tab-content #new_templates li').unmark();
		$('#tab-content #new_templates li .box-header .box-title').mark(searchText);

		var cc = $('#tab-content #new_templates li:visible').length;
		$('.template_type.active .cont').hide();
		$('.template_type.active .cont_new').html(cc).show();
		var seachdata_type = $('.template_type.active').data('type');
		if( cc < 1 && seachdata_type == 3 ){

			var html = '<div class="col-sm-9 select_msg_main"> <div class="select_msg col-sm-9"  > NO KNOWLEDGE TEMPLATES AVAILABLE </div></div>';
			$(html).insertAfter("#template_tab .row");
		} else if( cc < 1 && seachdata_type != 3 ){

			var html = '<div class="col-sm-12 select_msg_main"> <div class="select_msg col-sm-12"  > NO KNOWLEDGE TEMPLATES AVAILABLE </div></div>';
			$(html).insertAfter("#template_tab .row");
		} else {
			$('.select_msg').remove();
			$('.select_msg_main').remove();
		}

		  if(cc > 12){
			  $('.paginate_links').show();
			  setTimeout(function(){
				$.jsPaginationSearch({cur_page: 1, parent: '#new_templates' });
			},1000);
		 }else{
			$('.paginate_links').hide();
		}

return;

	});

	<?php  }else {  ?>

	$('body').delegate('.search_submit', 'click', function (event) {
        event.preventDefault();

		$('.sbox-rating').css('pointer-events','none');
		$('.sbox-rating').addClass('disable');


        var keyword = $("#temp_search").val();
		keyword = keyword.trim();
		 if(keyword.length > 0){
			$("#temp_search").css('border','solid 1px #d2d6de');
		}else{
		 $("#temp_search").css('border','solid 1px #f00');
		  setTimeout(function(){
			$("#temp_search").css('border','solid 1px #d2d6de');
		 },2000)
		//$('<span class="error text-red">Please enter value.</span>').insertAfter("#temp_search");
		return;
		}

		pageUrl = $js_config.base_url + 'templates/search_category';
		$('.search_clear').show();
		$('.search_submit').hide();
		$('.select_msg').remove();
		$('.select_msg_main').remove();

		<?php  if( isset($this->request->params['pass'][2]) && $this->request->params['pass'][2] > 0  ) {  ?>
							$('.jeera-dashboard-but').remove();
							$('#sel_template_tab').html('<span id="cat_trail" data-project="0" data-remote="'+$js_config.base_url + 'templates/create_workspace/0/0">Library</span>');

							<?php } else{ ?>
							//$("#sel_template_tab").find('.arrowcat').remove();
							$('.arrowcat').remove();

							$('.trails').remove();
						<?php }   ?>

		$.ajax({
			type:'POST',
			data: $.param({'keyword':keyword,'project_id':'<?php echo $project_id;?>'}),
			url: pageUrl,
			global: false,
			success: function( response, status, jxhr ) {
					setTimeout(function(){
						//templates_list
						//$("#new_templates").html(response);
						if($("#new_templates").length > 0){
							$("#new_templates").remove();

						}
						$(response).insertAfter("#template_tab .row");
					}, 700);

			}
		})
    });

	<?php } ?>


<?php  if( isset($this->request->params['pass'][2]) && $this->request->params['pass'][2] > 0  ) {  ?>

$('body').delegate('.search_clear', 'click', function (event) {

		$('#temp_search').val('');
		$('.search_submit').trigger('click');
		$('.search_clear').hide();
		$('.search_submit').show();
		$("#temp_search").css('border','solid 1px #d2d6de');

		$('.template_type.active .cont').show();
		$('.template_type.active .cont_new').hide();

		$('.sbox-rating').css('pointer-events','unset');
		$('.sbox-rating').removeClass('disable');

			$('.select_msg').remove();
			$('.select_msg_main').remove();
			//$('.paginate_links').remove();

					var seachdata_type = $('.template_type.active').data('type');
					if(seachdata_type == 3){
						$('.col-md-3.pull-right').show();
						//$('#new_templates').removeClass('col-sm-12');
						$('#new_templates').removeClass('col-md-9');
						$('#new_templates').addClass('col-md-9');

						$('#new_templates li').removeClass('col-lg-3');
						$('#new_templates li').removeClass('col-lg-4');
						$('#new_templates li').addClass('col-lg-4');

						$('.select_msg_main').removeClass('col-sm-12');
						$('.select_msg_main').removeClass('col-sm-9');
						$('.select_msg_main').addClass('col-sm-9');
					} else {
						$('.col-md-3.pull-right').hide();
						$('#new_templates').removeClass('col-md-9');
						//$('#new_templates').removeClass('col-sm-12');
					//	$('#new_templates').addClass('col-sm-12');

						$('#new_templates li').removeClass('col-lg-3');
						$('#new_templates li').removeClass('col-lg-4');
						$('#new_templates li').addClass('col-lg-3');

						$('.select_msg_main').removeClass('col-sm-12');
						$('.select_msg_main').removeClass('col-sm-9');
						$('.select_msg_main').addClass('col-sm-12');
					}

					var cc = $('#tab-content #new_templates li:visible').length;
					 if(cc > 12){
						  $('.paginate_links').show();
						  setTimeout(function(){
							$.jsPagination({cur_page: 1, parent: '#new_templates' });
						},1000);
					 }else{
						$('.paginate_links').hide();
					}

			/* setTimeout(function(){ //alert(0);
				$.jsPagination({cur_page: 1, parent: '#new_templates' });
			},1000); */

			//console.log("paging not showing");


	});
<?php }else { ?>

	$('body').delegate('.search_clear', 'click', function (event) {
		$('#temp_search').val('');
		pageUrl = $js_config.base_url + 'templates/search_category';



		$('.select_msg').remove();
		$('.select_msg_main').remove();
		$('.paginate_links').remove();

		var keyword = '';

		$.ajax({
			type:'POST',
			//data: $.param( ),
			url: pageUrl,
			global: false,
			success: function( response, status, jxhr ) {
					setTimeout(function(){
						//templates_list
						//$("#new_templates").html(response);
						if($("#new_templates").length > 0){
							$("#new_templates").remove();

						}
						var seachdata_type = $('.template_type.active').data('type');
						if(seachdata_type == 3){
						$('.col-md-3.pull-right').show();
						//$('#new_templates').removeClass('col-sm-12');
						$('#new_templates').removeClass('col-md-9');
						$('#new_templates').addClass('col-md-9');

						$('#new_templates li').removeClass('col-lg-3');
						$('#new_templates li').removeClass('col-lg-4');
						$('#new_templates li').addClass('col-lg-4');

						$('.select_msg_main').removeClass('col-sm-12');
						$('.select_msg_main').removeClass('col-sm-9');
						$('.select_msg_main').addClass('col-sm-9');
					}else{
						$('.col-md-3.pull-right').hide();
						$('#new_templates').removeClass('col-md-9');
						//$('#new_templates').removeClass('col-sm-12');
						//$('#new_templates').addClass('col-sm-12');

						$('#new_templates li').removeClass('col-lg-3');
						$('#new_templates li').removeClass('col-lg-4');
						$('#new_templates li').addClass('col-lg-3');

						$('.select_msg_main').removeClass('col-sm-12');
						$('.select_msg_main').removeClass('col-sm-9');
						$('.select_msg_main').addClass('col-sm-12');
					}

						$(response).insertAfter("#template_tab .row");
						$("#temp_search").css('border','solid 1px #d2d6de');
						<?php  if( isset($this->request->params['pass'][2]) && $this->request->params['pass'][2] > 0  ) {  ?>
							$('.jeera-dashboard-but').remove();
							$('#sel_template_tab').html('<span id="cat_trail" data-project="0" data-remote="'+$js_config.base_url + 'templates/create_workspace/0/0">Library</span>');

							<?php } else{ ?>
							//$("#sel_template_tab").find('.arrowcat').remove();
							$('.arrowcat').remove();

							$('.trails').remove();
						<?php }   ?>
					$('.sbox-rating').css('pointer-events','unset');
					$('.sbox-rating').removeClass('disable');

					}, 400);

			}
		})
		$('.search_clear').hide();
		$('.search_submit').show()

	});

	<?php } ?>



<?php  if( isset($this->request->params['pass'][2]) && $this->request->params['pass'][2] > 0  ) {  ?>
	$('body').delegate('.search_rating_close', 'click', function (event) {
	 event.preventDefault();

	 	$('.sbox').css('pointer-events','unset');
		$('.sbox').removeClass('disable')

		var template_category_id ='<?php echo (isset($this->params['pass']['1'])) ? $this->params['pass']['1'] : 0; ?>';
		var project_id ='<?php echo (isset($this->params['pass']['0'])) ? $this->params['pass']['0'] : 0; ?>';

		var searchdatatype = $('.template_type.active').data('type');

		if( searchdatatype == 3  ){

			$('.third-party-user').removeClass('opened').addClass('closed');
			$('.saveasdiv_ajax .panel-collapse').addClass('collapse').removeAttr('style');
		}


		$('.search_rating').find('i').addClass('text-gray');


		$('.search_rating').attr('data-active', 0);

				pageUrl = $js_config.base_url + 'templates/search_template';
		$('.select_msg').remove();
		$('.select_msg_main').remove();

		$('.pagination.pagination-large.pull-right').remove();
		$('.pagination_temp_filter_template').remove();
		 $('.paginate_links').remove();


		$.ajax({
			type:'POST',
			data: $.param({'starRating':0,'template_category_id':template_category_id,'searchdatatype':searchdatatype,'columnWidth':'<?php echo $columnWidth; ?>','project_id':project_id}),
			url: pageUrl,
			global: false,
			success: function( response, status, jxhr ) {
					setTimeout(function(){
						if($("#new_templates").length > 0){
							$("#new_templates").remove();

						}

						if($("#template_tab").find('.tparty').length > 0){
						$(response).insertAfter("#template_tab .tparty");
						}else{
						$(response).insertAfter("#template_tab .row");
						}

						var seachdata_type = $('.template_type.active').data('type');
						if(seachdata_type == 3){
						$('.col-md-3.pull-right').show();
						//$('#new_templates').removeClass('col-sm-12');
						$('#new_templates').removeClass('col-md-9');
						$('#new_templates').addClass('col-md-9');

						$('#new_templates li').removeClass('col-lg-3');
						$('#new_templates li').removeClass('col-lg-4');
						$('#new_templates li').addClass('col-lg-4');

						$('.select_msg_main').removeClass('col-sm-12');
						$('.select_msg_main').removeClass('col-sm-9');
						$('.select_msg_main').addClass('col-sm-9');
					}else{
						$('.col-md-3.pull-right').hide();
						$('#new_templates').removeClass('col-md-9');
						//$('#new_templates').removeClass('col-sm-12');
					//	$('#new_templates').addClass('col-sm-12');

						$('#new_templates li').removeClass('col-lg-3');
						$('#new_templates li').removeClass('col-lg-4');
						$('#new_templates li').addClass('col-lg-3');

						$('.select_msg_main').removeClass('col-sm-12');
						$('.select_msg_main').removeClass('col-sm-9');
						$('.select_msg_main').addClass('col-sm-12');
					}
						var cc = $('#new_templates li').length;
						$('.template_type.active .cont').hide();
						$('.template_type.active .cont_new').html(cc).show();

					}, 400);

					setTimeout(function(){
						$.jsPagination({cur_page: 1, parent: '#new_templates' });
					},1000);


			}
		})

	})

<?php } else {  ?>
	$('body').delegate('.search_rating_close', 'click', function (event) {
	 event.preventDefault();

		$('.search_rating').find('i').addClass('text-gray');

		$('.sbox').css('pointer-events','unset');
		$('.sbox').removeClass('disable')

		$('.search_rating').attr('data-active', 0);

		pageUrl = $js_config.base_url + 'templates/search_category';

		$('.select_msg').remove();
		$('.select_msg_main').remove();
		$('.paginate_links').remove();

		$.ajax({
			type:'POST',
			data: $.param({'starRating':0}),
			url: pageUrl,
			global: false,
			success: function( response, status, jxhr ) {
					setTimeout(function(){
						if($("#new_templates").length > 0){
							$("#new_templates").remove();

						}

						if($("#template_tab").find('.tparty').length > 0){

						$(response).insertAfter("#template_tab .tparty");
						}else{

						$(response).insertAfter("#template_tab .row");
						}

						var seachdata_type = $('.template_type.active').data('type');
						if(seachdata_type == 3){
						$('.col-md-3.pull-right').show();
						//$('#new_templates').removeClass('col-sm-12');
						$('#new_templates').removeClass('col-md-9');
						$('#new_templates').addClass('col-md-9');

						$('#new_templates li').removeClass('col-lg-3');
						$('#new_templates li').removeClass('col-lg-4');
						$('#new_templates li').addClass('col-lg-4');

						$('.select_msg_main').removeClass('col-sm-12');
						$('.select_msg_main').removeClass('col-sm-9');
						$('.select_msg_main').addClass('col-sm-9');
					}else{
						$('.col-md-3.pull-right').hide();
						$('#new_templates').removeClass('col-md-9');
						//$('#new_templates').removeClass('col-sm-12');
						$('#new_templates').addClass('col-md-12');

						$('#new_templates li').removeClass('col-lg-3');
						$('#new_templates li').removeClass('col-lg-4');
						$('#new_templates li').addClass('col-lg-3');

						$('.select_msg_main').removeClass('col-sm-12');
						$('.select_msg_main').removeClass('col-sm-9');
						$('.select_msg_main').addClass('col-sm-12');
					}


						<?php  if( isset($this->request->params['pass'][2]) && $this->request->params['pass'][2] > 0  ) {  ?>
							$('.jeera-dashboard-but').remove();
							$('#sel_template_tab').html('<span id="cat_trail" data-project="0" data-remote="'+$js_config.base_url + 'templates/create_workspace/0/0">Library</span>');

							<?php } else{ ?>
							//$("#sel_template_tab").find('.arrowcat').remove();
							$('.arrowcat').remove();

							$('.trails').remove();
						<?php }   ?>


						/* var cc = $('#new_templates li').length;
						$('.template_type.active .cont').hide();
						$('.template_type.active .cont_new').html(cc).show(); */

					}, 400);


					/* setTimeout(function(){
						$.jsPagination({cur_page: 1, parent: '#new_templates' });
					},1000); */

			}
		})

	})

	<?php } ?>

	$('body').delegate('.trails', 'click', function (event) {
        event.preventDefault();
		//$(this).find('i').removeClass('text-gray');
		//var template_category_id = $(this).data('id');



		var starRating = $( $(".search_rating[data-active=1]") ).map(function() {
			return $(this).data('rating');
		  }).get().join();


		var project_id = 0;


		var keyword = $('#temp_search').val();
		$('.trails').remove();

		if(keyword.length > 0){

		}else{
			keyword = null;
		}

		pageUrl = $js_config.base_url + 'templates/search_category';
		var category_title = $(this).find('.cat-title').data('ctitle');


		$('#sel_template_tab').append(' <span class="arrowcat">></span> <span class="trails">'+category_title+'</span>');
		 //alert(category_title);
		if(category_title =='undefined' || category_title ==undefined){
		//$('#sel_template_tab').html('<span id="cat_trail" data-project="0">Library</span>');
		//window.location.href= $js_config.base_url + 'templates/create_workspace/0/0';
		}

		$('.arrowcat').remove();
		$('.trails').remove();

		$('.select_msg').html('');

		$.ajax({
			type:'POST',
			data: $.param({'keyword':keyword,'starRating':starRating ,'columnWidth':'<?php echo $columnWidth; ?>','project_id':project_id}),
			url: pageUrl,
			global: false,
			success: function( response, status, jxhr ) {
					setTimeout(function(){
						if($("#new_templates").length > 0){
							$("#new_templates").remove();

						}
						$(response).insertAfter("#template_tab .row");


					}, 400)

			}
		})
    });



<?php  if( isset($this->request->params['pass'][2]) && $this->request->params['pass'][2] > 0  ) {  ?>


	$('body').delegate('.search_rating', 'click', function (event) {
        event.preventDefault();
		//var seachdata_type = 0;

		$('.sbox').css('pointer-events','none');
		$('.sbox').addClass('disable');
		$('.paginate_links').remove();


		//$(this).find('i').removeClass('text-gray');
		var template_category_id ='<?php echo (isset($this->params['pass']['1'])) ? $this->params['pass']['1'] : 0; ?>';
		var project_id ='<?php echo (isset($this->params['pass']['0'])) ? $this->params['pass']['0'] : 0; ?>';

		$('.pagination.pagination-large.pull-right').remove();
		$('.pagination_temp_filter_template').remove();

		/* if( $(this).data('serachtype') != 0 ){
			var seachdata_type = $(this).data('serachtype');
		}  */

		var seachdata_type = $('.template_type.active').data('type');

		$(this).find('i').toggleClass('text-gray','');
		$(this).attr('data-active', $(this).attr('data-active') == '0' ? '1' : '0');
		pageUrl = $js_config.base_url + 'templates/search_template';

			var starRating = $( $(".search_rating[data-active=1]") ).map(function() {
			return $(this).data('rating');
		  }).get().join();

		$('.select_msg').remove();
		$('.select_msg_main').remove();

		//console.log("111111111111111111");
		//return false;
		if( seachdata_type == 3  ){

			$('.third-party-user').removeClass('opened').addClass('closed');

			$('.saveasdiv_ajax .panel-collapse').addClass('collapse').removeAttr('style');
		}


		if( starRating == ""  ){
			$('.sbox').css('pointer-events','unset');
			$('.sbox').removeClass('disable')

		}

		if( starRating == "" && seachdata_type == 1  ){

			$(".template_type[data-type="+seachdata_type+"]").trigger( "click" );
			$(this).find('i').toggleClass('text-gray','');
			//return false;


		} else {

			$.ajax({
				type:'POST',
				data: $.param({'starRating':starRating,'template_category_id':template_category_id,'columnWidth':'<?php echo $columnWidth; ?>','project_id':project_id,'searchdatatype':seachdata_type}),
				url: pageUrl,
				global: false,
				success: function( response, status, jxhr ) {
						setTimeout(function(){
							if($("#new_templates").length > 0){
								$("#new_templates").remove();
							}
							$(response).insertAfter("#template_tab .row");

						var seachdata_type = $('.template_type.active').data('type');
						if(seachdata_type == 3){
						$('.col-md-3.pull-right').show();
						//$('#new_templates').removeClass('col-sm-12');
						$('#new_templates').removeClass('col-md-9');
						$('#new_templates').addClass('col-md-9');

						$('#new_templates li').removeClass('col-lg-3');
						$('#new_templates li').removeClass('col-lg-4');
						$('#new_templates li').addClass('col-lg-4');

						$('.select_msg_main').removeClass('col-sm-12');
						$('.select_msg_main').removeClass('col-sm-9');
						$('.select_msg_main').addClass('col-sm-9');
					}else{
						$('.col-md-3.pull-right').hide();
						$('#new_templates').removeClass('col-md-9');
					//	$('#new_templates').removeClass('col-sm-12');
					//	$('#new_templates').addClass('col-sm-12');

						$('#new_templates li').removeClass('col-lg-3');
						$('#new_templates li').removeClass('col-lg-4');
						$('#new_templates li').addClass('col-lg-3');

						$('.select_msg_main').removeClass('col-sm-12');
						$('.select_msg_main').removeClass('col-sm-9');
						$('.select_msg_main').addClass('col-sm-12');
					}
							$('#new_templates li').hide()
							var cc = $('#new_templates li').length;
							$('.template_type.active .cont').hide();
							$('.template_type.active .cont_new').html(cc).show();
							//$(".template_type[data-type="+seachdata_type+"]").trigger( "click" );

							if($('.utemp_cat_list').length > 0 && $('.utemp_cat_list:visible').length > 0){
								$('.select_msg').remove();
								$('.select_msg_main').remove();
							}

					}, 300);
					setTimeout(function(){
						$.jsPagination({cur_page: 1, parent: '#new_templates' });
					},500);

				}
			})
		}


    });


	<?php  } else {  ?>

	$('body').delegate('.search_rating', 'click', function (event) {
        event.preventDefault();
		//$(this).find('i').removeClass('text-gray');

		$('.sbox').css('pointer-events','none');
		$('.sbox').addClass('disable');
		$('.paginate_links').remove();

		<?php  if( isset($this->request->params['pass'][2]) && $this->request->params['pass'][2] > 0  ) {  ?>
		$('.jeera-dashboard-but').remove();
		$('#sel_template_tab').html('<span id="cat_trail" data-project="0" data-remote="'+$js_config.base_url + 'templates/create_workspace/0/0">Library</span>');

		<?php } else{ ?>
		$("#sel_template_tab").find('.arrowcat').remove();
		$('.trails').remove();
		<?php }   ?>

		$(this).find('i').toggleClass('text-gray','');

		$(this).attr('data-active', $(this).attr('data-active') == '0' ? '1' : '0');

		pageUrl = $js_config.base_url + 'templates/search_category';

		$('#cat_trail').attr('data-remote',$js_config.base_url+'templates/create_workspace/0/0');

		var starRating = $( $(".search_rating[data-active=1]") ).map(function() {
			return $(this).data('rating');
		  }).get().join();


		$('.select_msg').remove();
		$('.select_msg_main').remove();
		//console.log("33333333333333333333");

		if(starRating ==''){
			$('.sbox').css('pointer-events','unset');
			$('.sbox').removeClass('disable');
		}
							//console.log('length')
			$.ajax({
				type:'POST',
				data: $.param({'starRating':starRating}),
				url: pageUrl,
				global: false,
				success: function( response, status, jxhr ) {

						if($("#new_templates").length > 0){
							$("#new_templates").remove();
						}
						$(response).insertAfter("#template_tab .row");
						setTimeout(function(){
							if($('.utemp_cat_list').length > 0 && $('.utemp_cat_list:visible').length > 0){
								$('.select_msg').remove();
								$('.select_msg_main').remove();
							}
							else{
								$("#template_tab .row").html('<div class="select_msg" style="">NO KNOWLEDGE TEMPLATES FOUND</div>')
							}
						}, 700);

						if( starRating == '' ){
							$("#template_tab").find(".select_msg").html('');
							//$(".search_rating_close").trigger('click');
						}

				}
			})

    });

	<?php  }   ?>




<?php } ?>

<?php if( isset($this->request->params['pass'][2]) && $this->request->params['pass'][2] > 0){ ?>

			var tabCntType = '<?php echo $this->request->params['pass'][2]; ?>';

			if( tabCntType == 2 ){

				setTimeout(function(){
					$(".template_type[data-type=2]").trigger( "click" );

					$(".template_type[data-type=3] .fa-long-arrow-down").addClass('hide');
					$(".template_type[data-type=1] .fa-long-arrow-down").addClass('hide');

					$(".template_type[data-type=3]").removeClass('active');
					$(".template_type[data-type=1]").removeClass('active');

					$(".template_type[data-type=2]").addClass('active');
					$('.paginate_links').remove();
				},400)


			} else if( tabCntType == 3 ){
				setTimeout(function(){

					$(".template_type[data-type=3]").trigger( "click" );

					$(".template_type[data-type=2]").removeClass('active');
					$(".template_type[data-type=2] .fa-long-arrow-down").addClass('hide');
					$(".template_type[data-type=1]").removeClass('active');
					$(".template_type[data-type=1] .fa-long-arrow-down").addClass('hide');

					$(".template_type[data-type=3]").addClass('active');
					$('.paginate_links').remove();

				},400)
			} else {
				setTimeout(function(){

					$(".template_type[data-type=1]").trigger( "click" );

					$(".template_type[data-type=2] .fa-long-arrow-down").addClass('hide');
					$(".template_type[data-type=3] .fa-long-arrow-down").addClass('hide');

					$(".template_type[data-type=2]").removeClass('active');
					$(".template_type[data-type=3]").removeClass('active');

					$(".template_type[data-type=1]").addClass('active');
					$('.paginate_links').remove();

				},400)
			}

	<?php } ?>

})


$(function () {

setTimeout(function(){
$('.footer-content a').trigger('click');

},400)

$('body').delegate('.pagination li a', 'click', function(e) {

$('.footer-content a').trigger('click');
})



	$.jsPagination = function(args) {
		$js_config.search_limit = 12;
		var chkTotalTrRows = $('#new_templates').children('li').length,
			rows_per_page = $js_config.search_limit,
			total_rows;

		/* if( args && args.hasOwnProperty('par	ent') && args.parent !== '' ) {
			var $parent = args.parent;
			chkTotalTrRows = $($parent).children('li').length;
		} */
		if( chkTotalTrRows > $js_config.search_limit ) {
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


	$.jsPaginationSearch = function(args) {
		$js_config.search_limit = 12;
		var chkTotalTrRows = $('#tab-content #new_templates li').find('h3 mark:first').length,

			rows_per_page = $js_config.search_limit,
			total_rows;

		/* if( args && args.hasOwnProperty('par	ent') && args.parent !== '' ) {
			var $parent = args.parent;
			chkTotalTrRows = $($parent).children('li').length;
		} */

		//console.log(chkTotalTrRows);

		if( chkTotalTrRows > $js_config.search_limit ) {
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
		 	//$($parent).children().css('display', 'none').slice(start, end).css('display', 'block')
		//	$($parent).children().css('display', 'none').slice(start, end).css('display', 'block')



			$('#tab-content #new_templates li').hide();
			$('#tab-content #new_templates li').find('h3 mark:first').parents('li').slice(start, end).css('display', 'block');




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
					setTimeout(function(){
					$('.paginate_links .js-pages a').removeAttr('onclick');

					$('.paginate_links .js-pages a').attr('onclick','clickAnchorN(this);');
					},500)

				}
			}
		});
	}


	/*=============================================================*/

	/*=============================================================*/


});
	function clickAnchorN(t) {

		var element = $(t);
		var cur_page = element.attr("data-value");
		//console.log('cur_page', cur_page)
		var args = {'cur_page': cur_page, parent:'#new_templates'}
		$.jsPaginationSearch(args);

			$('.footer-content a').trigger('click');
	}

	function clickAnchor(t) {

		var element = $(t);
		var cur_page = element.attr("data-value");
		//console.log('cur_page', cur_page)
		var args = {'cur_page': cur_page, parent:'#new_templates'}

		$.jsPagination(args);

			$('.footer-content a').trigger('click');

	}


	$(function(){

	$('body').delegate('.utemp_cat_list:not(.utemp_cat_list_actual)', 'click', function (event) {
        event.preventDefault();

	//   return;
		//$(this).find('i').removeClass('text-gray');
		var template_category_id = $(this).data('id');
		var starRating = $(this).data('revrating');
		var project_id = '<?php echo $project_id;?>';

		var keyword = $('#temp_search').val();
		$('.trails').remove();
		$('.arrowcat').remove();

		if(keyword.length > 0){

		}else{
			keyword = null;
		}

		pageUrl = $js_config.base_url + 'templates/search_template';
		var category_title = $(this).find('.cat-title').data('ctitle');

		$('#sel_template_tab').append(' <span class="arrowcat">></span> <span class="trails">'+category_title+'</span>');


		var create_wsp = $js_config.base_url +"templates/create_workspace/0/0";

		if( project_id && project_id > 0 ){
			create_wsp = $js_config.base_url +"templates/create_workspace/"+project_id+"/0";
		}

		/*if( project_id && project_id > 0 && template_category_id > 0 ){
			create_wsp = $js_config.base_url +"templates/create_workspace/"+project_id+"/"+template_category_id;
		}

		 if( project_id && project_id == 0 && template_category_id > 0 ){
			create_wsp = $js_config.base_url +"templates/create_workspace/0/"+template_category_id;
		} */
		$("#cat_trail").attr("data-remote",create_wsp);

		$('.select_msg').html('');
		 $('.paginate_links').remove();
		$.ajax({
			type:'POST',
			data: $.param({'keyword':keyword,'starRating':starRating,'template_category_id':template_category_id,'columnWidth':'<?php echo $columnWidth; ?>','project_id':project_id}),
			url: pageUrl,
			global: false,
			success: function( response, status, jxhr ) {
					setTimeout(function(){
						if($("#new_templates").length > 0){
							$("#new_templates").remove();

						}

						if($(".paginate_links").length > 0){
							 $(".paginate_links").remove();

						}

						$(response).insertAfter("#template_tab .row");
						//$("<div class='paginate_links'></div>").insertAfter("#new_templates");
						//$('#new_templates').insertAfter("<div class='paginate_links'></div>");

					}, 400)

					setTimeout(function(){


					$.jsPagination({ cur_page : 1, parent : '#new_templates'
					});

					}, 700)

			}
		})
		 //event.stop​Propagation();
    });


	$('html').addClass('no-scroll');

	// RESIZE MAIN FRAME
    ($.adjust_resize = function(){
        $('.borderworkspace').animate({
            minHeight: (($(window).height() - $('.borderworkspace').offset().top) ) - 17,
            maxHeight: (($(window).height() - $('.borderworkspace').offset().top) ) - 17
        }, 1)
    })();

    // WHEN DOM STOP LOADING CHECK AGAIN FOR MAIN FRAME RESIZING
    var interval = setInterval(function() {
        if (document.readyState === 'complete') {
            $.adjust_resize();
            clearInterval(interval);
        }
    }, 1);

    // RESIZE FRAME ON SIDEBAR TOGGLE EVENT
    $(".sidebar-toggle").on('click', function() {
        $.adjust_resize();
        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
        setTimeout( () => clearInterval(fix), 1500);
    })

    // RESIZE FRAME ON WINDOW RESIZE EVENT
    $(window).resize(function() {
        $.adjust_resize();
    })

    $("#myTempletTabs").on('show.bs.tab', function(e){
        // $.adjust_resize();
        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
        setTimeout( () => clearInterval(fix), 1000);
    })



})

</script>
<style>
#new_templates{ position:relative;}
.select_msg_main{
 	position:relative;
	 top: 35%;
}

#template_list {
  min-height: 560px;
  padding: 0;
}

.select_msg {
    color: #bbbbbb;
    font-size: 30px;
    left: 4px;
    position: absolute;
    text-align: center;
    text-transform: uppercase;
    top: 18%;
    width: 98%;
}

.tab-content>.tab-pane {
    display: none !important;
    visibility: hidden;
}

.tab-content>.active {
    display: block !important;
    visibility: visible;
}

#btn_create_template1 i , #btn_create_template i  { cursor : pointer !important;
     
}
</style>