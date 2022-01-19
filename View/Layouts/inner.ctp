<!DOCTYPE html>
<?php if($this->request->params['controller'] == 'competencies' || $this->request->params['controller'] == 'departments'){ ?>
<html lang="en" style="overflow-x: hidden" class="modal-open">
<?php }else{ ?>
<html lang="en" style="overflow-x: hidden" >
<?php } ?>

    <head>
        <!-- start: Meta -->
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
        <meta charset="utf-8">
        <meta name="_token" content="<?php echo (isset($this->params['_Token']['key']) && !empty($this->params['_Token']['key'])) ? $this->params['_Token']['key'] : ''; ?>" />
        <title>
            <?php echo $title_for_layout; ?>
        </title>

        <!-- end: Meta -->
        <?php echo $this->element('front/head_inner'); ?>
    </head>
    <?php
        $sidebar_status = sidebar_status($this->Session->read('Auth.User.id'));

        if ($sidebar_status == 1) {
            $class = "";
        } else {
            $class = "sidebar-collapse";
        }
    ?>
    <body class="skin-blue inner-view fixed <?php echo $class; ?>">

        <div class="flash_message" style="display: none;"></div>
        <div class="fix-progress-wrapper" style=""></div>
        <?php // Below div is an flash message box. ?>
        <div class="ajax_flash bg-red">
            <div class="" id=""></div>
        </div>
        <div class="ajax_text_overlay">
            <div id="ajax_overlay_text" class="ajax_overlay_text"></div>
        </div>
        <?php // Below div is an overlay of body, while AJAX request is in progress. ?>
        <div id="ajax_overlay" class="ajax_overlay_preloader">
            <div id="" class="gif_preloader" style="">
                <div id="" class="loading_text" style="">Loading..</div>
            </div>
        </div>
        <div id="tl_overlay" class="tl_preloader">
            <div id="" class="gif_preloader" style="">
                <div id="" class="loading_text" style="">Loading..</div>
            </div>
        </div>
        <div id="ajax_overlay1" style="display:none">
            <div class="ajax_overlay_loader"></div>
        </div>
        <div class="wrapper">
            <!-- start: Header -->
            <?php
			$user_theme = user_theme();
			echo $this->element('front/header_inner',array('user_theme' => $user_theme)); ?>
            <div class="inner-wrapper">
                <!-- start: Main Menu -->
                <?php echo $this->element('front/sidebar-left_inner',array('user_theme' => $user_theme)); ?>
                <!-- end: Main Menu -->
                <!-- start: Content -->
                <div class="content-wrapper">
                    <noscript>
                        <div class="alert alert-block span10">
                            <h4 class="alert-heading">Warning!</h4>
                            <p>You need to have JavaScript enabled to use OpusView.</p>
                        </div>
                    </noscript>
                    <?php
                        if( $this->request->params['action'] != 'user_add_new' &&  $this->request->params['action'] != 'user_add' &&  ($this->request->params['controller'] != 'users' &&  $this->request->params['action'] != 'myaccountedit') && ($this->request->params['action'] != 'manage_project')) {
                    ?>
                    <section class="content-header">
                        <?php echo $this->element('front/breadcrumb'); ?>
                    </section>
                    <?php } ?>
                    <section class="content">
                        <?php echo $this->fetch('content'); ?>
                        <?php
                        // echo $this->element('front/logs');
                        ?>
                    </section>
                    <?php
                    echo $this->element('front/footer_inner',array('user_theme' => $user_theme));

                    // echo $this->element('sql_dump');
                    ?>
                </div>
                <!--/.fluid-container-->
            </div>
            <?php
            // echo $this->element('front/footer_inner');
            ?>

        </div>

        <?php
            $project_id = (isset($project_id) && !empty($project_id)) ? $project_id : null;
            $_sidebarProjectId = (isset($_sidebarProjectId) && !empty($_sidebarProjectId)) ? $_sidebarProjectId : null;
            // echo $this->element('front/chat_7');
        ?>

        <?php echo $this->Js->writeBuffer(); // Write cached scripts  ?>
    </body>

</html>