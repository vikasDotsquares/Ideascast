
<?php
if(isset($project_elements) && !empty($project_elements)) {
    $elements = getByDbIds('Element', $project_elements, ['id', 'title']);
    if(isset($elements) && !empty($elements)) {
        $elements = Set::combine($elements, '{n}.Element.id', '{n}.Element.title');
        $temp = array_map(function($v){
            return trim(htmlentities($v));
        }, $elements);
        $elements = $temp;
    }

    $risk_elements = [];
    if(isset($risk_id) && !empty($risk_id)) {
        $risk_elements = $this->ViewModel->risk_elements($risk_id);
    }
    else if(isset($element_param) && !empty($element_param)){
        /*$workspace_elements = workspace_elements($element_param);
        if(isset($workspace_elements) && !empty($workspace_elements)){
            $element = Set::extract($workspace_elements, '{n}.Element.id' );
            $risk_elements = $element;
        }*/
        $risk_elements = [$element_param];
    }

    echo $this->Form->select('project_elements', $elements, array('escape' => false, 'empty' => false, 'class' => 'form-control', 'id' => 'project_elements', 'multiple' => 'multiple', 'value' => $risk_elements ));
}
else {
?>
<select class="form-control" id="project_elements"></select>
<?php
}
?>

<script type="text/javascript">
    $(function(){
        // ELEMENT'S MULTISELECT BOX INITIALIZATION
        $.project_element = $('#project_elements').multiselect({
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'elements[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Task',
            enableCaseInsensitiveFiltering: true,
            enableUserIcon: false,
            nonSelectedText: 'Select Tasks',
            onChange: function(option, checked, select) {
                // Get selected options.
                /*var selectedOptions = jQuery('#project_elements option:selected');

                if (selectedOptions.length >= 200) {
                    // Disable all other checkboxes.
                    var nonSelectedOptions = jQuery('#project_elements option').filter(function() {
                        return !jQuery(this).is(':selected');
                    });

                    nonSelectedOptions.each(function() {
                        var input = jQuery('input[value="' + jQuery(this).val() + '"]');
                        input.prop('disabled', true);
                        input.parent('li').addClass('disabled');
                    });
                }
                else {
                    // Enable all checkboxes.
                    jQuery('#project_elements option').each(function() {
                        var input = jQuery('input[value="' + jQuery(this).val() + '"]');
                        input.prop('disabled', false);
                        input.parent('li').addClass('disabled');
                    });
                }*/
            },
            onDropdownHidden: function(option, closed, select) {}
        });
    })
</script>