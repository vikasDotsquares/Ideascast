<?php echo $this->element('email_header'); ?>

<tr>
  <td bgcolor="#ffffff" style="padding: 20px;"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	  <tbody>
		<tr>
		   <td align="left" style="font-size:15px; padding:10px 0px;font-family:Arial, sans-serif;">Hi <?php if(isset($receiver)) echo $receiver; ?>,</td>
		</tr>
		<tr>
		   <td align="left" style="padding:5px 0px 20px 0px; font-size: 18px; font-weight: bold;font-family:Arial, sans-serif;">You have been sent a nudge.</td>
		</tr>
		<tr>
		  <td align="center">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tbody>
				<tr>
				  <td align="left" style="padding:3px 0px;font-family:Arial, sans-serif;"><strong> Message:</strong> <?php echo $message; ?></td>
				</tr>
			  </tbody>
			</table>
			</td>
		</tr>
		<?php // SHOW IF LINK IS ON
		if($hide_button){ ?>
		<tr>
		  <td align="left" style=" padding:15px 0px 10px 0;font-family:Arial, sans-serif;">
		  <a href="<?php echo $open_page;?>"><img src="<?php echo SITEURL;?>images/email_open.png" style="border:none;" alt="open-btn"></a>
		  </td>
		</tr>
		<?php } ?>
	  </tbody>
	</table></td>
</tr>

 <?php echo $this->element('email_footer'); ?>




