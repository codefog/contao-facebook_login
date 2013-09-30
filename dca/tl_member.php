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
 * Add palettes to tl_member
 */
$GLOBALS['TL_DCA']['tl_member']['palettes']['default'] = str_replace(',username,', ',username,fblogin,', $GLOBALS['TL_DCA']['tl_member']['palettes']['default']);


/**
 * Add the "w50" class to the "username" field
 */
$GLOBALS['TL_DCA']['tl_member']['fields']['username']['eval']['tl_class'] = 'w50';


/**
 * Make the "password" field not mandatory
 */
if (TL_MODE == 'BE')
{
	$GLOBALS['TL_CONFIG']['minPasswordLength'] = 0;
	$GLOBALS['TL_DCA']['tl_member']['fields']['password']['eval']['minlength'] = 0;
	$GLOBALS['TL_DCA']['tl_member']['fields']['password']['eval']['mandatory'] = false;
}


/**
 * Add fields to tl_member
 */
$GLOBALS['TL_DCA']['tl_member']['fields']['fblogin'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_member']['fblogin'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('unique'=>true, 'tl_class'=>'w50'),
	'sql'                     => "bigint(20) unsigned NOT NULL default '0'"
);
