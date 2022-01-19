<?php 	
	echo $this->Html->script(array('ckeditor/ckeditor'));
	//echo $this->Html->script(array('jquery-ui')); 
	//echo $this->Html->css(array('jquery-ui-1.10.0.custom.min'));
?>
<style>
#successFlashMsg p{ 
	margin:0;
}
</style>
<div id="main">
<?php echo $this->Session->flash(); ?>
   <div class="panel panel-primary">
      <div class="panel-heading">
         <h3 class="panel-title">
            Edit <?php echo $this->data['HomeSlider']['slider_title'];?>
            <?php echo $this->Html->link('Back',array('controller' => 'sliders', 'action' => 'index', 'admin'=>true),array('class' => 'btn btn-warning btn-xs pull-right'));?>
         </h3>
      </div>
	  
      <div class="panel-body form-horizontal">
         <div class="row rownot">
            <div class="col-sm-12">             
			   <?php echo $this->Form->create(array('url'=>array('controller' => 'sliders', 'action' => 'edit',$this->data['HomeSlider']['id'], 'admin'=>true),'type' => 'file','class'=>'addEdit','enctype' => 'multipart/form-data')); ?>
               <?php  echo $this->Form->hidden('HomeSlider.id',array('label'=>false));   ?>
               <div class="form-content">
                  <div class="page-header">
                     <h3>Edit Slider</h3>
                  </div>
				
						<div class="row">
							<div class="col-sm-9">
								<div class="form-group">
									<label for="" class="control-label col-sm-3">Slider Name<span class="star"> * </span>
									</label>
									<div class="col-sm-9">
										<?php  echo $this->Form->input('HomeSlider.slider_title',array('type'=>'text','label'=>false, 'class'=>"field form-control" ));   ?>
									</div>
								</div>
							</div>							
						</div>

						<div class="row">
							<div class="col-sm-9">
								<div class="form-group">
									<label for="" class="control-label  col-sm-3">Slider Image</label>
									<div class="col-sm-9">									
										<?php echo $this->Form->input('HomeSlider.slider_image', array('type' => 'file', 'label' => false, 'div' => false, 'class' => 'form-control', 'style'=>'height:auto')); ?>
										<br >
										<?php 
										$folder_url = SITEURL . HOME_SLIDER_PATH.$this->data['HomeSlider']['slider_image'];	
										?>
										<img src="<?php echo $folder_url;?>" width="100" >										
									</div>
								</div>
							</div>
						</div>


						<div class="row">
							<div class="col-sm-9">
								<div class="form-group">
									<label for="" class="control-label  col-sm-3">Slider Text</label>
									<div class="col-sm-9">	

										<?php echo $this->Form->input('HomeSlider.slider_text', array('type' => 'textarea','label' => false,'maxlength'=>256,'class'=>"field form-control"));?>
									</div>
								</div>
							</div> 
						</div>
				
					    <div class="row">						 
						 <div class="col-sm-9">
							<div class="form-group">
							   <label for="" class="control-label  col-sm-3">Status</label>
							   <div class="col-sm-9">
								  <?php $arr_list = array( '0' =>'Inactive', '1' => 'Active'); ?>
								  <?php  echo $this->Form->input('status', array('label'=>false, 'options' =>$arr_list,'class'=>"field form-control")); ?>
							   </div>								   
							</div>							
						 </div>
					  </div>
				  
			
				  
				  
				  				 <?php $url =  SITEURL."sitepanel/sliders/";
					?>
					<div class="modal-footer clearfix">

					<button type="submit" class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Save</button>
					
					
					<?php 
					
					?><a class="btn btn-danger" href="<?php echo $url; ?>">Cancel</a>
					
					<!-- <button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button> -->
				</div>
                  <?php echo $this->Form->end(); ?> 
               </div>
            </div>
         </div>
      </div>
      <div id="dialog"></div>
   </div>
</div>
<div id="dialog"></div>