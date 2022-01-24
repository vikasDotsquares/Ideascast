<?php
$dolist_count = 0;

$counters = $this->Group->dolist_counters();
$projects = $this->Group->header_dolist();
if (isset($counters['total']) && !empty($counters['total'])) {
	$dolist_count += $counters['total'];

}

$projects = $this->Group->header_dolist();
if(isset($projects) && !empty($projects)){
$projects = array_unique($projects);
}

$overdue_count = 0;

if (isset($projects) && !empty($projects)) {
	foreach ($projects as $toid => $prid) {

		$projectData = project_detail($prid);
		$pcounters = $this->Group->dolist_counters($prid);
		// pr($pcounters);
		if (isset($pcounters['total']) && !empty($pcounters['total'])) {
			$overdue_count += $pcounters['over_count'];
			// $dolist_count += $pcounters['today_count'] + $pcounters['tom_count'] + $pcounters['up_count'] + $pcounters['ns_count'] + $pcounters['over_count'];
		}
	}
}
?>
<li>
    <a title="Overdue To-dos" href="<?php echo SITEURL; ?>todos/index" style="text-transform:none !important;" class="tipText ">
      <span class="nav-icon-all">  <i class="icon-size-nav to-do-icon"></i>
        <?php if ($overdue_count > 0) { ?><i class="bg-gray header-counter"><?php echo ($overdue_count > 99) ? '99+' : $overdue_count; ?></i></span><?php } ?>
    </a>
</li>