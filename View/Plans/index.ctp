<div class="container">
 <div class="plan">
 
 
 <!-- <div class="page-header">
              <h3>Forms</h3>
            </div>
	-->		
<section class="plan-box">			
 
<div class="plan-body"><h4 class="box-title"><?php echo __('Here is a list of features you can add');?></h4>  
    <?php echo $this->Session->flash('auth'); ?>
    <?php echo $this->Form->create('User'); ?>
	 
<?php echo $this->Session->flash(); //var_dump($this->Session->read('newRegistrationId'));?> 
        
          
        <table class="table  table-bordered  ">
            <thead>
                <tr class="bg-yellow">
                    <th>Description of feature</th>
                    <th>Fee (monthly) shown</th> 
                    <th>Fee charged - 12 months</th>
                   <!--  <th>Once off fee</th>-->
                </tr>
            </thead>   
            <tbody>
                <?php $i=0;
                 foreach ($planList as $key => $val) { $i++;
				 $planID = $val['Plan']['id'];
                ?>
                <tr id="<?php echo $val['Plan']['id'] ?>">
                    <td><?php  echo $val['Plan']['description']; ?></td>
                   <td><div class="radio">
					<label for="<?php echo 'planId' . $val['Plan']['id'] ?>">
                        <?php 
                        if($val['Plan']['plantype_monthly'] == 0){
                           echo 'none';
                        }else{ 
						   
/*    echo $this->Form->input("plan.$planID.m", array(
                                'type' => 'checkbox',
                                'options' => array(1 => $this->Common->currencySymbol().$this->Common->currencyConvertor($val['Plan']['plantype_monthly']),),
                                'class' => 'testClass',
								'div' => false,
                                'id' => 'planId_1' ,
                                'planid' => '1' ,
                                'value' =>'','label' => false,
                                'hiddenField' => false, // added for non-first elements
                            )); */
                         

						 //  echo "".$this->Common->currencySymbol()."";
                            	echo " ".$this->Common->currencySymbol().$this->Common->currencyConvertor($val['Plan']['plantype_monthly']);
                        }?></label>
						</div>
                    </td>
                    <td><div class="radio">
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
                    <td style="display:none; visibility:hidden;"><div class="radio">
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
		 <?php 
            echo $this->Form->submit('Submit',array('name'=>'add_plan','class' => 'btn btn-warning   btn-flat rl', 'title' => 'Submit','div' => false));
            echo $this->Form->submit('No Thanks',array('name'=>'skip_plan','class' => 'btn btn-warning   btn-flat', 'title' => 'Submit','div' => false));
        ?>
   <?php echo $this->Form->end(); ?>
		
		
       <?php // echo $this->Form->end(__('Submit')); ?>
	    </div> 
	 
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
 
  