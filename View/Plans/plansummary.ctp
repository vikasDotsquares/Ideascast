<div class="container">
 <div class="plan">
<section class="plan-box">	
 <?php echo $this->Session->flash();
 ?>
<div class="plan-body"><?php echo $this->Session->flash('good')  ?>
<div class="panel-heading"><h4 class="box-title"><?php echo __('You have selected below plans here');?></h4> </div>
		<table class="table  table-bordered ">
            <thead>
                <tr class="bg-yellow">
                    <th>Feature</th>
                    <th>Plan Type</th>
                    <th>Fees</th>
                </tr>
            </thead>   
            <tbody>
                <?php $totalFees = 0;

				
				foreach($UserPlanData as $UserPlanDataVal){  ?>
				<tr>
					<td><?php echo $UserPlanDataVal['Plan']['description']?></td>
					<?php if($UserPlanDataVal['UserPlan']['plan_type']=='1'){ 
						$totalFees = $totalFees+$UserPlanDataVal['Plan']['plantype_monthly'];
					?>
						<td>Monthly</td>
						<td><?php echo " ".$this->Common->currencySymbol()."";  echo $this->Common->currencyConvertor($UserPlanDataVal['Plan']['plantype_monthly']);?></td>
					<?php } ?>
					<?php if($UserPlanDataVal['UserPlan']['plan_type']=='4'){
							$totalFees = $totalFees+$UserPlanDataVal['Plan']['plantype_yearly'];
					?>
						<td>Yearly</td>
						<td><?php echo " ".$this->Common->currencySymbol().""; echo $this->Common->currencyConvertor($UserPlanDataVal['Plan']['plantype_yearly']); ?></td>
					<?php } ?>
					<?php if($UserPlanDataVal['UserPlan']['plan_type']=='5'){
							$totalFees = $totalFees+$UserPlanDataVal['Plan']['plantype_once'];
					?>
						<td>Only Once</td>
						<td><?php echo " ".$this->Common->currencySymbol()."".$this->Common->currencyConvertor($UserPlanDataVal['Plan']['plantype_once']); ?></td>
					<?php } ?>					
				<?php }
					if(isset($this->params['pass']['1']) && !empty($this->params['pass']['1'])){
					 $discountamount = $this->params['pass']['1'];
					}
				?>
				
				</tr>
				
				<tr class="bg-green">
					<td style="text-align:right;color:#ffffff">Total Amount</td>
					<td colspan="2" style="text-align:right;color:#ffffff;padding-right:42px;font-weight:bold;">
					<?php if(isset($disamount) && !empty($disamount) && $disamount !='a' && $disamount !='-1'){					
						echo "<strike>"." ".$this->Common->currencySymbol()."".$this->Common->currencyConvertor($totalFees)."</strike>"; 
						}else{
						echo " ".$this->Common->currencySymbol()."".$this->Common->currencyConvertor($totalFees); 
						if(isset($vat) && !empty($vat)){	
						echo " + ".$vat."% (Vat) = ".$this->Common->currencySymbol(). $this->Common->currencyConvertor($totalFees + $totalFees * $vat /100);
						}
						}
					?></td>
				</tr>
	
				<?php if(isset($disamount) && !empty($disamount) && ($disamount >0) ){ ?>
				<tr class="bg-maroon">
					<td style="text-align:right;color:#ffffff">Pay for Amount</td>
					<td colspan="2" style="text-align:right;color:#ffffff;padding-right:42px;font-weight:bold;">
					<?php if(isset($disamount) && !empty($disamount)){
					echo"".$this->Common->currencySymbol().""; } 
					echo $this->Common->currencyConvertor($disamount); 
					 if(isset($vat) && !empty($vat)){	
					echo " + ".$vat."% (Vat) = ".$this->Common->currencySymbol().$vatamount ;
					}
					?></td>
				</tr>
				<?php } ?>
				
				<?php if(isset($disamount) && !empty($disamount) && ($disamount =='free') ){ 
				    echo $this->Form->create('Plan',array('url'=>'/plans/thanks/'.$this->request->params['pass']['0'])); 
                   if(isset($coup_id) && !empty($coup_id)){	
						echo $this->Form->input("plan.coup_id", array('type' => 'hidden','value' => $coup_id,'label' => false));
						}
						
					if(isset($associated_user_id) && !empty($associated_user_id)){
						echo $this->Form->input("plan.associated_user_id", array('type' => 'hidden','value' => $associated_user_id,'label' => false));
					}	
						
					 
					echo $this->Form->input("plan.free", array('type' => 'hidden','value' => 'free','label' => false));
				 }
				 if((isset($disamount) && !empty($disamount)) || (!isset($disamount) || $disamount !='free') ){ 
				  echo $this->Form->create('Plan'); 
				 }
				 ?>
				
				<tr>
					<td colspan="3" >
						
						 <?php //pr($this->request->data);
						if(isset($disamount) && !empty($disamount)){ 
						$disamount1 =   $this->Session->read('disamount');
						 if(isset($disamount1) && !empty($disamount1)){
							echo $this->Form->input("plan.discount", array('type' => 'hidden','value' => $disamount1,'label' => false));
						}
						}						
						if(isset($coup_id) && !empty($coup_id)){	
						echo $this->Form->input("plan.coup_id", array('type' => 'hidden','value' => $coup_id,'label' => false));
						}
						
						if(isset($associated_user_id) && !empty($associated_user_id)){
						echo $this->Form->input("plan.associated_user_id", array('type' => 'hidden','value' => $associated_user_id,'label' => false));
						}	
							
						  echo $this->Form->input("plan.paypal", array('type' => 'hidden','value' => "yes",'label' => false));
						 echo $this->Form->input("plan.totalfees", array('type' => 'hidden','value' => "$totalFees",'label' => false,));
						 
						 if(isset($disamount) && !empty($disamount) && ($disamount =='free') ){							
						 echo $this->Form->submit('Get Free Now',array('class' => 'btn btn-warning   btn-flat', 'title' => 'Pay Now','div' => false,'style' => array('float:right')));
						 }else{
						 echo $this->Form->submit('Pay Now',array('class' => 'btn btn-warning   btn-flat', 'title' => 'Pay Now','div' => false,'style' => array('float:right')));
						 }
					?>
					</td>
				</tr>
				 <?php echo $this->Form->end(); ?>
            </tbody>
        </table>
	
	<?php  //pr($vikas); 

	echo $this->Form->create('Plan',array('url'=>'/plans/plansummary/'.$this->request->params['pass']['0'], "id" => "editcoupon","class"=>"form-horizontal")); 
	
	

	 echo $this->Form->input("plan.totalfees", array('type' => 'hidden','value' => "$totalFees",'label' => false,));

	 echo $this->Form->input("plan.id", array('type' => 'hidden','value' => $this->request->params['pass']['0'],'label' => false,));
	?>
	<h4 class="login-box-msg">Get discount here! </h4>
	<div class="row">
		<!--<div class="col-md-6"> 
			<div class="form-group has-feedback ">			
			
			  <?php echo $this->Form->input('UserInstitution.membership_code', array('type' => 'text', 'div' => false, 'placeholder' => 'Enter Code','required'=>false, 'class' => 'form-control')); ?>

			</div>
		</div> 	 -->
		<div class="col-md-12">
			<div class="form-group has-feedback">
			<label class="col-md-3 col-xs-3 text-right  control-label" for="UserCpassword">Coupon Code:</label>
				<div class="col-md-6 col-xs-6">
				  <?php echo $this->Form->input('Coupon.name', array('type' => 'text', 'div' => false, 'required' => true, 'label' => false, 'placeholder' => 'Enter Code', 'class' => 'form-control', 'size' => 20)); 
				  ?> 	
				</div>
				<div class=" col-md-3 col-xs-3 text-left no-padding">
					<input type="submit" value="Submit" title="Submit" class="btn btn-warning btn-flat">        
				</div> 
    </div>
			</div>
		</div>
	</div>
	<div class="row">
	
	</form>
		</div>
		
	  </section>
  </div>  
</div> 

<script type="text/javascript" >
$("#editcoupon").submit(function(e){
	var postData = new FormData($(this)[0]);
	var formURL = $(this).attr("action");	
	$.ajax({
		url : formURL,
		type: "POST",
		data : postData,
		success:function(response){	 
			if($.trim(response) != 'success'){ alert(response);
				$('#editcoupon').html(response);
			}else{
				location.reload(); // Saved successfully
			}
		},
		//cache: false,
       // contentType: false,
       // processData: false,
		error: function(jqXHR, textStatus, errorThrown){
			// Error Found
		}
	});
	e.preventDefault(); //STOP default action
	//e.unbind(); //unbind. to stop multiple form submit.
});
</script>
 
 