<?php echo $this->element('email_header'); ?>
<tr>
  <td bgcolor="#ffffff" style="padding: 20px;"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	  <tbody>
		<tr> 
		  <td align="left" style="font-size:15px; padding:10px 0px;font-family:Arial, sans-serif;">Hi <?php if(isset($orgfullname)) echo $orgfullname; ?>,</td>
		</tr>
		<tr>
		   <td align="left" style="padding:5px 0px 20px 0px; font-size: 18px; font-weight: bold;font-family:Arial, sans-serif;">Welcome to OpusView!</td>
		</tr>	 
		
		<tr>
		  <td align="center">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tbody>
				<tr>
				  <td align="left" style="padding:5px 0px;font-family:Arial, sans-serif;">
				  	Your organization has added you as the OpusView System Administrator.
				  </td>
				</tr>				 
				<tr>
				  <td align="left" style="padding:5px 0px;font-family:Arial, sans-serif;"><strong> OpusView:</strong> <?php echo $siteurl; ?></td>
				</tr>
				<tr>
				  <td align="left" style="padding:5px 0px;font-family:Arial, sans-serif;"><strong> Username:</strong> <?php echo $orgEmail; ?></td>
				</tr>
				<tr>
				  <td align="left" style="padding:5px 0px;font-family:Arial, sans-serif;"><strong> Password:</strong> <?php echo $orgPassword; ?></td>
				</tr>
			  </tbody>
			</table>
			</td>
		</tr>
		<tr>
		  <td align="left" style=" padding:15px 0px 10px 0;font-family:Arial, sans-serif;"><a href="<?php echo $siteurl;?>"><img src="<?php echo SITEURL;?>images/email_open.png" style="border:none;" alt="open-btn"></a></td>
		</tr>
		 
	  </tbody>
	</table></td>
</tr>
</tbody>
</table>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="deviceWidth">
  <tbody>
    <tr>
      <td bgcolor="#ffffff" style="padding:0 20px 20px 20px;"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
          <tbody>
            <tr>
              <td align="left" style=" padding:12px 0px 10px 0;font-family:Arial, sans-serif;">Protect this account information as it has powerful security privileges.</td>
            </tr>
            <tr>
              <td align="left" style=" padding:10px 0px 15px 0;font-family:Arial, sans-serif;"> The OpusView Team </td>
            </tr>
          </tbody>
        </table></td>
    </tr>
  </tbody>
</table>
</body>
</html>