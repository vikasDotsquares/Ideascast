<div class="container add_feture">
 <div class="plan">
 
 
 <!-- <div class="page-header">
              <h3>Forms</h3>
            </div>
	-->		
<section class="plan-box">			
 
 <?php if(!empty($planList)) { ?>
 
<div class="plan-body">
 <?php echo $this->Session->flash('auth'); ?>
    <?php echo $this->Form->create('User'); ?>
<div class="row">

<div class="col-sm-12">
<h4 class="box-title"><?php echo __('Here is a list of features you can add');?></h4> 
</div>
 
   



        
 <!--<div class="col-sm-6">       
	<div class="select_years">	
		<select id="p_type">
		 <option value="1">Monthly</option>
		 <option value="2">Yearly</option>		 
		 <option value="3">One Off</option>
		 
		</select>
        </div>
        </div>-->
	
<style>
#free_plan,.free_plan,#month_plan,.month_plan,#one_plan,.one_plan{ display:none;}
</style>
	
<script type="text/javascript" >
$(document).ready(function(){

$('#free_plan ,.free_plan').show();
$('#month_plan ,.month_plan').show();


var va = $("#p_type").val();
if(va == 1){
$('#free_plan ,.free_plan').show();
$('#month_plan ,.month_plan').hide();
$('#one_plan ,.one_plan').hide();
}else if(va == 2){
$('#free_plan ,.free_plan').hide();
$('#month_plan ,.month_plan').show();
$('#one_plan ,.one_plan').hide();
}
else if(va == 3){
$('#free_plan ,.free_plan').hide();
$('#month_plan ,.month_plan').hide();
$('#one_plan ,.one_plan').show();
}


$('#p_type').change(function(){

var vall = $(this).val();

if(vall == 1){
$('#free_plan , .free_plan').show();
$('#month_plan , .month_plan').hide();
$('#one_plan , .one_plan').hide();

$('.free_plan input').removeAttr('disabled','disabled');

$('.month_plan input').attr('disabled','disabled');
$('.free_plan input').removeAttr('checked','checked');

$('.one_plan input').attr('disabled','disabled');
$('.one_plan input').removeAttr('checked','checked');

}
else if(vall == 2){
$('#free_plan , .free_plan').hide();
$('#month_plan , .month_plan').show();
$('#one_plan , .one_plan').hide();

$('.free_plan input').attr('disabled','disabled');
$('.free_plan input').removeAttr('checked','checked');

$('.month_plan input').removeAttr('disabled','disabled');

$('.one_plan input').removeAttr('checked','checked');
$('.one_plan input').attr('disabled','disabled');

}

 else if(vall == 3){
$('#free_plan , .free_plan').hide();
$('#month_plan , .month_plan').hide();
$('#one_plan , .one_plan').show();

$('.free_plan input').attr('disabled','disabled');
$('.free_plan input').removeAttr('checked','checked');

$('.month_plan input').attr('disabled','disabled');
$('.month_plan input').removeAttr('checked','checked');

$('.one_plan input').removeAttr('disabled','disabled');

}
})



})



</script>

</div>
<div class="row">
	<div class="col-sm-12">
    	<?php echo $this->Session->flash(); //var_dump($this->Session->read('newRegistrationId'));?> 
    </div>
</div>	
	<div class="row">
    <div class="col-sm-12">	
    <div class="table-responsive">	
        <table class="table table-hover table-bordered custom-table-border">
            <thead>
                <tr class="bg-yellow">
                    <th>Description of features</th>
                    <th id="free_plan">Fee (monthly) shown </th>
                    <th id="month_plan">Fee charged - 12 months</th>
                    <th id="one_plan">Once off fee</th>
                </tr>
            </thead>   
            <tbody>
                <?php $i=0;
                 foreach ($planList as $key => $val) { $i++;
				 $planID = $val['Plan']['id'];
                ?>
                <tr id="<?php echo $val['Plan']['id'] ?>" >
                    <td><?php  echo $val['Plan']['description']; ?></td>
                    <td class="free_plan"><div class="radio">
					<label for="<?php echo 'planId' . $val['Plan']['id'] ?>">
                        <?php 
                        if($val['Plan']['plantype_monthly'] == 0){
                           echo 'none';
                        }else{ 
						
                            /*
							echo $this->Form->input("plan.$planID.m", array(
                                'type' => 'checkbox',
                                'options' => array(1 => $this->Common->currencySymbol().$this->Common->currencyConvertor($val['Plan']['plantype_monthly']),),
                                'class' => 'testClass',
								'div' => false,
                                'id' => 'planId_1' ,
                                'planid' => '1' ,
                                'value' =>'','label' => false,
                                'hiddenField' => false, 
                            ));
							*/
							
                           //  echo "".$this->Common->currencySymbol()."";
                            	echo " ".$this->Common->currencySymbol().$this->Common->currencyConvertor($val['Plan']['plantype_monthly']);
                        }?></label>
						</div>
                    </td> 
                    <td class="month_plan"><div class="radio">
					<label for="planId_<?php echo $val['Plan']['plantype_yearly'].$planID; ?>">
                        <?php 
                            if($val['Plan']['plantype_yearly'] == 0){
                               // echo 'none';
                            
							
							}
							else{  
                                
								//  echo "".."";
					
								 echo $this->Form->input("plan.$planID.m", array(
                                'type' => 'checkbox',
                                'options' => array(1 =>$this->Common->currencySymbol().$this->Common->currencyConvertor($val['Plan']['plantype_yearly'])),
                                'class' => 'testClass minimal',
								'div' => false,
								'id' => 'planId_4' ,
								 'planid' => '4' ,
								'label' => false,								 
                                'hiddenField' => false, // added for non-first elements
                                ));
								echo " ".$this->Common->currencySymbol().$this->Common->currencyConvertor($val['Plan']['plantype_yearly']);
								?>
								<!--<input type="radio" id="planId_<?php echo $val['Plan']['plantype_yearly'].$planID; ?>"   planid = "4"  value="<?php echo $val['Plan']['plantype_yearly']; ?>" name="<?php echo "data['plan']['$planID']['m']" ?>"/> -->
								
								<?php

                                echo $this->Form->input("plan.$planID.plantype", array('type' => 'hidden','value' => '','class'=>'txt_'.$planID,'label' => false,));
                             
							 //echo $this->Form->input("plan.id.", array('id' => 'planId' . $val['Plan']['id'], 'type' => 'checkbox', 'title' => $val['Plan']['plantype_yearly'], 'label' => $val['Plan']['plantype_yearly'], 'div' => false, 'hiddenField' => false, 'value' => $val['Plan']['plantype_yearly']));
                                
                            }
                        ?>
						  
						  <?php 						
						   if($val['Plan']['plantype_yearly'] == 0){
						     echo 'none';
						   }else{
						 // echo $val['Plan']['plantype_yearly'] ;
						  
						  }?>
						  </label>
						</div>
                    </td>
                    <td class="one_plan"><div class="form-group">
					<label for="planId_<?php echo  $val['Plan']['plantype_once'].$planID; ?>">
                        <?php 
                            if($val['Plan']['plantype_once'] == 0){
                              //  echo 'none';
                            }else{
							//echo "3";
							 
                             echo $this->Form->input("plan.$planID.m", array(
                                'type' => 'checkbox',
                                'options' => array(1 => $this->Common->currencySymbol().$this->Common->currencyConvertor($val['Plan']['plantype_once'])),
                                'class' => 'testClass minimal',
								'div' => false,
								'id' => 'planId_5' ,
								'planid' => '5' ,
								'label' => false,
                                'hiddenField' => false, // added for non-first elements
                                )); 
								
								echo " ".$this->Common->currencySymbol().$this->Common->currencyConvertor($val['Plan']['plantype_once']);
							//echo "".$this->Common->currencySymbol()."";
							?>
								<!-- <input type="radio" id="planId_<?php echo  $val['Plan']['plantype_once'].$planID; ?>"  planid = "5" , class = "minimal" , value="<?php echo $val['Plan']['plantype_once']; ?>" name="<?php echo "data['plan']['$planID']['m']" ?>"/> -->

								<?php
							
                            }
							echo $this->Form->input("plan.$planID.plantype", array('type' => 'hidden','value' => '','class'=>'txt_'.$planID,'label' => false,));
							
                        ?>
						  
						  <?php 
						   if($val['Plan']['plantype_once'] == 0){
						     echo 'none';
						   }else{
						//  echo $val['Plan']['plantype_once'] ;
						  
						  }
						  ?></label>
						
						</div>
                    </td>
                </tr>
                <?php } ?>
				
            </tbody>
        </table>
		<p>Note: You will be billed for a 12 month period.</p>
        </div>
		</div>
		
        </div>
		 <?php 
            echo $this->Form->submit('Submit',array('name'=>'add_plan','class' => 'btn btn-warning   btn-flat rl', 'title' => 'Submit','div' => false));
           // echo $this->Form->submit('Skip',array('name'=>'skip_plan','class' => 'btn btn-warning   btn-flat', 'title' => 'Submit','div' => false));
		   
        ?><a  class="btn btn-warning   btn-flat" href="<?php echo SITEURL.'projects/lists/' ?>" >No Thanks</a>
   <?php echo $this->Form->end(); ?>
		
		
       <?php // echo $this->Form->end(__('Submit')); ?>
	    </div> 
	
<?php } else { 

//pr($planListRenew);

?>	


<p>You already have all features.</p>
<?php } ?>
</section>	   

	   
  </div>   </div> 
 <script type="text/javascript" >
 $(document).ready(function(){
	 $('.testClass ').click(function() {
		var planId = $(this).attr('planid');
		var trId =  $(this).closest('tr').attr('id');		 
		$('.txt_'+trId).val(planId);
	}); 
	  $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
          checkboxClass: 'icheckbox_minimal-blue',
          radioClass: 'iradio_minimal-blue'
        });
		        //Flat red color scheme for iCheck
       /*  $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
          checkboxClass: 'icheckbox_flat-green',
          radioClass: 'iradio_flat-green'
        }); */
	
});


$(window).load(function(){
	  $('.iCheck-helper').click(function() {
		var planId = $(this).prev().attr('planid');		
		var trId =  $(this).prev().closest('tr').attr('id');	
		
		$('.txt_'+trId).val(planId);
		}); 
})
 </script>
 <style>.btn.btn-warning.btn-flat.rl {
    margin-right: 10px;
}</style>
  