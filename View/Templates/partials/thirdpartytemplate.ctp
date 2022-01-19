<?php 
//pr($thirdpartyUsers);
if(isset($thirdpartyUsers) && !empty($thirdpartyUsers) ){ 
	foreach($thirdpartyUsers as $userlist){	
 
?>	
	<div class="panel panel-default third_party_template" id="panel-id-<?php echo $userlist['ThirdParty']['id'];?>">
            <div class="panel-heading bg-green" role="tab" id="headingOne-<?php echo $userlist['ThirdParty']['id'];?>">
                <h4 data-original-title="<?php echo ucfirst($userlist['ThirdParty']['username']); ?>" class="panel-title  tipText">
                    <a role="button" style="font:11px;"  href="#collapseOne-<?php echo $userlist['ThirdParty']['id'];?>" class="third-party-user closed" data-partyuserid="<?php echo $userlist['ThirdParty']['id'];?>" aria-expanded="true" aria-controls="collapseOne-<?php echo $userlist['ThirdParty']['id'];?>">
                        <i style="color:#fff;" class="more-less fa"></i>
                        <?php echo ucfirst($userlist['ThirdParty']['username']); ?>
                    </a>
                </h4>
            </div>
            <div id="collapseOne-<?php echo $userlist['ThirdParty']['id'];?>" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne-<?php echo $userlist['ThirdParty']['id'];?>">
                <div class="panel-body" style=" overflow:auto;max-height:250px;">                    
                    <?php
					$userTemplate = $this->Template->thirdPartyUserTemplate($userlist['ThirdParty']['id'], $template_category_id);
					//pr($userTemplate);
                    
                    if(isset($userTemplate) && !empty($userTemplate)) {
							foreach($userTemplate as $templateList){
                            ?>
							<div class="pull-left">
                                <?php  $des ="";                                  
                                    if (isset($templateList['TemplateRelation']['description']) && !empty($templateList['TemplateRelation']['description'])) {
                                        $des = $templateList['TemplateRelation']['description'];
                                    }
								 
                                ?>
                                    <span style="margin:0 5px;margin-top:9px;" class="pull-right clickable panel-collapsed  pophover" data-placement="top"  data-toggle="popover" data-trigger="hover"  data-content="<?php echo $des ?>" data-project="2"><i class="fa fa-info fa-3 martop save_as"></i></span>
                            </div>
                            <div class="edited-template" id="edited-template-saveas-<?php echo $templateList['TemplateRelation']['id']; ?>">
                                <div class="edited-template-right">
								
                                    <a class="tipText" href="javascript:" data-original-title="<?php echo strip_tags($templateList['TemplateRelation']['title']); ?>" >
                                        <?php echo strip_tags($templateList['TemplateRelation']['title']); ?>
                                    </a>                                                                                                   
                                </div>
                            </div>
				<?php }
				} else { ?>
					<div class="edited-template text-center">
						No Knowledge Templates
					</div>
				<?php
				}
				?>
                </div>
            </div>
        </div>
<?php  }
	} else { ?>	
	<div class="panel panel-default">
        <div class="panel-heading bg-white" role="tab" id="headingOne">
            <h4 class="panel-title text-center"><a> No Knowledge Templates</a></h4>
        </div>
    </div>
<?php } ?>	

<script type="text/javascript">
$(function(){
	$('a[href="#"],a[href=""]').attr('href', 'javascript:;');	
	$('.template_pophover').popover({
        placement : 'top',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });	
	
/* 	$('.pophover').popover({
        placement : 'top',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    }); */
 });
	
</script>	