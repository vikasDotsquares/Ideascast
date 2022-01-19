<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Project Sharing New Additions</title>
</head>

<body style="margin:0px; padding:0; font-family:Verdana, Arial, Helvetica, sans-serif;font-size:14px; background:#f0f0f0; line-height:23px;">
	<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" style="background:#fff; padding:0px 10px;" >
	  <tr>
		<td colspan="3" style="color:#016165; font-size:16px; padding:15px 10px;">Hello <?php if(isset($Custname)) echo $Custname; ?>, </td>
	  </tr>
	  <tr>
              <td colspan="3" style="padding:5px 10px;"><b><?php if(isset($name)) echo $name; ?></b> has shared some additional items in the Project,"<?php echo strip_tags($projectDetails['Project']['title']); ?>" with you. </td>
	  </tr>

	  <tr>
              <td colspan="3" style="padding:5px 10px;"> Please login into your OpusView account and go to the Received Projects area to find out more.</td>
	  </tr>
	  <tr>
		<td colspan="3" style="padding:5px 10px;">&nbsp;</td>
	  </tr>	  
	  <tr>
		<td colspan="3" style="padding:5px 10px;">Thank you very much.<br />
		The IdeasCast Team</td>
	  </tr>
	</table>
</body>
</html>