
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


				hello
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

							<div class="tag-container">
								<input type="text" name="term" class="term" />
							</div>


							<div class="col-xs-4" style="margin-top: 40px;">
								<label style="width: 100%; vertical-align:middle; position: relative;" class="custom-dropdown0">
									<select id="my_projects" class="form-control aqua view-detail" name="project_list">
										<option value="test 1">test 1</option>
										<option value="test 2">test 2</option>
									</select>
								</label>
							</div>
							<div class="col-xs-4" style="margin-top: 40px;">
								<label style="width: 100%; vertical-align:middle; position: relative;" class="custom-dropdown0">
									<select id="rec_projects" class="form-control aqua view-detail" name="project_list">
										<option value="test 1">test 33</option>
										<option value="test 2">test 44</option>
									</select>
								</label>
							</div>
							<div class="col-xs-4" style="margin-top: 40px;">
								<div class="dropdown">
								    <button class="btn btn-primary dropdown-toggle" id="menu1" type="button" data-toggle="dropdown">	Dropdown Example <span class="caret"></span>
								    </button>
								    <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
								      	<li role="presentation"><a role="menuitem" tabindex="-1" href="#">HTML</a></li>
								      	<li role="presentation"><a role="menuitem" tabindex="-1" href="#">CSS</a></li>
								      	<li role="presentation"><a role="menuitem" tabindex="-1" href="#">JavaScript</a></li>
								      	<li role="presentation"><a role="menuitem" tabindex="-1" href="#">About Us</a></li>
								    </ul>
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
<style type="text/css">
	.custom-list {
		position: absolute;
	    top: 0;
	    left: 0;
	    width: 100%;
	    padding: 0;
	    border: 1px solid #ccc;
	    list-style: none;
	}
	.custom-list li {
	    padding: 0 15px;
	    cursor: pointer;
	    font-size: 14px;
	    font-weight: normal;
	    line-height: 25px;
	    border-bottom: 1px solid #ccc;
	}
	.custom-list li:last-child {
		border-bottom: none;
	}
</style>
<!-- END OUTER WRAPPER -->
<script type="text/javascript">
	(function(old) {
	  $.fn.attr = function() {
	    if(arguments.length === 0) {
	      if(this.length === 0) {
	        return null;
	      }

	      var obj = {};
	      $.each(this[0].attributes, function() {
	        if(this.specified) {
	          obj[this.name] = this.value;
	        }
	      });
	      return obj;
	    }

	    return old.apply(this, arguments);
	  };
	})($.fn.attr);

	$(function(){
		$('.view-detail').each(function(index, el) {
			var name = $(this).attr('name'),
				id = $(this).attr('id'),
				classes = $(this).attr('class');
			var $list = $('<ul />', {
				'id': id,
				'name': name,
				'class': "custom-list",
			});
			$(this).parent().append($list);
			$('option', $(this)).each(function(){
				console.log($(this).attr())
				var $li = $('<li />', $(this).attr()).text($(this).text())
			  	$list.append($li);
			});
			$(this).remove();
			// $('#newyearfilter').attr('id', 'yearfilter');
		});









		/*$('.chk').on('change', function(event){
			event.preventDefault();
			$('.chk').not(this).prop('checked', false);
			$(this).prop('checked', true);
		})*/

		$('body').delegate('.tag-text', 'click', function(event) {
			event.preventDefault();
			var $this = $(this),
				$parent = $(this).parents('.tag-row:first');

			if($('.uploader').length > 0) {
            	$('.uploader').slideUp(100, function(){
            		$(this).remove();
            	});
            }

			$('.tag-row').not($parent[0]).removeClass('expended');
			if($parent.hasClass('expended')){
				$parent.removeClass('expended');
			}
			else{
				$parent.addClass('expended');
				setTimeout(function(){
					$.pdf_uploader($this.get(0));
				}, 300)
			}
		});

		$.pdf_uploader = function(t){

			var $this = $(t),
				$parent = $this.parents('.tag-row:first'),
				coordinates = $this.offset();
			var css = {
                left: coordinates.left - ($this.width() / 2) + ($this.outerWidth() / 2) - 5,
                top: coordinates.top + $this.outerHeight() + 12,
                right: 'auto'
            }

            if($('.uploader').length > 0) {
            	$('.uploader').remove();
            }

			var skill_id = $this.data('tagid');

			var uploaderHtml = '<div class="management-box"><h5>'+$this.text()+'</h5><form id="skillpdf"><div class="management-box-in"><div class="management-attach"><div class="btn btn-sm btn-success btn-file">Attach<input type="file" id="pdf_file" name="pdf_file" class="form-control" ></div><span class="pull-right management-action"><div class="btn-group action"><a class="btn btn-xs btn-success savepdf not-working" ><i class="fa fa-check"></i></a><a class="btn btn-xs btn-warning clear-form"><i class="fa fa-close"></i></a></div></span></div><input type="text" class="form-control" name="pdf_name"><div class="pdf-size">PDF only- max 5 mb</div><ul class="pdf-list"></ul></div><input type="hidden" class="form-control" name="skill_id" value="'+skill_id+'"></form></div>';

			var $uploader = $('<div />', {
				'class': 'uploader'
			})
			.css(css)
			.html(uploaderHtml);


			$uploader.appendTo($('body')).slideDown(200);
			$.get_skill_pdf(skill_id);

			$parent.data('uploader', $uploader);
			$uploader.data('tag', $parent);

			if($('.selector').length > 0 && $('.selector').is(':visible')) {
				$('.selector').slideUp(100, function(){
	        		$(this).remove();
	        		$('.term').val('')
	        	});
			}

		}

		$.get_skill_pdf = function(skill_id) {
			// ajax to get pdf of this skill id
			$('.uploader .pdf-list').html('<div class="loading-bar"></div>');
			$.ajax({
				url: $js_config.base_url + 'projects/get_skill_pdf',
				type: 'post',
				dataType: 'json',
				data: {skill_id: skill_id},
				success: function(response) {
					var skill_lists = '';
					$.each(response.content, function(index, el) {
						skill_lists += '<li><span>'+el.SkillPdf.pdf_name+'</span> <a data-skillid="'+el.SkillPdf.id+'" class="btn btn-xs btn-danger pdf_delete" href=""> <i class="fa fa-trash-o"></i></a></li>';
					});
					$('.uploader .pdf-list').html(skill_lists);
				}
			})
		}

		$(document).on('click', function(e) {

	        $('.tag-row').each(function() {
	            var $this = $(this);
	            var $uploader = $('.uploader');
	            //the 'is' for buttons that trigger popups
	            //the 'has' for icons within a button that triggers a popup
	            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $uploader.has(e.target).length === 0) {
	                if($uploader) {
	                	$uploader.slideUp(300, function(){
	                		$(this).remove();
            				$('.tag-row').removeClass('expended');
            				$.setInputWidth();
	                	});
	                }
	            }
	        });
	        $.setInputWidth();
	    });

		$('body').delegate('.tag-container', 'click', function(event) {
			event.preventDefault();
			if($(event.target).is($(this))) {
				$(this).find('.term').focus();
				// $.addSkills()
				console.log('adfsfdf')
			}

		})

		$('body').delegate('.close-selector', 'click', function(event) {
			event.preventDefault();
			$('.selector').slideUp(100, function(){
        		$(this).remove();
        		$('.term').val('')
        	});
		})

		$('body').delegate('.tag-container .term', 'focus', function(event) {
			event.preventDefault();
			if($(this).val().length >= 1){
				if($('.selector').length > 0 && !$('.selector').is(':visible')) {
	            	$('.selector').slideDown(100);
	            }
			}
			if($(this).val().length >= 1){
				$.tag_selector($(this).get(0));
			}
		})
		$('body').delegate('.tag-container .term', 'keyup', function(event) {
			event.preventDefault();
			var $this = $(this);
			if($(this).val().length < 1){
				if($('.selector').length > 0) {
	            	$('.selector').slideUp(100, function(){
	            		$(this).remove();
	            	});
	            }
				return;
			}
			$(this).addClass('selector_visible');
			setTimeout(function(){
				$.tag_selector($this.get(0));
				$this.data('selector', $('.selector'));
				$('.selector').data('input', $this);
			}, 1000)
		})

		$(document).on('click', function(e) {

	        $('.tag-container .term').each(function() {
	            var $this = $(this);
	            var $selector = $('.selector');
	            //the 'is' for buttons that trigger popups
	            //the 'has' for icons within a button that triggers a popup
	            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $selector.has(e.target).length === 0) {
	                if($selector) {
	                	$selector.slideUp(300, function(){
	                		$(this).remove();
	                		$this.val('')
	                	});
	                }
	            }
	        });
	    });


		$.tag_selector = function(t){
			var $this = $(t),
				$parent = $this.parents('.tag-row:first'),
				coordinates = $this.offset();
			var css = {
                left: coordinates.left - ($this.width() / 2) + ($this.outerWidth() / 2),
                top: coordinates.top + $this.outerHeight() + 10,
                right: 'auto'
            }

            if($('.selector').length <= 0) {
				var $selector = $('<div />', {
					'class': 'selector'
				})
				.css(css)
				//.html('<i class="fa fa-times close-selector"></i>');
				/*$selector.appendTo($('body')).slideDown(200, function(){
					$.addSkills($this.val());
				});*/
				$selector.appendTo($('body'));
				$.addSkills($this.val());
            }
            else{
            	$.addSkills($this.val());
            }
		}

		$.addSkills = function(val){
			var $div = $('.selector');
			var $ul = $('<ul />', {
					'class': 'loading-bar'
				});
			$ul.appendTo($div);
			$.ajax({
				url: $js_config.base_url + 'projects/get_skills?term='+val,
				type: 'get',
				dataType: 'json',
				success: function(response) {

					if(response.success) {
						var data = response.content;
						$div.find('ul').remove();
						var $ul = $('<ul />', {
							'class': ''
						});
						$.each(data, function(index, text) {
							var $li = $('<li />', {
								'class': 'list-tag'
							})
							.text(text)
							.data('index', index);
							$li.appendTo($ul)
						});
						$ul.appendTo($div);
						$div.slideDown(500);
					}
					else{
						$div.find('ul').remove();
					}
				}
			})

		}

		$.adjust_selector = function(){
			var $this = $('.term'),
				coordinates = $this.offset();
			var css = {
                left: coordinates.left - ($this.width() / 2) + ($this.outerWidth() / 2),
                top: coordinates.top + $this.outerHeight() + 10,
                right: 'auto'
            }
			$('.selector').css(css);
		}
		;($.setInputWidth = function(){
			var $container = $('.tag-container'),
				container_width = $container.innerWidth();
			var wid = 0;
			$('.tag-row').each(function(index, el) {
				wid += $(this).width();
			});
			var input_width = container_width - wid - 130;
			$('.tag-container input.term').css('width', input_width + 'px')
		})();

		$("body").on("click", ".list-tag", function(event) {
			var tagHtml = '';
			$that = $(this);
			var tagId = $that.data('index');
			var tagName = $that.text();
			var checktagid = 'entered';

			var existingindex = $(".tag-row span:first-child");
			existingindex.each(function(){
				if( tagId == $(this).data('tagid') ){
					checktagid = 'outfromhere';
				}
			})

			if( checktagid == 'entered' ){
				tagHtml = '<div class="tag-row"><span data-tagid="'+tagId+'" class="tag-text">'+tagName+'</span><span class="tag-delete">✖</span></div>';
				$( tagHtml ).insertBefore('.term');
				setTimeout(function(){
					$.adjust_selector();
					$.setInputWidth();
				}, 300)
			}
		})

		$('body').delegate('.tag-delete', 'click', function(event) {
			event.preventDefault();
			var $parentRow = $(this).parents('.tag-row:first');
			$parentRow.remove();
			$.setInputWidth();
			$('.uploader').remove();
		})

		$('body').delegate('.savepdf', 'click', function(event) {
			event.preventDefault();
			$(".pdf-size").text('');

			if( $('#pdf_file').val() === undefined || $('#pdf_file').val() === '' ){
				$(".pdf-size").text('Please select a file.').css('color','#f00');
				return;
			}

			var filetype = $('#pdf_file')[0].files[0]['type'];
			var filesize = $('#pdf_file')[0].files[0]['size'];
			myfile = $('#pdf_file')[0].files[0]['name'];
			var ext = myfile.split('.').pop();
			if( ext !== "pdf" ){
			   $(".pdf-size")/*.text('This file type is unsupported.')*/.css('color','#f00');
			   return false;
			}
			if( filesize > 5242880 ){
				$(".pdf-size")/*.text('Selected file size is greater then 5MB')*/.css('color','#f00');
				return false;
			}


			var formData = new FormData();
			formData.append('pdf_file',  $('#pdf_file')[0].files[0]);
			formData.append('pdf_name',  $('[name=pdf_name]').val());
			formData.append('skill_id',  $('[name=skill_id]').val());
			$.ajax({
				url: $js_config.base_url + 'projects/skillpdfupload',
				type: "POST",
				data : formData,
				dataType: 'json',
				processData: false,
				contentType: false,
				success: function(response){
					$("#skillpdf")[0].reset();
					var skilllists ='';
					var results = response.content;
					$.each(results, function(index, value) {
						skilllists += '<li><span>'+value.SkillPdf.pdf_name+'</span> <a data-skillid="'+value.SkillPdf.id+'" class="btn btn-xs btn-danger pdf_delete" href=""> <i class="fa fa-trash-o"></i></a></li>';
					});
					$(".pdf-list").html(skilllists);
					$('.savepdf').addClass('not-working');

				},
				error: function(xhr, ajaxOptions, thrownError) {
				   // console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			})
			$('#pdf_file').val('');
			$('[name=pdf_name]').val('')
		})

		$('body').delegate('.clear-form', 'click', function(event) {
			event.preventDefault();
			$('#pdf_file').val('');
			$('[name=pdf_name]').val('');
			$('.savepdf').addClass('not-working');
		})

		$('body').delegate('.pdf_delete', 'click', function(event) {
			event.preventDefault();
			$that = $(this);
			var pdfid = $(this).data('skillid');
			if( pdfid != undefined && pdfid != '' ){
				$.ajax({
					url: $js_config.base_url + 'projects/skillpdfdelete',
					type: "POST",
					dataType: "json",
					data : $.param({ 'id': pdfid }),
					success: function(response){
						if( response.success ){
							$that.parents("li").animate({
								'background-color': "#f38080"
							}, 400, function(){
								$that.parents("li").remove();
							});
							setTimeout(function(){
								if($(".pdf-list li").length < 5 && ($('#pdf_file').val() === undefined || $('#pdf_file').val() === '' )) {
									$('.savepdf').removeClass('not-working');
								}
							}, 1000)
						}
					}
				})

			}

		})


		var file_onchange = function () {
		  var input = this; // avoid using 'this' directly
		  $(".pdf-size").text('PDF ONLY- MAX 5 MB').css('color','');
		  if (input.files && input.files[0]) {
			var type = input.files[0].type;
			var filesize = input.files[0].size;
			myfile = $( this ).val();
			var ext = myfile.split('.').pop();
			if( ext !== "pdf" ){
			   $(".pdf-size")/*.text('This file type is unsupported.')*/.css('color','#f00');
			   return;
			}
			if( filesize > 5242880 ){
				$(".pdf-size")/*.text('Selected file size is greater then 5MB')*/.css('color','#f00');
				return;
			}
		  }
		  if($(".pdf-list li").length < 5) {
				$('.savepdf').removeClass('not-working');
			}

		};
		$("body").delegate('#pdf_file', 'change', file_onchange);

	})
</script>

<style>
	.tag-container {
		width: 100%;
		margin-top: 0px;
	    padding: 5px;
	    border: 1px solid #00B3DB;
	    cursor: text;
	}
	.tag-container .term {
	    background: none;
	    width: 60px;
	    min-width: 300px;
	    border: 0;
	    height: 25px;
	    padding: 0;
	    margin-bottom: 0;
	    -webkit-box-shadow: none;
	    box-shadow: none;
	}
	.tag-container .term:focus {
		border: none;
		outline: none;
	}
	.tag-row {
		display: inline-block;
		padding: 2px 5px;
	    border: 1px solid #ccc;
	    border-radius: 3px;
	    min-width: 100px;
	    margin: 0 0 2px 2px;
	    transition: all 0.6s ease-in-out;
	}
	.tag-row.expended {
		/*width: 350px;*/
	}
	.tag-text {
		display: inline-block;
		cursor: default;
	}

	.tag-delete {
	    float: right;
	    cursor: pointer;
	    padding: 0 3px;
	    margin-left: 5px;
	    border-radius: 3px;
	}
	.tag-delete:hover {
	    background-color: #c00;
	    color: #fff;
	}
	.uploader {
		display: none;
		width: auto;
		height: auto;
		position: absolute;
		background-color: #effcff;
	}
	.selector {
		display: none;
		width: 350px;
		position: absolute;
		border: 1px solid #00B3DB;
		border-radius: 3px;
		background-color: #effcff;
		padding: 0;
		transition: all 0.6s ease-in-out;
		max-height: 300px;
		overflow-x: hidden;
		overflow-y: auto;
	}
	.selector ul {
		background-color: transparent;
		padding: 0;
		margin: 0;
	}
	.list-tag {
		display: block;
		width: 100%;
		padding: 5px 10px;
		color: #333;
		cursor: pointer;
	}
	.list-tag:hover {
		background-color: #00B3DB;
		color: #fff;
	}
	.close-selector {
	    position: absolute;
	    right: -12px;
	    top: -10px;
	    padding: 0px 2px;
	    color: #c00;
	    font-size: 20px;
	    cursor: pointer;
	    background: #fff;
	    border-radius: 2px;
	    border: 1px solid #ccc;
	}

	/* managment box start  */
	.management-box{
		border: 1px solid #98c8ef;
		display: inline-block;
		width: 420px;
		background: #fff;
		border-radius: 5px;
		/* margin-top: 30px; */
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
		padding-top: 10px;
		margin-top: 10px;
		max-height: 160px;
		overflow: auto;
		transition: all 0.6s ease-in-out;
	}
	.pdf-list li:first-child {
		border-top: 1px solid #ccc;
		padding-top: 5px;
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
	.not-working {
		pointer-events: none;
		opacity: 0.6;
	}
	/* managment box end */

	.loading-bar {
	    height: 2px;
	    width: 100%;
	    position: relative;
	    overflow: hidden;
	}

	.loading-bar:before {
	    display: block;
	    position: absolute;
	    content: "";
	    left: -200px;
	    width: 200px;
	    height: 2px;
	    background-color: #2980b9;
	    animation: running_bar 2s linear infinite;
	}


	@keyframes running_bar {
	    from {
	        left: -200px;
	        width: 30%;
	    }

	    50% {
	        width: 30%;
	    }

	    70% {
	        width: 70%;
	    }

	    80% {
	        left: 50%;
	    }

	    95% {
	        left: 120%;
	    }

	    to {
	        left: 100%;
	    }
	}
</style>


