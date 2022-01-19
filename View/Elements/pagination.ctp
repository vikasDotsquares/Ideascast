<?php

	$this->Paginator->options(
			 
				array( 
					'limit'	=> $limit,
					'url' 	=> array_merge( $JeeraPaging['options']['url'], ['model' => $model] ) 
				)
			 
		);
	
	$this->Paginator->defaultModel($model);  
    if ( $this->params['paging'][$model]['count'] > 0) { 
?> 
	
	<?php if ( isset($JeeraPaging['show_summary']) && $JeeraPaging['show_summary'] ) { ?>
		<div class="pagination-summary pull-left" >
			<?php
                echo $this->Paginator->counter(array(
                'format' => __('{:current} of {:count} ' . ucfirst( $this->request->params['controller'] ) . ' &nbsp;/&nbsp; Page {:page} of {:pages}')
                ));
			?>
		</div>         
	<?php } ?>
	
    <ul id="paginate" class="pagination pagination-large pull-right">
        <?php
            echo $this->Paginator->prev(__('previous'), array('tag' => 'li', 'model'=> $model), null, array('tag' => 'li', 'class' => 'disabled', 'disabledTag' => 'span'));
            echo $this->Paginator->numbers( array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li', 'first' => 1 ) );
            echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'span'));
        ?>
    </ul> 

<?php 
    }
    // echo $this->Js->writeBuffer(); 
?>  