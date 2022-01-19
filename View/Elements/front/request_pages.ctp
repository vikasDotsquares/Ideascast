<?php
    $pending_feedback_request = $pending_vote_request =  $group_requests = $getTDCount = $total_requests = $getPBordRCount = $getWikiRequestCount =  0;


	$pending_feedback_request = $this->ViewModel->pending_feedback_request();
    $pending_vote_request = $this->ViewModel->pending_vote_request();
	$group_requests = $this->ViewModel->pending_group_request();

	$getTDCount = $this->ViewModel->getTDCount();
	$getPBordRCount = $this->ViewModel->getBoardRequestCount();
	//$getWikiRequestCount = $this->ViewModel->getWikiRequestCount();

    $total_requests = $pending_feedback_request + $pending_vote_request + $group_requests + $getTDCount + $getPBordRCount + $getWikiRequestCount;
?>

<li class="nav-requests-top">
<a class="tipText" title="" href="#" style="text-transform:none !important;" data-toggle="dropdown" data-original-title="Requests"><span class="nav-icon-all">
	<i class="icon-size-nav request-nav"></i><?php if(isset($total_requests) && !empty($total_requests)){ ?><i class="bg-gray header-counter"><?php echo $total_requests; ?></i><?php } ?></span></a>

	<ul class="dropdown-menu">

			<li  class="dropdown-submenu"><a href="<?php echo SITEURL?>shares/group_requests"><span class="requests-nav-icon"><i class="requests-all-icon requests-group-icon"></i></span> Group (<span id="group_total_request"><?php echo $group_requests; ?></span>)</a></li>

			<li class="dropdown-submenu"><a href="<?php echo SITEURL?>entities/vote_request "><span class="requests-nav-icon"><i class="requests-all-icon requests-vote-icon"></i></span> Vote <?php if(isset($pending_vote_request)) echo '('.$pending_vote_request.')'; ?></a></li>

			<li class="dropdown-submenu"><a href="<?php echo SITEURL?>entities/feedback_request"><span class="requests-nav-icon"><i class="requests-all-icon requests-feedback-icon"></i></span> Feedback <?php if(isset($pending_feedback_request)) echo '('.$pending_feedback_request.')'; ?></a></li>

			<?php /* ?><li class="dropdown-submenu"><a href="<?php echo SITEURL?>todos/requests" class="request-link"><span class="requests-nav-icon"><i class="requests-all-icon requests-to-dos-icon"></i></span> To-do <?php if(isset($getTDCount)) echo '('.$getTDCount.')'; ?></a></li><?php */ ?>

			<li class="dropdown-submenu"><a href="<?php echo SITEURL?>boards/opportunity/request"><span class="requests-nav-icon"><i class="requests-all-icon opportunitiesblack-icon"></i></span> Opportunity <?php if(isset($getPBordRCount)) echo '('.$getPBordRCount.')'; ?></a></li>

			<?php /* ?><li class="dropdown-submenu"><a href="<?php echo SITEURL?>wikies/request"><span class="requests-nav-icon"><i class="requests-all-icon requests-wiki-icon"></i></span> Wiki <?php if(isset($getWikiRequestCount)) echo '('.$getWikiRequestCount.')'; ?></a></li><?php */ ?>
	</ul>
</li>
<style type="text/css">
	.request-icon {
		border: 1px solid #333;
		padding: 1px 2px 0px 1px;
	    border-radius: 2px;
	    font-size: 12px;
	}
	.theme_black .request-icon {
		border: 1px solid #333;
	}
	.theme_default .request-link:hover .request-icon {
		border: 1px solid #fff;
	}
	.to-rht{ margin: 0 0px 0 -2px }
</style>