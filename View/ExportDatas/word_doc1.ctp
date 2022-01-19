<?php
//error_reporting(0);
require_once WWW_ROOT . 'PHPWord-P-1/src/PhpWord/Autoloader.php';

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
$cellfooterright->addPreserveText(htmlspecialchars('Page {PAGE} of {NUMPAGES}'), null, array('align' => 'left'));


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
$text = $section->addText(htmlspecialchars('Date Report - ' . _displayDate(date("d M Y"),$formate = 'd M Y') ), array('size' => 26, 'bold' => false), 'pStyle');

$paragraphStyle = array('align' => 'center');
$phpWord->addParagraphStyle('pStyle', $paragraphStyle);
$text = $section->addText(htmlspecialchars($user_name), array('size' => 26, 'bold' => false), 'pStyle');


 
if (isset($is_show_doc_img) && !empty($is_show_doc_img) && $is_show_doc_img == 'Y') {
    $menuprofile = $this->Session->read('Auth.User.UserDetail.document_pic');
    $menuprofiles = SITEURL . USER_PIC_PATH . $menuprofile;

    if (!empty($menuprofile) && file_exists(USER_PIC_PATH . $menuprofile)) {
        $menuprofiles = SITEURL . USER_PIC_PATH . $menuprofile;
        $section->addImage($menuprofiles, array('width' => 230, 'align' => 'center'));
    } 
    //$section->addTextBreak(2);
}
$section->addLine(['weight' => 2, 'color' => '000000', 'width' => $linewidth, 'height' => 0]);
if (isset($is_show_project_img) && !empty($is_show_project_img) && $is_show_project_img == 'Y') {
    
    if(isset($data[$project_id]['image_file']) && !empty($data[$project_id]['image_file'])){
        $projectImg = $data[$project_id]['image_file'];
        $projectfiles = SITEURL . UPLOAD.'project/' . $projectImg;
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





$section->addLine(['weight' => 1, 'width' => $linewidth, 'height' => 0]);





$phpWord->addFontStyle('myOwnStyle', array());
$phpWord->addParagraphStyle('P-Style', array('spaceAfter' => 95));
$phpWord->addNumberingStyle(
        'multilevel', array(
    'type' => 'multilevel',
    'levels' => array(
        array('format' => 'decimal', 'text' => '%1.', 'left' => 360, 'hanging' => 360, 'tabPos' => 360),
        array('format' => 'upperLetter', 'text' => '%2.', 'left' => 720, 'hanging' => 360, 'tabPos' => 720),
    ),
        )
);
$predefinedMultilevel = array('listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED);

$linkIsInternal = true;



//$section->addLink('MyBookmark', htmlspecialchars('hii'), null, null, $linkIsInternal);
//$a = $section->addListItem('Sum', 0, 'myOwnStyle', $predefinedMultilevel, 'P-Style');
//$section->addLink('MyBookmark','heloo', null, null, $linkIsInternal);
 
$summary = $section->addListItemRun(null, 0, 'myOwnStyle', $predefinedMultilevel, 'P-Style');
$summary->addLink('SUMMARY', htmlspecialchars(strtoupper('Summary')), null, null, $linkIsInternal);

$Peopleonproject = $section->addListItemRun(null, 0, 'myOwnStyle', $predefinedMultilevel, 'P-Style');
$Peopleonproject->addLink('Peopleonproject', htmlspecialchars(strtoupper('People on project')), null, null, $linkIsInternal);

$Keytargetresults = $section->addListItemRun(null, 0, 'myOwnStyle', $predefinedMultilevel, 'P-Style');
$Keytargetresults->addLink('Keytargetresults', htmlspecialchars(strtoupper('Key target results')), null, null, $linkIsInternal);


//$section->addListItem(htmlspecialchars(strtoupper('People on project')), 0, 'myOwnStyle', $predefinedMultilevel, 'P-Style'); 
//$section->addListItem(htmlspecialchars(strtoupper('People on project')), 0, 'myOwnStyle', $predefinedMultilevel, 'P-Style');
//$section->addListItem(htmlspecialchars(strtoupper('Key target results')), 0, 'myOwnStyle', $predefinedMultilevel, 'P-Style');

function remove($str, $allow = 0) {
    if ($allow > 0) {
        $str = trim($str);
    } else {
        $str = strip_tags(trim($str));
    }
    return ucfirst($str);
}

foreach ($data as $key => $project) {
    if (isset($project['Workspace']) && !empty($project['Workspace'])) {
        $row_counter = 0;
        foreach ($project['Workspace'] as $key => $workspace) {
            if ((isset($workspace['id']) && !empty($workspace['id'])) && (isset($workspace['title']) && !empty($workspace['title']))) {
                //$section->addListItem(strtoupper(htmlspecialchars(remove($workspace['title']))), 1, 'myOwnStyle', $predefinedMultilevel, 'P-Style');
                
                $workspace_key = $section->addListItemRun(1, 1, 'myOwnStyle', $predefinedMultilevel, 'P-Style');
                $workspace_key->addLink('workspace_link_'.$workspace['id'], strtoupper(htmlspecialchars(remove($workspace['title']))), null, null, $linkIsInternal);
 
                if (isset($workspace['Area']) && !empty($workspace['Area'])) {
                    foreach ($workspace['Area'] as $a_key => $area) {
                        //$section->addListItem(strtoupper(htmlspecialchars(remove($area['title']))), 2, 'myOwnStyle', $predefinedMultilevel, 'P-Style');
                        
                        $area_key = $section->addListItemRun(2, 2, 'myOwnStyle', $predefinedMultilevel, 'P-Style');
                        $area_key->addLink('area_link_'.$area['id'], strtoupper(htmlspecialchars(remove($area['title']))), null, null, $linkIsInternal);
 
                        if (isset($area['Element']) && !empty($area['Element'])) {
                            foreach ($area['Element'] as $element) {
                                $element['Element']['title'] = trim($element['Element']['title']);
                                if (isset($element['Element']['title']) && !empty($element['Element']['title'])) {
                                    //$section->addListItem(strtoupper(htmlspecialchars(remove($element['Element']['title']))), 3, 'myOwnStyle', $predefinedMultilevel, 'P-Style');
                                    
                                    $element_key = $section->addListItemRun(3, 3, 'myOwnStyle', $predefinedMultilevel, 'P-Style');
                                    $element_key->addLink('element_link_'.$element['Element']['id'], strtoupper(htmlspecialchars(remove($element['Element']['title']))), null, null, $linkIsInternal);
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
    $tableFirst->addCell(3000, array('valign' => 'center')
    )->addText(
            htmlspecialchars('Business Report:'),array('size' => 18, 'color' => 'FFFFFF', 'bold' => true), array('align' => 'both', 'spaceAfter' => $spaceAfter)
    );
    $tableFirst->addCell(12000, array('valign' => 'center')
    )->addText(
            htmlspecialchars('Created By: ' . $user_name . ' Created: ' . _displayDate(date("Y-m-d h:i:s A"))), array('color' => 'FFFFFF'), array('align' => 'both', 'spaceAfter' => $spaceAfter)
    );
    $section->addTextBreak(1, array('size' => 1));


    /*     * ****************  Second ********************************************************************************************************** */
    $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
    $styleFirstRow = array('borderBottomSize' => 18, 'bold' => true, 'borderBottomColor' => '000000', 'bgColor' => $this->Phpword->get_color_code($project['color_code']));
    $styleCellHeading = array('valign' => 'bottom', 'gridSpan' => 8);
    $styleCellBTLR = array('valign' => 'center', 'textDirection' => \PhpOffice\PhpWord\Style\Cell::TEXT_DIR_BTLR);
    $fontStyleHeading = array('bold' => true, 'size' => 11, 'align' => 'center');
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
    $table->addCell(11000, array('gridSpan' => 7))->addText(htmlspecialchars(remove($project['title'])), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $table->addRow();
    $table->addCell(3000, $styleCell)->addText(htmlspecialchars('Alignment'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));
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
    \PhpOffice\PhpWord\Shared\Html::addHtml($cell_objective, $project['objective']);

    $table->addRow();
    $table->addCell(3000, $styleCell)->addText(htmlspecialchars('Description'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    //$table->addCell(11000, array('gridSpan'=>7))->addText(htmlspecialchars(remove($project['description'])), null,array('align'=>'both', 'spaceAfter'=>$spaceAfter));
    $cell_description = $table->addCell(11000, array('gridSpan' => 7), array('align' => 'both', 'spaceAfter' => $spaceAfter));
    \PhpOffice\PhpWord\Shared\Html::addHtml($cell_description, $project['description']);



    $table->addRow();
    $table->addCell(3000, $styleCell)->addText(htmlspecialchars('Project Schedule'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $start = $table->addCell(11000, array('gridSpan' => 7))->addText(htmlspecialchars('Start: ' . _displayDate(date("d M Y", strtotime($project['start_date'])),$formate = 'd M Y') . ' End: ' . _displayDate(date("d M Y", strtotime($project['end_date'])),$formate = 'd M Y')), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $totalEle = $totalWs = 0;
    $totalAssets = null;
    $projectData = $this->ViewModel->getProjectDetail($project['id']);
    $wsList = Set::extract($projectData, '/ProjectWorkspace/workspace_id');
    $totalWs = ( isset($project['Workspace']) && !empty($project['Workspace']) ) ? count($project['Workspace']) : 0;
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
    $table->addRow();
    $table->addCell(3000, $styleCell)->addText(htmlspecialchars('No. Tasks'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $table->addCell(11000, array('gridSpan' => 7))->addText(htmlspecialchars((!empty($totalEle)) ? $totalEle : 0), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
    $table->addRow();
    $table->addCell(3000, $styleCell)->addText(htmlspecialchars('No. Resources'), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $link = (isset($totalAssets['links']) && !empty($totalAssets['links'])) ? $totalAssets['links'] : 0;
    $table->addCell(1600, $styleCell)->addText(htmlspecialchars('Links:' . $link), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $notes = (isset($totalAssets['notes']) && !empty($totalAssets['notes'])) ? $totalAssets['notes'] : 0;
    $table->addCell(1600, $styleCell)->addText(htmlspecialchars('Notes:' . $notes), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $documents = (isset($totalAssets['docs']) && !empty($totalAssets['docs'])) ? $totalAssets['docs'] : 0;
    $table->addCell(1700, $styleCell)->addText(htmlspecialchars('Documents:' . $documents), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $mindmaps = (isset($totalAssets['mindmaps']) && !empty($totalAssets['mindmaps'])) ? $totalAssets['mindmaps'] : 0;
    $table->addCell(1700, $styleCell)->addText(htmlspecialchars('Mind Maps:' . $mindmaps), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $decision = (isset($totalAssets['decisions']) && !empty($totalAssets['decisions'])) ? $totalAssets['decisions'] : 0;
    $table->addCell(1600, $styleCell)->addText(htmlspecialchars('Decision:' . $decision), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $feedback = (isset($totalAssets['feedbacks']) && !empty($totalAssets['feedbacks'])) ? $totalAssets['feedbacks'] : 0;
    $table->addCell(1700, $styleCell)->addText(htmlspecialchars('Feedback:' . $feedback), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $votes = (isset($totalAssets['votes']) && !empty($totalAssets['votes'])) ? $totalAssets['votes'] : 0;
    $text = $table->addCell(1600, $styleCell)->addText(htmlspecialchars('Votes:' . $votes), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

    $section->addTextBreak(1, array('size' => 1));
    
    $section->addPageBreak();
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

        foreach ($project['Workspace'] as $workspace_data) {



            


            if ((isset($workspace_data['id']) && !empty($workspace_data['id'])) && (isset($workspace_data['title']) && !empty($workspace_data['title']))) {

                $wsp_people = $this->Phpword->wsp_people(workspace_pwid( $project['id']  , $workspace_data['id']  ), $project['id']);
				
				
				
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
                $workspace_title = remove($workspace_data['title']);
                $textrun1->addText(htmlspecialchars($workspace_title), array('color' => 'FFFFFF'), null);


                $cell1 = $table->addCell(6000, array("valign" => "center"), null);
                $textrun1 = $cell1->addTextRun(array('align' => 'both', 'spaceAfter' => $spaceAfter));
                $textrun1->addText(htmlspecialchars('Start: '), array("bold" => true, 'color' => 'FFFFFF'), null);
                $textrun1->addText(htmlspecialchars($w_start), array('color' => 'FFFFFF'), null);
                $textrun1->addText(htmlspecialchars(' End: '), array("bold" => true, 'color' => 'FFFFFF'), null);
                $textrun1->addText(htmlspecialchars($w_end), array('color' => 'FFFFFF'), null);

                $table->addRow();
                $table->addCell(4000, array('valign' => 'top'))->addText(htmlspecialchars('Key Result Target'), array('bold' => true), array('spaceAfter' => $spaceAfter));

                $keyResultTarget = isset($workspace_data['description']) && !empty($workspace_data['description']) ? $workspace_data['description'] : 'N/A';
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

                        $cell1 = $table->addCell(6000, $styleCell);
                        $textrun1 = $cell1->addTextRun(array('align' => 'both', 'spaceAfter' => $spaceAfter));
                        $textrun1->addText(htmlspecialchars('Zone: '), array("bold" => true), null);
                        $textrun1->addText(htmlspecialchars(remove($area_data['title'])), null, null);


                        $cell1 = $table->addCell(9000, $styleCell);
                        $textrun1 = $cell1->addTextRun(array('align' => 'both', 'spaceAfter' => $spaceAfter));
                        $textrun1->addText(htmlspecialchars('Purpose: '), array("bold" => true), null);
                        $textrun1->addText(remove((isset($area_data['description']) && !empty($area_data['description'])) ? htmlspecialchars($area_data['description']) : htmlspecialchars('N/A')), null, null);
                    }
                    $section->addTextBreak(1, array('size' => 1));
                    //   Tasks ///
                    
                    if (isset($area_data['Element']) && !empty($area_data['Element'])) {
                        foreach ($area_data['Element'] as $key => $element_data) {
                            if (isset($element_data['Element']['id']) && !empty($element_data['Element']['id'])) {
                                $e_start = (isset($element_data['Element']['start_date']) && !empty($element_data['Element']['start_date'])) ? _displayDate(date("Y-m-d", strtotime($element_data['Element']['start_date'])), "d M Y") : 'N/A';
                                $e_end = (isset($element_data['Element']['end_date']) && !empty($element_data['Element']['end_date'])) ? _displayDate(date("Y-m-d", strtotime($element_data['Element']['end_date'])), "d M Y") : 'N/A';

                                $phpWord->addTableStyle($element_data['Element']['id'] . 'E8Table', array(
                                    'borderSize' => 6,
                                    'borderColor' => '000000',
                                    'cellMargin' => 80,
                                    'cellMarginBottom' => 0), array(
                                    'borderBottomSize' => 18,
                                    'bold' => true,
                                    'borderBottomColor' => '000000',
                                    'bgColor' => $this->Phpword->get_color_code($element_data['Element']['color_code'])
                                        )
                                );
                                $section->addBookmark('element_link_'.$element_data['Element']['id']);
                                $table = $section->addTable($element_data['Element']['id'] . 'E8Table');

                                $table->addRow();

                                $cell1 = $table->addCell(10000, array('gridSpan' => 5, 'align' => 'both', 'spaceAfter' => $spaceAfter));
                                $cell1->addText(htmlspecialchars('Task: '), array("bold" => true, 'color' => 'FFFFFF'), array('align' => 'both', 'spaceAfter' => $spaceAfter));
                                $cell1->addText(htmlspecialchars(remove($element_data['Element']['title'])), array('color' => 'FFFFFF'), array('align' => 'both', 'spaceAfter' => $spaceAfter));

                                $cell1 = $table->addCell(5500, array('gridSpan' => 3));
                                $cell1->addText(htmlspecialchars('Task Schedule: '), array("bold" => true, 'color' => 'FFFFFF'), array('align' => 'both', 'spaceAfter' => $spaceAfter));
                                $cell1->addText(htmlspecialchars('Start: ' . $e_start . ' End: ' . $e_end), array('color' => 'FFFFFF'), array('align' => 'both', 'spaceAfter' => $spaceAfter));


                                $table->addRow();


                                $description1 = remove($element_data['Element']['description']);
                                $description = isset($description1) && !empty($description1) ? $description1 : 'N/A';


                                $cell_description = $table->addCell(7500, array('gridSpan' => 4, 'align' => 'both', 'spaceAfter' => $spaceAfter));
                                $cell_description->addText(htmlspecialchars('Task Description: '), array("bold" => true), array('align' => 'both', 'spaceAfter' => $spaceAfter));
                                //$cell_description->addText(htmlspecialchars(remove($description)),null,array('align'=>'both', 'spaceAfter'=>$spaceAfter));
                                \PhpOffice\PhpWord\Shared\Html::addHtml($cell_description, $element_data['Element']['description']);


                                $summery1 = remove($element_data['Element']['comments']);
                                $summery = isset($summery1) && !empty($summery1) ? $summery1 : 'N/A';

                                $cell_summery = $table->addCell(7500, array('gridSpan' => 4));
                                $cell_summery->addText(htmlspecialchars('Task Outcome: '), array("bold" => true), array('align' => 'both', 'spaceAfter' => $spaceAfter));
                                //$cell_summery->addText(htmlspecialchars(remove($summery,1)),null,array('align'=>'both', 'spaceAfter'=>$spaceAfter));
                                \PhpOffice\PhpWord\Shared\Html::addHtml($cell_summery, $element_data['Element']['comments']);

                                $table->addRow();
                                $users_element = $this->Common->ProjectElementAllUsers($project['id'], $element_data['Element']['id']);
                                //pr($users_element);

                                $element_user = array();
                                if (isset($users_element) && !empty($users_element)) {
                                    foreach ($users_element as $user_id) {
                                        $user_data = $this->ViewModel->get_user_data($user_id);
                                        $user_name = $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'];
                                        $element_user[] = $user_name;
                                    }
                                }



                                $users = (isset($element_user) && !empty($element_user)) ? implode(", ", $element_user) : 'N/A';


                                $cell1 = $table->addCell(15000, array('gridSpan' => 8));
                                $cell1->addText(htmlspecialchars('People on Task: '), array("bold" => true), array('align' => 'both', 'spaceAfter' => $spaceAfter));
                                $cell1->addText(remove($users), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));


                                $totalAssets = element_assets($element_data['Element']['id'], true);
                                $element_decisions = _element_decisions($element_data['Element']['id'], 'decision');
                                $element_feedbacks = _element_decisions($element_data['Element']['id'], 'feedback');
                                $element_statuses = _element_statuses($element_data['Element']['id']);


                                $feedback_live = $totalAssets['feedbacks'];
                                $vote_live = $totalAssets['votes'];






                                $table->addRow();
                                $table->addCell(1875, $styleCell)->addText(htmlspecialchars('Resources: '), $fontStyle, array('align' => 'both', 'spaceAfter' => $spaceAfter));

                                $link = (isset($totalAssets['links']) && !empty($totalAssets['links'])) ? $totalAssets['links'] : 0;
                                $table->addCell(1875, $styleCell)->addText(htmlspecialchars('Links:' . $link), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

                                $notes = (isset($totalAssets['notes']) && !empty($totalAssets['notes'])) ? $totalAssets['notes'] : 0;
                                $table->addCell(1875, $styleCell)->addText(htmlspecialchars('Notes:' . $notes), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

                                $documents = (isset($totalAssets['docs']) && !empty($totalAssets['docs'])) ? $totalAssets['docs'] : 0;
                                $table->addCell(1875, $styleCell)->addText(htmlspecialchars('Documents:' . $documents), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

                                $mindmaps = (isset($totalAssets['mindmaps']) && !empty($totalAssets['mindmaps'])) ? $totalAssets['mindmaps'] : 0;
                                $table->addCell(1875, $styleCell)->addText(htmlspecialchars('Mind Maps:' . $mindmaps), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

                                $decision = (isset($element_decisions['decision_short_term']) && !empty($element_decisions['decision_short_term'])) ? '(' . $element_decisions['decision_short_term'] . ')' : 0;
                                $table->addCell(1875, $styleCell)->addText(htmlspecialchars('Decision:' . $decision), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

                                $totfeedback = (isset($totalAssets['total_feedbacks']) && !empty($totalAssets['total_feedbacks'])) ? $totalAssets['total_feedbacks'] : 0;
                                $table->addCell(1875, $styleCell)->addText(htmlspecialchars('Feedback:' . $totfeedback . ',' . $feedback_live . ' Live'), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));

                                $totvotes = (isset($totalAssets['total_votes']) && !empty($totalAssets['total_votes'])) ? $totalAssets['total_votes'] : 0;
                                $text = $table->addCell(1875, $styleCell)->addText(htmlspecialchars('Votes:' . $totvotes . ',' . $vote_live . ' Live'), null, array('align' => 'both', 'spaceAfter' => $spaceAfter));
                            }
                            $section->addTextBreak(1, array('size' => 1));
                        }
                    }
                    
 
                    //pr($area_data,1);

                    $section->addTextBreak(1, array('size' => 1));
                }
                $section->addPageBreak();
            }




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




header("Content-Description: File Transfer");
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Expires: 0');
$xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$xmlWriter->save("php://output");
exit;
