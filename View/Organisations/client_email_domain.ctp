
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
                <h1 class="pull-left"><?php echo $viewData['page_heading']; ?>
                    <p class="text-muted date-time" style="padding: 4px 0px; text-transform: none;">
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
						<div class="col-sm-12">
							<p class="domain-manage">Domain:
							<?php
								echo "<a title='https://".$subdomain.WEBDOMAIN."' target='_blank' href='https://".$subdomain.WEBDOMAIN."' class='customtipText'>".$subdomain.WEBDOMAIN."</a>";
							?></p>
						</div>

					</div><!-- /.box-header -->
					<div class="box-body table-responsive">
						<table id="example2" class="table table-bordered table-hover">
							<thead>
								<tr>
									<th><?php echo __("SN");?></th>
									<th><?php echo __("Email Domain");?></th>
									<th><?php echo __("Created by");?></th>
									<?php /*<th><?php echo __("Create Account");?></th> */ ?>
									<th><?php echo __("Enabled");?></th>
									<th><?php echo __("Action");?></th>
								</tr>
							</thead>
							<tbody id="tbody_skills">
								<?php

									if (!empty($listdomain)) {
										$icount = 1;
										foreach ($listdomain as $listdomains){

										$userDetail = $this->ViewModel->get_user_data( $listdomains['user_id'] );
										$user_name = '';
										if(isset($userDetail) && !empty($userDetail)) {
											$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
										}

                                ?>

								<tr data-id="<?php echo $listdomains['id']; ?>">

									<td><?php echo __($icount);?></td>
									<td><span class="tipText" title="<?php echo "Total user ".$this->Common->getclientEmailDomainUser($subdomain,$listdomains['id']);?>" style="text-transform: none !important;"><a href="<?php echo SITEURL?>organisations/client_manage_users/<?php echo $subdomain.'/'.$listdomains['id'];?>"><?php echo $listdomains['domain_name'];?></a></span></td>
									<td><?php echo $user_name;?></td>
									<td><?php
											$clasificationId = $listdomains['id'];
											if($listdomains['create_account'] == 1){ ?>
												<button rel="deactivate" id="<?php echo $clasificationId; ?>"  class="btn btn-default disabled"><i class="fa fa-fw fa-check alert-success"></i></button>
										<?php } else { ?>
												<button  rel="activate" id="<?php echo $clasificationId; ?>"  class="btn btn-default disabled"><i class="fa fa-fw fa-times alert-danger"></i></button>
										<?php } ?>
									</td>
									<td>
										<a class="tipText disabled" title="Editing not allowed"   data-tooltip="tooltip" data-placement="top" style="cursor:pointer;" ><i class="fa fa-fw fa-edit"></i></a>
										<a class="tipText disabled" title="Deletion not allowed" data-tooltip="tooltip" data-placement="top" style="cursor:pointer;"><i class="fa fa-fw fa-trash-o"></i></a>
									</td>
								</tr>
							<?php
									$icount++;
							} //end foreach
							/*if($this->params['paging']['ManageDomain']['pageCount'] > 1) {
							?>
								<tr>
                                    <td colspan="6" align="right">
									<ul class="pagination">
										<?php echo $this->Paginator->prev('« Previous', array('class' => 'prev'), null, array('class' => 'disabled', 'tag'=>'li')); ?>
										<?php echo $this->Paginator->numbers(array('currentClass' => 'avtive', 'Class' => '', 'tag'=>'li', 'separator'=>'')); ?>
										<?php echo $this->Paginator->next('Next »',  array('class' => 'next'), null, array('class' => 'disabled', 'tag'=>'li')); ?>
									</ul>
									</td>
								</tr>
							<?php } */
							} else { ?>
								<tr>
                                    <td colspan="5" style="color:RED;text-align: center;">No Records Found!</td>
								</tr>
							<?php }   ?>
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

$(function(){

	$('.customtipText').tooltip({
		template: '<div class="tooltip CUSTOM-CLASS" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>'
		, 'container': 'body', 'placement': 'top',
	})

	setTimeout(function(){

		$('#successFlashMsg').hide('slow', function(){ $('#successFlashMsg').remove() })

	},4000);


	$('body').delegate('#ManageDomainDomainName1', 'keypress', function(event){

		var englishAlphabetAndWhiteSpace = new RegExp('^[a-zA-Z0-9]$');
		var key = String.fromCharCode(event.which);

		if (event.keyCode == 8 || event.keyCode == 37 || event.keyCode == 39 || englishAlphabetAndWhiteSpace.test(key)) {
			$("#add_edit_msg").html("");
			return true;
		}else {
			$("#add_edit_msg").removeClass('text-green').addClass('text-red').html("Special Characters and white spaces are not allowed");
		}
		return false;

	});


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

});



</script>