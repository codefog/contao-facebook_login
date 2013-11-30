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

namespace FacebookLogin;


/**
 * Class FrontendUser
 *
 * Override the default FrontendUser class
 */
class FrontendUser extends \Contao\FrontendUser
{

	/**
	 * Try to login with Facebook
	 * @param array
	 * @return boolean
	 */
	public function login($arrProfile=null)
	{
		if (parent::login() === true)
		{
			return true;
		}

		// Return if the user is not found
		if (!$arrProfile || $this->findBy('fblogin', $arrProfile['id']) == false)
		{
			\Message::addError($GLOBALS['TL_LANG']['ERR']['invalidLogin']);
			return false;
		}

		// Return if the user ID does not match
		if (!$this->fblogin || $this->fblogin != $arrProfile['id'])
		{
			\Message::addError($GLOBALS['TL_LANG']['ERR']['invalidLogin']);
			return false;
		}

		$this->setUserFromDb();

		// Update the record
		$this->lastLogin = $this->currentLogin;
		$this->currentLogin = time();
		$this->loginCount = $GLOBALS['TL_CONFIG']['loginCount'];
		$this->save();

		// Generate the session
		$this->generateSession();
		$this->log('User "' . $this->username . '" has logged in', get_class($this) . ' login()', TL_ACCESS);

		// HOOK: post login callback
		if (isset($GLOBALS['TL_HOOKS']['postLogin']) && is_array($GLOBALS['TL_HOOKS']['postLogin']))
		{
			foreach ($GLOBALS['TL_HOOKS']['postLogin'] as $callback)
			{
				$this->import($callback[0], 'objLogin', true);
				$this->objLogin->$callback[1]($this);
			}
		}

		return true;
	}
}
