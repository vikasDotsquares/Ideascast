<?php
/**
 * Breadcrumbs helper
 * Allows to generate and display breadcrumbs with a convenient syntax
 *
 * It uses a <ul><li> syntax but can be extended and protected method overriden to
 * generate the markup adapted to your situation
 * 
 */
class BreadcrumbsHelper extends HtmlHelper {
/**
 * Helpers needed
 *
 * @var array
 */
	public $_crumbs = array();

/**
 * Separator string to use between each crumb
 * Set an empty string to not use a text separator
 *
 * @var string
 */
	// public $separator = '';

/**
 * Breadcrumbs array
 *
 * @var array
 */
	// protected $_breadcrumbs = array();

/**
 * Adds a crumb to the list and disable the link if it is the current page
 *
 * @param string $label Text for link
 * @param mixed $link URL for link (if empty it won't be a link)
 * @return Instance of the helper to allow chained calls
 */
	
	public function getCrumbList($options = array(), $startText = false) {
		
		$defaults = array('firstClass' => 'first', 'lastClass' => 'last', 'separator' => '', 'escape' => true );
		$options = (array)$options + $defaults;
		$firstClass = $options['firstClass'];
		$lastClass = $options['lastClass'];
		$separator = $options['separator'];
		$escape = $options['escape'];
		 
		unset( $options['firstClass'], $options['lastClass'], $options['separator'], $options['escape'] );
		
		
		$crumbs = $this->_prepareCrumbs($startText, $escape);
		if (empty($crumbs)) {
			return null;
		}
		
		$result = '';
		$crumbCount = ( isset($crumbs) && !empty($crumbs) ) ? count($crumbs) : 0;
		$ulOptions = $options;
		foreach ($crumbs as $which => $crumb) {
			$options = array();
			if (empty($crumb[1])) {
				$elementContent = $crumb[0];
			} else {
				$elementContent = $this->link($crumb[0], $crumb[1], $crumb[2]);
			}
			if (!$which && $firstClass !== false) {
				$options['class'] = $firstClass;
			} elseif ($which == $crumbCount - 1 && $lastClass !== false) {
				$options['class'] = $lastClass;
			}
			if (!empty($separator) && ($crumbCount - $which >= 2)) {
				$elementContent .= $separator;
			} 
			
			$result .= $this->tag('li', $elementContent, $options);
		}
		return $this->tag('ol', $result, $ulOptions);
	}

}