<!-- END MODAL BOX -->
<div class="row">
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">
                <h1 class="pull-left"><?php echo $viewData['page_heading']; ?>
                    <p class="text-muted date-time" style="padding: 4px 0px;">
                        <span style="text-transform: none;"><?php echo $viewData['page_subheading']; ?></span>
                    </p>
                </h1>
            </section>
		</div>
		<!-- END HEADING AND MENUS -->

	<!-- Content Header (Page header) -->
	 <div class="box-content">
		<div class="row">
			<div class="col-xs-12">
			
				<div class="box noborder-top">
						<?php echo $this->Session->flash(); ?>
						<section class="box-body no-padding">
							<?php $class = 'collapse';
									if(isset($in) && !empty($in)){
										$class = 'in';
									}
							?>
							<div class="row" id="Recordlisting">
								<div class="col-xs-12">
									<div class="box no-box-shadow box-success">					
										<div class="box-body table-responsive">
											<table id="example2" class="table table-bordered table-hover">
												<thead>
													<tr>
														<th>SN</th>
														<th><?php echo __("Linked Domains");?></th>
														<th><?php echo __("Link");?></th>
														<th><?php echo __("Action");?></th>
													</tr>
												</thead>	
												<tbody id="tbody_skills">
													<?php 
													
													if (!empty($listDomain)) {										
													$icount = 1;										
													foreach ($listDomain as $domainlist){
															
													if( $whatINeed != $domainlist['OrgSetting']['subdomain']  ){
													?>
													<tr data-id="<?php echo $domainlist['OrgSetting']['id']; ?>">
														
														<td><?php echo $icount;?></td>								
														<td><?php echo $domainlist['OrgSetting']['subdomain'];?></td>
														<td><a href="https://<?php echo $domainlist['OrgSetting']['subdomain'].WEBDOMAIN;?>" target="_blank">https://<?php echo $domainlist['OrgSetting']['subdomain'].WEBDOMAIN;?></a></td>
														<td><a href="<?php echo SITEURL;?>organisations/client_email_domain/<?php echo $domainlist['OrgSetting']['subdomain'];?>" class="tipText" title="Linked Domain Emails"><i class="fa fa-globe"></i></a> <a href="<?php echo SITEURL;?>organisations/client_manage_users/<?php echo $domainlist['OrgSetting']['subdomain'];?>" class="tipText" title="Linked Domain Users"><i class="fa fa-users"></i></a> </td>
													</tr>
													<?php }
														$icount++;
													} //end foreach
													
													if($this->params['paging']['OrgSetting']['pageCount'] > 1) { ?> 
													<tr>
														<td colspan="3" align="right">
														<ul class="pagination">
															<?php echo $this->Paginator->prev('« Previous', array('class' => 'prev'), null, array('class' => 'disabled', 'tag'=>'li')); ?>
															<?php echo $this->Paginator->numbers(array('currentClass' => 'avtive', 'Class' => '', 'tag'=>'li', 'separator'=>'')); ?>
															<?php echo $this->Paginator->next('Next »',  array('class' => 'next'), null, array('class' => 'disabled', 'tag'=>'li')); ?>
														</ul>
														</td>
													</tr>
													<?php } ?>
													
													<?php } else { ?>
													<tr>
														<td colspan="6" style="text-align: center;">No Linked Domains.</td>
													</tr>
														<?php } ?>
												</tbody>							
											</table>
										</div><!-- /.box-body -->
									</div><!-- /.box -->				
								</div>
							</div>
						</section>
					</div>
				</div>
			</div>
		 </div>
	   </div>
	</div>
 </div>
<script type="text/javascript" >
$(function(){	

	setTimeout(function(){	
	
		$('#successFlashMsg').hide('slow', function(){ $('#successFlashMsg').remove() })
		
	},3000)
	
});	
</script>	