<?php
$taskCountAst = $this->ViewModel->getTaskCountAssetByTask($taskID);

$total_assets = (isset($taskCountAst['0']['0']['total_assets']) && !empty($taskCountAst['0']['0']['total_assets'])) ? $taskCountAst['0']['0']['total_assets'] : 0;
$dc_tot = (isset($taskCountAst['0']['0']['dc_tot']) && !empty($taskCountAst['0']['0']['dc_tot'])) ? $taskCountAst['0']['0']['dc_tot'] : 0;
$fb_tot = (isset($taskCountAst['0']['0']['fb_tot']) && !empty($taskCountAst['0']['0']['fb_tot'])) ? $taskCountAst['0']['0']['fb_tot'] : 0;
$vt_tot = (isset($taskCountAst['0']['0']['vt_tot']) && !empty($taskCountAst['0']['0']['vt_tot'])) ? $taskCountAst['0']['0']['vt_tot'] : 0;
$links_tot = (isset($taskCountAst['0']['wwel']['links_tot']) && !empty($taskCountAst['0']['wwel']['links_tot'])) ? $taskCountAst['0']['wwel']['links_tot'] : 0;
$notes_tot = (isset($taskCountAst['0']['wwen']['notes_tot']) && !empty($taskCountAst['0']['wwen']['notes_tot'])) ? $taskCountAst['0']['wwen']['notes_tot'] : 0;
$docs_tot = (isset($taskCountAst['0']['wwed']['docs_tot']) && !empty($taskCountAst['0']['wwed']['docs_tot'])) ? $taskCountAst['0']['wwed']['docs_tot'] : 0;
$mms_tot = (isset($taskCountAst['0']['wwem']['mms_tot']) && !empty($taskCountAst['0']['wwem']['mms_tot'])) ? $taskCountAst['0']['wwem']['mms_tot'] : 0;

$prg_dec_tot = (isset($taskCountAst['0']['wdc']['dc_prg']) && !empty($taskCountAst['0']['wdc']['dc_prg'])) ? $taskCountAst['0']['wdc']['dc_prg'] : 0;
$cmp_dec_tot = (isset($taskCountAst['0']['wdc']['dc_cmp']) && !empty($taskCountAst['0']['wdc']['dc_cmp'])) ? $taskCountAst['0']['wdc']['dc_cmp'] : 0;


$nst_fb_tot = (isset($taskCountAst['0']['wfc']['fb_nst']) && !empty($taskCountAst['0']['wfc']['fb_nst'])) ? $taskCountAst['0']['wfc']['fb_nst'] : 0;
$prg_fb_tot = (isset($taskCountAst['0']['wfc']['fb_prg']) && !empty($taskCountAst['0']['wfc']['fb_prg'])) ? $taskCountAst['0']['wfc']['fb_prg'] : 0;
$ovd_fb_tot = (isset($taskCountAst['0']['wfc']['fb_ovd']) && !empty($taskCountAst['0']['wfc']['fb_ovd'])) ? $taskCountAst['0']['wfc']['fb_ovd'] : 0;
$cmp_fb_tot = (isset($taskCountAst['0']['wfc']['fb_cmp']) && !empty($taskCountAst['0']['wfc']['fb_cmp'])) ? $taskCountAst['0']['wfc']['fb_cmp'] : 0;

$nst_vot_tot = (isset($taskCountAst['0']['wvc']['vt_nst']) && !empty($taskCountAst['0']['wvc']['vt_nst'])) ? $taskCountAst['0']['wvc']['vt_nst'] : 0;
$prg_vot_tot = (isset($taskCountAst['0']['wvc']['vt_prg']) && !empty($taskCountAst['0']['wvc']['vt_prg'])) ? $taskCountAst['0']['wvc']['vt_prg'] : 0;
$ovd_vot_tot = (isset($taskCountAst['0']['wvc']['vt_ovd']) && !empty($taskCountAst['0']['wvc']['vt_ovd'])) ? $taskCountAst['0']['wvc']['vt_ovd'] : 0;
$cmp_vot_tot = (isset($taskCountAst['0']['wvc']['vt_cmp']) && !empty($taskCountAst['0']['wvc']['vt_cmp'])) ? $taskCountAst['0']['wvc']['vt_cmp'] : 0;

$deci_class = 'light-gray';
$dec_total = 0;
$dec_total_tip = 'Not Started';
if($prg_dec_tot > 0){
	$deci_class = 'yellow';
	$dec_total = $prg_dec_tot;
	$dec_total_tip = 'In Progress';
	
}else if($cmp_dec_tot > 0){
	$deci_class = 'green-bg';
	$dec_total = $cmp_dec_tot;
	$dec_total_tip = 'Completed';
}
 
$vot_class = 'light-gray';
$fb_class = 'light-gray';

$vot_total = $fb_total = 0; 

if($ovd_fb_tot > 0){
	$fb_class = 'red';
	$fb_total = $ovd_fb_tot;
}else if($prg_fb_tot > 0){
	$fb_class = 'yellow';
	$fb_total = $prg_fb_tot;
}else if($nst_fb_tot > 0){
	$fb_class = 'dark-gray';
	$fb_total = $nst_fb_tot;
}else if($cmp_fb_tot > 0){
	$fb_class = 'green-bg';
	$fb_total = $cmp_fb_tot;
}


if($ovd_vot_tot > 0){
	$vot_class = 'red';
	$vot_total = $ovd_vot_tot; 
}else if($prg_vot_tot > 0){
	$vot_class = 'yellow';
	$vot_total = $prg_vot_tot; 	
}else if($nst_vot_tot > 0){
	$vot_class = 'dark-gray';
	$vot_total = $nst_vot_tot; 
}else if($cmp_vot_tot > 0){
	$vot_class = 'green-bg';
	$vot_total = $cmp_vot_tot; 
}


//pr($taskCountAst);

 ?>

				 <div class="progress-col task_assets">
				 <div class="progress-col-heading progress-dropdown ot-progress-dropdown">
					<span class="prog-h"><a href="javascript:void(0);" data-toggle="dropdown" class="dropdown-toggle" aria-expanded="false">ASSETS <i class="arrow-down"></i></a>
					 
					 <ul class="dropdown-menu">
						 <li><a href="<?php echo SITEURL.'users/projects/'.$project_id.'?wsp='.$workspace_id; ?>"><span class="comt-dp-icon"><i class="compet-all-icon asblack"></i></span>  Task Assets</a></li>
					 </ul>					 
					 </span>
					<span class="percent-text tipText" title="Total Assets"><?php echo $total_assets; ?></span>
					</div>
					 <div class="progress-col-cont">
					  <ul class="progress-assets progress-col-cont">
						<li>
						<span class="assets-count blue tipText <?php if(!isset($links_tot) || $links_tot == 0){ echo 'zero_class'; }?>" title="Total Links"><?php echo $links_tot; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-LinkBlack tipText" title="Links"></i> </span>
						</li> 
						
						  <li>
						<span class="assets-count blue tipText <?php if(!isset($notes_tot) || $notes_tot == 0){ echo 'zero_class'; }?>" title="Total Notes"><?php echo $notes_tot; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-NoteBlack tipText" title="Notes"></i> </span>
						</li>
						<li>
						<span class="assets-count blue  tipText <?php if(!isset($docs_tot) || $docs_tot == 0){ echo 'zero_class'; }?>" title="Total Documents"><?php echo $docs_tot; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-DocumentBlack tipText" title="Documents"></i> </span>
						</li>
						  <li>
						<span class="assets-count blue tipText <?php if(!isset($mms_tot) || $mms_tot == 0){ echo 'zero_class'; }?>" title="Total Mind Maps"><?php echo $mms_tot; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-MindMapBlack tipText" title="Mind Maps"></i> </span>
						</li>
						  <li>
						<span class="assets-count <?php echo $deci_class; ?> cost-tooltip <?php if(!isset($dec_total) || $dec_total == 0){ echo 'zero_class'; }?>" title="<?php echo $dec_total_tip; ?> "><?php echo $dec_total; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-DecisionBlack tipText" title="Decisions"></i> </span>
						</li>
						 <li>
						<span class="assets-count <?php echo $fb_class; ?> cost-tooltip <?php if(!isset($fb_total) || $fb_total == 0){ echo 'zero_class'; }?>" title="<?php echo $cmp_fb_tot; ?> Completed <br /> <?php echo $ovd_fb_tot; ?> Overdue <br /> <?php echo $prg_fb_tot; ?> In Progress <br /> <?php echo $nst_fb_tot; ?> Not Started ""><?php echo $fb_total; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-FeedbackBlack tipText" title="Feedback"></i> </span>
						</li> 
						
						 <li>
						<span class="assets-count  <?php echo $vot_class; ?> cost-tooltip <?php if(!isset($vot_total) || $vot_total == 0){ echo 'zero_class'; }?>" title="<?php echo $cmp_vot_tot; ?> Completed <br /> <?php echo $ovd_vot_tot; ?> Overdue <br /> <?php echo $prg_vot_tot; ?> In Progress <br /> <?php echo $nst_vot_tot; ?> Not Started "><?php echo $vot_total; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-VoteBlack tipText" title="Votes"></i> </span>
						</li> 
						  
					  </ul>
					 </div>

				 </div>	
<script>
$(function(){	
	
$('.cost-tooltip').tooltip({
'placement': 'top',
'container': 'body',
'html': true
})

})
</script>