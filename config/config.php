<?php

/**
 * facebook_login extension for Contao Open Source CMS
 *
 * Copyright (C) 2013 Codefog
 *
 * @package facebook_login
 * @author  Codefog <http://codefog.pl>
 * @author  Kamil Kuzminski <kamil.kuzminski@codefog.pl>
 * @license LGPL
 */


/**
 * Extension version
 */
@define('FACEBOOK_LOGIN_VERSION', '1.0');
@define('FACEBOOK_LOGIN_BUILD', '3');


/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['user']['facebook_login'] = 'ModuleFacebookLogin';
