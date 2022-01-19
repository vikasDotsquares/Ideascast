<?php echo $this->Html->script('projects/plugins/wysihtml5.editor' , array('inline' => true)); ?>

<?php //echo $this->Html->script('projects/create_element', array('inline' => true)); ?>
<?php //echo $this->Html->script('projects/plugins/wysihtml5.editor', array('inline' => true)); ?>


<?php
	echo $this->Form->create('Element', array('url' => array('controller' => 'entities', 'action' => 'create_element', $response['area_id']), 'class' => 'form-bordered', 'id' => 'modelFormAddElement')); ?>

<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Create Element</h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<!-- <h5 class="project-name"> popup box heading </h5>	-->
		<?php echo $this->Form->input('Element.area_id', [ 'type' => 'hidden', 'value' => $response['area_id'] ] ); ?>
		
		
		<div class="form-group">
			<label class=" " for="txa_title">Title:</label>
			<?php echo $this->Form->textarea('Element.title', [ 'class'	=> 'form-control', 'id' => 'txa_title', 'escape' => true, 'rows' => 1, 'placeholder' => 'max chars allowed 50' ] );   ?>
			<span class="error-message text-danger" ></span>
            <span class="title_error error-message text-danger" ></span>
		</div>
		<div class="form-group">
			<label class=" " for="txa_description">Description:</label>
			<?php echo $this->Form->textarea('Element.description', [ 'class'	=> 'form-control', 'id' => 'txa_description', 'escape' => true, 'rows' => 4, 'placeholder' => 'max chars allowed 250' ] ); ?>
			<span class="error-message text-danger" ></span>
                        <span class="desc_error error-message text-danger" ></span>
		</div>
		<div class="clearfix form-group">
			<label class="pull-left col-sm-2" style="margin: 0px; padding: 0px;" for="">Color Theme:</label>
			<?php echo $this->Form->input('Element.color_code', [ 'type' => 'hidden', 'value' => 'panel-gray', 'id' => 'color_code' ] ); ?>
			
			<div class="col-sm-6 pull-left" > 
				<a href="#" data-color="panel-red" data-preview-color="bg-red" class="btn btn-default btn-xs el_color_box"><i class="fa fa-square text-red"></i></a>
				<a href="#" data-color="panel-blue" data-preview-color="bg-blue" class="btn btn-default btn-xs el_color_box"><i class="fa fa-square text-blue"></i></a>
				<a href="#" data-color="panel-maroon" data-preview-color="bg-maroon" class="btn btn-default btn-xs el_color_box"><i class="fa fa-square text-maroon"></i></a>
				<a href="#" data-color="panel-aqua" data-preview-color="bg-aqua" class="btn btn-default btn-xs el_color_box"><i class="fa fa-square text-aqua"></i></a>
				<a href="#" data-color="panel-yellow" data-preview-color="bg-yellow" class="btn btn-default btn-xs el_color_box"><i class="fa fa-square text-yellow"></i></a> 
				<a href="#" data-color="panel-orange" data-preview-color="bg-orange" class="btn btn-default btn-xs el_color_box"><i class="fa fa-square text-orange"></i></a>
				<a href="#" data-color="panel-teal" data-preview-color="bg-teal" class="btn btn-default btn-xs el_color_box"><i class="fa fa-square text-teal"></i></a>
				<a href="#" data-color="panel-purple" data-preview-color="bg-purple" class="btn btn-default btn-xs el_color_box"><i class="fa fa-square text-purple"></i></a>
				<a href="#" data-color="panel-navy" data-preview-color="bg-navy" class="btn btn-default btn-xs el_color_box"><i class="fa fa-square text-navy"></i></a>
			</div> 
			<div class="col-sm-4 pull-left preview" style="text-align: center; display: none;"><span style="width: 100%; display: inline-block; font-size: 12px;">Click color box to see preview here.</span></div>
		</div>

		<!-- 
		<div class="form-group">
			<input type="radio"  id="el_dc_yes" name="data[Element][date_constraints]" class="fancy_input" value="1"  />
			<label class="fancy_label" for="el_dc_yes">Yes</label>

			<input  type="radio" id="el_dc_no" name="data[Element][date_constraints]" class="fancy_input" value="0" checked />
			<label class="fancy_label" for="el_dc_no">No</label>
		</div>

		<div class="clearfix" id="date_constraints_dates" style="width: 100%; display: none;">
			 
				<div class="form-group">
					<label class="control-label col-sm-6" for="start_date">Start date:</label>
					<label class="control-label col-sm-6" for="end_date">End date:</label>
				</div>
			 
			<div class="col-sm-12">
				
				<div class="col-sm-6">
					 
						<?php //echo $this->Form->input('Element.start_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date', 'class'	=> 'form-control' ] ); ?>
					 
				</div>
				<div class="col-sm-6">
					 
						<?php //echo $this->Form->input('Element.end_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'end_date', 'class'	=> 'form-control' ] ); ?>
					 
				</div>
			</div>
		</div>
	-->
		</div>
		
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="submit" id="submitSave" class="btn btn-success">Save</button>
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>

		<?php echo $this->Form->end(); ?>
				
<script type="text/javascript" >
 
(function ($) {
	
	$.fn.removeStyle = function(style)
    {
        var search = new RegExp(style + '[^;]+;?', 'g');
		// //console.log(search)
        return this.each(function()
        {
            $(this).attr('style', function(i, style)
            {
                return style.replace(search, '');
            });
        });
    };
	
	
	var $draging,
		staticOffset,
		iLastMousePos = 0,
		iMin = 32,
		grip;
		
		
	$.fn.TextAreaResizer = function () {
		return this.each(function () {
			$draging = $(this).addClass('processed'),
			staticOffset = null;

			// $(this).parent().find('grippie').remove()

			$(this).wrap('<div class="resizable-textarea"><span></span></div>').parent().append($('<div class="grippie"></div>').bind('mousedown', {
				el: this
			}, startDrag));

			var grippie = $('div.grippie', $(this).parent()) [0];
			$(grippie).attr('style', 'display: table; max-width: 10%; width: 100px; margin: 0px auto; text-align: center; background: rgb(96, 92, 168) none repeat scroll 0% 0%; font-size: 16px; color: rgb(255, 255, 255); border-radius: 0px 0px 3px 3px; min-height: 20px;')
			.html('<i class="fa fa-ellipsis-h"></i>')
			// grippie.style.marginRight = (grippie.offsetWidth - $(this) [0].offsetWidth) + 'px'
		})
	};
	function startDrag(e) {

		// //console.log($(e.data.el))
		$draging = $(e.data.el);
		$draging.blur();
		iLastMousePos = mousePosition(e).y;
		staticOffset = $draging.height() - iLastMousePos;
		$draging.css('opacity', 0.75);
		$(document).mousemove(performDrag).mouseup(endDrag);

		var currentHeight = $draging.height(),
			currentRows = parseInt(currentHeight / 20); 

		$('textarea').each(function(i, v) {
			
			var $txarea = $(this),
				wysihtml5_data = $txarea.data('wysihtml5'),
				$iframe = $();
				
			if( wysihtml5_data ) {
				
				var ifme = $(wysihtml5_data.editor.composer.iframe)	;
				if( $(e.data.el).is(ifme) ) {
					$txarea.removeStyle('height');
					var tstyle = $txarea.attr('style') + '; height:' + currentHeight + 'px;' 
					$txarea.attr('style', tstyle );
					$txarea.attr('rows', currentRows)
					
					$draging.removeStyle('height');
					var istyle = $draging.attr('style') + '; height:' + currentHeight + 'px;' 
					$draging.attr('style', istyle );
				} 
			} 
		})

		return false
	}
	function performDrag(e) {
		// textarea.removeStyle('height');
		// textarea.css('height', textarea.height() + 'px');
		// $txarea.removeStyle('height');
		
		var iThisMousePos = mousePosition(e).y;
		var iMousePos = staticOffset + iThisMousePos;
		if (iLastMousePos >= (iThisMousePos)) {
			iMousePos -= 5
		}
		iLastMousePos = iThisMousePos;
		iMousePos = Math.max(iMin, iMousePos);
		$draging.height(iMousePos + 'px');
		if (iMousePos < iMin) {
			endDrag(e)
		}
		return false
	}
	
	function endDrag(e) {
		$(document).unbind('mousemove', performDrag).unbind('mouseup', endDrag);
		$draging.css('opacity', 1);
		$draging.focus();

		var currentHeight = $draging.height(),
			currentRows = parseInt(currentHeight / 20);
		 
		$('textarea').each(function(i, v) {
			
			var $txarea = $(this),
				wysihtml5_data = $txarea.data('wysihtml5'),
				$iframe = $();
				
			if( wysihtml5_data ) {
				
				var ifme = $(wysihtml5_data.editor.composer.iframe);
				if( $draging.is(ifme) ) {
					
					$txarea.removeStyle('height');
					var tstyle = $txarea.attr('style') + '; height:' + currentHeight + 'px;' 
					//console.log(tstyle)
					$txarea.attr('style', tstyle );
					$txarea.attr('rows', currentRows)
					
					$draging.removeStyle('height');
					var istyle = $draging.attr('style') + '; height:' + currentHeight + 'px;' 
					$draging.attr('style', istyle );
				} 
			} 
		})

		$draging = null;
		staticOffset = null;
		iLastMousePos = 0
	}

	function mousePosition(e) {
		return {
			x: e.clientX + document.documentElement.scrollLeft,
			y: e.clientY + document.documentElement.scrollTop
		}
	}
}) (jQuery);
 
 
 
 
 
 
    $(function() { 
	 
		var elm = [ $('#txa_title'), $('#txa_description') ];
		wysihtml5_editor.set_elements(elm)
		$.wysihtml5_config = $.get_wysihtml5_config()
		
		var title_config = $.extend( {}, {'remove_underline': true}, $.wysihtml5_config)
		
		// var title_config = $.wysihtml5_config;
		$.extend( title_config, { 'parserRules': { 'tags': { 'br': { 'remove': 0 },'ul': { 'remove': 0 } ,'li': { 'remove': 0 },'b': { 'remove': 0 },'i': { 'remove': 0 } ,'blockquote': { 'remove': 0 } ,'ol': { 'remove': 0 } ,'u': { 'remove': 0 }     } } })
		
		
		$("#txa_title").wysihtml5( title_config );
		
		$("#txa_description").wysihtml5( $.extend( $.wysihtml5_config, {'remove_underline': false, 'lists': true, 'limit': 250, 'parserRules': { 'tags': { 'br': { 'remove': 0 },'ul': { 'remove': 0 } ,'li': { 'remove': 0 },'b': { 'remove': 0 },'i': { 'remove': 0 } ,'blockquote': { 'remove': 0 } ,'ol': { 'remove': 0 } ,'u': { 'remove': 0 }     } }}, $.wysihtml5_config)  );
		
		// $("#txa_description").wysihtml5( $.extend( $.wysihtml5_config, {'remove_underline': false, 'lists': true, 'limit': 250, 'parserRules': wysihtml5ParserRules }, $.wysihtml5_config)  );
		
		
		$('#popup_modal').on('shown.bs.modal', function (e) {
			 
			var $btnSubmit = $(this).find("button[type=submit]")
			
			$btnSubmit.data('target-area', $(e.relatedTarget))
		});
		
		$('#popup_modal').on('hidden.bs.modal', function () {
			var $btnSubmit = $(this).find("button[type=submit]")
			// if( $btnSubmit.has('target-area'))
				// $btnSubmit.removeData('target-area')
			if( $(this).data('bs.modal') )
				$(this).removeData('bs.modal');
			 //location.reload();
		});
		
		
		$(".el_color_box").on('click', function( event ) {
			event.preventDefault();
			
			$.each( $('.el_color_box'), function(i, el){
					$(el).find('i').addClass('fa-square').removeClass('fa-check')
			} )
			
			var $cb = $(this)
				$cb.find('i').addClass('fa-check').removeClass('fa-square')
			
			var $frm = $cb.closest('form#modelFormAddElement')
			var $hd = $frm.find('input#color_code')
			var cls = $hd.val()
			
			var foundClass = (cls.match (/(^|\s)panel-\S+/g) || []).join('')
			if( foundClass != '' ) {
				$hd.val('')
			}
			
			var applyClass = $cb.data('color')
			
			 
			var splited = applyClass.split('-'),
				previewClass = 'bg-jeera',
				previewText = 'Color preview';
				
				
			if(splited[1] != '') {
				previewClass = 'bg-' + splited[1];
				previewText = $.ucwords(splited[1]);
			} 
			
			$(".preview span").removeAttr('class')
							.attr('class', previewClass)
							.text(previewText) 
			
			$hd.val(applyClass); 
		})
		
		$(".fancy_input").on('change', function(e) {
			
			var $t = $(e.target);
			
			if( $t.val() > 0 ) {
					$("#date_constraints_dates").slideDown(700)
			}
			else {
					$("#date_constraints_dates").slideUp(700)
			}
		})

// Get title field iframe from its data and stop scrolling and seamless to disable scrollbars on different browsers.
// This also remove br tags from the text

// for Firefox
if( $.check_browser() == 3) {
	
	// $( ".wysihtml5-sandbox" ).TextAreaResizer();
	
	$("iframe").each(function () {
		var title_wysi = $('#txa_title').data("wysihtml5");
		var iframe = title_wysi.editor.composer.iframe
		
		
		$(this).load(function (event) {
			
			if( $(this).is(iframe)) {
				var $body = $(this).contents().find('body')
				
				
				
				
				/* $("#resizable_element").resizable({
					grid: true,
					containment: "parent",
					distance: 0,
					ghost: true,
					helper: "resizable-helper",
					aspectRatio: true,
					autoHide: false,
					create: function( event, ui ) {
						//console.log(event);
					},
					start: function( event, ui ) {
						// //console.log(ui.helper);
					},
					resize: function( event, ui ) {
						// //console.log(ui.size);
					}
				}) */
				
				// //console.log($(this).data())
				
				
			
				$body.bind('keyup', function(events){ 
					
					if(events.keyCode == 13) {
						events.preventDefault(); 
					
						$('br', $(this)).replaceWith(''); 
						
						return;
					} 
				})
			
			}
		});
	});

}
else if( $.check_browser() == 1 ||  $.check_browser() == 2 ||  $.check_browser() == 4 ) { 
	// For Google Chrome. Opera, Safari and IE
	var title_wysi = $('#txa_title').data("wysihtml5");
	if( title_wysi ) {
		var edtor = $('#txa_title').data("wysihtml5").editor;
		
		if( edtor ) {
			var iram = edtor.composer.iframe;
			
			$(iram).attr('scrolling', 'no')
			$(iram).attr('seamless', 'seamless') 
			var $body = $(iram).contents().find('body') 
			$body.on('keyup', function(event){
				
				if(event.keyCode == 13) {
					event.preventDefault();
					
					$('br', $(this)).replaceWith(''); 
					return;
				} 
			}) 
		}
	}
}	
		
	})

</script>