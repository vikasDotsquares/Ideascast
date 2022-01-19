<?php
//error_reporting(0);
require_once WWW_ROOT . 'PHPWord-P/src/PhpWord/Autoloader.php';

use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;

Autoloader::register();
Settings::loadConfig();
$spaceAfter = 10;
// New Word document
$phpWord = new \PhpOffice\PhpWord\PhpWord();

$phpWord->setDefaultFontName('Cambria (Headings)');

$phpWord->setDefaultFontSize(11);
$phpWord->addParagraphStyle('My Style', array('spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(10)));

$linewidth = 930;

$section = $phpWord->addSection(array('orientation' => 'landscape'));


//Header
$header = $section->addHeader();
$header->firstPage();

// Add footer
$footer = $section->addFooter();
$footer->addLine(['weight' => 2, 'color' => '635552', 'width' => $linewidth, 'height' => 0]);
$table = $footer->addTable();
$table->addRow();
$cellfooterleft = $table->addCell(7500);
$cellfooterright = $table->addCell(7500);
$cellfooterleft->addLink('http://ideascast.com', htmlspecialchars('IdeasCast Limited'));
$cellfooterright->addPreserveText(htmlspecialchars('Page {PAGE} of {NUMPAGES}'), null, array('align' => 'right'));


$phpWord->addTitleStyle(1, array('size' => 20, 'bold' => true, 'align' => 'right'), array('numStyle' => 'headingNumbering', 'numLevel' => 2));
$section->addTextRun(array('align' => 'center'));

$user_data = $this->ViewModel->get_user_data($this->Session->read("Auth.User.id"));
$user_id = $this->Session->read("Auth.User.id");
$pic = $user_data['UserDetail']['profile_pic'];
$profiles = SITEURL . USER_PIC_PATH . $pic;
$job_title = $user_data['UserDetail']['job_title'];
$user_name = $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'];


if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
    $profiles = SITEURL . USER_PIC_PATH . $pic;
}





// First Page start here

$section->addTextBreak(3);
$fontStyle = array('size' => 26, 'bold' => true);
$phpWord->addFontStyle('fStyle', $fontStyle);
$phpWord->addParagraphStyle('pStyle', array('align' => 'center', 'spaceAfter' => 100));
$text = $section->addText(htmlspecialchars(strtoupper($document_title)), 'fStyle', 'pStyle');

$paragraphStyle = array('align' => 'center');
$phpWord->addParagraphStyle('pStyle', $paragraphStyle);
$text = $section->addText(htmlspecialchars('Date Report - ' . _displayDate(date("d M Y"),$formate = 'd M Y') ), array('size' => 20, 'bold' => false), 'pStyle');

$paragraphStyle = array('align' => 'center');
$phpWord->addParagraphStyle('pStyle', $paragraphStyle);
$text = $section->addText(htmlspecialchars($user_name), array('size' => 20, 'bold' => false), 'pStyle');



if (isset($is_show_doc_img) && !empty($is_show_doc_img) && $is_show_doc_img == 'Y') {
    $menuprofile = $this->Session->read('Auth.User.UserDetail.document_pic');


	$menuprofiles =  USER_PIC_PATH . $menuprofile;

    if (!empty($menuprofile) && file_exists(USER_PIC_PATH . $menuprofile)) {
        $menuprofiles =   USER_PIC_PATH . $menuprofile;
        $section->addImage($menuprofiles, array('width' => 230, 'align' => 'center'));
    }
    //$section->addTextBreak(2);
}
$section->addLine(['weight' => 2, 'color' => '000000', 'width' => $linewidth, 'height' => 0]);
if (isset($is_show_project_img) && !empty($is_show_project_img) && $is_show_project_img == 'Y') {

    if(isset($data[$project_id]['image_file']) && !empty($data[$project_id]['image_file'])){
        $projectImg = $data[$project_id]['image_file'];
        $projectfiles =  UPLOAD.'project/' . $projectImg;
        if (!empty($projectfiles) && file_exists(UPLOAD.'project/' . $projectImg)) {
            $section->addImage($projectfiles, array('width' => 350,'height'=>105, 'align' => 'center'));
        }
    }
}

// End first page here

$section = $phpWord->addSection(array('orientation' => 'landscape'));

$fontStyle = array('size' => 18, 'bold' => true);
$phpWord->addFontStyle('fStyle', $fontStyle);
$phpWord->addParagraphStyle('pStyle', array('align' => 'center', 'spaceAfter' => 100));
$text = $section->addText(htmlspecialchars(strtoupper('CONTENT')), 'fStyle', 'pStyle');


function remove($str, $allow = 0) {
    if ($allow > 0) {
        $str = trim($str);
    } else {
        $str = strip_tags(trim($str));
    }

	$str =  str_replace ("&nbsp;" , " " , $str  );
	$str = html_entity_decode(strip_tags($str));
	$str = str_replace("'", "", $str);
	$str = str_replace('"', "", $str);
	/* $str = str_replace('/', "", $str);
	$str = str_replace('-', "", $str);
	$str = str_replace('(', "", $str);
	$str = str_replace(')', "", $str); */
	//$str = str_replace(',', "", $str);
	$str = str_replace('&', " and ", $str);


    return ucfirst($str);
}



$section->addLine(['weight' => 1, 'width' => $linewidth, 'height' => 0]);





$phpWord->addFontStyle('myOwnStyle', array('spaceAfter' => 10));
$phpWord->addParagraphStyle('P-Style', array('spaceAfter' => 10));
$phpWord->addNumberingStyle(
        'multilevel', array(
    'type' => 'multilevel',
    'levels' => array(
        array('format' => 'decimal', 'text' => '%1.', 'left' => 360, 'hanging' => 360, 'tabPos' => 360),
        array('format' => 'upperLetter', 'text' => '%2.', 'left' => 720, 'hanging' => 360, 'tabPos' => 720),
    ),
        )
);

//$textrun = $section->addTextRun();
//$textrun->addText("                    ",null,null);
//$textrun->addImage('http://192.168.4.29/ideascomposer/uploads/project/Jellyfish_27_780x150.jpg', array('width' => 20,'height'=>11, 'align' => 'center'));
//$textrun->addLink('SUMMARY', htmlspecialchars(strtoupper(' Summary')), null, null, true);
//
//$textrun = $section->addTextRun();
//$textrun->addText("                                ",null,null);
//$textrun->addImage('http://192.168.4.29/ideascomposer/uploads/project/Jellyfish_27_780x150.jpg', array('width' => 20,'height'=>11, 'align' => 'center'));
//$textrun->addLink('SUMMARY', htmlspecialchars(strtoupper(' Summary')), null, null, true);
//
//$textrun = $section->addTextRun();
//$textrun->addText("                                           ",null,null);
//$textrun->addImage('http://192.168.4.29/ideascomposer/uploads/project/Jellyfish_27_780x150.jpg', array('width' => 20,'height'=>11, 'align' => 'center'));
//$textrun->addLink('SUMMARY', htmlspecialchars(strtoupper(' Summary')), null, null, true);
//



$predefinedMultilevel = array('listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED);

$linkIsInternal = true;




$summary = $section->addListItemRun(0, array('spaceAfter' => 10), 'myOwnStyle', $predefinedMultilevel, 'P-Style');
$dotstr = ' ';
 //for($i = 0;$i <= (115 - strlen('Summary')) ; $i++){$dotstr .= '. ';}
$summary->addLink('SUMMARY', htmlspecialchars(strtoupper(' Summary'.$dotstr)), null, null, $linkIsInternal);


$Peopleonproject = $section->addListItemRun(0, array('spaceAfter' => 10), 'myOwnStyle', $predefinedMultilevel, 'P-Style');
$dotstr = ' ';
//for($i = 0;$i <= (115 - strlen('People on project')) ; $i++){$dotstr .= '. ';}
$Peopleonproject->addLink('Peopleonproject', htmlspecialchars(strtoupper('People on project'.$dotstr)), null, null, $linkIsInternal);

$Keytargetresults = $section->addListItemRun(0, array('spaceAfter' => 10), 'myOwnStyle', $predefinedMultilevel, 'P-Style');
$dotstr = ' ';
//for($i = 0;$i <= (115 - strlen('Key target results')) ; $i++){$dotstr .= '. ';}
$Keytargetresults->addLink('Keytargetresults', htmlspecialchars(strtoupper('Key target results'.$dotstr)), null, null, $linkIsInternal);




foreach ($data as $key => $project) {
    if (isset($project['Workspace']) && !empty($project['Workspace'])) {
        $row_counter = 0;
        foreach ($project['Workspace'] as $key => $workspace) {
            if ((isset($workspace['id']) && !empty($workspace['id'])) && (isset($workspace['title']) && !empty($workspace['title']))) {
                //$section->addListItem(strtoupper(htmlspecialchars(remove($workspace['title']))), 1, 'myOwnStyle', $predefinedMultilevel, 'P-Style');

				$workspaceTitle = html_entity_decode(strip_tags($workspace['title']));
				$workspaceTitle = str_replace("'", "", $workspaceTitle);
				$workspaceTitle = str_replace('"', "", $workspaceTitle);
				//$workspaceTitle = preg_replace('/[^A-Za-z0-9\-]/', '', $workspaceTitle);

                $workname = remove($workspaceTitle);
                $workspace_key = $section->addListItemRun(1, 1, 'myOwnStyle', $predefinedMultilevel, 'P-Style');
                $dotstr = ' ';
                $count = 109-strlen($workname);

                //for($i = 0;$i <=  $count; $i++){$dotstr .= '. ';}

                 $workspace_key->addImage('images/icons/word_icon/fa-th.jpg', array( 'width' => 13,'height'=>11 ) );


                $workspace_key->addLink('workspace_link_'.$workspace['id'],htmlspecialchars(strtoupper(' '.$workname)) .$dotstr, null, null, $linkIsInternal);
                $count = $dotstr = null;
                if (isset($workspace['Area']) && !empty($workspace['Area'])) {
                    foreach ($workspace['Area'] as $a_key => $area) {
                        //$section->addListItem(strtoupper(htmlspecialchars(remove($area['title']))), 2, 'myOwnStyle', $predefinedMultilevel, 'P-Style');

						$areaTitle = html_entity_decode(strip_tags($area['title']));
						$areaTitle = str_replace("'", "", $areaTitle);
						$areaTitle = str_replace('"', "", $areaTitle);
						//$areaTitle = preg_replace('/[^A-Za-z0-9\-]/', '', $areaTitle);

                        $areaname = remove($areaTitle);
                        $area_key = $section->addListItemRun(2, 2, 'myOwnStyle', $predefinedMultilevel, 'P-Style');
                        $dotstr = ' ';
                        $count = 105-strlen($areaname);

                       //for($i = 0;$i <=  $count; $i++){$dotstr .= '. ';}

                        $area_key->addImage('images/icons/word_icon/fa-list-alt.jpg', array('width' => 13,'height'=>11, 'align' => 'center'));
                        $area_key->addLink('area_link_'.$area['id'], htmlspecialchars(strtoupper(' '.$areaname.$dotstr)), null, null, $linkIsInternal);

                        if (isset($area['Element']) && !empty($area['Element'])) {
                            foreach ($area['Element'] as $element) {
                                $element['Element']['title'] = trim($element['Element']['title']);
                                if (isset($element['Element']['title']) && !empty($element['Element']['title'])) {
                                    //$section->addListItem(strtoupper(htmlspecialchars(remove($element['Element']['title']))), 3, 'myOwnStyle', $predefinedMultilevel, 'P-Style');

									$elementTitle = html_entity_decode(strip_tags($element['Element']['title']));
									$elementTitle = str_replace("'", "", $elementTitle);
									$elementTitle = str_replace('"', "", $elementTitle);
/* 									$elementTitle = str_replace('/', "", $elementTitle);
									$elementTitle = str_replace('-', "", $elementTitle);
									$elementTitle = str_replace('(', "", $elementTitle);
									$elementTitle = str_replace(')', "", $elementTitle);
									$elementTitle = str_replace(',', "", $elementTitle);
									$elementTitle = str_replace('&', "", $elementTitle);*/
									//$elementTitle = preg_replace('/[^A-Za-z0-9\-]/', '', $elementTitle);

                                    $elementname = remove($elementTitle);
                                    $element_key = $section->addListItemRun(3, 3, 'myOwnStyle', $predefinedMultilevel, 'P-Style');
                                    $dotstr = ' ';
                                    $count = 97-strlen($elementname);

                                    //for($i = 0;$i <=  $count; $i++){$dotstr .= '. ';}

                                    if(isset($element['Element']['id'] ) && !empty($element['Element']['id'])){
                                        $element_key->addImage('images/icons/word_icon/black_md-b.jpg', array('width' => 13,'height'=>11, 'align' => 'center'));
                                        $element_key->addLink('element_link_'.$element['Element']['id'], htmlspecialchars(strtoupper(' '.$elementname.$dotstr)), null, null, $linkIsInternal);
                                    }
                                }
                            }
                        }

                    }
                }
            }
        }
    }
}


// End second page here

$section = $phpWord->addSection(array('orientation' => 'landscape'));
$phpWord->setDefaultFontName('Calibri (Body)');
// Thired Page here start
foreach ($data as $key => $project) {

    /*     * ****************  First ********************************************************************************************************** */
    $phpWord->addTableStyle('FirstTable', array('borderSize' => 6, 'borderColor' => 'C00000', 'cellMargin' => 80, 'cellMarginBottom' => 0), array('borderBottomSize' => 18, 'bold' => true, 'borderBottomColor' => 'E0301D', 'bgColor' => 'C00000'));
    $tableFirst = $section->addTable('FirstTable');
    $tableFirst->addRow(700);
    $t_text_h = $tableFirst->addCell(3000, array('valign' => 'center'));
    $textrun = $t_text_h->addTextRun();
    $textrun->addText(htmlspecialchars('Business Report:'),array('size' => 18, 'color' => 'FFFFFF', 'bold' => true), array('align' => 'center', 'spaceAfter' => 5));


    $t_text = $tableFirst->addCell(12000,array('valign' => 'center'));
    $textrun = $t_text->addTextRun();
    $s =  array('align' => 'center', 'spaceAfter' => 5);
    $textrun->addText(htmlspecialchars('Created By: '), array('valign' => 'center','color' => 'FFFFFF', 'bold' => true),$s);
    $textrun->addText( htmlspecialchars($user_name), array('valign' => 'center','color' => 'FFFFFF', 'bold' => true), $s);
    $textrun->addText(htmlspecialchars(',  Created: '), array('valign' => 'center','color' => 'FFFFFF', 'bold' => true), $s);
    $textrun->addText( htmlspecialchars(_displayDate(date("Y-m-d h:i:s A"))), array('valign' => 'center', 'bold' => true,'color' => 'FFFFFF'), $s);


    $section->addTextBreak(1, array('size' => 1));


    /*     * ****************  Second ********************************************************************************************************** */
    $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
    $styleFirstRow = array('borderBottomSize' => 18, 'bold' => true, 'borderBottomColor' => '000000', 'bgColor' => $this->Phpword->get_color_code($project['color_code']));
    $styleCellHeading = array('valign' => 'bottom', 'gridSpan' => 8);
    $styleCellBTLR = array('valign' => 'center', 'textDirection' => \PhpOffice\PhpWord\Style\Cell::TEXT_DIR_BTLR);
    $fontStyleHeading = array('bold' => true, 'size' => 11,'color' =>'FFFFFF', 'align' => 'center');
    $cellColSpan = array('gridSpan' => 2, 'valign' => 'center');
    $styleCell = array('valign' => 'top');
    $fontStyle = array('bold' => true, 'align' => 'center');


    $phpWord->addTableStyle('Fancy Table', $styleTable, $styleFirstRow);
    $section->addBookmark('SUMMARY');
    $table = $section->addTable('Fancy Table');

    $table->addRow();
    $table->addCell(15000, $styleCellHeading)->addText(htmlspecialchars('SUMMARY'), $fontStyleHeading, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $table->addRow();
    $table->addCell(3000, $styleCell)->addText(htmlspecialchars('Project Title'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));

	$projectTitle = html_entity_decode(strip_tags($project['title']));
	///$projectTitle = str_replace("'", "", $projectTitle);
	//$projectTitle = str_replace('"', "", $projectTitle);
	//$projectTitle = preg_replace('/[^A-Za-z0-9\-]/', '', $projectTitle);

    $table->addCell(11000, array('gridSpan' => 7))->addText(htmlspecialchars(remove($projectTitle)), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $table->addRow();
    $table->addCell(3000, $styleCell)->addText(htmlspecialchars('Project Type'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $alignement = get_alignment($project['aligned_id']);
    $align = "N/A";
    if (!empty($alignement)) {
        $align = $alignement['title'];
    }
    //$table->addCell(11000, array('gridSpan'=>7))->addText(htmlspecialchars(remove($align)), null,array('align'=>'both', 'spaceAfter'=>$spaceAfter));
    $cell_align = $table->addCell(11000, array('gridSpan' => 7), array('align' => 'both', 'spaceAfter' => $spaceAfter));
    \PhpOffice\PhpWord\Shared\Html::addHtml($cell_align, $align);

    $table->addRow();
    $table->addCell(3000, $styleCell)->addText(htmlspecialchars('Project Objective'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    //$table->addCell(11000, array('gridSpan'=>7))->addText(htmlspecialchars(remove($project['objective'])), null,array('align'=>'both', 'spaceAfter'=>$spaceAfter));
    $cell_objective = $table->addCell(11000, array('gridSpan' => 7), array('align' => 'both', 'spaceAfter' => $spaceAfter));

	$projectObjective = html_entity_decode(strip_tags($project['objective']));
	$projectObjective = str_replace("'", "", $projectObjective);
	$projectObjective = str_replace('"', "", $projectObjective);
	//$projectObjective = preg_replace('/[^A-Za-z0-9\-]/', '', $projectObjective);

    \PhpOffice\PhpWord\Shared\Html::addHtml($cell_objective, $projectObjective);

    $table->addRow();
    $table->addCell(3000, $styleCell)->addText(htmlspecialchars('Description'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    //$table->addCell(11000, array('gridSpan'=>7))->addText(htmlspecialchars(remove($project['description'])), null,array('align'=>'both', 'spaceAfter'=>$spaceAfter));
    $cell_description = $table->addCell(11000, array('gridSpan' => 7), array('align' => 'both', 'spaceAfter' => $spaceAfter));

	$projectDescription = html_entity_decode(strip_tags($project['description']));
	$projectDescription = str_replace("'", "", $projectDescription);
	$projectDescription = str_replace('"', "", $projectDescription);
	//$projectDescription = preg_replace('/[^A-Za-z0-9\-]/', '', $projectDescription);

    \PhpOffice\PhpWord\Shared\Html::addHtml($cell_description, $projectDescription);



    $table->addRow();
    $table->addCell(3000, $styleCell)->addText(htmlspecialchars('Project Schedule'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $start = $table->addCell(11000, array('gridSpan' => 7))->addText(htmlspecialchars('Start: ' . _displayDate(date("d M Y", strtotime($project['start_date'])),$formate = 'd M Y') . ' End: ' . _displayDate(date("d M Y", strtotime($project['end_date'])),$formate = 'd M Y')), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));


	$table->addRow();
    $table->addCell(3000, $styleCell)->addText(htmlspecialchars('RAG Status'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $RAG = $this->Common->getRAG($key);

    if(isset($RAG['rag_color']) && $RAG['rag_color'] == 'bg-green'){
        $rag_class = 'Green';
    }else if(isset($RAG['rag_color']) && $RAG['rag_color'] == 'bg-red'){
        $rag_class = 'Red';
    }else{
        $rag_class = 'Amber';
    }


    $start = $table->addCell(11000, array('gridSpan' => 7))->addText(htmlspecialchars($rag_class), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));


    $totalEle = $totalWs = 0;
    $totalAssets = null;
    $projectData = $this->ViewModel->getProjectDetail($project['id']);
    //$wsList = Set::extract($projectData, '/ProjectWorkspace/workspace_id');
     $wsList = get_project_workspace($project['id'],1);

	$totalWs = ( isset($wsList) && !empty($wsList) ) ? count($wsList) : 0;
	$elementdatas = workspace_elements(array_keys($wsList));
	$elementids = Set::extract($elementdatas, '/Element/id');

	// all total assingment

	$totalNotStartElement = $this->Common->project_element_count($elementids, 'STATUS_NOT_STARTED');
	$totalProgressElement = $this->Common->project_element_count($elementids, 'STATUS_PROGRESS');
	$totalOverdueElement = $this->Common->project_element_count($elementids, 'STATUS_OVERDUE');
	$totalCompletedElement = $this->Common->project_element_count($elementids, 'STATUS_COMPLETED');
	$totalNotSpecifiedElement = $this->Common->project_element_count($elementids, 'STATUS_NOT_SPACIFIED');
	$totalNotAssignedElement = $this->Common->project_element_count($elementids, 'STATUS_NOT_ASSIGNED', $project['id']);

    if (isset($project['Workspace']) && !empty($project['Workspace'])) {
        $row_counter = 0;
        foreach ($project['Workspace'] as $key => $val) {
            $workspace_data = $val;

            $wsData = $this->ViewModel->countAreaElements($workspace_data['id']);
            $totalEle += $wsData['active_element_count'];
            if (isset($wsData['assets_count']) && !empty($wsData['assets_count'])) {

                foreach ($wsData as $k => $subArray) {
                    if (is_array($subArray)) {
                        foreach ($subArray as $m => $value) {
                            if (!isset($totalAssets[$m]))
                                $totalAssets[$m] = $value;
                            else
                                $totalAssets[$m] += $value;
                        }
                    }
                }
            }
        }
    }


    $table->addRow();
    $table->addCell(3000, $styleCell)->addText(htmlspecialchars('No. Workspaces'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $table->addCell(11000, array('gridSpan' => 7))->addText(htmlspecialchars((isset($totalWs) && !empty($totalWs)) ? $totalWs : 0), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

	//========= No. of Tasks Start ===============
     $table->addRow();
    $table->addCell(3000, $styleCell)->addText(htmlspecialchars('Tasks'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    //$table->addCell(1600, array('gridSpan' => 7))->addText(htmlspecialchars((!empty($totalEle)) ? "Total:".$totalEle : "Total:0" ), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

	$cell = $table->addCell(1600, $styleCell);
	$cell->addText(htmlspecialchars('Total:'), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $cell->addText(htmlspecialchars((!empty($totalEle)) ? $totalEle : 0 ), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));


	$cell = $table->addCell(1600, $styleCell);
    $cell->addText(htmlspecialchars('Not Assigned: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $cell->addText(htmlspecialchars($totalNotAssignedElement), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

	$cell = $table->addCell(1600, $styleCell);
    $cell->addText(htmlspecialchars('No Schedule: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $cell->addText(htmlspecialchars($totalNotSpecifiedElement), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

	$cell = $table->addCell(1600, $styleCell);
    $cell->addText(htmlspecialchars('Not Started: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $cell->addText(htmlspecialchars($totalNotStartElement), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

	$cell = $table->addCell(1600, $styleCell);
    $cell->addText(htmlspecialchars('Progressing: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $cell->addText(htmlspecialchars($totalProgressElement), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

	$cell = $table->addCell(1600, $styleCell);
    $cell->addText(htmlspecialchars('Completed: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $cell->addText(htmlspecialchars($totalCompletedElement), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

	$cell = $table->addCell(1600, $styleCell);
    $cell->addText(htmlspecialchars('Overdue: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $cell->addText(htmlspecialchars($totalOverdueElement), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

	//========= No. of Tasks End ===============

    $table->addRow();
    $table->addCell(3000, $styleCell)->addText(htmlspecialchars('No. Resources'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $link = (isset($totalAssets['links']) && !empty($totalAssets['links'])) ? $totalAssets['links'] : 0;
    $cell = $table->addCell(1600, $styleCell);
    $cell->addText(htmlspecialchars('Links: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $cell->addText(htmlspecialchars($link), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $notes = (isset($totalAssets['notes']) && !empty($totalAssets['notes'])) ? $totalAssets['notes'] : 0;
    $cell = $table->addCell(1600, $styleCell);
    $cell->addText(htmlspecialchars('Notes: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $cell->addText(htmlspecialchars($notes), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $documents = (isset($totalAssets['docs']) && !empty($totalAssets['docs'])) ? $totalAssets['docs'] : 0;
    $cell = $table->addCell(1700, $styleCell);
    $cell->addText(htmlspecialchars('Documents: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $cell->addText(htmlspecialchars($documents), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $mindmaps = (isset($totalAssets['mindmaps']) && !empty($totalAssets['mindmaps'])) ? $totalAssets['mindmaps'] : 0;
    $cell = $table->addCell(1700, $styleCell);
    $cell->addText(htmlspecialchars('Mind Maps: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $cell->addText(htmlspecialchars($mindmaps), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $decision = (isset($totalAssets['decisions']) && !empty($totalAssets['decisions'])) ? $totalAssets['decisions'] : 0;
    $cell = $table->addCell(1600, $styleCell);
    $cell->addText(htmlspecialchars('Decision: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $cell->addText(htmlspecialchars($decision), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $feedback = (isset($totalAssets['feedbacks']) && !empty($totalAssets['feedbacks'])) ? $totalAssets['feedbacks'] : 0;
    $cell = $table->addCell(1700, $styleCell);
    $cell->addText(htmlspecialchars('Feedback: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $cell->addText(htmlspecialchars($feedback), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $votes = (isset($totalAssets['votes']) && !empty($totalAssets['votes'])) ? $totalAssets['votes'] : 0;
    $cell = $table->addCell(1600, $styleCell);
    $cell->addText(htmlspecialchars('Votes: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $cell->addText(htmlspecialchars($votes), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

	/*========== Added by Dev 3rd 20th December 2017 ===========================*/

		$crysymbol = '&pound;';
		if( !empty($projectData['Project']['currency_id']) ){

			// $projectCurrencyName = $this->Common->getCurrencySymbolName($projectData['Project']['currency_id']);
			$projectCurrencyName = $this->Common->getCurrencySymbolName($projectData['Project']['id']);

			if($projectCurrencyName == 'USD') {
				//$projectCurrencysymbol = '<i class="fa fa-dollar"></i>';
				//$projectCurrencysymbol = '&dollar;';
				$projectCurrencysymbol = '$';
			}
			else if($projectCurrencyName == 'GBP') {
				//$projectCurrencysymbol = '<i class="fa fa-gbp"></i>';
				$projectCurrencysymbol = '&pound;';
			}
			else if($projectCurrencyName == 'EUR') {
				//$projectCurrencysymbol = '<i class="fa fa-eur"></i>';
				$projectCurrencysymbol = '&euro;';
			}
			else if($projectCurrencyName == 'DKK' || $projectCurrencyName == 'ISK') {
				//$projectCurrencysymbol = '<span style="font-weight: 600">Kr</span>';
				$projectCurrencysymbol = 'Kr';
			}

			if( isset($projectCurrencysymbol) && !empty($projectCurrencysymbol) ){
				$crysymbol = $projectCurrencysymbol;
			}
		}

		/*==================== Project Cost ==================================*/
		$table->addRow();
		$table->addCell(3000, $styleCell)->addText(htmlspecialchars('Project Costs'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));


		$project_wsps = get_project_workspace($projectData['Project']['id']);
		$workspaces = Set::extract($project_wsps, '/Workspace/id');
		$all_workspace_elements = workspace_elements($workspaces);

		$westimate_sum = $wspend_sum = 0;
		if(isset($all_workspace_elements) && !empty($all_workspace_elements)){
			$wels = Set::extract($all_workspace_elements, '/Element/id');
			$westimate_sum = $this->ViewModel->wsp_element_cost($wels, 1);
			$wspend_sum = $this->ViewModel->wsp_element_cost($wels, 2);
		}

/* 		$projectBudget = $crysymbol.number_format($projectData['Project']['budget'],2, '.', '');
		$estimatedBudget = $crysymbol.number_format($westimate_sum,2, '.', '');
		$spendBudget = $crysymbol.number_format($wspend_sum,2, '.', ''); */

		$projectBudget = $crysymbol.number_format($projectData['Project']['budget'],2, '.', '');
		$estimatedBudget = $crysymbol.number_format($westimate_sum,2, '.', '');
		$spendBudget = $crysymbol.number_format($wspend_sum,2, '.', '');

		$cell = $table->addCell(1600, array('gridSpan' => 2),$styleCell);
		$cell->addText(htmlspecialchars('Budget: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

		\PhpOffice\PhpWord\Shared\Html::addHtml($cell, $projectBudget);

		$cell = $table->addCell(1600, array('gridSpan' => 2),$styleCell);
		$cell->addText(htmlspecialchars('Estimated Cost: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
		\PhpOffice\PhpWord\Shared\Html::addHtml($cell, $estimatedBudget);

		$cell = $table->addCell(1600, array('gridSpan' => 3),$styleCell);
		$cell->addText(htmlspecialchars('Spend: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
		\PhpOffice\PhpWord\Shared\Html::addHtml($cell, $spendBudget);


	/*================= Risk exposer and status ==============================*/

	$highRisks = project_risks_exposer($project['id'], 'high');
	$severeRisks = project_risks_exposer($project['id'], 'severe');
	if( empty($highRisks) ){
		$highRisks = 0;
	}
	if( empty($severeRisks) ){
		$severeRisks = 0;
	}

	$project_risks_status = project_risks_status($project['id']);
	$total_open = 0;
	$total_review = 0;
	$total_signoff = 0;
	$total_overdue = 0;
    if(isset($project_risks_status) && !empty($project_risks_status)) {
        $rbeat = [];
        foreach ($project_risks_status as $key => $value) {
            $rbeat[] = $value['rd'];
        }
        $open_status = arraySearch($rbeat, 'status', 1);
        $review_status = arraySearch($rbeat, 'status', 2);
        $signoff_status = arraySearch($rbeat, 'status', 3);
        $overdue_status = arraySearch($rbeat, 'status', 4);

        $total_open = (isset($open_status) && !empty($open_status)) ? count($open_status) : 0;
        $total_review = (isset($review_status) && !empty($review_status)) ? count($review_status) : 0;
        $total_signoff = (isset($signoff_status) && !empty($signoff_status)) ? count($signoff_status) : 0;
        $total_overdue = (isset($overdue_status) && !empty($overdue_status)) ? count($overdue_status) : 0;
    }
	/*================ ========================== ======================= ================= */








	$table->addRow();
	$table->addCell(3000, $styleCell)->addText(htmlspecialchars('Project Risks'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));

	$cell = $table->addCell(1600, array('gridSpan' => 2),$styleCell);
	$cell->addText(htmlspecialchars('HIGH:'.$highRisks), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
	$cell->addText(htmlspecialchars('SEVERE:'.$severeRisks), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

	$cell = $table->addCell(1600, array('gridSpan' => 5),$styleCell);
	$cell->addText(htmlspecialchars('OPENING:'.$total_open.', REVIEWING:'.$total_review.', SIGNED-OFF:'.$total_signoff.', OVERDUE:'.$total_overdue), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

	/***************************************************************************/

    $section->addTextBreak(1, array('size' => 1));

   // $section->addPageBreak();
    /*     * ****************  Thired ********************************************************************************************************** */
    $phpWord->addTableStyle('3Table', array('borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80, 'cellMarginBottom' => 0), array('borderBottomSize' => 18, 'bold' => true, 'borderBottomColor' => '000000', 'bgColor' => '000000'));
    $section->addBookmark('Peopleonproject');
    $tableFirst = $section->addTable('3Table');
    $tableFirst->addRow();
    $tableFirst->addCell(15000, array('valign' => 'bottom')
    )->addText(
            htmlspecialchars('PEOPLE ON PROJECT '), array('size' => 11, 'color' => 'FFFFFF', 'bold' => true), array('align' => 'both', 'spaceAfter' => $spaceAfter)
    );
    $section->addTextBreak(1, array('size' => 1));

    /*     * ****************  Fourth ********************************************************************************************************** */
    $users = $this->Phpword->project_people($project['id']);

    $keySkillsPresent = $this->Phpword->skill_required($project['id']);
    $keySkillsMissing = $this->Phpword->skill_missing($project['id']);
    $keySkillsPresent1 = implode(", ", $keySkillsPresent);

    $phpWord->addTableStyle('4Table', array('borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80, 'cellMarginBottom' => 0), array('borderBottomSize' => 18, 'bold' => true, 'borderBottomColor' => '000000'));
    $table = $section->addTable('4Table');
    $table->addRow();
    $table->addCell(4000, $styleCell)->addText(htmlspecialchars('Creator'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $table->addCell(12000, array('gridSpan' => 7))->addText(htmlspecialchars(isset($users['creator']) && !empty($users['creator']) ? implode(', ', $users['creator']) : 'N/A'), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $table->addRow();
    $table->addCell(4000, $styleCell)->addText(htmlspecialchars('Owners'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $table->addCell(12000, array('gridSpan' => 7))->addText(htmlspecialchars(isset($users['owners']) && !empty($users['owners']) ? implode(', ', $users['owners']) : 'N/A'), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $table->addRow();
    $table->addCell(4000, $styleCell)->addText(htmlspecialchars('Sharers'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $table->addCell(12000, array('gridSpan' => 7))->addText(htmlspecialchars(isset($users['sharer']) && !empty($users['sharer']) ? implode(', ', $users['sharer']) : 'N/A'), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $table->addRow();
    $table->addCell(4000, $styleCell)->addText(htmlspecialchars('Group Owners'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $table->addCell(12000, array('gridSpan' => 7))->addText(htmlspecialchars(isset($users['GpOwner']) && !empty($users['GpOwner']) ? implode(', ', $users['GpOwner']) : 'N/A'), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $table->addRow();
    $table->addCell(4000, $styleCell)->addText(htmlspecialchars('Group Sharers'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $table->addCell(12000, array('gridSpan' => 7))->addText(htmlspecialchars(isset($users['GpSharer']) && !empty($users['GpSharer']) ? implode(', ', $users['GpSharer']) : 'N/A'), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $table->addRow();
    $table->addCell(4000, $styleCell)->addText(htmlspecialchars('Key Skills Present'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $table->addCell(12000, array('gridSpan' => 7))->addText(htmlspecialchars(strip_tags($keySkillsPresent1)), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $table->addRow();
    $table->addCell(4000, $styleCell)->addText(htmlspecialchars('Key Skills Missing'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $table->addCell(12000, array('gridSpan' => 7))->addText(htmlspecialchars(implode(",", $keySkillsMissing)), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $section->addTextBreak(1, array('size' => 1));
    $section->addPageBreak();

    /*     * ****************  Fifth ********************************************************************************************************** */
    $phpWord->addTableStyle('5Table', array('borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80, 'cellMarginBottom' => 0), array('borderBottomSize' => 18, 'bold' => true, 'borderBottomColor' => '000000', 'bgColor' => '000000'));
    $section->addBookmark('Keytargetresults');
    $table = $section->addTable('5Table');
    $table->addRow();
    $table->addCell(15000, array('valign' => 'bottom')
    )->addText(
            htmlspecialchars('KEY RESULT TARGETS'), array('size' => 11, 'color' => 'FFFFFF', 'bold' => true), array('align' => 'both', 'spaceAfter' => $spaceAfter)
    );
    $section->addTextBreak(1, array('size' => 1));

    /*     * ****************  Six ********************************************************************************************************** */

    if (isset($project['Workspace']) && !empty($project['Workspace'])) {
        //   WorkSpace ///
		$i = 1;

        foreach ($project['Workspace'] as $workspace_data) {

            if ((isset($workspace_data['id']) && !empty($workspace_data['id'])) && (isset($workspace_data['title']) && !empty($workspace_data['title']))) {

                $wsp_people = $this->Phpword->wsp_people(workspace_pwid( $project['id'], $workspace_data['id']  ), $project['id']);

                $w_start = $w_end = null;
                $w_start = (isset($workspace_data['start_date']) && !empty($workspace_data['start_date'])) ? _displayDate(date("d M Y", strtotime($workspace_data['start_date'])), "d M Y") : 'N/A';
                $w_end = (isset($workspace_data['end_date']) && !empty($workspace_data['end_date'])) ? _displayDate(date("d M Y", strtotime($workspace_data['end_date'])), "d M Y") : 'N/A';
                $phpWord->addTableStyle($workspace_data['id'] . 'W6Table', array(
                    'borderSize' => 6,
                    'borderColor' => '000000',
                    'cellMargin' => 80,
                    'cellMarginBottom' => 0), array(
                    'borderBottomSize' => 18,
                    'bold' => true,
                    'borderBottomColor' => '000000',
                    'bgColor' => $this->Phpword->get_color_code($workspace_data['color_code'])
                        )
                );

                $section->addBookmark('workspace_link_'.$workspace_data['id']);

                $table = $section->addTable($workspace_data['id'] . 'W6Table');

                $table->addRow();
                $cell1 = $table->addCell(9000, array("valign" => "center", 'gridSpan' => 2), null);
                $textrun1 = $cell1->addTextRun(array('align' => 'both', 'spaceAfter' => $spaceAfter));
                $textrun1->addText(htmlspecialchars('Workspace: '), array("bold" => true, 'color' => 'FFFFFF'), null);

				$workspace_data_title = html_entity_decode(strip_tags($workspace_data['title']));
				$workspace_data_title = str_replace("'", "", $workspace_data_title);
				$workspace_data_title = str_replace('"', "", $workspace_data_title);
				//$workspace_data = preg_replace('/[^A-Za-z0-9\-]/', '', $workspace_data);
				$workspace_title = remove($workspace_data_title);

				$workspace_data_description = html_entity_decode(strip_tags($workspace_data['description']));
				$workspace_data_description = str_replace("'", "", $workspace_data_description);
				$workspace_data_description = str_replace('"', "", $workspace_data_description);
				//$workspace_data = preg_replace('/[^A-Za-z0-9\-]/', '', $workspace_data);
				$workspace_description = remove($workspace_data_description);

                $textrun1->addText(htmlspecialchars($workspace_title), array('color' => 'FFFFFF'), null);


                $cell1 = $table->addCell(6000, array("valign" => "center"), null);
                $textrun1 = $cell1->addTextRun(array('align' => 'both', 'spaceAfter' => $spaceAfter));
                $textrun1->addText(htmlspecialchars('Start: '), array("bold" => true, 'color' => 'FFFFFF'), null);
                $textrun1->addText(htmlspecialchars($w_start), array('color' => 'FFFFFF'), null);
                $textrun1->addText(htmlspecialchars(' End: '), array("bold" => true, 'color' => 'FFFFFF'), null);
                $textrun1->addText(htmlspecialchars($w_end), array('color' => 'FFFFFF'), null);

                $table->addRow();
                $table->addCell(4000, array('valign' => 'top'))->addText(htmlspecialchars('Key Result Target'), array('bold' => true), array('spaceAfter' => $spaceAfter));

                $keyResultTarget = isset($workspace_data['description']) && !empty($workspace_data['description']) ? $workspace_description : 'N/A';
                $keyResultTarget_cell = $table->addCell(12000, array('gridSpan' => 2));
                //$keyResultTarget_cell->addText(htmlspecialchars($keyResultTarget),null,array('align'=>'both', 'spaceAfter'=>$spaceAfter));
                \PhpOffice\PhpWord\Shared\Html::addHtml($keyResultTarget_cell, $keyResultTarget);


                $table->addRow();
                $table->addCell(4000, array('valign' => 'top'))->addText(htmlspecialchars('People on Workspace'), array('bold' => true), array('spaceAfter' => $spaceAfter));
                $people_w = (isset($wsp_people) && !empty($wsp_people)) ? implode(", ", $wsp_people) : 'N/A';
                $table->addCell(12000, array('gridSpan' => 2))->addText(htmlspecialchars($people_w), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

                $section->addTextBreak(1, array('size' => 1));
            }
            //$section->addPageBreak();
            //   Area ///

            if (isset($workspace_data['Area']) && !empty($workspace_data['Area'])) {

			   $f = 1;
                foreach ($workspace_data['Area'] as $area_data) {

                    if (isset($area_data['id']) && !empty($area_data['id'])) {
                        $phpWord->addTableStyle($area_data['id'] . 'A7Table', array(
                            'borderSize' => 6,
                            'borderColor' => '000000',
                            'cellMargin' => 80,
                            'cellMarginBottom' => 0), array(
                            'borderBottomSize' => 18,
                            'bold' => true,
                            'borderBottomColor' => '000000',
                            'bgColor' => 'B0B4BC'
                                )
                        );
                        $section->addBookmark('area_link_'.$area_data['id']);
                        $table = $section->addTable($area_data['id'] . 'A7Table');
                        $table->addRow();

						/* $area_data = html_entity_decode(strip_tags($area_data['title']));
						$area_data = str_replace("'", "", $area_data);
						$area_data = str_replace('"', "", $area_data); */

						$area_data['title'] = html_entity_decode(strip_tags($area_data['title']));
						$area_data['title'] = str_replace("'", "", $area_data['title']);
						$area_data['title'] = str_replace('"', "", $area_data['title']);
						//$area_data = preg_replace('/[^A-Za-z0-9\-]/', '', $area_data);

						$area_data_description = html_entity_decode(strip_tags($area_data['description']));
						$area_data_description = str_replace("'", "", $area_data_description);
						$area_data_description = str_replace('"', "", $area_data_description);
						//$area_data_description = preg_replace('/[^A-Za-z0-9\-]/', '', $area_data_description);


                        $cell1 = $table->addCell(6000, $styleCell);
                        $textrun1 = $cell1->addTextRun(array('align' => 'both', 'spaceAfter' => $spaceAfter));
                        $textrun1->addText(htmlspecialchars('Area: '), array("bold" => true), null);
                        $textrun1->addText(htmlspecialchars(remove($area_data['title'])), null, null);


                        $cell1 = $table->addCell(9000, $styleCell);
                        $textrun1 = $cell1->addTextRun(array('align' => 'both', 'spaceAfter' => $spaceAfter));
                        $textrun1->addText(htmlspecialchars('Purpose: '), array("bold" => true), null);
                        $textrun1->addText(remove((isset($area_data['description']) && !empty($area_data['description'])) ? htmlspecialchars($area_data_description) : htmlspecialchars('N/A')), null, null);
                    }
                    $section->addTextBreak(1, array('size' => 1));
                    //   Tasks ///

					$j = 1;

					//pr($area_data['Element']);

                    if (isset($area_data['Element']) && !empty($area_data['Element'])) {
                        foreach ($area_data['Element'] as $key => $element_data) {


                            if (isset($element_data['Element']['id']) && !empty($element_data['Element']['id'])) {
								$area_data_tot = (isset($area_data['Element']) && !empty($area_data['Element'])) ? count($area_data['Element']) : 0;

                                 if( $j < $area_data_tot ){
								 $section->addTextBreak(1, array('size' => 1));

							}

                                $e_start = (isset($element_data['Element']['start_date']) && !empty($element_data['Element']['start_date'])) ? _displayDate(date("Y-m-d", strtotime($element_data['Element']['start_date'])), "d M Y") : 'N/A';
                                $e_end = (isset($element_data['Element']['end_date']) && !empty($element_data['Element']['end_date'])) ? _displayDate(date("Y-m-d", strtotime($element_data['Element']['end_date'])), "d M Y") : 'N/A';

                                $element_status = element_status($element_data['Element']['id']);
                                $color_code = 'A6A6A6';
                                if( !empty($element_status) && isset($element_status) && $element_status ==  'not_started'){
                                    $color_code = '897E40';
                                }else if( !empty($element_status) && isset($element_status) && $element_status == 'overdue'){
                                    $color_code = 'FF0000';
                                }else if( !empty($element_status) && isset($element_status) && $element_status == 'completed'){
                                    $color_code = '00A83B';
                                }else if( !empty($element_status) && isset($element_status) && $element_status == 'progress'){
                                    $color_code = 'ffc000';
                                }
                                //  $description1 = remove($element_data['Element']['description']);

								$description1 = html_entity_decode(strip_tags($element_data['Element']['description']));
								$description1 = str_replace("'", "", $description1);
								$description1 = str_replace('"', "", $description1);
								//$description1 = preg_replace('/[^A-Za-z0-9\-]/', '', $description1);

                                //$description1 = $element_data['Element']['description'];
                                $description = isset($description1) && !empty($description1) ? remove($description1) : 'N/A';

                                //$summery1 = remove($element_data['Element']['comments']);
                                $summery1 = $element_data['Element']['comments'];
                                $summery = isset($summery1) && !empty($summery1) ? remove($summery1) : 'N/A';

                                $users_element = $this->Common->ProjectElementAllUsers($project['id'], $element_data['Element']['id']);

                                $element_user = array();
                                if (isset($users_element) && !empty($users_element)) {
                                    foreach ($users_element as $user_id) {
                                        $user_data = $this->ViewModel->get_user_data($user_id);
                                        $user_name = $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'];

											//================================================

												$element_assigned = element_assigned($element_data['Element']['id']);
												//$assign_class = 'not-avail';
												$assign_class = '';
												if( $element_assigned['ElementAssignment']['assigned_to'] == $user_id ){
													if($element_assigned['ElementAssignment']['reaction'] == 1) {
														$assign_class = '(Task Leader : Accepted)';
													}
													else if($element_assigned['ElementAssignment']['reaction'] == 2) {
														$assign_class = '(Task Leader : Schedule Not Accepted)';
													}
													else if($element_assigned['ElementAssignment']['reaction'] == 3)
													{
														$assign_class = '(Task Leader : Disengaged)';

													}  else {
														if(!empty($element_assigned['ElementAssignment']['assigned_to'])) {
															$assign_class = '(Task Leader : Assigned)';
														}
													}
												}

											//====================================================

                                        $element_user[] = $user_name." ".$assign_class;

                                    }
                                }

                                $totalAssets = element_assets($element_data['Element']['id'], true);
                                $element_decisions = _element_decisions($element_data['Element']['id'], 'decision');
                                $element_feedbacks = _element_decisions($element_data['Element']['id'], 'feedback');
                                $element_statuses = _element_statuses($element_data['Element']['id']);


                                $feedback_live = $totalAssets['feedbacks'];
                                $vote_live = isset($totalAssets['votes']) && !empty($totalAssets['votes']) ? $totalAssets['votes'] : 0 ;


								$element_usern = array_filter($element_user, create_function('$value', 'return trim($value)!=="";'));

                                $users = (isset($element_usern) && !empty($element_usern)) ? implode(", ", $element_usern) : 'N/A';
                                $decision = (isset($element_decisions['decision_short_term']) && !empty($element_decisions['decision_short_term'])) ? '(' . $element_decisions['decision_short_term'] . ')' : 0;
                                $totfeedback = (isset($totalAssets['total_feedbacks']) && !empty($totalAssets['total_feedbacks'])) ? trim($totalAssets['total_feedbacks']) : 0;
                                $totvotes = (isset($totalAssets['total_votes']) && !empty($totalAssets['total_votes'])) ? $totalAssets['total_votes'] : 0;



                                $phpWord->addTableStyle($element_data['Element']['id'] . 'E8Table', array(
                                    'borderSize' => 6,
                                    'borderColor' => '000000',
                                    'cellMargin' => 80,
                                    'cellMarginBottom' => 0), array(
                                    'borderBottomSize' => 18,
                                    'bold' => true,
                                    'borderBottomColor' => '000000'
                                    )
                                );
                                $section->addBookmark('element_link_'.$element_data['Element']['id']);
                                $table = $section->addTable($element_data['Element']['id'] . 'E8Table');

                                $table->addRow();
                                $cell1 = $table->addCell(10000, array('gridSpan' => 5, 'align' => 'both', 'spaceAfter' => $spaceAfter,'bgColor' => $this->Phpword->get_color_code($element_data['Element']['color_code'])));
                                $cell1->addText(htmlspecialchars('Task: '), array("bold" => true, 'color' => 'FFFFFF'), array('align' => 'both', 'spaceAfter' => $spaceAfter));
                                $cell1->addText(htmlspecialchars(remove($element_data['Element']['title'])), array('color' => 'FFFFFF'), array('align' => 'both', 'spaceAfter' => $spaceAfter));




                                $cell1 = $table->addCell(5500, array('gridSpan' => 3,'bgColor' =>$color_code),array('bgColor' =>$color_code));
                                $cell1->addText(htmlspecialchars('Task Schedule: '), array("bold" => true, 'color' => 'FFFFFF'), array('align' => 'both', 'spaceAfter' => $spaceAfter));
                                $cell1->addText(htmlspecialchars('Start: ' . $e_start . ' End: ' . $e_end), array('color' => 'FFFFFF'), array('align' => 'both', 'spaceAfter' => $spaceAfter));


                                $table->addRow();
                                $cell_description = $table->addCell(7500, array('gridSpan' => 4, 'align' => 'both', 'spaceAfter' => $spaceAfter));
                                $cell_description->addText(htmlspecialchars('Task Description: '), array("bold" => true), array('align' => 'both', 'spaceAfter' => $spaceAfter));
                                \PhpOffice\PhpWord\Shared\Html::addHtml($cell_description, $description);

                                $cell_summery = $table->addCell(7500, array('gridSpan' => 4));
                                $cell_summery->addText(htmlspecialchars('Task Outcome: '), array("bold" => true), array('align' => 'both', 'spaceAfter' => $spaceAfter));
                                \PhpOffice\PhpWord\Shared\Html::addHtml($cell_summery, $summery);

                                $table->addRow();
                                $cell1 = $table->addCell(15000, array('gridSpan' => 8));
                                $cell1->addText(htmlspecialchars('People on Task: '), array("bold" => true), array('align' => 'both', 'spaceAfter' => $spaceAfter));
                                $cell1->addText(remove($users), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));




                                $table->addRow();
                                $table->addCell(1500, $styleCell)->addText(htmlspecialchars('Resources: '), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));

                                $link = (isset($totalAssets['links']) && !empty($totalAssets['links'])) ? $totalAssets['links'] : 0;
                                $cell = $table->addCell(1700, $styleCell);
                                $cell->addText(htmlspecialchars('Links: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
                                $cell->addText(htmlspecialchars($link), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

                                $notes = (isset($totalAssets['notes']) && !empty($totalAssets['notes'])) ? $totalAssets['notes'] : 0;
                                $cell = $table->addCell(1700, $styleCell);
                                $cell->addText(htmlspecialchars('Notes: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
                                $cell->addText(htmlspecialchars($notes), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

                                $documents = (isset($totalAssets['docs']) && !empty($totalAssets['docs'])) ? $totalAssets['docs'] : 0;
                                $cell = $table->addCell(1500, $styleCell);
                                $cell->addText(htmlspecialchars('Documents: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
                                $cell->addText(htmlspecialchars($documents), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

                                $mindmaps = (isset($totalAssets['mindmaps']) && !empty($totalAssets['mindmaps'])) ? $totalAssets['mindmaps'] : 0;
                                $cell = $table->addCell(1700, $styleCell);
                                $cell->addText(htmlspecialchars('Mind Maps: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
                                $cell->addText(htmlspecialchars($mindmaps), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));


                                $cell = $table->addCell(2100, $styleCell);
                                $cell->addText(htmlspecialchars('Decision: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
                                $cell->addText(htmlspecialchars($decision), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));


                                $cell = $table->addCell(2500, $styleCell);
                                $cell->addText(htmlspecialchars('Feedback: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
                                $cell->addText(htmlspecialchars($totfeedback.' and '.$feedback_live.' Live'), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));


                                $cell = $table->addCell(2300, $styleCell);
                                $cell->addText(htmlspecialchars('Votes: '), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
                                $cell->addText(htmlspecialchars($totvotes . ' and ' . $vote_live . ' Live'), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));




                            }




                        }
                    }


					$workspace_data_tot = ( isset($workspace_data['Area']) && !empty($workspace_data['Area']) ) ? count($workspace_data['Area']) : 0;

					if($k < $workspace_data_tot ){
								 $section->addTextBreak(1, array('size' => 1));

					}
                }

				$project_tot = ( isset($project['Workspace']) && !empty($project['Workspace']) ) ? count($project['Workspace']) : 0;
				if($i < $project_tot){
					$section->addPageBreak();

				}
            }



			$i++;
            //pr($workspace_data['Area'],1);
        }
    }

}



$path = WWW_ROOT . "PHPWord-P/samples/results/";

$filename = trim($document_title);

function clean($string) {
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

$dt = clean($filename);

$file = $dt . ".doc";

$filename = $dt . '.docx';




// At least write the document to webspace:
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save($path . $filename);
$temp_file_uri = tempnam('', 'xyz');

$objWriter->save($temp_file_uri);
//download code


header('Content-Disposition: attachment; filename="' . $filename . '"');
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');



 readfile($filename);

/*
header("Content-Description: File Transfer");
header('Content-Disposition: attachment; filename="' . $filename . '"');
//header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

header("Pragma: public");
header("Cache-Control: private",false);
header("Content-Type: application/msword");

header('Expires: 0'); */


$xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$xmlWriter->save("php://output");



//
exit;
