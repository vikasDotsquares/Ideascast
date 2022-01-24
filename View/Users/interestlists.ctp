<?php if( !empty($interestlists) ){
	foreach($interestlists as $listinterest){
?>
	<div class="interest-row" data-interestid="<?php echo $listinterest['UserInterest']['id']; ?>" style="border-radius: 0;">
		<span  class="interest-text"><?php echo htmlentities($listinterest['UserInterest']['title'], ENT_QUOTES, "UTF-8");?></span>
		<div class="btn btn-xs interest-confirm pull-right" style="display: none;">
			<span class="btn-confirm confirm-yes btn-success tipText" title="" data-original-title="Remove Interest"><i class="fa fa-check"></i></span>
			<span class="btn-confirm confirm-no btn-danger tipText" title="" data-original-title="Cancel Delete"><i class="fa fa-times"></i></span>
		</div>
		<span class="interest-delete tipText" title="Delete"><i class="deleteblack"></i></span>
		<span class="interest-edit tipText" title="Edit"><i class="edit-icon"></i></span>
	</div>
<?php }
} else { ?>
<div class="interest-row no-interest-record" >
	No interests found
</div>
<?php } ?>