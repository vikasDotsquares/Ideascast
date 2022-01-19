<br><br><br><br><br><br><br><br><br><br>
<link rel="stylesheet" type="text/css" href="<?php echo SITEURL; ?>/css/font-awesome.min.css" />
<style>
 h2.sup {
    position: relative;
    font-size:15px;
    padding:3px  0 !important;
    margin:0px;
        
}

h2.sup span {
    background-color: white;
     
}

h2.sup:after {
    content:"";
    position: absolute;
    bottom: 0;
    left: -1px;
    right: 0;
    height: 0.5em;
    border-top: 1px solid black;
    z-index: -1;
    top:20px;
}
 
</style>

<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
    <tbody>
        <?php
        $user_data = $this->ViewModel->get_user_data($this->Session->read("Auth.User.id"));
        $user_id = $this->Session->read("Auth.User.id");
        $pic = $user_data['UserDetail']['profile_pic'];
        $profiles = SITEURL . USER_PIC_PATH . $pic;
        $job_title = $user_data['UserDetail']['job_title'];
        $user_name = $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'];


        if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
            $profiles = SITEURL . USER_PIC_PATH . $pic;
        } else {
            $profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
        }
        ?>
        <tr>
            <td style="font-weight: 800; font-size: 30px; padding: 5px 10px; text-align: center;">Report Title (<?php echo$document_title; ?>)</td>

        </tr>
        <tr>
            <td style="font-weight: 300; font-size: 30px; padding: 5px 10px; text-align: center;">Date Report (<?php echo _displayDate(date("Y-m-d h:i:s")); ?>)</td>
        </tr>
        <tr>
            <td style="font-weight: 300; font-size: 30px; padding: 5px 10px; text-align: center;">Output By (<?php echo $user_name; ?>)</td>
        </tr>
        <?php if (isset($is_show_doc_img) && !empty($is_show_doc_img) && $is_show_doc_img == 'Y') { ?>
            <tr>
                <td style="font-weight: 300; font-size: 30px; padding: 5px 10px; text-align: center;">
                    <div class="header1" style="border-bottom:solid 5px #016165; box-shadow:none; padding-bottom:5px;" >

                        <div class="wrapper" style="width:100%; text-align:center; ">
                            <?php
                            $menuprofile = $this->Session->read('Auth.User.UserDetail.document_pic');
                            $menuprofiles = SITEURL . USER_PIC_PATH . $menuprofile;

                            if (!empty($menuprofile) && file_exists(USER_PIC_PATH . $menuprofile)) {
                                $menuprofiles = SITEURL . USER_PIC_PATH . $menuprofile;
                            } else {
                                $menuprofiles = SITEURL . 'images/icast-logo-wo-tm.png';
                            }
                            ?>
                            <?php //echo $this->Html->image("logo.jpg", array("alt" => "IdeasCast", 'url' => array('controller'=>'customers','action'=>'index','home'))); ?>
                            <img style="text-align:center;margin-right:auto;margin-left:auto;margin: 0px auto ;padding:0;" src="<?php echo $menuprofiles; ?>" alt="IdeasCast"/>

                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<br clear="all" style="page-break-before:always" />
<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
    <tbody>
        <?php
        $len = 170;
        ?>
        <tr>
            <td style="font-weight: bold; font-size: 14px; padding: 5px 10px;">
                 <h2 class="sup"><span href="sum">Summary</span></h2>
            </td>

        </tr>
        <tr>
            <td style="font-weight: bold; font-size: 14px; padding: 5px 10px;">
                 <h2 class="sup"><span>People on project</span></h2>
            </td>

        </tr>
        <tr>
            <td style="font-weight: bold; font-size: 14px; padding: 5px 10px;">
                <h2 class="sup"><span>Key target results</span></h2> 
            </td>

        </tr>
        

        <?php
        foreach ($data as $key => $project) {
            if (isset($project['Workspace']) && !empty($project['Workspace'])) {
                $row_counter = 0;
                foreach ($project['Workspace'] as $key => $workspace) {
                    echo '<tr>';
                        echo '<td style="font-weight: bold; font-size: 14px; padding: 5px 10px;">';
                            echo '<h2 class="sup"><span>&emsp;&emsp;'.strip_tags($workspace['title']).'</span></h2>';
                        echo '</td>';
                    echo '</tr>';



                    if (isset($workspace['Area']) && !empty($workspace['Area'])) {
                        foreach ($workspace['Area'] as $area) {
                            echo '<tr>';
                                echo '<td style="font-weight: bold; font-size: 14px; padding: 5px 10px;">';
                                    echo '<h2 class="sup"><span>&emsp;&emsp;&emsp;&emsp;'.strip_tags($area['title']).'</span></h2>';
                                echo '</td>';
                            echo '</tr>';






                            if (isset($area['Element']) && !empty($area['Element'])) {
                                foreach ($area['Element'] as $element) {
                                    $element['Element']['title'] = trim($element['Element']['title']);
                                    if (isset($element['Element']['title']) && !empty($element['Element']['title'])) {
                                        echo '<tr>';
                                            echo '<td style="font-weight: bold; font-size: 14px; padding: 5px 10px;">';
                                                echo '<h2 class="sup"><span>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<i class="fa fa-book"></i> '.strip_tags($element['Element']['title']).'</span></h2>';
                                            echo '</td>';
                                        echo '</tr>';
                                    }
                                }
                            }



                            if (isset($area['Element']) && !empty($area['Element'])) {
                                foreach ($area['Element'] as $element) {
                                    $element['ElementDecision']['title'] = trim($element['ElementDecision']['title']);
                                    if (isset($element['ElementDecision']['title']) && !empty($element['ElementDecision']['title'])) {
                                        echo '<tr>';
                                            echo '<td style="font-weight: bold; font-size: 14px; padding: 5px 10px;">';
                                                echo '<h2 class="sup"><span>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<i class="fa fa-expand"></i> '.strip_tags($element['ElementDecision']['title']).'</span></h2>';
                                            echo '</td>';
                                        echo '</tr>';
                                    }
                                }
                            }



                            if (isset($area['Element']) && !empty($area['Element'])) {
                                foreach ($area['Element'] as $element) {
                                    $element['ElementFeedback']['title'] = trim($element['ElementFeedback']['title']);
                                    if (isset($element['ElementFeedback']['title']) && !empty($element['ElementFeedback']['title'])) {
                                        echo '<tr>';
                                            echo '<td style="font-weight: bold; font-size: 14px; padding: 5px 10px;">';
                                                echo '<h2 class="sup"><span>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<i class="fa fa-bullhorn"></i> '.strip_tags($element['ElementFeedback']['title']).'</span></h2>';
                                            echo '</td>';
                                        echo '</tr>';
                                    }
                                }
                            }



                            if (isset($area['Element']) && !empty($area['Element'])) {
                                foreach ($area['Element'] as $element) {
                                    if (isset($element['Links']) && !empty($element['Links'])) {
                                        foreach ($element['Links'] as $link) {
                                            $link['title'] = trim($link['title']);
                                            if (isset($link['title']) && !empty($link['title'])) {
                                                echo '<tr>';
                                                    echo '<td style="font-weight: bold; font-size: 14px; padding: 5px 10px;">';
                                                        echo '<h2 class="sup"><span>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<i class="fa fa-link"></i> '.strip_tags($link['title']).'</span></h2>';
                                                    echo '</td>';
                                                echo '</tr>';
                                            }
                                        }
                                    }

                                    if (isset($element['Documents']) && !empty($element['Documents'])) {
                                        foreach ($element['Documents'] as $doc) {
                                            $doc['title'] = trim($doc['title']);
                                            if (isset($doc['title']) && !empty($doc['title'])) {
                                                echo '<tr>';
                                                    echo '<td style="font-weight: bold; font-size: 14px; padding: 5px 10px;">';
                                                        echo '<h2 class="sup"><span>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<i class="fa fa-folder-o"></i> '.strip_tags($doc['title']).'</span></h2>';
                                                    echo '</td>';
                                                echo '</tr>';
                                            }
                                        }
                                    }

                                    if (isset($element['Notes']) && !empty($element['Notes'])) {
                                        foreach ($element['Notes'] as $note) {
                                            $note['title'] = trim($note['title']);
                                            if (isset($note['title']) && !empty($note['title'])) {
                                                echo '<tr>';
                                                    echo '<td style="font-weight: bold; font-size: 14px; padding: 5px 10px;">';
                                                        echo '<h2 class="sup"><span>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<i class="fa fa-file-text"></i> '.strip_tags($note['title']).'</span></h2>';
                                                    echo '</td>';
                                                echo '</tr>';
                                            }
                                        }
                                    }
                                    

                                    if (isset($element['Mindmaps']) && !empty($element['Mindmaps'])) {
                                        foreach ($element['Mindmaps'] as $mind) {
                                            $mind['title'] = trim($mind['title']);
                                            if (isset($mind['title']) && !empty($mind['title'])) {
                                                echo '<tr>';
                                                    echo '<td style="font-weight: bold; font-size: 14px; padding: 5px 10px;">';
                                                        echo '<h2 class="sup"><span>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<i class="fa fa-sitemap"></i> '.strip_tags($mind['title']).'</span></h2>';
                                                    echo '</td>';
                                                echo '</tr>';
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
        ?>



    </tbody>
</table>
<br clear="all" style="page-break-before:always" />
<?php foreach ($data as $key => $project) { ?>

    <table id="" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
        <tbody>
            <tr>
                <td align="left" style="font-weight: 600; font-size: 14px; padding: 5px 10px; background-color: #000000; color: #ffffff; border: 1px solid #000000;" colspan="8">SUMMARY</td>
            </tr>
            <tr>
                <td align="left" style="font-weight: 600; font-size: 12px; padding: 5px 10px; border: 1px solid #000000;">Project Title</td>
                <td align="left" style="font-size: 12px; padding: 5px 10px; border: 1px solid #000000;" colspan="7"><?php echo $project['title']; ?></td>
            </tr>
            <tr>
                <td align="left" style="font-weight: 600; font-size: 12px; padding: 5px 10px; border: 1px solid #000000;">Alignment</td>
                <td align="left" style="font-size: 12px; padding: 5px 10px; border: 1px solid #000000;" colspan="7">
                <?php
                $alignement = get_alignment($project['aligned_id']);
                if (!empty($alignement))
                    echo $alignement['title'];
                else
                    echo "N/A";
                ?>
                </td>
            </tr>
            <tr>
                <td align="left" style="font-weight: 600; font-size: 12px; padding: 5px 10px; border: 1px solid #000000;">Project Objective</td>
                <td align="left" style="font-size: 12px; padding: 5px 10px; border: 1px solid #000000;" colspan="7"><?php echo $project['objective']; ?></td>
            </tr>
            <tr>
                <td align="left" style="font-weight: 600; font-size: 12px; padding: 5px 10px; border: 1px solid #000000;">Description</td>
                <td align="left" style="font-size: 12px; padding: 5px 10px; border: 1px solid #000000;" colspan="7"><?php echo $project['description']; ?></td>
            </tr>
            <tr>
                <td align="left" style="font-weight: 600; font-size: 12px; padding: 5px 10px; border: 1px solid #000000;">Project Schedule</td>
                <td align="left" style="font-size: 12px; padding: 5px 10px; border: 1px solid #000000;" colspan="7">Start: <?php echo $project['start_date']; ?> End: <?php echo $project['end_date']; ?></td>
            </tr>

        <?php
        $totalEle = $totalWs = 0;
        $totalAssets = null;
        $projectData = $this->ViewModel->getProjectDetail($project['id']);
        $wsList = Set::extract($projectData, '/ProjectWorkspace/workspace_id');
        $totalWs = ( isset($wsList) && !empty($wsList) ) ? count($wsList) : 0;

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
        ?>



            <tr>
                <td align="left" style="font-weight: 600; font-size: 12px; padding: 5px 10px; border: 1px solid #000000;">No. Workspaces</td>
                <td align="left" style="font-size: 12px; padding: 5px 10px; border: 1px solid #000000;" colspan="7"><?php echo (isset($totalWs) && !empty($totalWs)) ? $totalWs : 0; ?> </td>
            </tr>
            <tr>
                <td align="left" style="font-weight: 600; font-size: 12px; padding: 5px 10px; border: 1px solid #000000;">No. Tasks</td>
                <td align="left" style="font-size: 12px; padding: 5px 10px; border: 1px solid #000000;" colspan="7"><?php echo (!empty($totalEle)) ? $totalEle : 0; ?></td>
            </tr>
            <tr>
                <td width="10%" align="left" style="font-weight: 600; font-size: 12px; padding: 5px 10px; border: 1px solid #000000;">No. Resources</td>
                <td width="12%" align="left" style="font-size: 12px; padding: 5px 10px; border: 1px solid #000000;" colspan="">
                    Links: <?php echo (isset($totalAssets['links']) && !empty($totalAssets['links'])) ? $totalAssets['links'] : 0; ?> 
                </td>
                <td width="12%" align="left" style="font-size: 12px; padding: 5px 10px; border: 1px solid #000000;" colspan="">
                    Notes: <?php echo (isset($totalAssets['notes']) && !empty($totalAssets['notes'])) ? $totalAssets['notes'] : 0; ?>
                </td>
                <td width="12%" align="left" style="font-size: 12px; padding: 5px 10px; border: 1px solid #000000;" colspan="">
                    Documents: <?php echo (isset($totalAssets['docs']) && !empty($totalAssets['docs'])) ? $totalAssets['docs'] : 0; ?>
                </td>
                <td width="12%" align="left" style="font-size: 12px; padding: 5px 10px; border: 1px solid #000000;" colspan="">
                    Mind Maps: <?php echo (isset($totalAssets['mindmaps']) && !empty($totalAssets['mindmaps'])) ? $totalAssets['mindmaps'] : 0; ?> 
                </td>
                <td width="12%" align="left" style="font-size: 12px; padding: 5px 10px; border: 1px solid #000000;" colspan="">
                    Decision: <?php echo (isset($totalAssets['decisions']) && !empty($totalAssets['decisions'])) ? $totalAssets['decisions'] : 0; ?> 
                </td>
                <td width="12%" align="left" style="font-size: 12px; padding: 5px 10px; border: 1px solid #000000;" colspan="">
                    Feedback: <?php echo (isset($totalAssets['feedbacks']) && !empty($totalAssets['feedbacks'])) ? $totalAssets['feedbacks'] : 0; ?>
                </td>
                <td width="12%" align="left" style="font-size: 12px; padding: 5px 10px; border: 1px solid #000000;" colspan="">
                    Votes: <?php echo (isset($totalAssets['votes']) && !empty($totalAssets['votes'])) ? $totalAssets['votes'] : 0; ?> 
                </td>
            </tr>
        </tbody>
    </table>
    <?php
//echo $totalEle;
//pr($projectData);
//die; 
    ?>

<?php } ?>
