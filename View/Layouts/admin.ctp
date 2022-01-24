<!DOCTYPE html>
<html lang="en">
<head>
    <!-- start: Meta -->
    <meta charset="utf-8">
    <title><?php echo $title_for_layout; ?></title>
    <!-- end: Meta -->	
    <?php echo $this->element('admin/head_inner'); ?>	
</head>

<body class="skin-blue inner-view fixed">
    <div class="wrapper">
	<!-- start: Header -->
    <?php echo $this->element('admin/header_inner'); ?>
    
	<div class="inner-wrapper">
	<!-- start: Main Menu -->
        <?php echo $this->element('admin/sidebar-left_inner'); ?>
   <!-- end: Main Menu -->
	
		 
			 <!-- start: Content -->
			 <div class="content-wrapper">
                 <noscript>
                     <div class="alert alert-block span10">
                         <h4 class="alert-heading">Warning!</h4>
                         <p>You need to have JavaScript enabled to use OpusView.</p>
                     </div>
                 </noscript>
                 
			
                 <section class="content-header">
                    
                    <?php //echo $this->element('admin/breadcrumb'); ?>
                 </section>
                <section class="content">
                 <?php echo $this->fetch('content');	?>
                <?php 
			echo $this->element('admin/logs'); 
		?>
                </section>
 
			 </div><!--/.fluid-container-->
			 </div>
		
			  
	<?php echo $this->element('admin/footer_inner'); ?>
 
	<?php 	echo $this->Js->writeBuffer(); // Write cached scripts ?>
</div>

		
    </body>
</html>


<script type="text/javascript">
    function selectCity(country_id) {
        if (country_id != "-1") {
            loadData('state', country_id);
            $("#city_dropdown").html("<option value=''>Select city</option>");
        } else {
            $("#state_dropdown").html("<option value=''>Select state</option>");
            $("#city_dropdown").html("<option value=''>Select city</option>");
        }
    }

    function selectState(state_id) {
        if (state_id != "-1") {
            loadData('city', state_id);
        } else {
            $("#city_dropdown").html("<option value=''>Select city</option>");
        }
    }

    function loadData(loadType, loadId) {
        var dataString = 'loadType=' + loadType + '&loadId=' + loadId;
        $("#" + loadType + "_loader").show();
        $("#" + loadType + "_loader").fadeIn(400).html('Please wait... <?php echo $this->Html->image('loading1.gif'); ?>');
        $.ajax({
            type: "POST",
            url: "<?php echo Router::url(array('controller' => 'users', 'action' => 'get_state_city','admin'=>true)); ?>",
            data: dataString,
            cache: false,
            success: function (result) {
                //$("#" + loadType + "_loader").hide();
                if($("#" + loadType + "_dropdownEdit").length > 0 && $("#Recordedit").css('display') != 'none'){
					$("#" + loadType + "_dropdownEdit").html("<option value=''>Select " + loadType + "</option>");
					$("#" + loadType + "_dropdownEdit").append(result);
				}else{
					$("#" + loadType + "_dropdown").html("<option value=''>Select " + loadType + "</option>");
					$("#" + loadType + "_dropdown").append(result);
				}
            }
        });
    }
</script>