<?php echo $this->element('email_header'); ?>
<tr>
  <td bgcolor="#ffffff" style="padding: 20px;"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	  <tbody>
		<tr> 
		  <td align="left" style="font-size:15px; padding:10px 0px;font-family:Arial, sans-serif;">Hi <?php if(isset($Custname)) echo $Custname; ?>,</td>
		</tr>
		<tr>
		  <td align="left" style="padding:5px 0px 20px 0px; font-size: 18px; font-weight: bold;font-family:Arial, sans-serif;">
			Feedback has been received.
		 </td>	 
		</tr>
		<tr>
		  <td align="center">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tbody>				 
				<tr>
				  <td align="left" style="padding:3px 0px;font-family:Arial, sans-serif;"><strong> Project:</strong> <?php echo ucfirst(htmlentities($projectName,ENT_QUOTES)); ?></td>
				</tr>
				<tr>
				  <td align="left" style="padding:3px 0px;font-family:Arial, sans-serif;"><strong> Workspace:</strong> <?php echo ucfirst(htmlentities($workspaceName,ENT_QUOTES)); ?>
				  </td>
				</tr> 
				<tr>
				  <td align="left" style="padding:3px 0px;font-family:Arial, sans-serif;"><strong> Task:</strong> <?php echo ucfirst(htmlentities($elementName,ENT_QUOTES)); ?>
				  </td>
				</tr> 
				<tr>
				  <td align="left" style="padding:3px 0px;font-family:Arial, sans-serif;"><strong> Feedback:</strong> <?php echo ucfirst(htmlentities($feedbackTitle,ENT_QUOTES)); ?>
				  </td>
				</tr> 	
			  </tbody>
			</table>
			</td>
		</tr>
		<tr>
		  <td align="left" style=" padding:15px 0px 10px 0;font-family:Arial, sans-serif;"><a href="<?php echo $open_page;?>"><img src="<?php echo SITEURL;?>images/email_open.png" style="border:none;" alt="open-btn"></a></td>
		</tr>
	  </tbody>
	</table></td>
</tr>
<?php echo $this->element('email_footer'); ?>