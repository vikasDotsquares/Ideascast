<?php
// pr($data);

 ?>



<?php
if(isset($data) && !empty($data)) { //pr($data);
    $result = [];
    foreach ($data as $key => $value) {
        $details = array_chunk($value[0], 4, true);
        $result[] = [ 'competency' => $value['ud'], 'cdetails' => $details];
    }
}
?>


<?php
        if($section == 'left'){
            foreach ($result as $key => $value) {
                $competency = $value['competency'];
                // pr($value);
                $comp_id = $competency['comp_id'];
                $comp_type = $competency['comp_type'];
                $comp_name = htmlentities($competency['comp_name'], ENT_QUOTES, "UTF-8");
                $type_border = $type_icon = '';
                $comp_action = 'view_skills';
                if($comp_type == 'Skill') {$type_border = 'skill-border-left';$type_icon = 'com-skills-icon';$comp_action = 'view_skills';}
                else if($comp_type == 'Subject') {$type_border = 'subjects-border-left';$type_icon = 'com-subjects-icon';$comp_action = 'view_subjects';}
                else if($comp_type == 'Domain') {$type_border = 'domain-border-left';$type_icon = 'com-domain-icon';$comp_action = 'view_domains';}

                $compe_url = Router::Url( array( "controller" => "competencies", "action" => $comp_action, $comp_id, 'admin' => FALSE ), true );

                ?>
            <div class="compare-row"> <!--  -->
                <div class="compare-com-list <?php echo $type_border; ?>">
                    <span class="com-list-bg">
                        <i class="<?php echo $type_icon; ?> tipText" title="<?php echo $comp_type; ?>"></i>
                        <span class="com-sks-title tipText" title="<?php echo $comp_name; ?>" data-html="true" data-remote="<?php echo $compe_url; ?>" data-target="#modal_view_skill" data-toggle="modal"><?php echo $comp_name; ?></span>
                    </span>
                </div>
            </div>
            <?php } ?>
        <?php } ?>



            <?php
        if($section == 'right'){
            foreach ($result as $key => $value) {
                $comp_id = $value['competency']['comp_id'];
            ?>
            <div class="compare-row sync-hover full-row">
                <?php foreach ($value['cdetails'] as $ckey => $cvalue) {
                    $user_id = array_shift($cvalue);
                    $level = array_shift($cvalue);
                    $experience = array_shift($cvalue);
                    $files = array_shift($cvalue);

                    $profile_url = Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'tab_competencies', 'admin' => FALSE ), true );

                ?>
                <div class="com-col tipText" title="<?php //echo $user_id; ?>">
                    <?php if(!empty($level) || !empty($experience) || !empty($files)){ ?>
                    <div class="compare-icon-bg" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo $profile_url; ?>">
                        <?php if(!empty($level)){
                            $level_icon = $this->Permission->level_exp_icon($level); ?>
                            <i class="<?php echo $level_icon; ?> tipText" title="" data-original-title="Level: <?php echo $level ?>"></i>
                        <?php } ?>
                        <?php if(!empty($experience)){
                            $exp_icon = $this->Permission->level_exp_icon($experience, false);
                            $exp_num = $this->Permission->exp_number($experience); ?>
                            <i class="<?php echo $exp_icon; ?> tipText" title="" data-original-title="Experience: <?php echo ($exp_num>1)?$experience.' Years':$experience.' Year'; ?>"></i>
                        <?php } ?>
                        <?php if(!empty($files)){ ?>
                            <i class="fas fa-file-pdf tipText" title="<?php echo ($files>1)?$files.' Files':$files.' File'; ?>"></i>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
            <?php } ?>
        <?php } ?>
