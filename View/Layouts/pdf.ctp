<!DOCTYPE html>
<?php 

if(isset($_POST['vikas']))
{
require_once DOC_ROOT.'word/src/PhpWord/Autoloader.php';
\PhpOffice\PhpWord\Autoloader::register();

// Creating the new document...
$phpWord = new \PhpOffice\PhpWord\PhpWord();

/* Note: any element you append to a document must reside inside of a Section. */

// Adding an empty Section to the document...
$section = $phpWord->addSection();
// Adding Text element to the Section having font styled by default...


//echo '<h2>Adding Text element with font customized using named font style</h2>';
// Adding Text element with font customized using named font style
$fontStyleName = 'oneUserDefinedStyle';
$phpWord->addFontStyle(
    $fontStyleName,
    array('name' => 'Tahoma', 'size' => 10, 'color' => 'red', 'bold' => true)
);
// Adding Text element with font customized using explicitly created font style object...

$fontStyle = new \PhpOffice\PhpWord\Style\Font();
$fontStyle->setBold(true);
$fontStyle->setName('Tahoma');
$fontStyle->setSize(13);
$myTextElement  = $section->addText(
     strip_tags($this->fetch('content')),
    array('name' => 'Calibri', 'size' => 18,'color' => 'red')
);

//$myTextElement->setFontStyle($fontStyle);


// Saving the document as OOXML file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

//$objWriter->save('helloWorld6.docx');

// Saving the document as ODF file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'ODText');
//$objWriter->save('helloWorld6.odt');

// Saving the document as HTML file...
//$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
//$objWriter->save('helloWorld6.html');

//header("location:helloWorld6.docx");
           
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment;Filename=vikas.docx");
}



$file_type = 'doc';

$filename = 'myfile';

$html =  '<html><body>'.  '<p>Put your html here, or generate it with your favourite '.   'templating system.</p>'.  '</body></html>'; 

            $file = $filename.".doc";
           header("Content-type: application/vnd.ms-word");
           header("Content-Disposition: attachment;Filename=$file");
        //   echo $this->fetch('content');  
//die;
 
 ?>
<html lang="en">
<head>
  
    <meta charset="utf-8"> 
    <title><?php echo $title_for_layout; ?></title>
   
    <?php //echo $this->element('front/head_inner'); ?>	
	
</head>

<body class="skin-blue inner-view fixed" >
	
	<div class="ajax_text_overlay">
		<div id="ajax_overlay_text" class="ajax_overlay_text"  ></div> 
	</div>
	<?php // Below div is an overlay of body, while AJAX request is in progress. ?>
	
	<div id="ajax_overlay"><div class="ajax_overlay_loader"></div></div>
		
    <div class="wrapper">
		
		<div class="header1" style="border-bottom:solid 5px #016165; box-shadow:none; padding-bottom:5px;" >
		<div class="wrapper">
		<?php 
			 $menuprofile = $this->Session->read('Auth.User.UserDetail.document_pic');
			 $menuprofiles = SITEURL.USER_PIC_PATH.$menuprofile;

			if(!empty($menuprofile) && file_exists(USER_PIC_PATH.$menuprofile)){
				$menuprofiles = SITEURL.USER_PIC_PATH.$menuprofile;
			}else{
				$menuprofiles = SITEURL.'images/icast-logo-wo-tm.png';
			}
			
		?>
    	
		<div class="logo" style="width:100%; background:#fff; padding:15px; margin:auto; text-align:center">
        	<?php //echo $this->Html->image("logo.jpg", array("alt" => "IdeasCast", 'url' => array('controller'=>'customers','action'=>'index','home'))); ?>
			<img src="<?php echo $menuprofiles; ?>" alt="IdeasCast"/>
        </div>
		</div>
		</div>
		
		
			<div class="section">
		
				<section class="details" style="clear: both;"> <?php echo $this->fetch('content');  ?> </section>
			
			</div>
			
	</div>

<script type="text/javascript" >
$(function() {
	
	/*
	 * @todo  Hide each success flash message after 4 seconds
	 * */
	if( $("#successFlashMsg").length > 0 ) {
			
		setTimeout(function() {
			$("#successFlashMsg").animate({
				opacity: 0,
				height: 0
				}, 1000, function() {
				$(this).remove()
			})
			
		}, 4000)
		
	}
	
	/*
	 * @todo  Global setup of AJAX on document. It can be used when any ajax call is performed
	 * */
	$( document ).ajaxSend(function() {
		window.theAJAXInterval = 1;
		$("#ajax_overlay_text").textAnimate("..........");
		$(".ajax_text_overlay")
			.fadeIn(300)
			.bind('click', function(e) {
				$(this).fadeOut(300);
			});
		$("body").addClass('noscroll');
	})
	.ajaxComplete(function() {
		setTimeout( function() {
			$(".ajax_text_overlay").fadeOut(300); 
			$("body").removeClass('noscroll');
			clearInterval(window.theAJAXInterval); 
		}, 2000)
				
		// console.clear()
	});
	/*
	 * @todo  Initially stop all global AJAX events.
	 * */
	$.ajaxSetup({
		global: false
	})
	
	/*
	 * @todo  Also stop Global AJAX events on any modal window will triggered to shown. 
	 * 			It ensure that not to show ajax overlay twice. 
	 * 			Because modal window already have its own ajax overlay.
	 * @see  To start all global ajax setup, just turn on the global setup.
	 * @example  $.ajax({
	 * 					... ..,
	 * 					global: true,
	 * 					...
	 * 				})
	 * */
	$(".modal").on('show', function(e) {
		$.ajax({
			global: false	
		}); 	
	}) 
})
	
</script> 
	<?php 	echo $this->Js->writeBuffer(); // Write cached scripts ?>
</div>

		
    </body>
</html>
