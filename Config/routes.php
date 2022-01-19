<?php Router::parseExtensions('json');
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */

$whatINeed = explode('/', $_SERVER['REQUEST_URI']);
$whatINeed = $whatINeed[1];

$hostname = $_SERVER['HTTP_HOST'];
$remote_address = $_SERVER['REMOTE_ADDR'];


if ($whatINeed == 'ideascast') {

	if( MAINTENANCE > 0 ) {
		Router::connect('/*', array('controller' => 'offlines', 'action' => 'index'));
	}

	Router::connect('/app/webroot/mindmup', array('controller' => 'pages', 'action' => 'display', 'about', 'admin' => false));
	//	Router::connect('/about', array('controller' => 'pages', 'action' => 'display', 'about', 'admin'=>false));

	Router::connect('/blog', array('controller' => 'posts', 'action' => 'showblog', 'admin' => false));
	Router::connect('/blog/*', array('controller' => 'posts', 'action' => 'blogdetails', 'admin' => false));

	//Router::connect('/blogdetail/*', array('controller' => 'posts', 'action' =>'blogdetails', 'admin'=>false));

	Router::connect('/about', array('controller' => 'pages', 'action' => 'about', 'admin' => false));
	Router::connect('/faq', array('controller' => 'pages', 'action' => 'faq', 'admin' => false));
	Router::connect('/price', array('controller' => 'pages', 'action' => 'price', 'admin' => false));
	Router::connect('/privacy', array('controller' => 'pages', 'action' => 'privacy', 'admin' => false));
	// Router::connect('/why-jeera', array('controller' => 'pages', 'action' => 'why_jeera', 'admin' => false));
	Router::connect('/why-opusview', array('controller' => 'pages', 'action' => 'why_jeera', 'admin' => false));
	Router::connect('/partners', array('controller' => 'pages', 'action' => 'partners', 'admin' => false));
	Router::connect('/templates', array('controller' => 'pages', 'action' => 'templates', 'admin' => false));
	Router::connect('/work_board/*', array('controller' => 'boards', 'action' => 'status_board', 'admin' => false));

	// Router::connect('/why-jeera-benefits', array('controller' => 'pages', 'action' => 'why_jeera_benefits', 'admin' => false));
	Router::connect('/why-opusview-benefits', array('controller' => 'pages', 'action' => 'why_jeera_benefits', 'admin' => false));
	Router::connect('/why-jeera-focus', array('controller' => 'pages', 'action' => 'why_jeera_focus', 'admin' => false));
	Router::connect('/why-jeera-approach', array('controller' => 'pages', 'action' => 'why_jeera_approach', 'admin' => false));
	Router::connect('/why-jeera-solution', array('controller' => 'pages', 'action' => 'why_jeera_solution', 'admin' => false));
	Router::connect('/downloads', array('controller' => 'pages', 'action' => 'downloads', 'admin' => false));
	Router::connect('/newcontactus', array('controller' => 'pages', 'action' => 'newcontactus', 'admin' => false));
	Router::connect('/contactfordemo', array('controller' => 'pages', 'action' => 'contactfordemo', 'admin' => false));

	Router::connect('/terms', array('controller' => 'pages', 'action' => 'terms', 'admin' => false));
	Router::connect('/product', array('controller' => 'pages', 'action' => 'product', 'admin' => false));

	Router::connect('/contact', array('controller' => 'pages', 'action' => 'display', 'contact', 'admin' => false));
	Router::connect('/faq', array('controller' => 'pages', 'action' => 'display', 'faq', 'admin' => false));
	//Router::connect('/privacy', array('controller' => 'pages', 'action' => 'display', 'privacy', 'admin'=>false));
	Router::connect('/terms-of-use', array('controller' => 'pages', 'action' => 'display', 'terms-of-use', 'admin' => false));
	Router::connect('/pricing-plans', array('controller' => 'pages', 'action' => 'display', 'pricing-plans', 'admin' => false));

	Router::connect('/', array('controller' => 'users', 'action' => 'login', 'home', 'admin' => false));

	Router::connect('/sitepanel', array('controller' => 'users', 'action' => 'login', 'admin' => true));

	//Router::connect('/sitepanel/users', array('controller' => 'users', 'action' => 'index', 'admin'=>true));

	Router::connect('/contactus', array('controller' => 'pages', 'action' => 'contactus', 'admin' => false));
	Router::connect('/empoweringteamwork', array('controller' => 'pages', 'action' => 'empoweringteamwork', 'admin' => false));
	Router::connect('/features', array('controller' => 'pages', 'action' => 'features', 'admin' => false));
	Router::connect('/jeera-offer', array('controller' => 'pages', 'action' => 'jeera_offer', 'admin' => false));
	// Router::connect('/jeera-demo', array('controller' => 'pages', 'action' => 'jeera_demo', 'admin' => false));
	Router::connect('/opusview-demo', array('controller' => 'pages', 'action' => 'jeera_demo', 'admin' => false));
	Router::connect('/request-demo/*', array('controller' => 'pages', 'action' => 'request_demo', 'admin' => false));
	Router::connect('/how-buy/*', array('controller' => 'pages', 'action' => 'how_buy', 'admin' => false));
	Router::connect('/login', array('controller' => 'users', 'action' => 'login', 'admin' => false));
	Router::connect('/logout', array('controller' => 'users', 'action' => 'logout', 'admin' => false));

	Router::connect('/dashboard', array('controller' => 'dashboards', 'action' => 'index', 'admin' => true));

	//Router::connect('/sitepanel/:action/*', array('controller' => 'users','admin'=>true));
	Router::connect( '/analytics/social/*', array('controller' => 'subdomains', 'action' => 'index' , 'admin' => false));
	Router::connect( '/analytics/knowledge/*', array('controller' => 'subdomains', 'action' => 'knowledge_analytics' , 'admin' => false));

	Router::connect('/sitepanel/:controller', array('action' => 'index', 'prefix' => 'admin', 'admin' => true));
	Router::connect('/sitepanel/:controller/:action/*', array('prefix' => 'admin', 'admin' => true));

	Router::connect( '/resources/people/*', array('controller' => 'searches', 'action' => 'people' , 'admin' => false));
	Router::connect( '/resources/planning/*', array('controller' => 'searches', 'action' => 'planning' , 'admin' => false));

} else {

	/*  Uncomment below code for subdomain routing  */

	//WWW_ROOT = "/var/www/html/ideasjeera/app/webroot/";

	Router::connect('/' . $whatINeed . '/app/webroot/mindmup', array('controller' => 'pages', 'action' => 'display', 'about', 'admin' => false));
	//	Router::connect('/about', array('controller' => 'pages', 'action' => 'display', 'about', 'admin'=>false));

	Router::connect('/' . $whatINeed . '/blog', array('controller' => 'posts', 'action' => 'showblog', 'admin' => false));
	Router::connect('/' . $whatINeed . '/blog/*', array('controller' => 'posts', 'action' => 'blogdetails', 'admin' => false));

	//Router::connect('/blogdetail/*', array('controller' => 'posts', 'action' =>'blogdetails', 'admin'=>false));

	Router::connect('/' . $whatINeed . '/about', array('controller' => 'pages', 'action' => 'about', 'admin' => false));
	Router::connect('/' . $whatINeed . '/faq', array('controller' => 'pages', 'action' => 'faq', 'admin' => false));
	Router::connect('/' . $whatINeed . '/price', array('controller' => 'pages', 'action' => 'price', 'admin' => false));
	Router::connect('/' . $whatINeed . '/privacy', array('controller' => 'pages', 'action' => 'privacy', 'admin' => false));
	// Router::connect('/' . $whatINeed . '/why-jeera', array('controller' => 'pages', 'action' => 'why_jeera', 'admin' => false));
	Router::connect('/' . $whatINeed . '/opusview-jeera', array('controller' => 'pages', 'action' => 'why_jeera', 'admin' => false));

	// Router::connect('/' . $whatINeed . '/why-jeera-benefits', array('controller' => 'pages', 'action' => 'why_jeera_benefits', 'admin' => false));
	Router::connect('/' . $whatINeed . '/why-opusview-benefits', array('controller' => 'pages', 'action' => 'why_jeera_benefits', 'admin' => false));
	Router::connect('/' . $whatINeed . '/why-jeera-focus', array('controller' => 'pages', 'action' => 'why_jeera_focus', 'admin' => false));
	Router::connect('/' . $whatINeed . '/why-jeera-approach', array('controller' => 'pages', 'action' => 'why_jeera_approach', 'admin' => false));
	Router::connect('/' . $whatINeed . '/why-jeera-solution', array('controller' => 'pages', 'action' => 'why_jeera_solution', 'admin' => false));
	Router::connect('/' . $whatINeed . '/downloads', array('controller' => 'pages', 'action' => 'downloads', 'admin' => false));
	Router::connect('/' . $whatINeed . '/newcontactus', array('controller' => 'pages', 'action' => 'newcontactus', 'admin' => false));

	Router::connect('/' . $whatINeed . '/terms', array('controller' => 'pages', 'action' => 'terms', 'admin' => false));
	Router::connect('/' . $whatINeed . '/product', array('controller' => 'pages', 'action' => 'product', 'admin' => false));

	Router::connect('/' . $whatINeed . '/contact', array('controller' => 'pages', 'action' => 'display', 'contact', 'admin' => false));

	Router::connect('/' . $whatINeed . '/faq', array('controller' => 'pages', 'action' => 'display', 'faq', 'admin' => false));
	//Router::connect('/privacy', array('controller' => 'pages', 'action' => 'display', 'privacy', 'admin'=>false));
	Router::connect('/' . $whatINeed . '/terms-of-use', array('controller' => 'pages', 'action' => 'display', 'terms-of-use', 'admin' => false));
	Router::connect('/' . $whatINeed . '/pricing-plans', array('controller' => 'pages', 'action' => 'display', 'pricing-plans', 'admin' => false));

	Router::connect('/' . $whatINeed . '/analytics/social', array('controller' => 'subdomains', 'action' => 'index' , 'admin' => false));

	// Router::connect('/'.$whatINeed.'/*', array('controller' => 'pages', 'action' => 'display', 'admin'=>false));

	Router::connect('/' . $whatINeed . '/', array('controller' => 'pages', 'action' => 'display', 'home', 'admin' => false));

	Router::connect('/' . $whatINeed . '/sitepanel', array('controller' => 'users', 'action' => 'login', 'admin' => true));

	//Router::connect('/sitepanel/users', array('controller' => 'users', 'action' => 'index', 'admin'=>true));

	Router::connect('/' . $whatINeed . '/contactus', array('controller' => 'pages', 'action' => 'contactus', 'admin' => false));
	Router::connect('/' . $whatINeed . '/empoweringteamwork', array('controller' => 'pages', 'action' => 'empoweringteamwork', 'admin' => false));
	Router::connect('/' . $whatINeed . '/login', array('controller' => 'users', 'action' => 'login', 'admin' => false));
	Router::connect('/' . $whatINeed . '/logout', array('controller' => 'users', 'action' => 'logout', 'admin' => false));

	Router::connect('/' . $whatINeed . '/:controller', array('action' => 'index', 'admin' => false));
	Router::connect('/' . $whatINeed . '/:controller/:action/*', array('admin' => false));

	//Router::connect("/{$whatINeed}/:controller",  array('action' => 'index',  ));

	//Router::connect('/'.$whatINeed.'/dashboard', array('controller' => 'dashboards', 'action' => 'index', 'admin'=>true));

	//Router::connect('/sitepanel/:action/*', array('controller' => 'users','admin'=>true));

	Router::connect('/' . $whatINeed . '/sitepanel/:controller', array('action' => 'index', 'prefix' => 'admin', 'admin' => true));
	Router::connect('/' . $whatINeed . '/sitepanel/:controller/:action/*', array('prefix' => 'admin', 'admin' => true));



	// pr(Router::connect('/'));
	//pr(WWW_ROOT);

//	pr(WWW_ROOT);

}

/*  Uncomment above code for subdomain routing  */

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
Router::parseExtensions('xml','json');
CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
require CAKE . 'Config' . DS . 'routes.php';
