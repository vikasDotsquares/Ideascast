<?php
$item = $data['Project'];

$project_image = $item['image_file'];
if( !empty($project_image) && file_exists(PROJECT_IMAGE_PATH . $project_image)){
	$project_image = SITEURL . PROJECT_IMAGE_PATH . $project_image;
	// echo $this->Html->image( $project_image, ['class' => 'project_image', 'alt' => 'Project Image' ] );
	echo $this->Image->resize( $item['image_file'], 780, 150, array('style'=>'width: 100%', 'width' => '100%'), 100);
?>
	<div class="img-options" >
	<a class="btn btn-primary btn-xs image-upload project_image_upload" data-remote="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'project_image_upload', $item['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $item['id']; ?>" style="right: 70px;">Update</a>

	<a class="btn btn-danger btn-xs image-upload remove_pimage" data-id="<?php echo $item['id']; ?>">Remove</a>
	</div>
<?php
}
else {
?>
<div class="upload-text">
	Add a photo here to show off your project.<br />
	<a class="btn btn-primary btn-sm project_image_upload" style="margin-top: 10px;" data-remote="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'project_image_upload', $item['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $item['id']; ?>">Upload</a>
</div>
<?php } ?>
