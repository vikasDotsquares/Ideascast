<?php echo $this->element('email_header'); ?>
<tr>
<td style="padding:30px 20px; background-color: #fff;">
<table width="100%" align="left" cellspacing="0" cellpadding="0">
  <tr>
    <td style="padding-bottom: 10px;"><table  width="100%" align="left" cellspacing="0" cellpadding="0">
        <tr>
          <td style="font-size: 14px;">Hi <?php if(isset($fullname)) echo $fullname; ?>,</td>
        </tr>
        <tr>
          <td style="font-weight: 600; font-size: 18px; color: #000; padding: 20px 0 30px 0;">Someone has added competencies that you are watching. </td>
        </tr>
        <tr>
          <td style="font-size: 13px;"><strong>Watch Name:</strong> <?php if(isset($watch_name)) echo $watch_name; ?></td>
        </tr>
      </table></td>
  </tr>
  <?php

  foreach( $users as $k =>  $u) {
  	?>
  <tr>
    <td style="padding-top: 10px;"><table width="35%" align="left" cellspacing="0" cellpadding="0">

        <tr>
          <td align="left" valign="top" width="42" style="padding-top: 4px;"><table width="100%" align="left" cellspacing="0" cellpadding="0">
              <tr>
			  <?php
			  if(!empty($u['user_data']['image']) && file_exists(USER_PIC_PATH.$u['user_data']['image'])){
				$profilesPic = SITEURL.USER_PIC_PATH.$u['user_data']['image'];
			  } else {
					$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
			  }
			  ?>
                <td align="left" valign="top" width="40" style="font-size: 14px; border: 2px solid #ccc;"><img src="<?php echo $profilesPic;?>" width="40" height="40" alt="user image"></td>
              </tr>
            </table></td>
          <td align="left" valign="top" style="padding-left: 10px;"><p style="font-size: 14px; color: #444; margin: 0; padding: 0;"><?php echo $u['user_data']['user']; ?></p>
            <p style="font-size: 13px; color: #777; margin: 0; padding: 0;"><?php echo ( isset($u['user_data']['userrole']) && !empty($u['user_data']['userrole']) ) ? $this->Text->truncate(
                          $u['user_data']['userrole'],
                          20,
                          array(
                              'ellipsis' => '...',
                              'exact' => false
                          )
                      ) : 'None'; ?></p>
			<?php

      if( $currentOrg != $u['user_data']['organization_id'] ){ ?>

            <p style="font-size: 13px; color: #444; margin: 0; padding: 0; line-height: 16px;">Not in Your Org</p></td>
			<?php } ?>
        </tr>

      </table>
      <table width="60%" align="right" cellspacing="0"  cellpadding="0">
	   <?php
	   if(isset($u['skills']) && !empty($u['skills'])){
	   foreach($u['skills'] as $skill){  //pr( $skill);

		$level_icon = $this->Permission->level_exp_img($skill['user_level']);
		$exp_icon = $this->Permission->level_exp_img($skill['user_experience'], false);
	    ?>
        <tr>
          <td valign="top" align="left" style="padding-bottom: 5px;"><table align="left" cellspacing="0" cellpadding="0">
              <tr>
                <td  valign="middle"style="background-color: #ededed;border-left: 3px solid #3c8dbc; padding: 3px 8px 3px 7px;">
				<span style="display: inline-block; padding-left: 3px;vertical-align: middle;"><img src="<?php echo SITEURL;?>images/icons/SkillsBlack18x18.png" alt="Skills"></span>
				<span style="display: inline-block;vertical-align: middle;"> <img src="<?php echo SITEURL;?>images/icons/<?php echo $level_icon; ?>" alt="Skills"> </span>
				<span style="display: inline-block;vertical-align: middle;"><img src="<?php echo SITEURL;?>images/icons/<?php echo $exp_icon; ?>" alt="Skills"> </span> <?php echo '&nbsp;'.$skill['title']; ?> </td>
              </tr>
            </table></td>
        </tr>

	   <?php } } ?>

		<?php
		 if(isset($u['subjects']) && !empty($u['subjects'])){
		foreach($u['subjects'] as $sb){  //pr( $sb);

		$level_icon = $this->Permission->level_exp_img($sb['user_level']);
		$exp_icon = $this->Permission->level_exp_img($sb['user_experience'], false);
	    ?>
        <tr>
          <td valign="top" align="left" style="padding-bottom: 5px;"><table align="left" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="middle"  style="background-color: #ededed;border-left: 3px solid #c55a11; padding: 3px 8px 3px 7px;">
				<span style="display: inline-block; padding-left: 3px;vertical-align: middle; "><img src="<?php echo SITEURL;?>images/icons/SubjectsBlack18x18.png" alt="subjects"></span>
				<span style="display: inline-block;vertical-align: middle;"> <img src="<?php echo SITEURL;?>images/icons/<?php echo $level_icon; ?>" alt="subjects"> </span>
				<span style="display: inline-block;vertical-align: middle;"><img src="<?php echo SITEURL;?>images/icons/<?php echo $exp_icon; ?>" alt="subjects"> </span><?php echo '&nbsp;'.$sb['title']; ?> </td>
              </tr>
            </table></td>
        </tr>

		 <?php } } ?>

		<?php
		if(isset($u['domains']) && !empty($u['domains'])){
		foreach($u['domains'] as $dm){  //pr( $dm);

		$level_icon = $this->Permission->level_exp_img($dm['user_level']);
		$exp_icon = $this->Permission->level_exp_img($dm['user_experience'], false);
	    ?>
        <tr>
          <td valign="top" align="left" style="padding-bottom: 5px;"><table align="left" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="middle" style="background-color: #ededed;border-left: 3px solid #5f9322; padding: 3px 8px 3px 7px;">
				<span style="display: inline-block; padding-left: 3px;vertical-align: middle;"><img src="<?php echo SITEURL;?>images/icons/DomainBlack18x18.png" alt="domains"></span>
				<span style="display: inline-block;vertical-align: middle;"> <img src="<?php echo SITEURL;?>images/icons/<?php echo $level_icon; ?>" alt="domains"> </span>
				<span style="display: inline-block;vertical-align: middle;"><img src="<?php echo SITEURL;?>images/icons/<?php echo $exp_icon; ?>" alt="domains"> </span> <?php echo '&nbsp;'.$dm['title']; ?> </td>
              </tr>
            </table></td>
        </tr>
		<?php } } ?>
      </table></td>
  </tr>
  <?php  } ?>
  <tr>
		  <td align="left" style=" padding:15px 0px 10px 0;font-family:Arial, sans-serif;"><a href="<?php echo $siteurl;?>"><img src="<?php echo SITEURL;?>images/email_open.png" style="border:none;" alt="open-btn"></a></td>
		</tr>
		</table>
	</td>
	</tr>

<?php echo $this->element('email_footer'); ?>