<?php
echo $this->Html->script ( 'projects/plugins/marks/jquery.mark.min', array ( 'inline' => true ) );
// echo $this->Html->script ( 'projects/pagination', array ( 'inline' => true ) );

?>

<div class="search_wrapper">
	<div class="col-sm-12 col-md-4 col-lg-3 left-container nopadding-lefts">
		<div class="panel panel-green ">
			<div class="panel-heading">
				<h5>Search Results</h5>
			</div>
			<div class="panel-body searching-ac" id="search_accordion">
			<?php
 
 
			$leftpart = $this->Search->showResultAsLeft ( $result, $keyword );
			echo isset ( $leftpart ['tab_one'] ) && ! empty ( $leftpart ['tab_one'] ) ? $leftpart ['tab_one'] : null;

			echo isset ( $leftpart ['tab_two'] ) && ! empty ( $leftpart ['tab_two'] ) ? $leftpart ['tab_two'] : null;
			echo isset ( $leftpart ['tab_thired'] ) && ! empty ( $leftpart ['tab_thired'] ) ? $leftpart ['tab_thired'] : null;
			?>
           	</div>
		</div>
	</div>
	<div
		class="col-sm-12 col-md-8 col-lg-9 right-container panel panel-default no-padding">
		<div class="panel-heading">
			<h5 style="margin: 0px;">Search Details</h5>
		</div>

		<div class="panel-body">
			<?php
			$middlepart = $this->Search->showAjaxResultAsMiddle ( $result, $keyword );
			?>

			<span class="paginate_links">
				<?php
				// echo $middlepart ['pagination']; ?>
			</span>
			<?php
			echo $middlepart ['tab_middle'];
			?>
			<span class="paginate_links">
				<?php
				// echo $middlepart ['pagination']; ?>
			</span>
			<!-- <a href="#" id="loadMore" class="load-more" data-target="">Load More</a> -->
		</div>

	</div>
</div>



<div class="modal modal-success fade" id="popup_model_box_profile"
	style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content border-radius">
			<div style="background: #303030 none repeat scroll 0 0; display: block; padding: 100px; width: 100%;">
				<!--<img src="<?php echo SIREURL;?>images/ajax-loader-1.gif" style="margin: auto;">-->
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<script type="text/javascript">
    $(function () {
        $("#popup_model_box_profile").on('hidden.bs.modal', function () {
            $(this).data('bs.modal', null);
        });

        $('.search-items:not(.search-items-all) a[href*=#]').click(function (event) {
        	event.preventDefault();
            var target = $(this).attr('href');
            var $this = $(this);
            //$(".search-div-main").hide();
			$('.search-items li').removeClass('chat-shad');

            $(".search-div-main").fadeOut(1000, "linear", function () {});
            //$("#ajax_overlay").show();
            setTimeout(function () {
				$this.parent('li').addClass('chat-shad');
               // $("#ajax_overlay").hide();
                $(".search-div-main" + target).fadeIn(1600, "linear");

            }, 1100);
				  $.jsPagination({cur_page: 1, parent: ".search-div-main" + target });
            if (target.length) {
                event.preventDefault();
                $('html, body').stop().animate({
                    //scrollTop: (target.offset().top)-'125'
                }, 1000);
            }


        });

        $('.view-all a[href*=#]').click(function (event) {
			event.preventDefault();
            var $this = $(this);
            $('.search-items li').removeClass('chat-shad');
            $(".search-div-main").fadeOut(1000, "linear", function () {});
            //$("#ajax_overlay").show();
            setTimeout(function () {
              //  $("#ajax_overlay").hide();
                $this.parent('li').addClass('chat-shad');

                $(".search-items-all-"+$this.data("id")).fadeIn(1600, "linear");
            }, 1100);
				 $.jsPagination({cur_page: 1, parent: ".search-items-all-"+$this.data("id") });

        });


        $('.pophover').popover({
            placement: 'bottom',
            trigger: 'hover',
            html: true,
            container: 'body',
            delay: {show: 50, hide: 400}
        })

    })

$(function () {

	$.jsPagination = function(args) {

		$('.footer-content a').trigger('click');

		var chkTotalTrRows = $('.result-item').length,
			rows_per_page = $js_config.search_limit,
			total_rows;

		if( args && args.hasOwnProperty('parent') && args.parent !== '' ) {
			var $parent = args.parent;
			chkTotalTrRows = $($parent + " > .result-item").length;

		}
		if( chkTotalTrRows > 0 ) {
			total_rows = chkTotalTrRows;
		}


		var cur_page = (args) ? args.cur_page : 1;
		var start = (rows_per_page * (cur_page - 1));
		var end = start + rows_per_page;//(rows_per_page);

		if( args && args.hasOwnProperty('parent') && args.parent !== '' ) {
			var $parent = args.parent;
			// $($parent).children('.result-item').slice(start, end)
			if( total_rows <= $js_config.search_limit ) {
				$($parent + ' > .result-item').slice(start, end).show();
			}
			else {
				$($parent + " > .result-item").hide();
				$($parent + ' > .result-item').slice(start, end).show();
				// $($parent + ' > .result-item:gt('+(start)+'):lt('+end+')').show();
			}

		}
		else {
			if( total_rows <= $js_config.search_limit ) {
				$('.result-item').slice(start, end).show()
			}
			else {
				$(".result-item").hide();
				// $('.result-item:gt('+start+'):lt('+end+')').show();
				$('.result-item').slice(start, end).show();
			}
		}

		var pagination_data = {
			"total_rows": total_rows,
			"rows_per_page": $js_config.search_limit,
			"adjacents" : $js_config.search_adjacents,
			"cur_page": cur_page,
			"show_first": 1,
			"show_last": 1,
			"show_prev": 1,
			"show_next": 1,
			"btn_class": 'btn btn-default btn-sm',
		};

		// $('.paginate_links').html('<div class="loaders"></div>');
		$.ajax({
			url: $js_config.base_url + 'searches/get_pagination',
			type: 'POST',
			data: pagination_data,
			dataType: "JSON",
			global: false,
			success: function(response) {
				// Success
				if(response.success) {
					$('.paginate_links').html(response.output);
					// $('.js_pagination').show();
					// $('.total_rows').html(total_rows);
				}
			}
		});
	}

});
	function clickAnchor(t) {

		var element = $(t);
		var cur_page = element.attr("data-value");

		var args = {'cur_page': cur_page,parent:''}

		var pagination_data = {
			"total_rows": $('.result-item').length,
			"cur_page": cur_page,
			"rows_per_page": $js_config.search_limit,
			"adjacents" : $js_config.search_adjacents,
			"show_first": 1,
			"show_last": 1,
			"show_prev": 1,
			"show_next": 1,
			"btn_class": 'btn btn-default btn-sm',
		};

		/******************/
		var selectedTab = $('#search_accordion').find('.chat-shad');
		if( selectedTab.length > 0 ) {
			if( selectedTab.hasClass('view-all') ) {
				var link = selectedTab.find('a'),
					dataid = link.data('id');
				pagination_data.total_rows = $(".search-items-all-" + dataid).find(".result-item").length;
				args.parent = ".search-items-all-" + dataid;
			}
			else {
				var link = selectedTab.find('a'),
					dataid = link.attr('href');
				pagination_data.total_rows = $(".search-div-main" + dataid).find(".result-item").length;
				args.parent = ".search-div-main" + dataid;
			}

		}
		$.jsPagination(args);

		/***********************/


		/* $.ajax({
			url: $js_config.base_url + 'searches/get_pagination',
			type: 'POST',
			data: pagination_data,
			dataType: "JSON",
			success: function(response) {
				// Success
				if(response.success) {
					$('.paginate_links').html(response.output);
				}
			}
		});  */
	}


</script>

<style type="text/css">
.load-more, .load-more:focus {
	display: none;
	padding: 10px 0;
	text-align: center;
	background-color: #0064b4;
	color: #fff;
	border-radius: 3px;
	margin: 5px 0 0 0;
	transition: all 600ms ease-in-out;
    -webkit-transition: all 600ms ease-in-out;
    -moz-transition: all 600ms ease-in-out;
    -o-transition: all 600ms ease-in-out;
}
.load-more:hover {
	background-color: #67a028;
    color: #ffffff;
}

.mark, mark {
	padding: .2em;
	color: #f00;
	background: none !important;
}

.chat-shad {
	border: solid 1px #d9d9d9;
	background: #EFEFEF;
	color: #000 !important;
}

.chat-shad a {
	color: #000;
}

</style>
