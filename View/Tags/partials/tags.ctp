
<div class="tag-buttons-container no-data-show">
	<div class="tag-col-data tag-col-data-1">

			<div class="panel-heading people-section">
				<span class="peoplecount-info">People (<span class="people-count">0</span>)</span>

					<span class="short-arrow-wrap">
						<span class="short-arrow alphabetical tipText sort_order" title="Sort By First Name" data-sorted="asc" data-type="user_first_name">
							<i class="fa fa-sort" aria-hidden="true"></i>
							<i class="fa fa-sort-asc" aria-hidden="true"></i>
							<i class="fa fa-sort-desc" aria-hidden="true"></i>
					  	</span>
						<span class="short-arrow alphabetical tipText sort_order" title="Sort By Last Name" data-sorted="asc"  data-type="user_last_name">
							<i class="fa fa-sort" aria-hidden="true"></i>
							<i class="fa fa-sort-asc" aria-hidden="true"></i>
							<i class="fa fa-sort-desc" aria-hidden="true"></i>
					  	</span>
					</span>
					<!--<a class="btn btn-xs btn-control alphabetical tipText" title="Sort By First Name" data-sorted="asc" data-type="user_first_name">AZ</a>
					<a class="btn btn-xs btn-control alphabetical tipText" title="Sort By Last Name" data-sorted="asc" data-type="user_last_name">AZ</a>-->

			</div>

	</div>
	<div class="tag-col-data tag-col-data-2">

			<div class="panel-heading task-section">Tags
				<span class="short-arrow-wrap">
					<span class="short-arrow sort_order alphabetical tipText" title="Sort by Tag Count" data-sorted="asc"  data-type="tag"  >
						<i class="fa fa-sort" aria-hidden="true"></i>
						<i class="fa fa-sort-asc" aria-hidden="true"></i>
						<i class="fa fa-sort-desc" aria-hidden="true"></i>
				  	</span></span>
					<!--<a class="btn btn-xs btn-control alphabetical tipText" title="Sort by Tag Count" data-sorted="asc" data-type="tag">AZ</a>-->

			</div>

	</div>
	<div class="tag-col-data tag-col-data-3">

			<div class="panel-heading project-section">Actions</div>

	</div>
</div>
<?php
$current_user_id = $this->Session->read('Auth.User.id');
?>
<div class="tag-data paging-wrapper" id="tagData">
	<div class="no-row-wrapper" style="position:unset">NO PEOPLE</div>
</div>
<script type="text/javascript">
	$(function(){
		$('.popovers').popover({
			placement : 'bottom',
	        trigger : 'hover',
	        html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
		}).
		on('show.bs.popover', function(){
			var $popover = $(this).data('bs.popover');
			var $tip = $popover.$tip;
			$tip.find('.popover-content').css('padding', 0)
			$tip.find('.popover-title').attr('style', 'font-size: 13px;')
		})

		if ($('.tag-data-row').length <= 0) {
			$('.btn-control').addClass('disabled');
		}

	})
</script>
<style type="text/css">
	.rem_popover {
	    display: block;
	    font-size: 13px;
        margin: 5px 0 0 0;
	}
	.rem_popover .el-title {
	    white-space: nowrap;
	    text-overflow: ellipsis;
	    width: 100%;
	    display: block;
	    overflow: hidden;
	    background-color: #d6e9c6;
	    padding: 7px 15px;
    	font-weight: 600;
	}
	.rem_popover span.text-data {
	    display: block;
        margin: 3px 0;
	}
	.rem_popover span.comment-label {
	    display: block;
	}
	.rem_popover span.comment-data {
	    border: 1px solid #ccc;
	    display: block;
	    padding: 3px 5px;
	    min-height: 70px;
	}
	.rem_popover .data-content {
        padding: 7px 15px;
        font-size: 11px;
	}
</style>
