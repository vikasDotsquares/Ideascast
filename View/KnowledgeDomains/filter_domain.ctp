<thead>
	<tr>
		<th><input type="checkbox" name="checkAlltop" id="checkSkills" /></th>
		<th><?php echo $this->Paginator->sort('KnowledgeDomain.countryName',__("KnowledgeDomain"));?></th>									
		<th><?php echo $this->Paginator->sort('KnowledgeDomain.status',__("Status"));?></th>
		<th>Actions</th>
	</tr>
</thead>
<tbody id="tbody_skills">
	<?php
		if (!empty($currencies)) {
			$num = $this->Paginator->counter(array('format' => ' %start%'));
			$icount = 0;
			//pr($currencies); 
			foreach ($currencies as $currency) {
	?>
	<tr <?php if($this->params['paging']['KnowledgeDomain']['page']==1 && $icount == 0){ echo 'data-first="true"'; }?> <?php if($this->params['paging']['KnowledgeDomain']['page']==$this->params['paging']['KnowledgeDomain']['pageCount'] && $icount == (count($currencies)-1) ){ echo 'data-last="true"'; }?>  data-id="<?php echo $currency['KnowledgeDomain']['id']; ?>" data-order="<?php echo $currency['KnowledgeDomain']['sorder']; ?>">
		<td><?php //echo $num ?><input type="checkbox" class="checkSkillList" name="checkAll" value="<?php echo $currency['KnowledgeDomain']['id']; ?>" /></td>
		<td><?php echo $currency['KnowledgeDomain']['title']; ?></td>
		<td>
			<?php
				$clasificationId = $currency['KnowledgeDomain']['id'];
				if ($currency['KnowledgeDomain']['status'] == 1) { ?>
					<button data-toggle="modal" data-target="#StatusBox" rel="deactivate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-check alert-success"></i></button	>
			<?php	} else {
				 ?>
					<button data-toggle="modal" data-target="#StatusBox" rel="activate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-times alert-danger"></i></button>
			<?php	}	?>
			
		</td>
		 
		<td> 
			<?php $editURL = SITEURL."knowledge_domains/manage_domain/".$currency['KnowledgeDomain']['id']; ?>
			<a data-toggle="modal" class="edituser" data-target="#modal_box" title="Edit Domain" data-remote="<?php echo $editURL; ?>"  data-tooltip="tooltip" data-placement="top" style="cursor:pointer;" ><i class="fa fa-fw fa-edit"></i></a>
			
			<?php 
				//$deleteURL = SITEURL."skills/skill_delete/".$currency['Skill']['id'];
				$deleteURL = SITEURL."knowledge_domains/domain_delete/";
			?>										
			<a class="RecordDeleteClass" rel="<?php echo $currency['KnowledgeDomain']['id']; ?>" title="Delete Domain" data-whatever="<?php echo $deleteURL; ?>"  data-tooltip="tooltip" data-placement="top" style="cursor:pointer;"><i class="fa fa-fw fa-trash-o"></i></a>
		</td>
	</tr>								
	<?php
			$num++;
			$icount++;
			}//end foreach 
	?>								
	<?php } else { ?>
	<tr>
		<td colspan="6" style="color:RED;text-align: center;">No Records Found!</td>
	</tr>
		<?php
			}
		?>
</tbody>
<tfoot>
	<?php 
	if($this->params['paging']['KnowledgeDomain']['pageCount'] > 1) { ?> 
	<tr>
		<td colspan="8" align="right">
			
			<div class="pagination-summary pull-left" >
				<?php
					echo $this->Paginator->counter(array(
					'format' => __('{:current} of {:count} &nbsp;/&nbsp; Page {:page} of {:pages}')
					));
				?>
			</div>
			<ul class="pagination">
				<?php 											
				echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li', 'class' => 'disabled', 'disabledTag' => 'span')); ?>
				<?php //echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li', 'disabledTag' => 'span', 'first' => 1));?>
				<?php echo $this->Paginator->numbers(array('first'=>1,'last'=>1,'ellipsis'=>'<li><a>...</a></li>','modulus'=>3,'tag' => 'li','separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li'), null, array('tag' => 'li', 'class' => 'disabled', 'disabledTag' => 'span'));?>
				<?php echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'span')); ?>
			</ul>
			
		</td>
	</tr>
	<?php } ?>
</tfoot>