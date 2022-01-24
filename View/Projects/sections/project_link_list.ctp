
<?php if(isset($list) && !empty($list)){  ?>
	<ul class="doc-ul">
		<?php foreach ($list as $key => $value) {
		$data = $value['project_links'];

		?>
		<li class="li-doc" data-id="<?php echo $data['id']; ?>" data-sort="<?php echo $data['sort_order']; ?>">
			<span class="all-link-icon"></span>
			<div class="documents-list-text">
				<h6><?php echo htmlentities($data['title'], ENT_QUOTES, "UTF-8"); ?></h6>
				<div class="documents-list-info"><?php echo htmlentities($data['summary'], ENT_QUOTES, "UTF-8"); ?></div>
			</div>

			<?php if(!isset($sign_off[0]['projects']['sign_off']) || empty($sign_off[0]['projects']['sign_off'])){ ?>
				<div class="projdocuments-list-right ">
					<a href="#" class="sort-up <?php if($key == 0){ ?> not-shown <?php } ?> tipText" title="Move Up"><i class="upblack"></i></a>
					<a href="#" class="sort-down <?php if($key >= (count($list) - 1)){ ?> not-shown <?php } ?> tipText" title="Move Down"><i class="downblack"></i></a>
					<a href="#" class="toggle-sharer" <?php if(!empty($data['is_sharers'])){ ?> title="Visible To Sharers<br />Click To Hide From Sharers" <?php }else{ ?> title="Hidden From Sharers<br />Click To Make Visible To Sharers" <?php } ?>" ><i class="<?php if(!empty($data['is_sharers'])){ ?> visibleblack <?php }else{ ?> invisibleblack <?php } ?>"></i></a>
					<a href="#" class="doc-delete tipText" title="Delete"><i class="deleteblack"></i></a>
				</div>
			<?php } ?>
		</li>
		<?php } ?>
	</ul>
<?php }else{ ?>
	<div class="no-summary-found">No Links</div>
<?php } ?>

<script type="text/javascript">
	$(function(){
		var project_id = '<?php echo $project_id; ?>';

		$('.toggle-sharer').tooltip({
			html: true,
			placement: 'top',
			container: 'body',
			template: '<div class="tooltip tooltip-custom"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
		})
		// AFTER MOVE UP/DOWN SET DEFAULT STATE OF ICONS
	    $.link_default_state = () => {
	    	$('.sort-up, .sort-down').removeClass('not-shown');
    		$('.doc-ul').find('.li-doc:first:not(.clone)').find('.sort-up').addClass('not-shown');
			$('.doc-ul').find('.li-doc:last:not(.clone)').find('.sort-down').addClass('not-shown');
	    }

		// AFTER MOVE UP/DOWN SAVE ORDER TO DATABASE
	    $.project_link_sorting = (data) => {
	    	$('.tooltip').hide();
	    	$('.projdocuments-list-right').addClass('stopped');
	    	data.project_id = project_id;
	    	$.ajax({
				url: $js_config.base_url + 'projects/project_link_sorting',
				type: 'POST',
				data: data,
				success:function(response){
					$.link_added = true;
					$('.projdocuments-list-right').removeClass('stopped');
				}
			})
	    }

	    // UP ARROW CLICK EVENT
	    $('.sort-up').off('click').on('click',function(event) {
	    	event.preventDefault();

	    	var $this = $(this),
	    		$row = $this.parents('.li-doc:first'),
				$next = $row.prev('.li-doc:first');

			var current_order = $row.data('sort'),
				next_order = $next.data('sort');

			var data = {
				'current_id': $row.data('id'),
				'current_order': $row.data('sort'),
				'next_id': $next.data('id'),
				'next_order': $next.data('sort'),
			}
			$row.data('sort', next_order);
			$next.data('sort', current_order);
			$row.insertBefore($next);
			$.project_link_sorting(data);
			$.link_default_state();
	    });

	    // DOWN ARROW CLICK EVENT
	    $('.sort-down').off('click').on('click', function(event) {
	    	event.preventDefault();

	    	var $this = $(this),
	    		$row = $this.parents('.li-doc:first'),
				$next = $row.next('.li-doc:first');

			var current_order = $row.data('sort'),
				next_order = $next.data('sort');

			var data = {
				'current_id': $row.data('id'),
				'current_order': $row.data('sort'),
				'next_id': $next.data('id'),
				'next_order': $next.data('sort'),
			}
			$row.data('sort', next_order);
			$next.data('sort', current_order);
			$row.insertAfter($next);
			$.project_link_sorting(data);
			$.link_default_state();
	    });

	    // TOGGLE SHARERS PERMISSION
		$('.toggle-sharer').off('click').on('click', function(event) {
			event.preventDefault();
			var $li = $(this).parents('li.li-doc:first'),
				data = $li.data(),
				id = data.id,
				$this = $(this);
			var post = {
				id: id,
				project_id: project_id,
				is_sharers: ($('i',$(this)).hasClass('visibleblack') ? 0 : 1)
			}
			$('.projdocuments-list-right').addClass('stopped');

			$.ajax({
				url: $js_config.base_url + 'projects/project_link_sharing',
				type: 'POST',
				dataType: 'JSON',
				data: post,
				success:function(response){
					if(response.success){
						$('i',$this).toggleClass('visibleblack invisibleblack');
						$.link_list();
						$.link_added = true;
						$('.tooltip').hide();
					}
				}
			})
		});

	    // TOGGLE SHARERS PERMISSION
		$('.doc-delete').off('click').on('click', function(event) {
			event.preventDefault();
			var $li = $(this).parents('li.li-doc:first'),
				data = $li.data(),
				id = data.id,
				$this = $(this);
			var post = {
				id: id,
				project_id: project_id
			}
			$('.projdocuments-list-right').addClass('stopped');

			$.ajax({
				url: $js_config.base_url + 'projects/project_link_remove',
				type: 'POST',
				dataType: 'JSON',
				data: post,
				success:function(response){
					if(response.success){
						// $.document_list();
						$li.slideUp(100, function(){
							$(this).remove();
							$('.tooltip').hide();
							$.link_added = true;
							if($(".projdocuments-list ul li").length <= 0){
								$.link_list();
							}
							$.link_default_state();
						})
					}
				}
			})
		});
	})
</script>