
<?php
$projectPermitType = $this->ViewModel->projectPermitType($project_id, $this->Session->read('Auth.User.id'));
$list = $this->Permission->project_docs($project_id);
// pr($list );
if(isset($list) && !empty($list)){  ?>
	<ul class="summary-doc proj-doc-list">
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
			$show_list = true;
			if(!$projectPermitType){
				if(empty($data['is_sharers'])){
					$show_list = false;
				}
			}
			if($show_list){
		?>
		<li class="li-doc" data-sort="<?php echo $data['sort_order']; ?>">
			<span class="all-document-icon <?php echo $doc_icon; ?>" data-id="<?php echo $data['id']; ?>"></span>
			<div class="documents-list-text">
				<h6 class="title"  data-id="<?php echo $data['id']; ?>"><?php echo htmlentities($data['title'], ENT_QUOTES, "UTF-8"); ?></h6>
				<div class="documents-list-info"><?php echo htmlentities($data['summary'], ENT_QUOTES, "UTF-8"); ?></div>
			</div>
		</li>
			<?php } ?>
		<?php } ?>
	</ul>
<?php }else{ ?>
	<div class="no-sec-data-found">No Documents</div>
<?php } ?>
<script type="text/javascript">
	$(function(){

		if($('.summary-doc.proj-doc-list .li-doc').length <= 0){
			$('.summary-doc.proj-doc-list').html('<div class="no-summary-found">No Documents</div>');
		}
		$('.document-section').find('.ts-count').html($('.summary-doc.proj-doc-list .li-doc').length);

		$('.li-doc .all-document-icon, .li-doc .title').on('click', function(event) {
			event.preventDefault();
			var id = $(this).data('id');
			location.href = $js_config.base_url + 'projects/download_project_doc/' + id;
		});

		setTimeout(() => {
			// $(".summary-doc.proj-doc-list").slimScroll({height: $('.summary-doc.proj-doc-list').height(), maxHeight: '388px', alwaysVisible: false });
		}, 2)
	})
</script>