<?php echo $this->element('email_header'); ?>
<tr>
  <td bgcolor="#ffffff" style="padding: 20px;"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	  <tbody>
		<tr> 
		  <td align="left" style="font-size:15px; padding:10px 0px;font-family:Arial, sans-serif;">Hi <?php if(isset($name)) echo $name; ?>,</td>
		</tr>
		<tr>
		  <td align="left" style="padding:5px 0px 20px 0px; font-size: 18px; font-weight: bold;font-family:Arial, sans-serif;">
			Your password has been changed.
		 </td>	 
		</tr>
		<tr>
		  <td align="center">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tbody>				 
				<tr>
				  <td align="left" style="padding:3px 0px;font-family:Arial, sans-serif;"> If you made the change, you do not need to do anything.</td>
				</tr>	
				<tr>
				  <td align="left" style="padding:3px 0px;font-family:Arial, sans-serif;"> However, if you did not change your password, then contact your administrator.</td>
				</tr>			 				
			  </tbody>
			</table>
			</td>
		</tr>
		 
	  </tbody>
	</table></td>
</tr>
<?php echo $this->element('email_footer'); ?>