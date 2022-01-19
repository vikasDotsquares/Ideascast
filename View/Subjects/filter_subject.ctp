<thead>
	<tr>
		<th><input type="checkbox" name="checkAlltop" id="checkSkills" /></th>
		<th><?php echo $this->Paginator->sort('Skill.countryName',__("Skill"));?></th>									
		<th><?php echo $this->Paginator->sort('Skill.status',__("Status"));?></th>
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
	<tr <?php if($this->params['paging']['Skill']['page']==1 && $icount == 0){ echo 'data-first="true"'; }?> <?php if($this->params['paging']['Skill']['page']==$this->params['paging']['Skill']['pageCount'] && $icount == (count($currencies)-1) ){ echo 'data-last="true"'; }?>  data-id="<?php echo $currency['Skill']['id']; ?>" data-order="<?php echo $currency['Skill']['sorder']; ?>">
		<td><?php //echo $num ?><input type="checkbox" class="checkSkillList" name="checkAll" value="<?php echo $currency['Skill']['id']; ?>" /></td>
		<td><?php echo $currency['Skill']['title']; ?></td>
		<td>
			<?php
				$clasificationId = $currency['Skill']['id'];
				if ($currency['Skill']['status'] == 1) { ?>
					<button data-toggle="modal" data-target="#StatusBox" rel="deactivate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-check alert-success"></i></button	>
			<?php	} else {
				 ?>
					<button data-toggle="modal" data-target="#StatusBox" rel="activate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-times alert-danger"></i></button>
			<?php	}	?>
			
		</td>
		 
		<td> 
			<?php $editURL = SITEURL."skills/manage_skill/".$currency['Skill']['id']; ?>
			<a data-toggle="modal" class="edituser" data-target="#modal_box" title="Edit Skill" data-remote="<?php echo $editURL; ?>"  data-tooltip="tooltip" data-placement="top" style="cursor:pointer;" ><i class="fa fa-fw fa-edit"></i></a>
			
			<?php 
				//$deleteURL = SITEURL."skills/skill_delete/".$currency['Skill']['id'];
				$deleteURL = SITEURL."skills/skill_delete/";
			?>										
			<a class="RecordDeleteClass" rel="<?php echo $currency['Skill']['id']; ?>" title="Delete Skill" data-whatever="<?php echo $deleteURL; ?>"  data-tooltip="tooltip" data-placement="top" style="cursor:pointer;"><i class="fa fa-fw fa-trash-o"></i></a>
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
	if($this->params['paging']['Skill']['pageCount'] > 1) { ?> 
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