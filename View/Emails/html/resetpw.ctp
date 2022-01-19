<?php echo $this->element('email_header'); ?>
<tr>
  <td bgcolor="#ffffff" style="padding: 20px;"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	  <tbody>
		<tr>
		  <td align="left" style="font-size:15px; padding:10px 0px;font-family:Arial, sans-serif;">Hi <?php if(isset($name)) echo $name; ?>,</td>
		</tr>
		<tr>
		  <td align="left" style="padding:5px 0px 6px 0px; font-size: 18px; font-weight:bold; font-family:Arial, sans-serif;">
			A request was made to change your password.
		  </td>		 
		</tr>
		<tr>
		  <td align="left" style="padding:5px 0px 15px 0px; font-size: 13px; font-family:Arial, sans-serif;">
			If you did not request a password change, you can ignore this message.
			</td>
		</tr>
		<tr>
		  <td align="left" style=" padding:15px 0px 15px 0; font-size: 13px; font-family:Arial, sans-serif;"><a href="<?php echo $ms; ?>"><img src="<?php echo SITEURL;?>images/reset-btn.png" style="border:none;" alt="open-btn"></a></td>
		</tr>
		<tr>
		  <td align="left" style="padding:5px 0px 15px 0px; font-size: 13px; font-family:Arial, sans-serif;">
			Click on the Change button to change your password.
			</td>
		</tr>
		<tr>
		  <td align="left" style="padding:5px 0px 15px 0px; font-size: 13px; font-family:Arial, sans-serif;">
			This link will expire after 24 hours as a security precaution.

			</td>
		</tr>
		
		<!--<tr>
		  <td align="left" style="padding:5px 0px 15px 0px; font-size: 13px; font-family:Arial, sans-serif;">
			 If you did not request a password reset, then contact your administrator.
			</td>
		</tr>-->
	  </tbody>
	</table></td>
</tr>
<?php echo $this->element('email_footer'); ?>