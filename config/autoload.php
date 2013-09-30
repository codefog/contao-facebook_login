<?php

/**
 * facebook_login extension for Contao Open Source CMS
 *
 * Copyright (C) 2013 Codefog Ltd
 *
 * @package facebook_login
 * @author  Codefog Ltd <http://codefog.pl>
 * @author  Kamil Kuzminski <kamil.kuzminski@codefog.pl>
 * @license LGPL
 */


/**
 * Register the namespace
 */
ClassLoader::addNamespace('FacebookLogin');


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'FacebookLogin\FrontendUser'         => 'system/modules/facebook_login/classes/FrontendUser.php',

	// Modules
	'FacebookLogin\ModuleFacebookLogin'  => 'system/modules/facebook_login/modules/ModuleFacebookLogin.php',
	'FacebookLogin\ModulePersonalData'   => 'system/modules/facebook_login/modules/ModulePersonalData.php',

	// Library
	'FacebookLogin\FacebookApiException' => 'system/modules/facebook_login/library/Facebook/base_facebook.php',
	'FacebookLogin\BaseFacebook'         => 'system/modules/facebook_login/library/Facebook/base_facebook.php',
	'FacebookLogin\FacebookSDK'          => 'system/modules/facebook_login/library/Facebook/facebook.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_facebook_login' => 'system/modules/facebook_login/templates/modules'
));
