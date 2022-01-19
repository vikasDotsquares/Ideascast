<div class="container">
 <div class="plan">
<section class="panel panel-primary">
<div class="panel-heading"><h4 class="panel-title"><?php echo __('Offer features');?></h4> </div>
    <?php echo $this->Session->flash('auth'); ?>
    <?php echo $this->Form->create('User'); ?>
	 <div class="panel-body"> <p>
   <?php var_dump($this->Session->read('newRegistrationId'));?></p>
        
          
        <table class="table  table-bordered ">
            <thead>
                <tr>
                    <th>Feature</th>
                    <th>Fee (monthly) shown</th>
                    <th>Fee charged - 12 months</th>
                    <th>Once off fee</th>
                </tr>
            </thead>   
            <tbody>
                <?php $i=0;
                 foreach ($planList as $key => $val) { $i++;
                ?>
                <tr>
                    <td><?php  echo $val['Plan']['description']; ?></td>
                    <td><div class="radio">
					<label for="<?php echo 'planId' . $val['Plan']['id'] ?>">
                        <?php 
                        if($val['Plan']['plantype_monthly'] == -1){
                           echo 'none';
                        }else{
                            echo $this->Form->input("plan.$i", array(
                                'type' => 'radio',
                                'options' => array(1 => $val['Plan']['plantype_monthly'],),
                                'class' => 'testClass',
								'div' => false,
                                'id' => 'planId' . $val['Plan']['id'],
                                'value' =>'','label' => false,
								
                                // 'selected' => $selected,
                                // 'before' => '<div class="testOuterClass">',
                                // 'after' => '</div>',
                                'hiddenField' => false, // added for non-first elements
                            ));
                            
                           //echo $this->Form->input("plan.id.", array('id' => 'planId' . $val['Plan']['id'], 'type' => 'radio', 'title' => $val['Plan']['plantype_monthly'], 'label' => $val['Plan']['plantype_monthly'], 'div' => false, 'hiddenField' => false, 'value' => $val['Plan']['plantype_monthly']));
                            
                        }?></label>
						</div>
                    </td>
                    <td><div class="radio">
					<label for="  ">
                        <?php 
                            if($val['Plan']['plantype_yearly'] == -1){
                                echo 'none';
                            }else{
                                
                                echo $this->Form->input("plan.$i", array(
                                'type' => 'radio',
                                'options' => array(1 => $val['Plan']['plantype_yearly'],),
                                'class' => 'testClass',
								'div' => false,
								'label' => false,
								 
                                'hiddenField' => false, // added for non-first elements
                                ));
                                
                                //echo $this->Form->input("plan.id.", array('id' => 'planId' . $val['Plan']['id'], 'type' => 'checkbox', 'title' => $val['Plan']['plantype_yearly'], 'label' => $val['Plan']['plantype_yearly'], 'div' => false, 'hiddenField' => false, 'value' => $val['Plan']['plantype_yearly']));
                                
                            }
                        ?></label>
						</div>
                    </td>
                    <td><div class="radio">
					<label for=" ">
                        <?php 
                            if($val['Plan']['plantype_once'] == -1){
                                echo 'none';
                            }else{
                                echo $this->Form->input("plan.$i", array(
                                'type' => 'radio',
                                'options' => array(1 => $val['Plan']['plantype_once'],),
                                'class' => 'testClass',
								'div' => false,
								'label' => false,
                                'hiddenField' => false, // added for non-first elements
                                ));
                                
                                //echo $this->Form->input("plan.id.", array('id' => 'planId' . $val['Plan']['id'], 'type' => 'checkbox', 'title' => $val['Plan']['plantype_once'], 'label' => $val['Plan']['plantype_once'], 'div' => false, 'hiddenField' => false, 'value' => $val['Plan']['plantype_once']));
                                
                            }
                        ?></label>
						</div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
		
		 <?php 
            echo $this->Form->submit(
			'Submit', 
			array('class' => 'btn btn-warning   btn-flat', 'title' => 'Submit','div' => false)
            );
        ?>
   <?php echo $this->Form->end(); ?>
		
		
       <?php // echo $this->Form->end(__('Submit')); ?>
	    </div> 
	   </section>
  </div>   </div> 
 