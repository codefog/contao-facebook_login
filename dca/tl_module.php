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
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['facebook_login'] = '{title_legend},name,headline,type;{config_legend},fblogin_appId,fblogin_appKey;{account_legend},reg_groups,reg_assignDir;{redirect_legend},jumpTo;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';


/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['fblogin_appId'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fblogin_appId'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'maxlength'=>32, 'tl_class'=>'w50'),
	'sql'                     => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['fblogin_appKey'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fblogin_appKey'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'maxlength'=>32, 'tl_class'=>'w50'),
	'sql'                     => "varchar(32) NOT NULL default ''"
);
