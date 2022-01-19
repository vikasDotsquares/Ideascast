<!DOCTYPE html>
<?php 

if(isset($_POST['test']))
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

$html = '<table><tr><td>test</td></tr></table>';

$phpWord = new \PhpOffice\PhpWord\PhpWord();

$section = $phpWord->addSection();


\PhpOffice\PhpWord\Shared\Html::addHtml($section, $html);

$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('test.docx');

// Saving the document as ODF file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'ODText');
//$objWriter->save('helloWorld6.odt');

// Saving the document as HTML file...
//$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
//$objWriter->save('helloWorld6.html');

header("location:test.docx");
           
//header("Content-type: application/vnd.ms-word");
//header("Content-Disposition: attachment;Filename=vikas.docx");
}



$file_type = 'doc';

$filename =   trim($this->request->params['pass'][1]);



//$filename =   "Project";

function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

$dt = clean($filename); 

$html =  '<html><body>'.  '<p>Put your html here, or generate it with your favourite '.   'templating system.</p>'.  '</body></html>';



            $file = $dt.".doc";
           header("Content-type: application/vnd.ms-word");
           header("Content-Disposition: attachment;Filename=$file");
          // echo $this->fetch('content');  
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
		<div class="wrapper" style="width:100%; text-align:center; ">
		<?php 
			 $menuprofile = $this->Session->read('Auth.User.UserDetail.document_pic');
			 $menuprofiles = SITEURL.USER_PIC_PATH.$menuprofile;

			if(!empty($menuprofile) && file_exists(USER_PIC_PATH.$menuprofile)){
				$menuprofiles = SITEURL.USER_PIC_PATH.$menuprofile;
			}else{
				$menuprofiles = SITEURL.'images/icast-logo-wo-tm.png';
			}
			
		?>
    	
		
        	<?php //echo $this->Html->image("logo.jpg", array("alt" => "IdeasCast", 'url' => array('controller'=>'customers','action'=>'index','home'))); ?>
			<img style="text-align: center;  margin-right:auto;
    margin-left:auto;margin: 0px auto ;padding:0;   " src="<?php echo $menuprofiles; ?>" alt="IdeasCast"/>
        
		</div>
		</div>
		
		
			<div class="section">
		
				<section class="details" style="text-align: center;  margin-right:auto;
    margin-left:auto; "> <?php echo $this->fetch('content');  ?> </section>
			
			</div>
			
	</div>

	<?php 	 echo $this->Js->writeBuffer(); // Write cached scripts ?>
</div>

		
    </body>
</html>
<?php die; ?>