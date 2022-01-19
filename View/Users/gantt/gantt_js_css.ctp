<style>
    .gantt_task_progress span{ display :none;}
    .gantt_task_content{ display :none;}

    #gantt_here{ position: relative; z-index:1;}

    .gantt_task_row.gantt_selected .gantt_task_cell{ border-right-color : #ebebeb !important; }
    .gantt_task_row.gantt_selected{ background-color : #fff  !important; }

    .gantt_task_line{  overflow:hidden;}
    .gantt_tree_content1.granttData{
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        width: 240px !important;
    }

	.gantt_sort{ display : none ;}
    .gantt_tree_content.granttData{
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
       /*  width: 80px !important; */
        width: 101px !important;
    }
    .gantt_task_progress{
        text-align:left;
        padding-left:10px;
        box-sizing: border-box;
        color:white;
        font-weight: bold;
    }
    .weekend{
        background: #f4f7f4 !important;
    }
    .gantt_selected .weekend{
        background:#FFF3A1 !important;
    }
    .gantt_side_content{bottom: 0px;}

    .project_name_sec {

	  overflow: hidden;
	  text-overflow: ellipsis;
	  white-space: nowrap;
	  width: 350px;
          max-width: 350px;
	}



     .container-fluid .gantt_wrappers {
      display: inline-block;
      padding: 10px 10px 5px !important;
      width: 100%;
    }

    @media (min-width: 768px)
    .modal-sm {
        width: 300px;
    }

    @media screen and (max-width: 1199px) {

     .container-fluid .gantt_wrappers .project_date_sec {
     /*  width: 100%; */
     font-size : 13px;
    }

     .container-fluid .gantt_wrappers .project_name_sec {
       width: 51px;
       max-width: 51px;

    }



    }



    @media screen and (max-width: 1024px) {
     .container-fluid .gantt_wrappers .project_date_sec {

       font-size : 13px;
    }


    }

    @media screen and (max-width: 1023px) {
    .project_date_sec label {
      width: 50%;
    }

    }



</style>
<?php  //pr( $data); ?>
 <?php //pr( $link);

 //$link = '{id:"1311",source:"1311",target:"1350",type:"1"},{id:"1312",source:"1312",target:"1350",type:"1"},{id:"1350",source:"1350",target:"1311",type:"1"}';


 ?>
<script type="text/javascript">
    $(document).ready(function () {
        var h = $('.gantt_task').height()+20;
        $('.gantt_task').css('height',h+'px');
	$('.gantt_hor_scroll').css('width',$('.gantt_task').width());
        $('.gantt_hor_scroll').css('right','0px');
        /* $('.gantt_task_line').poshytip({
            followCursor: true,
            allowTipHover: true,
            liveEvents: true,
            className: 'tip-twitter',
            fade: false,
            slide: false
        }); */

		//$('.gantt_task_line').popover();

	//	$('.gantt_task_line').popover({container: 'body',html: true,placement: "left"});

/*    gantt.attachEvent("onTaskLoading", function(task){
    task.$open = false;
    return true;
	});   */

    });





	//$('#gantt_block').show();

    var strDate = $.datepicker.formatDate('d M y', new Date());


    var ganttmode = '<?php echo $mode; ?>';




    var project_name = '<?php echo strip_tags(str_replace("'",'',$project_name)); ?>';

/* 	gantt.attachEvent("onTaskLoading", function(task){
		 task.$open = true;
		return true;
		});  */






		gantt.attachEvent("onTaskOpenedd", function (id, item) {

console.log("vikas");
    // any custom logic here
		 var wsid = id.split("_");
		 console.log(wsid);

		//$('body').delegate(".gantt_row .gantt_tree_icon", 'click', function (event,  extraData = {priority:0,user_id:0,group_id:0,sharing_type:0,assign_type:'none'}) {

 console.log("raja ");
		   //console.log(extraData.priority+"=priority status=");

           var project_type = $(".project_type:checked").val();// project type like my project/received project
           var projectid = (project_type == 'my_p') ? $("#my_project").val() : $("#my_received_project").val();// project id get

		   //var wsid = $(this).parents('.gantt_row').attr('task_id').split("_");
		     wsid = wsid[1] ;
		   console.log(wsid);

           var workspaceid = $("#workspace_id").val(); // owrkspace id get
           var status = $(".statusid").val();// get element status


/* 		   var user_id = extraData.user_id;
		   var group_id = extraData.group_id;
		   var sharing_type = extraData.sharing_type;
		   var assign_type = extraData.assign_type; */

		   var user_id = 0;
		   var group_id = 0;
		   var sharing_type = 0;
		   var assign_type = 0;

		   // element critical status
			 var elementCritical = 0;
           /* if( extraData.priority && extraData.priority == 1 ){
				var elementCritical = 1;
		   } else {
			   var elementCritical = 0;
		   } */

		   if (workspaceid == '') {
               var srcvalue = "<?php echo SITEURL; ?>users/get_workspaces_by_project/<?php echo $project_type; ?>:<?php echo $project_id ?>/mode:<?php echo $mode;?>";
           } else {
               var srcvalue = "<?php echo SITEURL; ?>users/get_element_by_workspaces/<?php echo $project_type; ?>:<?php echo $project_id ?>/workspace_id:" + workspaceid+'/mode:<?php echo $mode;?>';
           }
		  // $('.container-fluid').html('');
           $.ajax({
               type: "POST",
               url: srcvalue,
               dataType: "html",
               async: false,
               //global: true,
               beforeSend: function () {
                  //  $(".ajax_overlay_preloader").fadeIn();

               },
               success: function (resultes) {
                   //alert(resultes);
                   $("#gantt_block").html(resultes);

				//   console.log(resultes);
                   setTimeout(function(){
                        $('.gantt_tree_content').tooltip({
                                html: true,
                                container: 'body'
                        })

                     },1000)
               },
               complete: function () {
                  // $(".ajax_overlay_preloader").fadeOut();
				   //	console.log("comp");

				 //  $('#gantt_block').show();
				 //  alert(0)
               },
               data: {"project_id": projectid, "project_type": project_type,"available_yes": wsid, "workspace": workspaceid,"status":status,"criticalStatus":elementCritical,"user_id":user_id,"group_id":group_id,"sharing_type":sharing_type,"assign_type":assign_type}
           });

	   //})

	    updateInfo();



	   });









    //alert(ganttmode);

    gantt.config.work_time = true;
    gantt.config.min_column_width = 50;
    gantt.config.row_height = 20;
    gantt.config.grid_width = 415;
    gantt.config.date_grid = "%d %M %y";
    gantt.config.scale_height = 55;
    gantt.config.autosize = true;
    gantt.config.gantt_task_drag = false;
	 gantt.config.sort = true;

    var demo_tasks = {
        data: [
<?php echo $data; ?>
<?php //echo $data; ?>
        ],
        links: [
<?php echo $link; ?>
        ],


    };


    var getListItemHTML = function (type, count, active) {
        return '<li' + (active ? ' class="active"' : '') + '><a href="#">' + type + 's <span class="badge">' + count + '</span></a></li>';
    };

    var updateInfo = function () {
        var state = gantt.getState(),
                tasks = gantt.getTaskByTime(state.min_date, state.max_date),
                types = gantt.config.types,
                result = {},
                html = "",
                active = false;
        //alert(state.min_date);alert(state.max_date);
        //console.log(state.min_date+'  '+state.max_date);
        // get available types
        for (var t in types) {
            result[types[t]] = 0;
        }
        // sort tasks by type
        for (var i = 0, l = tasks.length; i < l; i++) {
            if (tasks[i].type && result[tasks[i].type] != "undefined")
                result[tasks[i].type] += 1;
            else
                result[types.task] += 1;
        }
        // render list items for each type
        for (var j in result) {
            if (j == types.task)
                active = true;
            else
                active = false;
            html += getListItemHTML(j, result[j], active);
        }

        //document.getElementById("gantt_info").innerHTML = html;

    };






    var $work_start = '-';
    var $work_end = '-';
    //var $work_duration = 0;
    var $work_status;
    gantt.config.columns = [ //Workspaces
        {name: "text", template: function (obj) {
                return obj.text;
            }, label: "Workspaces",
            width: "*", align: "", tree: true},
        {name: "start_date", label: "Start", template: function (obj) {
            var type = obj.t_id.split("_");

            if (obj.type == 'workspace'){
                /* $.ajax({
                    url: $js_config.base_url + "users/get_workspacedate/" + obj.id+'/<?php echo $project_id; ?>' ,
                    async: false,
                    global: true,
                    dateType: 'json',
                    beforeSend: function () {
                        //$(".ajax_overlay_preloader").fadeIn();
                    },
                    complete: function () {
                       // $(".ajax_overlay_preloader").fadeOut();
                    },
                    success: function (response) {
                        var data = JSON.parse(response);
                        $work_start = data.start_date;
                        $work_end = data.end_date;
                        $work_duration = data.duration;
                        $work_status = data.status;

                    }
                }); */
                /* var updateInfo = function () {
                    var state = gantt.getState()
                    gantt.config.tast_start_date = $work_start;
                    gantt.config.tast_end_date = $work_end;

                } */
            }







                if (obj.status == 'NON') {
                    return '-';
                }/*  else if (type[0] == 'workspace' && obj.type == 'workspace' && obj.start_date =='Invalid Date') {
                    $("#"+obj.t_id).find(".gantt_task_content").removeClass('gantt_task_content').addClass('gantt_side_content gantt_right ');
                    return $work_start;
                }
                else if (type[0] == 'workspace' && obj.type == 'workspace' && obj.start_date !='Invalid Date') {
                    return $work_start;
                } */
                else{
                    return gantt.templates.date_grid(obj.start_date);
                }



            }, align: "center", width: 70},
        {name: "end_date", label: "End", template: function (obj) {
                var $work_start = '-';
                var type = obj.t_id.split("_");
                if (obj.status == 'NON') {
                    return '-';
                }/*  else if (type[0] == 'workspace' && obj.type == 'workspace' && obj.end_date =='Invalid Date') {
                    return $work_end;
                }
                else if (type[0] == 'workspace' && obj.type == 'workspace' && obj.end_date !='Invalid Date') {
                    return $work_end;
                } */
                else {
                    return gantt.templates.date_grid(obj.end_date);
                }
            }, align: "center", width: 70},
        {name: "duration", label: "Dur.", template: function (obj) {
                var type = obj.t_id.split("_");
                if (obj.status == 'NON') {
                    return '0';
                   // alert(1);
                }
               /*  else if (type[0] == 'workspace' && obj.type == 'workspace' && obj.end_date =='Invalid Date') {
                    return $work_duration;
                }
                else if (type[0] == 'workspace' && obj.type == 'workspace' && obj.end_date !='Invalid Date') {
                     return $work_duration;
                } */
				else{
                    //alert(4);
                    return obj.dur;
                }



            }, align: "center", width: 42},
        {name: "status", label: "Status", template: function (obj) {
                if (obj.status == 'NON') {
                    return 'Unknown';
                } else if (obj.status == 'PND') {
                    return 'Not Started';
                } else if (obj.status == 'PRG') {
                    return 'In Progress';
                } else if (obj.status == 'OVD') {
                    return 'Overdue';
                } else if (obj.status == 'CMP') {
                    return 'Completed';
                } else {
                    return $work_status;

                }


            }, align: "center", width: 78},
    ];

    gantt.config.columns[2].sort = true;



    gantt.templates.progress_text = function(start, end, task){
            return "<span style='text-align:left;'>"+Math.round(task.progress*100)+ "% </span>";
    };


    if (ganttmode == 'month') {
        gantt.config.scale_unit = "year";
        gantt.config.step = 1;
        gantt.config.date_scale = "%Y";
        gantt.config.min_column_width = 50;

        gantt.config.scale_height = 55;

        var monthScaleTemplate = function (date) {
            var dateToStr = gantt.date.date_to_str("%M");
            var endDate = gantt.date.add(date, 2, "month");
            return dateToStr(date) + " - " + dateToStr(endDate);
        };

        gantt.config.subscales = [
/*             {unit: "month", step: 3, template: monthScaleTemplate},
            {unit: "month", step: 1, date: "%M"} */
            {unit: "month", step: 1, date: "%M"},
            {unit: "day", step: 1, date: "%d"}

        ];
//    }else if(ganttmode == 'month'){
//        gantt.config.scale_unit = "month";
//	gantt.config.date_scale = "%F, %Y";
//
//	gantt.config.scale_height = 50;
//
//	gantt.config.subscales = [
//		{unit:"day", step:1, date:"%j, %D" }
//	];
    } else if (ganttmode == 'year') {
        gantt.config.scale_unit = "year";
        gantt.config.step = 1;
        gantt.config.date_scale = "%Y";
        gantt.config.min_column_width = 0;

        gantt.config.scale_height = 55;
        gantt.config.scale_width = 10;


        gantt.config.subscales = [

            {unit: "month", step: 1, date: "%M"},

        ];

    }else {
        gantt.templates.scale_cell_class = function (date) {
            if (date.getDay() == 0 || date.getDay() == 6) {
                return "weekend";
            }
        };
        gantt.templates.task_cell_class = function (item, date) {
            if (date.getDay() == 0 || date.getDay() == 6) {
                return "weekend";
            }
        };

        gantt.templates.rightside_text = function (start, end, task) {
            if (task.type == gantt.config.types.milestone) {
                return task.text;
            }
            return "";
        };
        var weekScaleTemplate = function(date){
		var dateToStr = gantt.date.date_to_str("%d %M");
		var weekNum = gantt.date.date_to_str("(week %W)");
		var endDate = gantt.date.add(gantt.date.add(date, 1, "week"), -1, "day");
		return dateToStr(date) + " - " + dateToStr(endDate) + " " + weekNum(date);
	};

	gantt.config.subscales = [
		{unit:"month", step:1, date:"%F, %Y"},
		{unit:"week", step:1, template:weekScaleTemplate}


	];

	gantt.templates.task_cell_class = function(task, date){
		if(!gantt.isWorkTime(date))
			return "week_end";
		return "";
	};
    }
    gantt.attachEvent("onAfterTaskAdd", function (id, item) {
        updateInfo();
    });

	gantt.attachEvent("onBeforeLinkDisplay", function (id, item) {

	// console.log($(".gantt_task_link[link_id='"+item.id+"']"));
			// $(".gantt_line_wrapper").mouseover(function(e) {


	})





	gantt.attachEvent("onTaskOpened", function (id, item) {


	$( '.gantt_task_cell' ).on( "mousemove", function( event ) {
				 $(".gantt_task_link").tooltip('destroy');
				 $(".gantt_line_wrapper").tooltip('destroy');
				 $(".gantt_line_wrapper").popover('destroy');
		 console.log("move");
		})







	 $('.gantt_tree_content').tooltip({
                html: true,
                container: 'body'
            })
		 var  possign = 'bottom';
        $('.gantt_task_line').popover({

			placement : function(e) {

			$( '.gantt_task_line' ).on( "mousemove", function( event ) {



				  $lets = event.pageX - 50  ;
				  $tos = event.pageY - 5 ;

				/*  $('.popover').css('left',$lets +"px")
				 $('.popover').css('top',$tos  +"px")
				 $('.popover .arrow').css('display',"none") */
				 if(event.pageY > $(window).height() && (event.pageY - $(window).height() < 271)){
				     possign = 'top';
					 $('.popover').css('left',$lets +"px")
					/*  $('.popover').css('top',$tos  +"px") */
				     $('.popover .arrow').css('display',"none")

				  }else{

				   possign = 'bottom';
				   $('.popover').css('left',$lets +"px")
				  $('.popover').css('top',$tos  +"px")
				  $('.popover .arrow').css('display',"none")
				  }


			})

			 return possign;
            },
			trigger : 'hover',
			html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
		});
    });


	gantt.attachEvent("onBeforeTaskDrag", function(id, mode, e){
			 console.log("Tester");
			 return false;           //allows dragging if the global task index is even
		});

	gantt.attachEvent("onTaskClosed", function (id, item) {

	//console.log("closed");
	$( '.gantt_task_cell' ).on( "mousemove", function( event ) {
				 $(".gantt_task_link").tooltip('destroy');
				 $(".gantt_line_wrapper").tooltip('destroy');
				 $(".gantt_line_wrapper").popover('destroy');

	})


	var possign = 'bottom';
        $('.gantt_task_line').popover({

			placement : function(e) {

			$( '.gantt_task_line' ).on( "mousemove", function( event ) {
				//console.log( "pageX: " + event.pageX + ", pageY: " + event.pageY );

				  $lets = event.pageX - 50  ;
				  $tos = event.pageY - 5 ;

				/*  $('.popover').css('left',$lets +"px")
				 $('.popover').css('top',$tos  +"px")
				  $('.popover .arrow').css('display',"none")
				   */
				   if(event.pageY > $(window).height() && (event.pageY - $(window).height() < 271)){
				     possign = 'top';
					 $('.popover').css('left',$lets +"px")
					/*  $('.popover').css('top',$tos  +"px") */
				     $('.popover .arrow').css('display',"none")

				  }else{

				   possign = 'bottom';
				   $('.popover').css('left',$lets +"px")
				  $('.popover').css('top',$tos  +"px")
				  $('.popover .arrow').css('display',"none")
				  }


			})

			 return possign;
            },
			trigger : 'hover',
			html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
		});
    });

    gantt.attachEvent("onTaskRowClick", function (id, item) {

        updateInfo();
    });

    gantt.attachEvent("onAfterTaskDelete", function (id, item) {
        updateInfo();
    });


    (function () {

      //gantt._sort = {name: "start_date", direction: "asc" }
        gantt.config.font_width_ratio = 7;
        gantt.templates.leftside_text = function leftSideTextTemplate(start, end, task) {
            if (getTaskFitValue(task) === "left") {
                return task.text;
            }
            return "";
        };
        gantt.templates.rightside_text = function rightSideTextTemplate(start, end, task) {
            if (getTaskFitValue(task) === "right") {
                return task.text;
            }
            return "";
        };
        gantt.templates.task_text = function taskTextTemplate(start, end, task) {
            if (getTaskFitValue(task) === "center") {
                return task.text;
            }
            return "";
        };

        function getTaskFitValue(task) {
            var taskStartPos = gantt.posFromDate(task.start_date),
                    taskEndPos = gantt.posFromDate(task.end_date);

            var width = taskEndPos - taskStartPos;
            var textWidth = (task.text || "").length * gantt.config.font_width_ratio;

            if (width < textWidth) {
                var ganttLastDate = gantt.getState().max_date;
                var ganttEndPos = gantt.posFromDate(ganttLastDate);
                if (ganttEndPos - taskEndPos < textWidth) {
                    return "left"
                }
                else {
                    return "right"
                }
            }
            else {
                return "center";
            }
        }
    })();


      gantt.config.sort = true;
	 // gantt._sort = {name: "duration", direction: "desc" }
	  //gantt._sort = {name: "start_date", direction: "desc" }


    //gantt.config.progress = true;

//    gantt.templates.leftside_text = function(start, end, task){
//        return task.duration + " days";
//    };


	   gantt.init("gantt_here");
	   gantt.parse(demo_tasks);

	setTimeout(function(){

		//$('.gantt_sort.gantt_desc').trigger('click');
		//$('.gantt_sort.gantt_asc').hide();
		gantt.config.sort = false;


		$('.gantt_row .gantt_close').show()
		//$('.gantt_task_line').removeAttr('data-toggle');


		$('#modal_medium').on('hidden.bs.modal',function(){

			$('.gantt_task_line').popover({
				placement : 'top',
				trigger : 'hover',
				html : true,
				container: 'body',
				delay: {show: 50, hide: 400}
			});



		})



		$( '.gantt_grid' ).on( "mouseover", function( event ) {
			$(".gantt_line_wrapper").popover('destroy');

			$(".gantt_line_wrapper").tooltip('destroy');

		});

		$( '.gantt_task_cell' ).on( "mousemove", function( event ) {
				 $(".gantt_task_link").tooltip('destroy');
				 $(".gantt_line_wrapper").tooltip('destroy');
				 $(".gantt_line_wrapper").popover('destroy');
		// console.log("move");
		})

		$( '.gantt_task_cell' ).on( "mouseover", function( event ) {

				 //$(".gantt_line_wrapper").popover('hide');
			//	$(".gantt_line_wrapper").tooltip('hide');
				 $(".gantt_task_link").tooltip('destroy');
				 $(".gantt_line_wrapper").tooltip('destroy');
				 $(".gantt_line_wrapper").popover('destroy');
				/* $('.tooltip-inner').hide();
				$('.tooltip').hide();
				$('.tooltip').remove(); */
				$('.tooltip').hide();
				$('.tooltip').remove()
				//$('.popover').hide();
				//$('.popover').remove()

				setTimeout(function(){
				$(".gantt_task_link").tooltip('destroy');
				 $(".gantt_line_wrapper").tooltip('destroy');
				 $(".gantt_line_wrapper").popover('destroy');
				 //$('.popover').remove()
				// console.log("hover");
				},500)

		})

		//$( '.get_depend' ).on( "click", function( event ) {
		$('body').delegate('.get_depend_new', 'click', function(event){
            $('.gantWorkspace').show();
            $('.ganttDep').hide();
		})


		$('body').delegate('.get_depend', 'click', function(event){

		 $('.gantWorkspace').hide();
		 $('.ganttDep').show();
        $that = $(this);
		//$that.tooltip('hide');
		$that.tooltip('destroy');
		$('.tooltip-inner').hide();
		$('.tooltip').hide();
		$('.tooltip').remove();
		$(".gantt_line_wrapper").tooltip('destroy');
		$(".gantt_task_link").tooltip('destroy');

		var element_id = $that.attr('task_id');

	console.log(element_id);
	 //$(".gantt_line_wrapper").popover('destroy');


		 var ele_list_url = $js_config.base_url+'dashboards/element_list_gantt';

        $.ajax({
            url: ele_list_url,
            type: 'POST',
            data: { element_id:element_id  },
            dataType: 'json',
            success: function(response, status, jxhr) {

				//	response.find('.popover-title').css('display','block');
					//$that.attr('data-content',response);
					$('.ganttDep .dep_data').html(response);

					$that.tooltip('hide');


            }
        })
    })

	function getVisible(dd) {
		var $el = $(dd);
		return $(window).height() - ($el.offset().top - $(window).scrollTop())

	}



        var possign = 'bottom';
		var flag = false;
		/* $('.gantt_task_line:not(:last-child)').popover({ */
		$('.gantt_task_linessssssssssssssssssssssssssssss').popover({

			placement : function(e) {

			/* $( '.gantt_task_line:not(:last-child)' ).on( "mousemove", function( event ) { */
			$( '.gantt_task_line' ).on( "mousemove", function( event ) {
				// console.log( "pageX: " + event.pageX + ", pageY: " + event.pageY );
				 var tms = getVisible(this);

				 $(".gantt_line_wrapper").popover('destroy');

				 $lets = event.pageX - 50  ;
				 $tos = event.pageY - 5 ;
				 $tosB = event.pageY + 5 ;
				 $tosU = event.pageY - 50 ;

				 var  $adjust =  $(window).width() - $lets;
				//  console.log($adjust);

				 if($adjust < 300 && $(window).width() > 360){
					$lets = $lets - 100;
				 }

				  if($adjust < 200  && $(window).width() > 360){
					$lets = $lets - 80;
				 }

				 if($adjust < 100  && $(window).width() > 360){
					$lets = $lets - 50;
				 }


				 /*  $('.popover').css('left',$lets +"px")
				  $('.popover').css('top',$tos  +"px")
				  $('.popover .arrow').css('display',"none")  */
				$('.popover .arrow').css('display',"none")

				//alert($(window).height() - event.pageY );
				/* console.log(event.pageY);
				console.log( $(window).height()); */

				// if(event.pageY > $(window).height() || (( $(window).height() - event.pageY ) < 350)){
				 if(tms < 350){
				     possign = 'top';
					 $('.popover').css('left',$lets +"px")
					// $('.popover').css('top',$tosB  +"px")
				  //    console.log("vikas");
				  }else{

				   possign = 'bottom';
				   $('.popover').css('left',$lets +"px")
				   $('.popover').css('top',$tos  +"px")
				   $('.popover .arrow').css('display',"none")
				  }

			})

			return possign;

            },
			trigger : 'hover',
			html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
		})




	},500)

   // gantt.parse(demo_tasks);


	// console.log(gantt);
	 //console.log(gantt);

    updateInfo();

</script>

<script type="text/javascript" >
    $(document).ready(function () {


        $( "body" ).on( "click", function() {
            $('.tooltip').each( function( i, val ) {
                $(this).css("display","none")
            });
        })

        $(document).on('click', '.granttData', function (e) {
            var id_value = $(this).attr("id").split("_");
            var id = id_value[1];
            var type = id_value[0];
            var urlV = $js_config.base_url + "users/" + type + "_popup_box/" + id + "/" + type + "/" + "<?php echo $project_id ?>"
            var $t = $(this);

			if(type=='workspace'){
				return;
			}
            $.ajax({
                url: urlV,
                async: false,
                global: false,
                beforeSend: function () {
                    //$(".ajax_overlay_preloader").fadeIn();
                },
                complete: function () {
                   // $(".ajax_overlay_preloader").fadeOut();

                },
                success: function (response) {
				//console.log(response);
                   if($t.data('target')=='#modal_large'){

						$('#modal_large').find('.modal-content').html(response);
						$t.modal('show');

					}else{

						$('#modal_medium').find('.modal-content').html(response);

						$t.modal('show');


					}

                }
            });

        })


        $('#modal_medium').on('hidden.bs.modal', function(){
				$(this).removeData()
				$(".gantt_line_wrapper").tooltip('destroy');
				$(".gantt_task_link").tooltip('destroy');

		})


		$(document).on('touchstart', '.gantt_task_line', function (e) {
			 var $t = $(this);

			 $('.gantt_task_line').popover({
				placement : 'top',
				trigger : 'touchstart',
				html : true,
				container: 'body',
				delay: {show: 50, hide: 400}
			});

			 $t.popover('show');

		})



        $(document).on('click', '.gantt_task_line', function (e) {

            var id_value = $(this).attr("id").split("_");
            var id = id_value[1];
            var type = id_value[0];
			if(type == 'workspace'){
			var urlV = $js_config.base_url + "users/" + type + "_popup_box/" + id + "/" + type + "/" + "<?php echo $project_id ?>";
			}else{
            var urlV = $js_config.base_url + "users/popup_box/" + id + "/" + type + "/" + "<?php echo $project_id ?>";
			}
            var $t = $(this);

			$('.popover.fade').remove();

			$t.popover('hide');
			$t.popover('destroy');
			$('.gantt_task_line').popover({
				placement : 'top',
				trigger : 'hover',
				html : true,
				container: 'body',
				delay: {show: 50, hide: 400}
			});



				/*
					$.ajax({
					url: urlV,
					async: false,
					global: true,
					beforeSend: function () {
						//$(".ajax_overlay_preloader").fadeIn();
					},
					complete: function () {
						//$(".ajax_overlay_preloader").fadeOut();
					},
					success: function (response) {
						$('#modal_medium').find('.modal-content').html(response);

						setTimeout(function(){
							$('.jvectormap-label').next().remove();
							$('.jvectormap-label').next().hide();


						}, 100)
						$t.modal('show');

					}
					})
				*/
        })





    });


</script>

<?php

$permissionTypeUser = $this->ViewModel->sharingPermitType($project_id,$this->Session->read('Auth.User.id'));
$permissionTypeUser = (isset($permissionTypeUser) && !empty($permissionTypeUser)) ? 1 : 0;

?>


<script type="text/javascript" >
(function($){



    $( '.users_tip_task' ).on( "click", function( e ) {
        var urlV = $js_config.base_url + "users/project_assign_people/<?php echo $project_id ?>/user_type:<?php echo $permissionTypeUser; ?>";

        var $t = $(this);
         $.ajax({
            url: urlV,
            async: false,
            global: false,
            success: function (response) {
                $t.popover({
					placement : 'right',
					trigger : 'click',
					html : true,
					template: '<div class="popover pop-content-parent" role="tooltip"><div class="arrow"></div><h2 class="popover-title" style="display:none;"></h2><div class="popover-content pop-content"></div></div>',
					container: 'body',
					delay: {  hide: 1000}
				});

				$t.attr('data-content',response);
				$t.popover('show');

            }
        })
    })




$('body').delegate("#gantt_here", 'mouseover', function (e) {
//$( '#gantt_here' ).on( "mouseover", function( e ) {
	 $( '.users_tip_task' ).popover('hide');
	 $( '.users_tip_task' ).popover('destroy');
	 $( '.popover.fade.right.in' ).remove();


})

                /*
 * @name DoubleScroll
 * @desc displays scroll bar on top and on the bottom of the div
 * @requires jQuery
 *
 * @author Pawel Suwala - http://suwala.eu/
 * @author Antoine Vianey - http://www.astek.fr/
 * @version 0.4 (18-06-2014)
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * Usage:
 * https://github.com/avianey/jqDoubleScroll
 */

jQuery.fn.doubleScroll = function(userOptions) {
	var $ = jQuery;
	// Default options
	var options = {
		contentElement: undefined, // Widest element, if not specified first child element will be used
		scrollCss: {
			'overflow-x': 'auto',
			'overflow-y': 'hidden'
        },
		contentCss: {
			'overflow-x': 'auto',
			'overflow-y': 'hidden'
		},
		onlyIfScroll: true, // top scrollbar is not shown if the bottom one is not present
		resetOnWindowResize: false, // recompute the top ScrollBar requirements when the window is resized
		timeToWaitForResize: 30 // wait for the last update event (usefull when browser fire resize event constantly during ressing)
	};
	$.extend(true, options, userOptions);
	// do not modify
	// internal stuff
	$.extend(options, {
		topScrollBarMarkup: '<div class="doubleScroll-scroll-wrapper" style="height: 20px; top:-21px; position:absolute;right:0px; z-index:9;"><div class="doubleScroll-scroll" style="height: 20px;"></div></div>',
		topScrollBarWrapperSelector: '.doubleScroll-scroll-wrapper',
		topScrollBarInnerSelector: '.doubleScroll-scroll'
	});

	var _showScrollBar = function($self, options) {

		if (options.onlyIfScroll && $self.get(0).scrollWidth <= $self.width()) {
			// content doesn't scroll
	    	// remove any existing occurrence...
			$self.prev(options.topScrollBarWrapperSelector).remove();
			return;
		}

	    // add div that will act as an upper scroll only if not already added to the DOM
	    var $topScrollBar = $self.prev(options.topScrollBarWrapperSelector);
	    if ($topScrollBar.length == 0) {

	    	// creating the scrollbar
	    	// added before in the DOM
	    	$topScrollBar = $(options.topScrollBarMarkup);
		    $self.before($topScrollBar);

		    // apply the css
		    $topScrollBar.css(options.scrollCss);
		    $self.css(options.contentCss);

		    // bind upper scroll to bottom scroll
		    $topScrollBar.bind('scroll.doubleScroll', function() {
		    	$self.scrollLeft($topScrollBar.scrollLeft());
		    });

		    // bind bottom scroll to upper scroll
		    var selfScrollHandler = function() {
		        $topScrollBar.scrollLeft($self.scrollLeft());
		    };
		    $self.bind('scroll.doubleScroll', selfScrollHandler);
	    }

	    // find the content element (should be the widest one)
		var $contentElement;
	    if (options.contentElement !== undefined && $self.find(options.contentElement).length !== 0) {
	        $contentElement = $self.find(options.contentElement);
	    } else {
	        $contentElement = $self.find('>:first-child');
	    }

	    // set the width of the wrappers
	    $(options.topScrollBarInnerSelector, $topScrollBar).width($contentElement.outerWidth());
	    $topScrollBar.width($self.width());
        $topScrollBar.scrollLeft($self.scrollLeft());

	}

	return this.each(function() {
		var $self = $(this);
		_showScrollBar($self, options);

	    // bind the resize handler
		// do it once
	    if (options.resetOnWindowResize) {
	    	var id;
	    	var handler = function(e) {
	    		_showScrollBar($self, options);
	    	};
	    	$(window).bind('resize.doubleScroll', function() {
    			// adding/removing/replacing the scrollbar might resize the window
    			// so the resizing flag will avoid the infinite loop here...
	    	    clearTimeout(id);
	    	    id = setTimeout(handler, options.timeToWaitForResize);
	    	});
	    }
	});
}

/* ******************************
*	Cost Budget POPOVERl
* *******************************/

$("body").delegate('.projectCstDetails', 'click', function(event){
	$that = $(this);

	$that = $(this);
	$that.popover({
		container: 'body',
		html: true,
		placement : 'right',
		// trigger : 'click',
		delay: {show: 50, hide: 400},
		template: '<div class="popover" style="width:215px;"><div class="arrow"></div><div class="popover-inner"><div class="popover-content"><p></p></div></div></div>'
	})
    .on('click', function() {
        var _this = this;
        $(this).popover('show');
        $('.popover').on('mouseleave', function() {
            $(_this).popover('hide');
        });
    })
    .on('mouseleave', function() {
        var _this = this;
        setTimeout(function() {
            if (!$('.popover:hover').length) {
                $(_this).popover('hide');
            }
        }, 300);
    });
	$that.popover('show');
	$that.tooltip('hide');
})
/*******************************/


$('.gantt_task').doubleScroll({resetOnWindowResize: false});

})(jQuery);


</script>


