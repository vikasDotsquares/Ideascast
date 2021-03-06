<?php
 
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Errors
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>

<section class="content">

  <div class="error-page">
	<h2 class="headline text-yellow"> 404</h2>
	<div class="error-content">
	  <h3><i class="fa fa-warning text-yellow"></i> Oops! Your database is not properly setup so, 
		</br>Please contact to your administrator or send an <a href="mailto:info@opusview.com">email</a> for more information.</h3>
		
	  <!-- <p>
		We could not find the page you were looking for.
		Meanwhile, you may <a href="<?php echo !empty(SUBSITEURL)? SUBSITEURL : 'https://www.opusview.com/'; ?>">return to website</a>.
	  </p>-->
	  
	</div><!-- /.error-content -->
  </div><!-- /.error-page -->
</section>
		
<style>
.error-page > .headline {
    float: none;
    font-size: 100px;
    font-weight: 300;
    text-align: center;
}

.error-page > .error-content > h3 {
    font-size: 25px;
    font-weight: 300;
    text-align: center;
}

p {
    margin: 0 0 10px;
}
</style>
 
 