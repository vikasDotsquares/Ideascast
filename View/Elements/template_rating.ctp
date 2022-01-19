
<?php 
// debug($this->Paginator->param('count'));
// echo $this->Paginator->numbers();

$jeera_paging = $template_rating;
 //pr($template_rating);
$this->Paginator->options( $jeera_paging['options'] ); 
$summary_model = (isset($jeera_paging['summary_model']) && !empty($jeera_paging['summary_model'])) 
					? $jeera_paging['summary_model']
					: ucfirst( $this->request->params['controller'] );
?> 
 
	<?php if ( isset($jeera_paging['show_summary']) && $jeera_paging['show_summary'] ) {?>
		<div class="pagination-summary pull-left" >
			<?php
                echo $this->Paginator->counter(array(
                'format' => __('{:current} of {:count} ' . $summary_model . ' &nbsp;/&nbsp; Page {:page} of {:pages}')
                ));
			?>
		</div>         
	<?php } ?>
	
	<ul class="pagination pagination-large pull-right">
		<?php
			echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li', 'class' => 'disabled', 'disabledTag' => 'span'));
			echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li', 'first' => 1, ));
			echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'span'));
		?>
	</ul> 
	
	<!-- /.pagination -->

<script type="text/javascript">
jQuery(function($) {
	
	$("body").delegate("li.disabled a", "click", function(event) {
		// alert("here")
		return false;
	})
	
})
</script>