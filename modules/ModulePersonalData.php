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

namespace FacebookLogin;


/**
 * Class ModulePersonalData
 *
 * Override the default front end module "personal data".
 */
class ModulePersonalData extends \Contao\ModulePersonalData
{

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			return parent::generate();
		}

		// Check if the user is a Facebook user
		if (FE_USER_LOGGED_IN)
		{
			$this->import('FrontendUser', 'User');
			$this->editable = deserialize($this->editable);

			// Remove the password field
			if ($this->User->fblogin && ($intKey = array_search('password', $this->editable)) !== false)
			{
				$fields = $this->editable;
				unset($fields[$intKey]);
				$this->editable = $fields;
			}
		}

		return parent::generate();
	}
}
