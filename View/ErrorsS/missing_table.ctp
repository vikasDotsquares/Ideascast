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

<style>
.error-content{
	padding-top: 35px;
}
.error-page-cont .left {
    float: left;
}
.error-page-cont .right {
    overflow: hidden;
	padding-top: 20px;
	font-size: 26px;
	line-height: normal;
	padding-left:15px;
}
.error-page-cont .right p{
	margin-bottom:5px;
}
.error-page-cont h3{
	    font-size: 50px;
    font-weight: 500;
}

@media (max-width:991px){
.error-page-cont .left {   
    width: 200px;
}
.error-page-cont h3{
	font-size: 40px;
}
.error-page-cont .right {
	    font-size: 22px;
		
}
}
@media (max-width:767px){
.error-page-cont .left {   
    width: 170px;
}
.error-page-cont h3{
	font-size: 30px;
}
.error-page-cont .right {
	    font-size: 17px;
		
}
}
@media (max-width:540px){
.error-page-cont .left {   
   width: 170px;
	float:none;	
}
.error-page-cont .right{
	padding-top:5px;
	padding-left:0px;
}
@media (max-width:479px){
.error-page-cont h3 {
    font-size: 21px;
}
.error-page-cont .right {
    font-size: 16px;
}
}


</style>

<section class="content"> 
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div class="error-page-cont">            
					<div class="error-content">
							<div class="left">
								<img src="<?php echo SITEURL?>images/exclaim.png">
							</div>
							<div class="right">
								<?php 
									$message = 'Oops - There is a problem.';								
								?>
								<h3><?php echo $message; ?></h3>
								<p>Please contact your administrator.</p>
								<p>REF: Database.</p>
								<p>We could not find the page you were looking for.</p>
						  </div>
						  <!--<p>
							We could not find the page you were looking for.
							Meanwhile, you may <a href="<?php echo !empty(SUBSITEURL)? SUBSITEURL : 'http://www.ideascast.com/'; ?>">return to website</a>.
						  </p>-->              
					</div><!-- /.error-content -->
				</div><!-- /.error-page -->
			</div>
		</div> 
	</div>
</section>