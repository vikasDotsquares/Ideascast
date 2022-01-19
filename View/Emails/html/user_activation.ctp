<?php echo $this->element('email_header'); ?>

<tr>
  <td bgcolor="#ffffff" style="padding: 20px;"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	  <tbody>
		<tr>
		   <td align="left" style="font-size:15px; padding:10px 0px;font-family:Arial, sans-serif;">Hi <?php if(isset($receiver)) echo $receiver; ?>,</td>
		</tr>
		<tr>
		   <td align="left" style="padding:5px 0px 20px 0px; font-size: 18px; font-weight: bold;font-family:Arial, sans-serif;">Welcome to OpusView!</td>
		</tr>
		<tr>
		  <td align="center">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tbody>
				<tr>
				  <td align="left" style="padding:3px 0px;font-family:Arial, sans-serif;">
				  	Your organization has added you as an OpusView user.
				  </td>
				</tr>
				<tr>
				  <td align="left" style="padding:3px 0px;font-family:Arial, sans-serif;"><strong> OpusView:</strong> <?php echo $domain_url; ?></td>
				</tr>
				<tr>
				  <td align="left" style="padding:3px 0px;font-family:Arial, sans-serif;"><strong> Username:</strong> <?php $email = explode('@', $username);
				  $email2 = explode('.', $email[1]);
				  echo $email[0].'<span>@</span>'.$email2[0].'<span>.</span>'.$email2[1]; ?></td>
				</tr>
				<tr>
				  <td align="left" style="padding:3px 0px;font-family:Arial, sans-serif;"><strong> Password:</strong> <?php echo $password; ?></td>
				</tr>
			  </tbody>
			</table>
			</td>
		</tr>
		<tr>
		  <td align="left" style=" padding:15px 0px 10px 0;font-family:Arial, sans-serif;">
		  <a href="<?php echo $domain_url.$activation_url;?>"><img src="<?php echo SITEURL;?>images/activate-btn.png" style="border:none;" alt="open-btn"></a>
		  </td>
		</tr>
	  </tbody>
	</table></td>
</tr>

<?php //echo $this->element('email_footer'); ?>
</tbody>
</table>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="deviceWidth">
  <tbody>
    <tr>
      <td bgcolor="#ffffff" style="padding:0 20px 20px 20px;"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
          <tbody>
            <tr>
              <td align="left" style=" padding:12px 0px 10px 0;font-family:Arial, sans-serif;">Click on the Activate button to verify your email address.</td>
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
