

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
                <h1 class="pull-left"><?php   echo $page_heading; ?> (<?php echo $this->Common->totalDataS('Subject'); ?>)
                    <p class="text-muted date-time" style="padding: 4px 0px;">
                        <span style="text-transform: none;"><?php echo $page_subheading; ?></span>
                    </p>
                </h1>
            </section>
		</div>
		<!-- END HEADING AND MENUS -->

	<!-- Content Header (Page header) -->
	 <div class="box-content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box  noborder-top">

				<?php echo $this->Session->flash(); ?>

		<section class="box-body no-padding">
			<?php
			$class = 'collapse';
			if(isset($in) && !empty($in)){
				$class = 'in';
			}
			$per_page_show = $this->Session->read('user.per_page_show');

			if( !empty($this->request->params['named']['search']) ){
				$keyword = $this->request->params['named']['search'];
			} else {
				$keyword = $this->Session->read('user.keyword');
			}

			?>
		<div class="row" id="Recordlisting">
            <div class="col-xs-12">
				<div class="box no-box-shadow box-success">
					<div class="box-header">

						<div class="pull-right padright">
						<?php

							$url = array('controller' => 'subjects','action' => 'index');
							if( $this->params['paging']['Subject']['page'] > 1 ){
								$my_params = array(
									'sort'=>'Subject.title',
									'direction' => 'desc'
								);
							} else {
								$my_params = array(
									'sort'=>'Subject.title',
									'direction' => 'desc'
								);
							}
							$sortUrl = $this->Html->url(array_merge($url, $my_params));
							$sortText = 'Sort Desc';

							if(isset($this->params['named']['sort']) && !empty($this->params['named']['sort']) && ($this->params['named']['direction'] == 'desc' || $this->params['named']['direction'] == 'DESC' )){

								if( $this->params['paging']['Subject']['page'] > 1 ){
									$my_params = array(
										'sort'=>'Subject.title',
										'direction' => 'asc'
									);
								} else {
									$my_params = array(
										'sort'=>'Subject.title',
										'direction' => 'asc'
									);
								}
								$sortUrl = $this->Html->url(array_merge($url, $my_params));
								$sortText = 'Sort ASC';

							}

							//echo $sortUrl;

							//pr($this->params['named']['sort']);
							//pr($this->params['named']['direction']);

						?>
							<a class="btn btn-primary edituser" data-toggle="modal" data-target="#modal_box" data-remote="<?php echo Router::url(['controller' => 'subjects', 'action' => 'manage_subject', 'admin' => false], true); ?>" >
								Add
							</a>&nbsp;<a class="btn btn-primary deletemultiple" >
								Delete
							</a>&nbsp;
							<a class="btn btn-primary" href="<?php echo $sortUrl; ?>" >
								<?php echo $sortText; ?>
							</a>&nbsp;<a class="btn btn-primary searchbtn" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
								Search
							</a>
						</div>

						<div class="<?php echo $class; ?> search pull-right" style="width: 100%; margin-top: 10px;" id="collapseExample">
							<div class="well">
								<?php

								if(isset($this->params['named']['sortorder']) && !empty($this->params['named']['sortorder'])){
									$formAction = SITEURL."subjects/index/page:".$this->params['paging']['Subject']['page']."/sortorder:".$this->params['named']['sortorder'];
								} else {
									$formAction = SITEURL."subjects/";
								}

								echo $this->Form->create('Subject', array( 'url' => $formAction, 'type' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'search_page_show_form')); ?>
									<div class="modal-body">
										<div class="form-group">
											<div class="col-xs-8 col-sm-8 col-md-8 col-lg-3" style="padding-right: 0">
												<?php
												echo $this->Form->input('keyword', array('placeholder' => 'Search for Subject…','type' => 'text','label' => false, 'value'=>$keyword,'div' => false, 'class' => 'form-control')); ?>
											</div>
											<div   style="text-align:right;float:left;">
												<button style="border-radius: 0 3px 3px 0" type="submit" class="searchbtn btn btn-success"><i class="fas fa-search"></i></button>
												<a class="btn btn-primary searchbtn" href="<?php echo Router::url(['controller' => 'subjects', 'action' => 'subject_resetfilter', 'admin' => false], true); ?>" >Close</a>
											</div>
										</div>
									</div>
								<?php echo $this->Form->end(); ?>
							</div>
						</div>
					</div><!-- /.box-header -->
					<div class="box-body table-responsive">
						<table id="example2" class="table table-bordered table-hover">
							<thead>
								<tr>
									<th><input type="checkbox" name="checkAlltop" id="checkSubjects" /></th>
									<th><?php echo $this->Paginator->sort('Subject.title',__("Subject"));?></th>
									<th><?php echo $this->Paginator->sort('Subject.status',__("Status"));?></th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody id="tbody_subjects">
								<?php
									if (!empty($subjects)) {
										$num = $this->Paginator->counter(array('format' => ' %start%'));
										$icount = 0;
										foreach ($subjects as $subject){
                                ?>
								<tr <?php if($this->params['paging']['Subject']['page']==1 && $icount == 0){ echo 'data-first="true"'; }?> <?php if($this->params['paging']['Subject']['page']==$this->params['paging']['Subject']['pageCount'] && $icount == (count($subjects)-1) ){ echo 'data-last="true"'; }?>  data-id="<?php echo $subject['Subject']['id']; ?>" data-order="<?php echo $subject['Subject']['sorder']; ?>">
									<td><?php //echo $num ?><input type="checkbox" class="checkSubjectList" name="checkAll" value="<?php echo $subject['Subject']['id']; ?>" /></td>
									<td><?php echo $subject['Subject']['title']; ?>
									</td>
									<td>
										<?php
											$clasificationId = $subject['Subject']['id'];
											if ($subject['Subject']['status'] == 1) { ?>
												<button rel="deactivate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-check alert-success"></i></button	>
										<?php	} else {
											 ?>
												<button  rel="activate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-times alert-danger"></i></button>
										<?php	}	?>

									</td>

									<td>
										<a data-toggle="modal" class="edituser tipText" data-target="#modal_box" title="Edit Subject" data-remote="<?php echo Router::url(['controller' => 'subjects', 'action' => 'subject_edit', $subject['Subject']['id'], 'admin' => false], true); ?>"  data-tooltip="tooltip" data-placement="top" style="cursor:pointer;" ><i class="fa fa-fw fa-edit"></i></a>

										<a class="RecordDeleteClass tipText" rel="<?php echo $subject['Subject']['id']; ?>" title="Delete Subject" data-whatever="<?php echo Router::url(['controller' => 'subjects', 'action' => 'subject_delete', 'admin' => false], true); ?>"  data-tooltip="tooltip" data-placement="top" style="cursor:pointer;"><i class="fa fa-fw fa-trash-o"></i></a>
									</td>
								</tr>
								<?php
											$num++;
											$icount++;
										}//end foreach
								?>
								<?php } else { ?>
								<tr>
                                    <td colspan="6" style="color:RED;text-align: center;">No Subjects Found!</td>
								</tr>
                                    <?php
										}
									?>
							</tbody>
							<tfoot>
								<?php
								if($this->params['paging']['Subject']['pageCount'] > 1) { ?>
								<tr>
                                    <td colspan="8" align="right">

										<div class="pagination-summary pull-left" >
											<?php
												echo $this->Paginator->counter(array(
												'format' => __('{:current} of {:count} &nbsp;/&nbsp; Page {:page} of {:pages}')
												));
											?>
										</div>
										<ul class="pagination">
											<?php
											echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li', 'class' => 'disabled', 'disabledTag' => 'span')); ?>
											<?php //echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li', 'disabledTag' => 'span', 'first' => 1));?>
											<?php echo $this->Paginator->numbers(array('first'=>1,'last'=>1,'ellipsis'=>'<li><a>...</a></li>','modulus'=>3,'tag' => 'li','separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li'), null, array('tag' => 'li', 'class' => 'disabled', 'disabledTag' => 'span'));?>
											<?php echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'span')); ?>
										</ul>

									</td>
								</tr>
								<?php } ?>
							</tfoot>
						</table>
					</div><!-- /.box-body -->
				</div><!-- /.box -->

				<div class="modal fade" id="deleteBox" tabindex="-1" role="dialog" aria-hidden="true">

				</div><!-- /.modal -->
        </div></div>
    </section>
					</div>
				</div>
			</div>
		 </div>
	   </div>
	</div>
 </div>
<script type="text/javascript" >
var currentPage = "<?php echo $this->params['paging']['Subject']['page']; ?>";

$(function(){

	$('#modal_box, #RecordFormedit').on('shown.bs.modal', function(){
		$('#SubjectTitle').focus();
	})

	$('input[name="checkAll"]').removeAttr('checked');
	$('#checkSubjects').attr('checked',false);

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

	var order = {};
	$('#example2 tbody tr').each( function(e) {
		order[$(this).index() ] =  $(this).attr('data-id');
	});
	$( "#example2sssss tbody" ).sortable({
		revert: true,
		helper: fixHelperModified,
		start:function(event, ui) {


		},
		update: function(event, ui) {

			console.log(order);

			var allCheckbox = {};
			var sortOrder = '<?php echo isset($this->params['named']['sortorder']) ? $this->params['named']['sortorder'] : '';?>';
			$('#example2 tbody tr').each( function(e) {
			//
				//order[$(this).index() ] =  $(this).attr('data-id');
				allCheckbox[e] = $(this).attr('data-order');

			});

			$.ajax({
				url : $js_config.base_url + 'subjects/subject_DragDrop',
				async:false,
				global: false,
				type:"POST",
				data:$.param({subject_id:order, subject_order:allCheckbox, sorder:sortOrder}),
				success:function(response){
					location.reload();
				}
			})
		}
	});

//=========================================================================


	$('.checkSubjectList', $('tr[data-first]') ).on('change', function(){
		if( $(this).prop('checked') == true ){
			$('.subjectMoveUp').addClass('disabled');
		} else {
			$('.subjectMoveUp').removeClass('disabled');
		}
	})

	$('.checkSubjectList', $('tr[data-last]') ).on('change', function(){
		if( $(this).prop('checked') == true ){
			$('.subjectMoveDown').addClass('disabled');
		} else {
			$('.subjectMoveDown').removeClass('disabled');
		}
	})

});


$(".subjectMoveUp").click(function(){
	var movetos = 'TOP';
	var chklgth = 0;
	var msgchk = '';
	var allCheckbox = [];
	var checkedBox = '';
	var sortOrder = '<?php echo isset($this->params['named']['direction']) ? $this->params['named']['direction'] : '';?>';

	checkedBox = $('input[name="checkAll"]:checked').val();
	chklgth =  $('input[name="checkAll"]:checked').length;

	$('.checkSubjectList').each(function(i) {
		allCheckbox[i] = this.value;
	});

	if( chklgth > 1 ){
		msgchk = 'You can not move multiple subject at a time.';
	} else if( chklgth < 1 ) {
		msgchk = 'Please select atleast one subject.';
	} else {

		$.ajax({
		url : $js_config.base_url + 'subjects/subject_swap',
		async:false,
		global: true,
		type:"POST",
		data:$.param({subject_id: allCheckbox, moveto: movetos, movetoid: checkedBox, sortorder: sortOrder}),
		success:function(response){
				if($.trim(response) != 'SUCCESS'){
					$('#Recordedit').html(response);
				}else{
					$('input[name="checkAll"]').removeAttr('checked');
					location.reload(); // Saved successfully
				}
			}
		});

	}
	//console.log(msgchk+" "+chkValue);
	//console.log(msgchk);

});

$(".subjectMoveDown").click(function(){
	var movetos = 'DOWN';
	var chklgth = 0;
	var msgchk = '';
	var allCheckbox = [];
	var checkedBox = '';
	var sortOrder = '<?php echo isset($this->params['named']['direction']) ? $this->params['named']['direction'] : '';?>';
	checkedBox = $('input[name="checkAll"]:checked').val();
	chklgth =  $('input[name="checkAll"]:checked').length;

	$('.checkSubjectList').each(function(i) {
		allCheckbox[i] = this.value;
	});

	if( chklgth > 1 ){
		msgchk = 'You can not move multiple subject at a time.';
	} else if( chklgth < 1 ) {
		msgchk = 'Please select atleast one subject.';
	} else {

		$.ajax({
		url : $js_config.base_url + 'subjects/subject_swap',
		async:false,
		global: true,
		type:"POST",
		data:$.param({subject_id:allCheckbox, moveto:movetos,movetoid:checkedBox,sortorder:sortOrder}),
		success:function(response){
				if($.trim(response) != 'SUCCESS'){
					$('#Recordedit').html(response);
				}else{
					$('input[name="checkAll"]').removeAttr('checked');
					location.reload();

				}
			}
		});
	}
	//console.log(msgchk);

});

	// Used for Sorting icons on listing pages
	$('th a').append(' <i class="fa fa-sort"></i>');
	$('th a.asc i').attr('class', 'fa fa-sort-down');
	$('th a.desc i').attr('class', 'fa fa-sort-up');

	// Status click Update
	$(document).on('click', '.RecordUpdateClass5', function(){
		id = $(this).attr('id');
		rel = $(this).attr('rel');

		$('#RecordStatusFormId').attr('action', $js_config.base_url + 'subjects/subject_updatestatus');
		$('#recordID').val(id);
		if(rel == 'activate'){
			$('#recordStatus').val(1);
		}else{
			$('#recordStatus').val(0);
		}
		$('#statusname').text(rel);

	});

	$(document).on('click', '.RecordUpdateClass', function(event){
		event.preventDefault();

		$that = $(this);
		var id = $that.attr('id');
		var rel = $that.attr('rel');

		var deleteURL = $js_config.base_url + 'subjects/subject_updatestatus';

		$('#recordID').val(id);
		if(rel == 'activate'){
			$('#recordStatus').val(1);
		}else{
			$('#recordStatus').val(0);
		}
		$('#statusname').text(rel);

		BootstrapDialog.show({
			title: 'Confirmation',
			message: 'Are you sure you want to change the status?',
			type: BootstrapDialog.TYPE_DANGER,
			draggable: true,
			buttons: [
			{
				//icon: '',
				label: ' Yes',
				cssClass: 'btn-success',
				autospin: true,
				action: function (dialogRef) {
					$.when(
						$.ajax({
							url : deleteURL,
							type: "POST",
							data: $.param({id:id,status:$('#recordStatus').val()}),
							global: true,
							async:false,
							success:function(response){ }
						})
					).then(function( data, textStatus, jqXHR ) {

						if($.trim(data) != 'success'){

							$('#Recordedit').html(data);

						}else{
							location.reload();
						}

						dialogRef.enableButtons(false);
						dialogRef.setClosable(false);
					})
				}
			},
			{
				label: ' No',
				//icon: '',
				cssClass: 'btn-danger',
				action: function (dialogRef) {
					dialogRef.close();
				}
			}
			]
		});


	});


$(function(){

		$(".checkSubjectList").each(function(){

			if( $(this).prop('checked') == true ) {
				$(this).parents('tr:first').css('background-color', '#EFFFFE')
			}
			else {
				$(this).parents('tr:first').css('background-color', '#ffffff')
			}

		})

		$("#checkSubjects").click(function(){

			var status = this.checked;
			$('.checkSubjectList').each(function(){
				this.checked = status;
				$(this).parents('tr:first').css('background-color', '#ffffff')
			});

		})

		$(".checkSubjectList").click(function(){

			//uncheck "select all", if one of the listed checkbox item is unchecked
			if(false == $(this).prop("checked")){
				$("#checkSubjects").prop('checked', false);
			}

			//check "select all" if all checkbox items are checked
			if ($('.checkSubjectList:checked').length == $('.checkSubjectList').length ){
				$("#checkSubjects").prop('checked', true);
			}

			// change parent row background-color on checked
			if( $(this).prop('checked') == true ) {
				$(this).parents('tr:first').css('background-color', '#EFFFFE')
			}
			else {
				$(this).parents('tr:first').css('background-color', '#ffffff')
			}

		});

		$(".RecordDeleteClass").click(function(event){
			event.preventDefault();

			$that = $(this);
			var row = $that.parents('tr:first');

			var deleteURL = $(this).attr('data-whatever'); // Extract info from data-* attributes
			var deleteid = $(this).attr('rel');

			BootstrapDialog.show({
				title: 'Confirmation',
				message: 'Are you sure you want to remove this Subject?',
				type: BootstrapDialog.TYPE_DANGER,
				draggable: true,
				buttons: [
				{
					//icon: '',
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: true,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url : deleteURL,
								type: "POST",
								dataType: "JSON",
								data: $.param({subject_id: deleteid, deltype: 'single'}),
								global: false,
								async:false,
								success:function(response){

								}
							})
						).then(function( data, textStatus, jqXHR ) {

							if(!data.success){

								$('#Recordedit').html(data);

							}else{

								$that.closest('tr').css('background-color','#FFBFBF');
								$that.closest('tr').remove();
								dialogRef.enableButtons(false);
								dialogRef.setClosable(false);
								// dialogRef.getModalBody().html('<div class="loader"></div>');
								setTimeout(function () {
									dialogRef.close();
									if(currentPage > data.page){
										currentPage = data.page;
									}

									if(currentPage > 1){
										window.location.href = $js_config.base_url + 'subjects/index/page:'+currentPage;
									}else{
										window.location.href = $js_config.base_url + 'subjects';
									}
								}, 500);
							}
						})
					}
				},
				{
					label: ' No',
					//icon: '',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
				]
			});
		})

		$(".deletemultiple").click(function(event){
			event.preventDefault();

			var row = $(".checkSubjectList").parents('tr:first');

			var allChecked = [];
			$('input[name="checkAll"]:checked').each(function(i) {
				allChecked[i] = this.value;
				$('tr[data-id='+this.value+']').css('background-color','#FFBFBF');
			});

			if( allChecked.length > 0 ){

				BootstrapDialog.show({
				title: 'Confirmation',
				message: 'Are you sure you want to remove the selected Subjects?',
				type: BootstrapDialog.TYPE_DANGER,
				draggable: true,
				buttons: [
				{
						//icon: '',
						label: ' Yes',
						cssClass: 'btn-success',
						autospin: true,
						action: function (dialogRef) {
							$.when(
								$.ajax({
									url : $js_config.base_url + 'subjects/subject_delete',
									type: "POST",
									data: $.param({subject_id:allChecked,deltype:'multiple'}),
									global: true,
									async:false,
									success:function(response){

											if($.trim(response) != 'success'){

												$('#Recordedit').html(response);

											}else{

												if(currentPage > response.page){
													currentPage = response.page;
												}

												if(currentPage > 1){
													window.location.href = $js_config.base_url + 'subjects/index/page:'+currentPage;
												}else{
													window.location.href = $js_config.base_url + 'subjects';
												}
											}

										}
									})
							).then(function( data, textStatus, jqXHR ) {


								dialogRef.enableButtons(false);
								dialogRef.setClosable(false);
								// dialogRef.getModalBody().html('<div class="loader"></div>');
								setTimeout(function () {
									dialogRef.close();
									location.reload();
								}, 500);
							})
						}
					},
					{
						label: ' No',
						//icon: '',
						cssClass: 'btn-danger',
						action: function (dialogRef) {
							dialogRef.close();
							$('#tbody_subjects tr').removeAttr('style');
						}
					}
					]
				});

			}

		});

		$('#Recordedit,#modal_box').on('hidden.bs.modal', function(){
			$(this).removeData('bs.modal')
			$(this).find('.modal-content').html('')
		})

});


</script>

<style>
.well{   margin-bottom: 0px; min-height: auto; padding: 0;}
#search_page_show_form .modal-body .form-group{ margin-bottom: 0px;}
</style>