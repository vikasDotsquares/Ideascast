<?php
// system("cmd /c D:/yourchat/start.bat");
// exec('cmd.exe /d START D:\yourchat\start.bat');
	echo $this->Html->css('projects/tokenfield/token-input');
	echo $this->Html->css('projects/tokenfield/token-input-facebook');
	echo $this->Html->css('projects/tokenfield/token-input-mac');
	echo $this->Html->script('projects/plugins/tokenfield/jquery.tokeninput', array('inline' => true));
?>

<script type="text/javascript" >
$(function(){
	// window.open('file:///D:/yourchat/start.bat')
	$("body").delegate("#config_demo", 'change', function (event) {
		event.preventDefault();
		var $t = $(this),
			url = $js_config.base_url + "projects/sample_multiuploader",
			$form = $("#forms");

		var formData = new FormData(),
			$fileInput = $t,
			$div_progress = $t.find('div.btn_progressbar'),
			file = $fileInput[0].files[0];

		var valid_flag = false,
			sizeMB = 0;

        var fd = new FormData();
		var finalFiles = {};
		$.each(this.files, function(idx, elm){
           finalFiles[idx] = elm;
        });

		$.each(finalFiles, function(i, elm){
           fd.append('photos[]', finalFiles[i], finalFiles[i].name);
           $('#files-uploaded').append('')
        });

		if ( $fileInput.val() !== "" ) {
			$.ajax({
				type: 'POST',
				dataType: "JSON",
				url: url,
				data: fd,
				global: true,
				cache: false,
				contentType: false,
				processData: false,
				xhr: function () {
					// 3-9-15 updates
					var xhr = new window.XMLHttpRequest();

					//Upload progress
					xhr.upload.addEventListener("progress", function (event) {
						if (event.lengthComputable) {
							var percentComplete = Math.round(event.loaded / event.total * 100);
							$('.ajax_overlay_preloader > .gif_preloader > .loading_text').text(percentComplete + "%");
						}
					}, false);
					return xhr;
				},
				success: function (response) {

				}
			});
		}
	});

	$( "#config_demo" ).wrap( "<div class='btn btn-sm btn-primary btn-file' id='wrapper'></div>" );
	$('#wrapper.btn-file').prepend('<i class="fa fa-upload"></i> Browse');


	$('.colors').on('click', function (event) {
		event.preventDefault();
		var $color_box = $(this).next('.color_box_outer')

		$color_box.slideToggle(200);
	});

	$('.colors').each(function () {
		var $color_box = $(this).parent().find('.color_box_outer')

		$(this).data('color_box_outer', $color_box)
		$color_box.data('colors', $(this))
	})
	$('body').on('click', function (e) {
		$('.colors').each(function () {
			//the 'is' for buttons that trigger popups
			//the 'has' for icons within a button that triggers a popup
			if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.color_box_outer').has(e.target).length === 0) {
				var color_box = $(this).data('color_box_outer')
				color_box.slideUp(300)
			}
		});
	});


	$(".ws_color_box .el_color_box").on('click', function (event) {
		event.preventDefault();

		var $cb = $(this)
		var applyClass = $cb.data('color')
		var $tr = $cb.parents("tr:first")
		var $td = $("td:eq(0)", $tr)

		$tr.find('td').each( function (i, v) {
			var $td = $(v);
			var _cls = $td.attr('class')
			var foundClass = (_cls.match(/(^|\s)bg-\S+/g) || []).join('')
			if (foundClass != '') {
				$td.removeClass(foundClass)
			}

			$td.addClass(applyClass);
		})
		$(this).setPanelColorClass();


		// SEND AJAX HERE TO CHANGE THE COLOR OF THE ELEMENT
	})

	$("#skills").tokenInput($js_config.base_url+'projects/get_token_data',{
        // theme: "facebook"
    });
})
</script>
<style type="text/css">
	.color_box_outer {
		position: absolute;
	    top: 100%;
    	left: 16px;
	}


	.btn-file {
	    position: relative;
	    overflow: hidden;
	}
	.btn-file input[type=file] {
	    position: absolute;
	    top: 0;
	    right: 0;
	    min-width: 100%;
	    min-height: 100%;
	    font-size: 100px;
	    text-align: right;
	    filter: alpha(opacity=0);
	    opacity: 0;
	    outline: none;
	    background: white;
	    cursor: inherit;
	    display: block;
	}
	.file-name {
		max-width: 90%;
	    width: auto;
	    white-space: nowrap;
	    overflow: hidden !important;
	    text-overflow: ellipsis;
	    display: inline-block;
	}
	.file-wrapper {
		display: inline-block;
        max-width: 70px;
    	max-height: 100px;
	    padding: 5px;
	    font-size: 12px;
	    width: 100px;
	    height: 130px;

	    position: relative;
	    width: 30%;
	    margin: 2em auto;
	    color: #fff;
	    background: #97C02F;
	    box-shadow: -1px 1px 0px rgba(0,0,0,0.3), -1px 1px 1px rgba(0,0,0,0.5);
	}
	.file-wrapper:before {
	  content: "";
	  position: absolute;
	  top: 0;
	  right: 0;
	  border-width: 0 20px 20px 0;
	  border-style: solid;
	  border-color: #fff #fff #658E15 #658E15;
	  background: #658E15;
	  -webkit-box-shadow: 0 2px 0px rgba(0,0,0,0.3), -1px 1px 1px rgba(0,0,0,0.2);
	  -moz-box-shadow: 0 2px 0px rgba(0,0,0,0.3), -1px 1px 1px rgba(0,0,0,0.2);
	  box-shadow: 0 2px 0px rgba(0,0,0,0.3), -1px 1px 1px rgba(0,0,0,0.2);
	  /* Firefox 3.0 damage limitation */
	  display: block; width: 0;
	}

	.file-wrapper.rounded {
	  -moz-border-radius: 5px 0 5px 5px;
	  border-radius: 5px 0 5px 5px;
	}

	.file-wrapper.rounded:before {
	  border-width: 8px;
	  border-color: #fff #fff transparent transparent;
	  -moz-border-radius: 0 0 0 5px;
	  border-radius: 0 0 0 5px;
	}
	.file-back {
	    display: block;
	    width: 100%;
	    height: 100%;
	    position: relative;
	    /*border: 1px solid #333;
	    background-color: #6d6aa3;
	    box-shadow: 2px 4px 11px -2px rgba(0, 0, 0, 0.7);*/
	}
	.file-type {
	    position: absolute;
	    top: 20px;
	    left: -10px;
	    min-width: 50px;
	    text-transform: uppercase;
	    background-color: #333;
	    border: 1px solid #fff;
	    color: #fff;
	    padding: 4px 0 4px 11px;
	    width: 70px;
        white-space: nowrap;
        overflow: hidden !important;
        text-overflow: ellipsis;
        box-shadow: 2px 4px 11px -2px rgba(0, 0, 0, 0.7);
        max-width: 70px;
	}
	.file-bottom {
	    position: absolute;
	    bottom: 0;
	    left: 0;
	    width: 100%;
	    height: 5px;
	    background-color: #fff;
	    line-height: 0;
	    font-size: 40px;
	    text-align: center;
	    vertical-align: text-bottom;
	}
	.file-bottom.line-1 {
	    bottom: 30px;
	    width: 50%;
	}
	.file-bottom.line-2 {
	    bottom: 20px;
	    width: 95%;
	}
	.file-bottom.line-3 {
	    bottom: 10px;
	}
	.file-bottom.line-4 {
	    bottom: 0px;
	    width: 70%;
	}

	.prg {
	    display: block;
	    min-height: 30px;
	    background-image: url(../images/icons/bar.png);
	    background-repeat: no-repeat;
	    background-size: cover;
	}
	.bar {
	    position: absolute;
	    border: 1px solid #333;
	    height: 152%;
	    top: -8px;
	    transition: all 0.9s ease-in-out;
	}
	.bar.low {
		left: 16%;
	}
	.bar.medium {
		left: 36%;
	}
	.bar.high {
		left: 60%;
	}
	.bar.severe {
		left: 82%;
	}





	.pop-outer {
		display: block;
		overflow: hidden;
		min-height: 150px;
		max-width: 300px;
		border: 1px solid #ccc;
		position: relative;
	}
	.pop {
	    margin: 0;
	    padding: 0;
	    text-align: left;
	    display: block;
	    position: absolute;
	    background: #c00;
	    max-width: 350px;
	    border: 1px solid #ccc;
	    padding: 5px 10px;
	    z-index: 999;
	    top: 0;
	    transition: all 0.6s linear;
	    border-radius: 4px;
	    right: -300px;
		opacity: 0;
		min-width: 300px;
		min-height: 150px;
	    /* visibility: hidden; */
	}
</style>


<!-- OUTER WRAPPER	-->
<div class="row">

	<!-- INNER WRAPPER	-->
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">

				<h1 class="pull-left">
					Page Heading
					<p class="text-muted date-time"><span>small text</span></p>
				</h1>

			</section>
		</div>
		<!-- END HEADING AND MENUS -->
		<!-- MAIN CONTENT -->
		<div class="box-content">

            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">

						<!-- CONTENT HEADING -->
                        <div class="box-header nopadding">

							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->

                        </div>
						<!-- END CONTENT HEADING -->


                        <div class="box-body border-top" style="min-height: 500px">
                        	<?php //pr($skills);
                        	echo $this->Form->select('skills', $skills, array('escape' => false));
                        	?>

							<form id="forms" action="">
								<div class="col-md-4 col-md-offset-2 demo clearfix">
									<!-- <div class="btn btn-sm btn-primary btn-file">Browse <i class="fa fa-upload"></i><input type="file" id="config_demo" name="config_demo[]" class="form-control" multiple>
									</div> -->
										<input type="file" id="config_demo" name="config_demo[]" class="form-control" multiple>
								</div>
								<div class="col-md-12 clearfix" id="files-uploaded">
									<div class="col-md-4" id="files-uploaded">
										<ul class="list-group">
											<li class="list-group-item justify-content-between">
												<span class="file-name">A motorcycle (also called a motorbike, bike, or cycle) is a two or three-wheeled motor vehicle. Motorcycle design varies greatly to suit a range of different purposes: long distance travel, commuting, cruising, sport including racing.</span>
												<span class="btn btn-danger btn-xs pull-right"><i class="fa fa-times"></i></span>
					                      	</li>
				                      	</ul>
				                    </div>
								</div>
							</form>
							<div class="col-md-12 clearfix">
								<div class="file-wrapper note">
									<div class="file-back">
										<span class="file-type">jpg</span>
										<span class="file-bottom line-1"></span>
										<span class="file-bottom line-2"></span>
										<span class="file-bottom line-3"></span>
										<span class="file-bottom line-4"></span>
									</div>
								</div>
							</div>
							<!-- <div class="col-md-12 clearfix">

								<a href="#" class="btn btn-default btn-sm colors">Click</a>
								<small class="color_box_outer" style="display: none;">
									<small style="display: inline-block; width: 100px; border: 1px solid #ccc; padding: 2px;">
										<b data-color="bg-red" data-remote="" class="btn btn-default btn-xs el_color_box" title="" data-original-title="Red"><i class="fa fa-square text-red"></i></b>
										<b data-color="bg-red" data-remote="" class="btn btn-default btn-xs el_color_box" title="" data-original-title="Red"><i class="fa fa-square text-red"></i></b>
										<b data-color="bg-red" data-remote="" class="btn btn-default btn-xs el_color_box" title="" data-original-title="Red"><i class="fa fa-square text-red"></i></b>
										<b data-color="bg-red" data-remote="" class="btn btn-default btn-xs el_color_box" title="" data-original-title="Red"><i class="fa fa-square text-red"></i></b>
										<b data-color="bg-red" data-remote="" class="btn btn-default btn-xs el_color_box" title="" data-original-title="Red"><i class="fa fa-square text-red"></i></b>
										<b data-color="bg-red" data-remote="" class="btn btn-default btn-xs el_color_box" title="" data-original-title="Red"><i class="fa fa-square text-red"></i></b>
									</small>
								</small>
							</div> -->

							<div class="col-md-2">
								<div class="prg">
									<div class="bar high"></div>
								</div>
							</div>

							<div class="col-md-12">
								<div class="pop-outer" >
									<div class="pop">test</div>
								</div>
								<a href="" class="animate btn btn-sm btn-warning" >Click</a>
							</div>

							<div class="col-sm-12">
								<div class="management-box">
								<h5>Sales Management</h5>
									<div class="management-box-in">
								<div class="management-attach">

									<div class="btn btn-sm btn-success btn-file">Attach<input type="file" id="config_demo" name="config_demo[]" class="form-control" multiple=""></div>

									<span class="pull-right management-action">
									<div class="btn-group action">
				<a class="btn btn-xs btn-success"><i class="fa fa-check"></i></a>
            <a class="btn btn-xs btn-warning"><i class="fa fa-close"></i></a>
  </div>


									</span></div>
								<input type="text" class="form-control">
								<div class="pdf-size">PDF only- max 5 mb</div>
								<ul class="pdf-list">
								<li><span>Name of Pdf</span> <a class="btn btn-xs btn-danger" href=""> <i class="fa fa-trash-o"></i></a></li>
									<li><span>Name of Pdf</span> <a class="btn btn-xs btn-danger" href=""> <i class="fa fa-trash-o"></i></a></li>
									<li><span>Name of Pdf</span> <a class="btn btn-xs btn-danger" href=""> <i class="fa fa-trash-o"></i></a></li>
								</ul>

								</div>
							</div>
							</div>

						</div>


                    </div>
                </div>
            </div>
        </div>
		<!-- END MAIN CONTENT -->

	</div>
</div>
<!-- END OUTER WRAPPER -->


<style>
	.management-box{
		border: 1px solid #98c8ef;
		display: inline-block;
		width: 420px;
		background: #fff;
		border-radius: 5px;
		margin-top: 30px;
	}



.management-box h5{
	border-bottom: 1px solid #98c8ef;
	padding: 10px 15px;
	margin: 0;

}
	.management-box-in {
		display: inline-block;
		width: 100%;
		padding: 15px;
	}
	.management-attach{
		display: inline-block;
		width: 100%;
		margin-bottom: 10px;
	}
	.management-attach .btn-file {
	font-size: 14px;
		line-height: 17px;
}
	.management-action{margin-top: 5px;}

	.pdf-size {
		text-transform: uppercase;
	}
	.pdf-list{
		padding: 0px;
		border-top:1px solid #ccc;
		padding-top: 10px;
		margin-top: 10px;
	}
	.pdf-list li {
		list-style-type: none;
		position: relative;
		margin: 3px 0px;
		padding-right: 30px;
		padding-left: 3px;
		display: inline-block;
		width: 100%;
	}
	.pdf-list li .btn{
		position: absolute;
		right: 0px;
	}


</style>


<!-- ---------------- MODEL BOX INNER HTML LOADED BY JS ------------------------ -->

<div class="hide" >
	<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">POPUP MODAL HEADING</h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<h5 class="project-name"> popup box heading </h5>
	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="submit" class="btn btn-warning">Save changes</button>
		 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	</div>
</div>

<!-- ---------------- JS TO OPEN MODEL BOX ------------------------ -->
<script type="text/javascript" >
    $('#myModal').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });

// Submit Add Form
      /*jQuery("#formID").submit(function (e) {
        var postData = jQuery(this).serializeArray();

        jQuery.ajax({
            url: jQuery(this).attr("action"),
            type: "POST",
            data: postData,
            success: function (response) {
                if (jQuery.trim(response) != 'success') {

                } else {

                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // Error Found
            }
        });
        e.preventDefault(); //STOP default action
    });*/

    $(function(){
    	$('.animate').on('click', function(event){
    		event.preventDefault();
    		$('.pop').css({'right': '0', 'opacity': 1})
    	})
    })
</script>


