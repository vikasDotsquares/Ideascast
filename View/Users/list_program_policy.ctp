<style>
 .user-image {
    border: 3px solid #000;
     }

.myaccountPic {
    max-width: 49px;
    display: block;
    display: inline;
    margin: 3px;
    cursor : pointer;
}
</style>
<div class="bg-user-tasks">New Password</div>
<div class="bg-selectuser" style="padding-bottom:8px;">Policy</div>	
<div class="myaccountpicsec">
<ul>
<?php  
if( isset($policydata) && !empty($policydata) ) { 

$atleastone = '';

if( 
	(isset($policydata['OrgPassPolicy']['numeric_char']) && !empty($policydata['OrgPassPolicy']['numeric_char']) && $policydata['OrgPassPolicy']['numeric_char'] > 0) || 
	
	(isset($policydata['OrgPassPolicy']['alph_char']) && !empty($policydata['OrgPassPolicy']['alph_char']) && $policydata['OrgPassPolicy']['alph_char'] > 0 ) || 
	
	(isset($policydata['OrgPassPolicy']['caps_char']) && !empty($policydata['OrgPassPolicy']['caps_char']) && $policydata['OrgPassPolicy']['caps_char'] > 0 ) || 
	
	(isset($policydata['OrgPassPolicy']['special_char']) && !empty($policydata['OrgPassPolicy']['special_char']) && $policydata['OrgPassPolicy']['special_char'] > 0 )
) {
$atleastone = ', of which:'; 
}


?>	
		<?php if(isset($policydata['OrgPassPolicy']['min_lenght']) && !empty($policydata['OrgPassPolicy']['min_lenght']) && $policydata['OrgPassPolicy']['min_lenght'] > 0 ){ ?>
		<li><?php echo $policydata['OrgPassPolicy']['min_lenght'];?> minimum characters<?php echo $atleastone;?></li>
		
		<?php } if(isset($policydata['OrgPassPolicy']['numeric_char']) && !empty($policydata['OrgPassPolicy']['numeric_char']) && $policydata['OrgPassPolicy']['numeric_char'] > 0 ){ ?>	
		<li>At least <?php echo $policydata['OrgPassPolicy']['numeric_char'];?> numeric</li>
		
		<?php } if(isset($policydata['OrgPassPolicy']['alph_char']) && !empty($policydata['OrgPassPolicy']['alph_char']) && $policydata['OrgPassPolicy']['alph_char'] > 0 ){ ?>	
		<li>At least <?php echo $policydata['OrgPassPolicy']['alph_char'];?> alphabetic</li>	
		
		<?php } if(isset($policydata['OrgPassPolicy']['caps_char']) && !empty($policydata['OrgPassPolicy']['caps_char']) && $policydata['OrgPassPolicy']['caps_char'] > 0 ){ ?>	
		<li>At least <?php echo $policydata['OrgPassPolicy']['caps_char'];?> capital</li>
		
		<?php } if(isset($policydata['OrgPassPolicy']['special_char']) && !empty($policydata['OrgPassPolicy']['special_char']) && $policydata['OrgPassPolicy']['special_char'] > 0 ){ ?>			
		<li>At least <?php echo $policydata['OrgPassPolicy']['special_char'];?> special e.g. #,@,?,/,!</li>
		<?php } /* if(isset($policydata['OrgPassPolicy']['pass_repeat']) && !empty($policydata['OrgPassPolicy']['pass_repeat']) && $policydata['OrgPassPolicy']['pass_repeat'] > 0 ){ ?>			
		<li>At least <?php echo $policydata['OrgPassPolicy']['pass_repeat'];?> special e.g. #,@,?,/,!</li>
		<?php } */ ?>	
<?php  
	} else {
?>
<li>4 minimum characters</li>
<?php } ?>
</ul>
</div>		 