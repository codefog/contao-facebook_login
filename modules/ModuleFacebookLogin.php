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
 * Class ModuleFacebookLogin
 *
 * Front end module "facebook login".
 */
class ModuleFacebookLogin extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_facebook_login';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['facebook_login'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// Return if a front end user is logged in
		if (FE_USER_LOGGED_IN)
		{
			return '';
		}

		// Execute the login
		if (\Input::get('fblogin'))
		{
			if ($this->loginWithFacebook())
			{
				$this->jumpToOrReload($this->jumpTo);
			}
			else
			{
				$_SESSION['FACEBOOK_LOGIN'] = \Message::generate();
				$this->redirect(preg_replace('/(\?|&)fblogin=1/', '', \Environment::get('request')));
			}
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$this->Template->id = 'facebook_login_' . $this->id;
		$this->Template->appId = $this->fblogin_appId;
		$this->Template->href = \Environment::get('base') . \Environment::get('request') . ((stripos(\Environment::get('request'), '?') !== false) ? '&amp;' : '?') . 'fblogin=1';
		$this->Template->linkTitle = specialchars($GLOBALS['TL_LANG']['MSC']['fblogin_login']);
		$this->Template->link = $GLOBALS['TL_LANG']['MSC']['fblogin_login'];
		$this->Template->messages = '';

		// Display the messages
		if ($_SESSION['FACEBOOK_LOGIN'])
		{
			$this->Template->messages = $_SESSION['FACEBOOK_LOGIN'];
			unset($_SESSION['FACEBOOK_LOGIN']);
		}
	}


	/**
	 * Try to login with Facebook and return true on success
	 * @return boolean
	 */
	protected function loginWithFacebook()
	{
		$objFacebook = new FacebookSDK(array
		(
			'appId' => $this->fblogin_appId,
			'secret' => $this->fblogin_appKey
		));

		$arrProfile = false;

		if ($objFacebook->getUser() > 0)
		{
			try
			{
				$arrProfile = $objFacebook->api('/me');
			}
			catch (FacebookApiException $e)
			{
				$arrProfile = false;
				$this->log('Could not fetch the Facebook data: ' . $e->getMessage(), 'ModuleFacebookLogin loginWithFacebook()', TL_ERROR);
			}
		}

		// Log the error message
		if (!$arrProfile)
		{
			$this->log('Could not fetch the Facebook user.', 'ModuleFacebookLogin loginWithFacebook()', TL_ERROR);
			return false;
		}

		$time = time();
		$objUser = $this->Database->prepare("SELECT id FROM tl_member WHERE fblogin=? AND login=1 AND (start='' OR start<$time) AND (stop='' OR stop>$time) AND disable=''")
								  ->limit(1)
								  ->execute($arrProfile['id']);

		// Create a new user if none found
		if (!$objUser->numRows)
		{
			$objUser = $this->createNewUser($arrProfile);

			if ($objUser === false)
			{
				return false;
			}
		}

		$this->import('FrontendUser', 'User');
		return $this->User->login($arrProfile);
	}


	/**
	 * Create a new user based on the given data
	 * @param array
	 * @return boolean
	 */
	protected function createNewUser($arrProfile)
	{
		\System::loadLanguageFile('tl_member');
		$this->loadDataContainer('tl_member');

		// Call onload_callback (e.g. to check permissions)
		if (is_array($GLOBALS['TL_DCA']['tl_member']['config']['onload_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_member']['config']['onload_callback'] as $callback)
			{
				if (is_array($callback))
				{
					$this->import($callback[0]);
					$this->$callback[0]->$callback[1]();
				}
			}
		}

		$time = time();
		$arrData = array
		(
			'tstamp' => $time,
			'dateAdded' => $time,
			'firstname' => $arrProfile['first_name'],
			'lastname' => $arrProfile['last_name'],
			'gender' => $arrProfile['gender'],
			'email' => $arrProfile['email'],
			'login' => 1,
			'username' => 'fb_' . $arrProfile['id'],
			'fblogin' => $arrProfile['id'],
			'groups' => $this->reg_groups
		);

		$blnHasError = false;

		// Check the data
		foreach ($arrData as $k => $v)
		{
			if (!isset($GLOBALS['TL_DCA']['tl_member']['fields'][$k]))
			{
				unset($arrData[$k]);
				continue;
			}

			$arrField = $GLOBALS['TL_DCA']['tl_member']['fields'][$k];

			// Make sure that unique fields are unique
			if ($arrField['eval']['unique'] && $v != '' && !$this->Database->isUniqueValue('tl_member', $k, $v))
			{
				$blnHasError = true;
				\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['unique'], $arrField['label'][0] ?: $k));
				continue;
			}

			// Save callback
			if (is_array($arrField['save_callback']))
			{
				foreach ($arrField['save_callback'] as $callback)
				{
					$this->import($callback[0]);

					try
					{
						$v = $this->$callback[0]->$callback[1]($v, null);
					}
					catch (\Exception $e)
					{
						$blnHasError = true;
						\Message::addError($e->getMessage());
					}
				}

				$arrData[$k] = $v;
			}
		}

		// HOOK: parse data before it is saved
		if (isset($GLOBALS['TL_HOOKS']['validateFacebookLogin']) && is_array($GLOBALS['TL_HOOKS']['validateFacebookLogin']))
		{
			foreach ($GLOBALS['TL_HOOKS']['validateFacebookLogin'] as $callback)
			{
				$this->import($callback[0]);

				try
				{
					$arrData = $this->$callback[0]->$callback[1]($arrData, $arrProfile);
				}
				catch (\Exception $e)
				{
					$blnHasError = true;
					\Message::addError($e->getMessage());
				}
			}
		}

		// Return false if there is an error
		if ($blnHasError)
		{
			return false;
		}

		$objNewUser = new \MemberModel();
		$objNewUser->setRow($arrData);
		$objNewUser->save();

		$insertId = $objNewUser->id;

		// HOOK: send insert ID and user data
		if (isset($GLOBALS['TL_HOOKS']['createNewUser']) && is_array($GLOBALS['TL_HOOKS']['createNewUser']))
		{
			foreach ($GLOBALS['TL_HOOKS']['createNewUser'] as $callback)
			{
				$this->import($callback[0]);
				$this->$callback[0]->$callback[1]($insertId, $arrData, $this, $arrProfile);
			}
		}

		return true;
	}
}
