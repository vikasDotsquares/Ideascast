
<?php if(isset($list) && !empty($list)){  ?>
	<ul class="doc-ul">
		<?php foreach ($list as $key => $value) {
		$data = $value['project_documents'];
		$folder_url = WWW_ROOT . 'uploads/project_documents/' . $data['filename'];
		$ext = pathinfo($folder_url, PATHINFO_EXTENSION);
		// pr($ext);
		$audio_ext = ['aif', 'aiff', 'au', 'cda', 'iff', 'm3u', 'm4a', 'mid', 'midi', 'mp3', 'mpa', 'ogg', 'wav', 'wma', 'wpl'];
		$compress_ext = ['7z', 'arj', 'bz2', 'cab', 'cbr', 'deb', 'gz', 'lha', 'lhz', 'pkg', 'rar', 'rpm', 'sitx', 'tar', 'gz', 'taz', 'tgz', 'z', 'zip', 'zipx'];
		$data_ext = ['csv', 'dat', 'db', 'dbf', 'log', 'mdb', 'pdb', 'sql', 'xml'];
		$document_ext = ['doc', 'docx', 'odt', 'pdf', 'rtf', 'tex', 'txt', 'wpd', 'wps'];
		$image_ext = ['ai', 'bmp', 'dds', 'eps', 'gif', 'heic', 'ico', 'jpeg', 'jpg', 'png', 'ps', 'psd', 'pspimage', 'svg', 'tga', 'thm', 'tif', 'tiff', 'yuv'];
		$ppt_ext = ['key', 'odp', 'pps', 'ppt', 'pptx'];
		$sheet_ext = ['ods', 'xlr', 'xls', 'xlsm', 'xlsx'];
		$video_ext = ['3g2', '3gp', 'asf', 'avi', 'flv', 'h264', 'm4v', 'mkv', 'mov', 'mp4', 'mpg', 'mpeg', 'rm', 'srt', 'swf', 'vob', 'wmv'];

		$doc_icon = 'fileblue';
		if(in_array($ext, $audio_ext)) {
			$doc_icon = 'audioblue';
		}
		else if(in_array($ext, $compress_ext)) {
			$doc_icon = 'compressedblue';
		}
		else if(in_array($ext, $data_ext)) {
			$doc_icon = 'datablue';
		}
		else if(in_array($ext, $document_ext)) {
			$doc_icon = 'documentblue';
		}
		else if(in_array($ext, $image_ext)) {
			$doc_icon = 'imageblue';
		}
		else if(in_array($ext, $ppt_ext)) {
			$doc_icon = 'presentationblue';
		}
		else if(in_array($ext, $sheet_ext)) {
			$doc_icon = 'sheetblue';
		}
		else if(in_array($ext, $video_ext)) {
			$doc_icon = 'videoblue';
		}
		?>
		<li class="li-doc" data-id="<?php echo $data['id']; ?>" data-sort="<?php echo $data['sort_order']; ?>" style="cursor: default;">
			<span class="all-document-icon <?php echo $doc_icon; ?>" style="cursor: default;"></span>
			<div class="documents-list-text">
				<h6><?php echo htmlentities($data['title'], ENT_QUOTES, "UTF-8"); ?></h6>
				<div class="documents-list-info"><?php echo htmlentities($data['summary'], ENT_QUOTES, "UTF-8"); ?></div>
			</div>

			<?php if(!isset($sign_off[0]['projects']['sign_off']) || empty($sign_off[0]['projects']['sign_off'])){ ?>
				<div class="projdocuments-list-right">
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
	<div class="no-summary-found">No Documents</div>
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
	    $.doc_default_state = () => {
	    	$('.sort-up, .sort-down').removeClass('not-shown');
    		$('.doc-ul').find('.li-doc:first:not(.clone)').find('.sort-up').addClass('not-shown');
			$('.doc-ul').find('.li-doc:last:not(.clone)').find('.sort-down').addClass('not-shown');
	    }

		// AFTER MOVE UP/DOWN SAVE ORDER TO DATABASE
	    $.project_doc_sorting = (data) => {
	    	$('.tooltip').hide();
	    	$('.projdocuments-list-right').addClass('stopped');
	    	data.project_id = project_id;
	    	$.ajax({
				url: $js_config.base_url + 'projects/project_doc_sorting',
				type: 'POST',
				data: data,
				success:function(response){
					$.docs_uploaded = true;
					$('.tooltip').hide();
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
			$.project_doc_sorting(data);
			$.doc_default_state();
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
			$.project_doc_sorting(data);
			$.doc_default_state();
	    });

	    // TOGGLE SHARERS PERMISSION
		$('.toggle-sharer').off('click').on('click', function(event) {
			$('.tooltip').hide();
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
				url: $js_config.base_url + 'projects/project_doc_sharing',
				type: 'POST',
				dataType: 'JSON',
				data: post,
				success:function(response){
					if(response.success){
						$('i',$this).toggleClass('visibleblack invisibleblack');
						$.document_list();
						$.docs_uploaded = true;
						$('.tooltip').hide();
					}
				}
			})
		});

	    // TOGGLE SHARERS PERMISSION
		$('.doc-delete').off('click').on('click', function(event) {
			$('.tooltip').hide();
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
				url: $js_config.base_url + 'projects/project_doc_remove',
				type: 'POST',
				dataType: 'JSON',
				data: post,
				success:function(response){
					if(response.success){
						// $.document_list();
						$li.slideUp(100, function(){
							$(this).remove();
							$('.tooltip').hide();
							$.docs_uploaded = true;
							if($(".projdocuments-list ul li").length <= 0){
								$.document_list();
							}
							$.doc_default_state();
						})
					}
				}
			})
		});
	})
</script>