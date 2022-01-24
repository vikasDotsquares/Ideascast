<?php echo $this->Html->css('projects/list-grid') ?>
<?php echo $this->Html->css('projects/dropdown');
 echo $this->Html->css('projects/animate');
//pr($templateData);

$keyword = $this->Session->read('TemplateRelation.template_relation_id');
?>
<?php echo $this->Html->css('projects/templates');
if(isset($in) && !empty($in)){
	$class = 'in';
}
$per_page_show = $this->Session->read('thirdtemplate.per_page_show');
$thirdparty_id = $this->Session->read('ThirdTemplateRelation.thirdparty_id');
$template_relation_id = $this->Session->read('ThirdTemplateRelation.template_relation_id');

?>
<style>
.template-dashboard .pagination .next, .pagination .prev {
    display: inline-block;
    vertical-align: top;
	float: none;
}
</style>
<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left" style="min-height: 50px;"><?php echo $data['page_heading']; ?>
					<p class="text-muted date-time" id="project_text"><?php echo  $data['page_subheading']; ?></p>
				</h1>
			</section>
		</div>

		<div class="box-content">
            <div class="row ">
                <div class="col-xs-12">
					<div class="box">

                        <div class="box-header" style="background: rgb(239, 239, 239) none repeat scroll 0px 0px; cursor: move; border-bottom: none; border: 1px solid #dddddd; padding: 8px 10px;">
							<?php echo $this->Form->create('templates', array("controller" => "templates", "url" => "thirdparty_dashboard", "method" => "post")); ?>
                            <div class="col-lg-12 no-padding project-board-top">

                                <div class="col-sm-12 col-md-4 col-lg-3">
                                   <?php /*  <label for="AlignedTo" class="pull-left" style="margin-top: 5px; margin-right: 5px; font-weight:bold;">Template Category:</label> */?>
                                    <label class="custom-dropdown" style="width: 100%">
                                        <?php
										$options = $this->Template->getThirdPartyUser();
                                        echo $this->Form->input('ThirdTemplateRelation.thirdparty_id', array(
                                            'empty' => 'Select Third Party',
                                            'type' => 'select',
											'value'=> $thirdparty_id,
                                            'options' => $options,
                                            'label' => false,
                                            'div' => false,
                                            'class' => 'form-control aqua',
                                            'style' => 'width:100%;'
                                        ));
                                        ?>
                                    </label>
                                </div>
								<div class="col-sm-12 col-md-4 col-lg-4">
                                    <?php /*  <label for="AlignedTo" class="pull-left" style="margin-top: 5px; margin-right: 5px; font-weight:bold;">Template Category:</label>*/?>
                                    <label class="custom-dropdown" style="width: 100%">
                                        <?php
										$options = $this->Template->templateCategory();
                                        echo $this->Form->input('ThirdTemplateRelation.template_relation_id', array(
                                            'empty' => 'Select Folder',
                                            'type' => 'select',
											'value'=> $template_relation_id,
                                            'options' => $options,
                                            'label' => false,
                                            'div' => false,
                                            'class' => 'form-control aqua',
                                            'style' => 'width:100%;'
                                        ));
                                        ?>
                                    </label>
                                </div>
                                <div class="col-sm-12 col-md-4 col-lg-5 text-right">
                                    <button type="submit" id="filter_list" class="btn btn-success btn-sm">Apply Filter</button>
									<a class="btn btn-danger btn-sm" href="<?php echo SITEURL; ?>templates/thirdparty_dashboard_resetfilter" >Reset</a>
                                    <!-- <button id="filter_reset" class="btn btn-danger btn-sm">Reset</button> -->

                                </div>
                            </div>
                            <?php echo $this->Form->end(); ?>
						</div>


					<div class="box-body" id="box_body">
						<div class="template-dashboard table-responsive">
							<table class="table table-bordered table-hover" data-id="">
								<thead>
									<tr>
										<th class="text-left" >Template Name</th>
										<th class="text-center">Folder</th>
										<th class="text-center">Uploaded</th>
										<th class="text-center">Selected</th>
										<th class="text-center">Reviews</th>
										<th class="text-center">Delete</th>
									</tr>
								</thead>
								<tbody>
								<?php if( isset($templateData) && !empty($templateData) ){
									foreach($templateData as $val){
								?>
									<tr id="rowid<?php echo $val['TemplateRelation']['id']; ?>" >
										<td width="30%"><?php echo strip_tags($val['TemplateRelation']['title']);?></td>
										<td width="20%" align="center"><?php echo strip_tags($val['TemplateCategory']['title']);?></td>
										<td width="15%" align="center"><?php echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($val['TemplateRelation']['modified'])),$format = 'd M, Y h:i:s');?></td>
										<td width="10%" align="center"><?php echo ( isset($val['Workspace']) && !empty($val['Workspace']) ) ? count($val['Workspace']) : 0;?></td>
										<td width="15%" align="center"><?php $average =  $this->Template->templateReview($val['TemplateRelation']['id']);
										?>
											<?php
												$item['id'] = $val['TemplateRelation']['id'];
												$review_count = template_reviews($item['id'], 1);
												$sum_template_reviews = sum_template_reviews($item['id']);
												$average = 0;
												if( (isset($sum_template_reviews[0][0]['total']) && !empty($sum_template_reviews[0][0]['total'])) && (isset($review_count) && !empty($review_count)) ) {
													$average = $sum_template_reviews[0][0]['total'] / $review_count;

													$whole = floor($average);      // 1
													$fraction = $average - $whole; // .25

													if($fraction > 0.5 || $fraction < 0.5){
													$average = round($average);

													}else{
													$average = $average;
													}

												}
											$item_id = $val['TemplateRelation']['id']; ?>
											<span class="star-rating" align="center">
												<input id="star5" name="rating_<?php echo $item_id ?>" value="5" type="radio" <?php if($average == 5){ ?>checked="checked" <?php } ?>>
												<label class="full lbl" for="star5" title="Awesome - 5 stars"></label>

												<input id="star4half" name="rating_<?php echo $item_id ?>" value="4 and a half" type="radio" <?php if($average == 4.5){ ?>checked="checked" <?php } ?>>
												<label class="half lbl" for="star4half" title="Pretty good - 4.5 stars"></label>

												<input id="star4" name="rating_<?php echo $item_id ?>" value="4" type="radio" <?php if($average == 4){ ?>checked="checked" <?php } ?>>
												<label class="full lbl" for="star4" title="Pretty good - 4 stars"></label>

												<input id="star3half" name="rating_<?php echo $item_id ?>" value="3 and a half" type="radio" <?php if($average == 3.5){ ?>checked="checked" <?php } ?>>
												<label class="half lbl" for="star3half" title="Meh - 3.5 stars"></label>

												<input id="star3" name="rating_<?php echo $item_id ?>" value="3" type="radio" <?php if($average == 3){ ?>checked="checked" <?php } ?>>
												<label class="full lbl" for="star3" title="Meh - 3 stars"></label>

												<input id="star2half" name="rating_<?php echo $item_id ?>" value="2 and a half" type="radio" <?php if($average == 2.5){ ?>checked="checked" <?php } ?>>
												<label class="half lbl" for="star2half" title="Kinda bad - 2.5 stars"></label>

												<input id="star2" name="rating_<?php echo $item_id ?>" value="2" type="radio" <?php if($average == 2){ ?>checked="checked" <?php } ?>>
												<label class="full lbl" for="star2" title="Kinda bad - 2 stars"></label>

												<input id="star1half" name="rating_<?php echo $item_id ?>" value="1 and a half" type="radio" <?php if($average == 1.5){ ?>checked="checked" <?php } ?>>
												<label class="half lbl" for="star1half" title="Meh - 1.5 stars"></label>

												<input id="star1" name="rating_<?php echo $item_id ?>" value="1" type="radio"  <?php if($average == 1){ ?>checked="checked" <?php } ?> >
												<label class="full lbl" for="star1" title="Sucks big time - 1 star"></label>

												<input id="starhalf" name="rating_<?php echo $item_id ?>" value="half"type="radio" <?php if($average == 0.5){ ?>checked="checked" <?php } ?>>
												<label class="half lbl" for="starhalf" title="Sucks big time - 0.5 stars"></label>
											</span>
										</td>
										<td width="5%" align="center"><?php
											if( !empty($val['TemplateRelation']['user_id']) && $val['TemplateRelation']['user_id'] == $this->Session->read("Auth.User.id") ){
												$deleteURL = SITEURL."templates/delete_thirdparty_template/".$val['TemplateRelation']['id'];
											?>
												<a data-valid="<?php echo $val['TemplateRelation']['id']; ?>" class="RecordDeleteClass btn btn-sm btn-danger" rel="<?php echo $val['TemplateRelation']['id']; ?>" title="Delete Template" href="javascript:" data-url="<?php echo $deleteURL; ?>" data-tooltip="tooltip" data-placement="top" ><i class="fa fa-trash-o"></i></a>
											<?php } ?>
										</td>
									</tr>
									<?php } ?>

									<?php
										if($this->params['paging']['TemplateRelation']['pageCount'] > 1) { ?>
										<tr>
											<td colspan="6" align="right">
											<ul class="pagination">
												<?php

												if($this->params['paging']['TemplateRelation']['page'] == 1){
													echo '<li class="disabled">First</li>';
												}

												echo $this->Paginator->first(__('First'), array('class' => 'first','tag' => 'li'), null, array('tag' => 'li', 'class' => 'disabled'));

												echo $this->Paginator->prev('« Prev', array('class' => 'prev','tag' => 'li'), null, array('class' => 'disabled', 'tag'=>'li'));

												echo $this->Paginator->numbers(array('currentClass' => 'avtive', 'Class' => '', 'tag'=>'li', 'separator'=>''));

												echo $this->Paginator->next('Next »',  array('class' => 'next','tag' => 'li'), null, array('class' => 'disabled', 'tag'=>'li'));

												echo $this->Paginator->last(__('Last'), array('class' => 'last','tag' => 'li'), null, array('tag' => 'li', 'class' => 'disabled'));

												if(isset($this->params['paging']['TemplateRelation']['options']['page']) && $this->params['paging']['TemplateRelation']['page'] == $this->params['paging']['TemplateRelation']['pageCount']){
													echo '<li class="disabled">Last</li>';
												}

												?>
												</ul>
											</td>
										</tr>
									<?php } ?>

								<?php } else { ?>
									<tr>
											<td colspan="6" align="center">No Third Party Template Found</td>
									</tr>
								<?php } ?>
								</tbody>
							</table>


						</div>
					</div>
					</div>

                </div>
            </div>
        </div>
	</div>
</div>
<script>
$(function(){
 $("body").delegate(".RecordDeleteClass", "click", function (event) {
	event.preventDefault(); //STOP default action
	$that =	$(this);
	var postData = $(this).serializeArray();
	var formURL = $(this).data("url");
	var rowsid = $that.data("valid");

			BootstrapDialog.show({
			title: 'Confirmation',
			message: 'Are you sure you want to delete this template?',
			type: BootstrapDialog.TYPE_DANGER,
			draggable: true,
			buttons: [
				{
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: false,
					action: function (dialogRef) {
						$.when(

							 $.ajax({
								url : formURL,
								type: "POST",
								data : postData,
								async:false,
								success:function(response){
									if(response == 'success'){
										$("#rowid"+rowsid).css('background-color','#FF9999');
										setTimeout(function(){
											$("#rowid"+rowsid).remove();
										}, 500);
									}
								}
							})

						).then(function( data, textStatus, jqXHR ) {
							dialogRef.close();

						})

					}
				},
				{
					label: ' No',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
			]
		})

});
$('#hidden_url').val('<?php echo $refer_url; ?>');
});
</script>
<style>
.star-rating {
	border: none;
	float: none;
	margin: auto;
	position: relative;
	text-align: center;
	overflow: hidden;
	display: inline-block;
}
</style>