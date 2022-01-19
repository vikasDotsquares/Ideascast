<!-- Change Language H1 -->
<?php echo $this->Html->Tag('h1', __('Change language:')); ?>
 
<!-- Change Language list -->
<ul>
<?php foreach($availableLanguages as $key=>$value) {
    $link = $this->Html->Link($value, array('controller' => 'pages', 'action' => 'changeLanguage', $key));
    echo $this->Html->Tag('li', $link, array('class' => $key == $language ? 'selected' : ''));
} ?>
</ul>
 
<p>&nbsp;</p>
<!-- Change Language test -->
<?php echo $this->Html->Tag('h2', __('Hello!')); ?>
 
<!-- Put in evidence the selected language -->
<style>
    ul li a {text-decoration: none;}
    ul li.selected a {text-decoration: underline;}
</style>