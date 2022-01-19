
<style>
#Recordlisting .tipText, .tipText > a:hover{
	text-transform:lowercase !important;
}
#Recordlisting .box-header p > a{
	text-transform:lowercase !important;
}
</style>
<div class="modal modal-success fade " id="Recordedit" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content"></div>
	</div>
</div><!-- /.modal -->
<!-- MODAL BOX WINDOW -->
<div class="modal modal-success fade " id="modal_box" tabindex="-1" role="dialog" aria-labelledby="modalBoxModelLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>
<!-- END MODAL BOX -->
<div class="row">
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">
                <h1 class="pull-left"><?php echo $viewData['page_heading']; ?> (<?php echo ( isset($listDomainUsers) && !empty($listDomainUsers) ) ? count($listDomainUsers) : 0;?>)
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
					<div class="box-header">
						<div class="col-sm-12 col-md-4">
							<p style="margin:0; padding-top:11px;">Domain:

							<?php
								echo "<a title='https://".$subdomain.WEBDOMAIN."' target='_blank' href='https://".$subdomain.WEBDOMAIN."' class='customtipText'>".$subdomain.WEBDOMAIN."</a>";
							?></p>
						</div>
						<div class="col-sm-12 col-md-8 filter-email">
							<div class="pull-right padright">
								<span style="float:left; padding-right:10px;padding-top: 7px;">Filter Email Domains: &nbsp;</span>
							<div class="filter-email-inside">
							<span style="float:left; padding-right:20px;">
								<?php //$edomains = $this->Common->getOrgEmailDomain($this->Session->read('Auth.User.id'));

								$selected = isset($this->request->params['pass'][1]) ? $this->request->params['pass'][1] : '';
								echo $this->Form->input('User.managedomain_id ', array('options' => $listEmailDomains, 'empty' => 'All Domains', 'label' => false, 'div' => false, 'selected'=>$selected, 'onchange' => 'searchUsers(this.value)','class' => 'form-control')); ?></span>

							<?php
							$whatINeed = explode('.', $_SERVER['HTTP_HOST']);
							$whatINeed = $whatINeed[0];
							if($whatINeed.WEBDOMAIN =="ibmindia.ideascast.com") {

							?>
					   <?php echo $this->Paginator->sort('UserDetail.first_name', __("Sort Desc."), array('class' => 'btn btn-primary'));  ?>
								   <a class="btn btn-primary searchbtn " data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
								Search
							</a>
								<?php } ?>

							</div>
							</div>
						</div>


					</div><!-- /.box-header -->
					<div class="box-body table-responsive">
						<table id="example2" class="table table-bordered table-hover">
							<thead>
								<tr>
									<th><?php echo __("SN");?></th>
									<th><?php echo __("User");?></th>
									<!-- <th><?php echo __("Last Name");?></th>-->
									<th><?php echo __("Email Address");?></th>
									<th><?php echo __("Status");?></th>
									<?php /* <th><?php echo __("Enabled");?></th>
									<th><?php echo __("Locked");?></th> */ ?>
									<th><?php echo __("Administrator");?></th>
									<th><?php echo __("Action");?></th>
								</tr>
							</thead>
							<tbody id="tbody_skills">
								<?php
									if (!empty($listDomainUsers)) {
										$icount = 1;
										foreach ($listDomainUsers as $listdomainusers){


                                ?>
								<tr data-id="<?php echo $listdomainusers['id']; ?>">

									<td><?php echo __($icount);?></td>
									<td><?php echo ucfirst($listdomainusers['first_name'].' '.$listdomainusers['last_name']);//."==".$listdomainusers['id'];?></td>
									<!-- <td><?php echo $listdomainusers['last_name'];?></td> -->
									<td><?php echo $listdomainusers['email'];?></td>
									<td>
										<?php
											$clasificationId = $listdomainusers['id'];
											if($listdomainusers['status'] == 1){ ?>
												<button rel="deactivate" id="<?php echo $clasificationId; ?>"  class="btn btn-default disabled"><i class="fa fa-fw fa-check alert-success"></i></button>
										<?php } else { ?>
												<button  rel="activate" id="<?php echo $clasificationId; ?>"  class="btn btn-default disabled"><i class="fa fa-fw fa-times alert-danger"></i></button>
										<?php }	?>
									</td>
									<td><?php
											$clasificationId = $listdomainusers['id'];
											if($listdomainusers['administrator'] == 1){ ?>
												<button rel="deactivate" data-userdetailid="<?php echo $listdomainusers['id'];?>" id="<?php echo $clasificationId; ?>"  class="btn btn-default disabled " title=""><i class="fa fa-fw fa-check alert-success" ></i></button>
										<?php } else { ?>
												<button rel="activate" data-userdetailid="<?php echo $listdomainusers['id'];?>" id="<?php echo $clasificationId; ?>"  class="btn btn-default disabled"><i class="fa fa-fw fa-times alert-danger"></i></button>
										<?php } ?>
									</td>
									<td>
										<a class="tipText disabled" title="Editing Not Allowed" data-tooltip="tooltip" data-placement="top" style="cursor:pointer;" ><i class="fa fa-fw fa-edit"></i></a>
										<a class="disabled tipText" title="Deleting Not Allowed"  data-tooltip="tooltip" data-placement="top" style="cursor:pointer;"><i class="fa fa-fw fa-trash-o"></i></a>
									</td>
								</tr>
								<?php
									$icount++;
								} //end foreach
								/*
								if($this->params['paging']['User']['pageCount'] > 1) { ?>
								<tr>
                                    <td colspan="6" align="right">
									<ul class="pagination">
										<?php echo $this->Paginator->prev('« Previous', array('class' => 'prev'), null, array('class' => 'disabled', 'tag'=>'li')); ?>
										<?php echo $this->Paginator->numbers(array('currentClass' => 'avtive', 'Class' => '', 'tag'=>'li', 'separator'=>'')); ?>
										<?php echo $this->Paginator->next('Next »',  array('class' => 'next'), null, array('class' => 'disabled', 'tag'=>'li')); ?>
									</ul>
									</td>
								</tr>
								<?php } */ ?>

								<?php } else { ?>
								<tr>
                                    <td colspan="6" style="color:RED;text-align: center;">No Records Found!</td>
								</tr>
                                    <?php
										}
									?>
							</tbody>
						</table>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
				<!------ Add New Classification ------>
				<div class="modal fade" id="deleteBox" tabindex="-1" role="dialog" aria-hidden="true"></div><!-- /.modal -->
			</div></div>
		</section>
					</div>
				</div>
			</div>
		 </div>
	   </div>
	</div>
 </div>
<link rel="stylesheet" type="text/css" href="/css/front-paging.css" />
<script type="text/javascript" >

function searchUsers(domainname){

	window.location.href='<?php echo SITEURL?>organisations/client_manage_users/<?php echo $this->request->params['pass'][0];?>/'+domainname;
	//location.reload();

}

$(function(){

	$('.customtipText').tooltip({
		template: '<div class="tooltip CUSTOM-CLASS" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>'
		, 'container': 'body', 'placement': 'top',
	})

	setTimeout(function(){

		$('#successFlashMsg').hide('slow', function(){ $('#successFlashMsg').remove() })

	},4000);


	$('input[name="checkAll"]').removeAttr('checked');

	// Sorting with drag and drop
	var fixHelperModified = function(e, tr) {
        var $originals = tr.children();
        var $helper = tr.clone();
        $helper.children().each(function(index)
        {
          $(this).width($originals.eq(index).width())
        });
        return $helper;
    };


	/*===================================================*/


})
</script>