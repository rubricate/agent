<?php # if ( ! defined('basepath')) exit('no direct script access allowed');

/**
 * codeigniter
 *
 * an open source application development framework for php 5.1.6 or newer
 *
 * @package		codeigniter
 * @author		expressionengine dev team
 * @copyright	copyright (c) 2008 - 2011, ellislab, inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * user agent class
 *
 * identifies the platform, browser, robot, or mobile devise of the browsing agent
 *
 * @package		codeigniter
 * @subpackage	libraries
 * @category	user agent
 * @author		expressionengine dev team
 * @link		http://codeigniter.com/user_guide/libraries/user_agent.html
 */

class CI_User_Agent {

	var $agent		= null;

	var $is_browser	= false;
	var $is_robot	= false;
	var $is_mobile	= false;

	var $languages	= array();
	var $charsets	= array();

	var $platforms	= array();
	var $browsers	= array();
	var $mobiles	= array();
	var $robots		= array();

	var $platform	= '';
	var $browser	= '';
	var $version	= '';
	var $mobile		= '';
	var $robot		= '';

	/**
	 * constructor
	 *
	 * sets the user agent and runs the compilation routine
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct()
	{
		if (isset($_server['http_user_agent']))
		{
			$this->agent = trim($_server['http_user_agent']);
		}

		if ( ! is_null($this->agent))
		{
			if ($this->_load_agent_file())
			{
				$this->_compile_data();
			}
		}

		log_message('debug', "user agent class initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * compile the user agent data
	 *
	 * @access	private
	 * @return	bool
	 */
	private function _load_agent_file()
	{
		if (defined('environment') and is_file(apppath.'config/'.environment.'/user_agents.php'))
		{
			include(apppath.'config/'.environment.'/user_agents.php');
		}
		elseif (is_file(apppath.'config/user_agents.php'))
		{
			include(apppath.'config/user_agents.php');
		}
		else
		{
			return false;
		}

		$return = false;

		if (isset($platforms))
		{
			$this->platforms = $platforms;
			unset($platforms);
			$return = true;
		}

		if (isset($browsers))
		{
			$this->browsers = $browsers;
			unset($browsers);
			$return = true;
		}

		if (isset($mobiles))
		{
			$this->mobiles = $mobiles;
			unset($mobiles);
			$return = true;
		}

		if (isset($robots))
		{
			$this->robots = $robots;
			unset($robots);
			$return = true;
		}

		return $return;
	}

	// --------------------------------------------------------------------

	/**
	 * compile the user agent data
	 *
	 * @access	private
	 * @return	bool
	 */
	private function _compile_data()
	{
		$this->_set_platform();

		foreach (array('_set_robot', '_set_browser', '_set_mobile') as $function)
		{
			if ($this->$function() === true)
			{
				break;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * set the platform
	 *
	 * @access	private
	 * @return	mixed
	 */
	private function _set_platform()
	{
		if (is_array($this->platforms) and count($this->platforms) > 0)
		{
			foreach ($this->platforms as $key => $val)
			{
				if (preg_match("|".preg_quote($key)."|i", $this->agent))
				{
					$this->platform = $val;
					return true;
				}
			}
		}
		$this->platform = 'unknown platform';
	}

	// --------------------------------------------------------------------

	/**
	 * set the browser
	 *
	 * @access	private
	 * @return	bool
	 */
	private function _set_browser()
	{
		if (is_array($this->browsers) and count($this->browsers) > 0)
		{
			foreach ($this->browsers as $key => $val)
			{
				if (preg_match("|".preg_quote($key).".*?([0-9\.]+)|i", $this->agent, $match))
				{
					$this->is_browser = true;
					$this->version = $match[1];
					$this->browser = $val;
					$this->_set_mobile();
					return true;
				}
			}
		}
		return false;
	}

	// --------------------------------------------------------------------

	/**
	 * set the robot
	 *
	 * @access	private
	 * @return	bool
	 */
	private function _set_robot()
	{
		if (is_array($this->robots) and count($this->robots) > 0)
		{
			foreach ($this->robots as $key => $val)
			{
				if (preg_match("|".preg_quote($key)."|i", $this->agent))
				{
					$this->is_robot = true;
					$this->robot = $val;
					return true;
				}
			}
		}
		return false;
	}

	// --------------------------------------------------------------------

	/**
	 * set the mobile device
	 *
	 * @access	private
	 * @return	bool
	 */
	private function _set_mobile()
	{
		if (is_array($this->mobiles) and count($this->mobiles) > 0)
		{
			foreach ($this->mobiles as $key => $val)
			{
				if (false !== (strpos(strtolower($this->agent), $key)))
				{
					$this->is_mobile = true;
					$this->mobile = $val;
					return true;
				}
			}
		}
		return false;
	}

	// --------------------------------------------------------------------

	/**
	 * set the accepted languages
	 *
	 * @access	private
	 * @return	void
	 */
	private function _set_languages()
	{
		if ((count($this->languages) == 0) and isset($_server['http_accept_language']) and $_server['http_accept_language'] != '')
		{
			$languages = preg_replace('/(;q=[0-9\.]+)/i', '', strtolower(trim($_server['http_accept_language'])));

			$this->languages = explode(',', $languages);
		}

		if (count($this->languages) == 0)
		{
			$this->languages = array('undefined');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * set the accepted character sets
	 *
	 * @access	private
	 * @return	void
	 */
	private function _set_charsets()
	{
		if ((count($this->charsets) == 0) and isset($_server['http_accept_charset']) and $_server['http_accept_charset'] != '')
		{
			$charsets = preg_replace('/(;q=.+)/i', '', strtolower(trim($_server['http_accept_charset'])));

			$this->charsets = explode(',', $charsets);
		}

		if (count($this->charsets) == 0)
		{
			$this->charsets = array('undefined');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * is browser
	 *
	 * @access	public
	 * @return	bool
	 */
	public function is_browser($key = null)
	{
		if ( ! $this->is_browser)
		{
			return false;
		}

		// no need to be specific, it's a browser
		if ($key === null)
		{
			return true;
		}

		// check for a specific browser
		return array_key_exists($key, $this->browsers) and $this->browser === $this->browsers[$key];
	}

	// --------------------------------------------------------------------

	/**
	 * is robot
	 *
	 * @access	public
	 * @return	bool
	 */
	public function is_robot($key = null)
	{
		if ( ! $this->is_robot)
		{
			return false;
		}

		// no need to be specific, it's a robot
		if ($key === null)
		{
			return true;
		}

		// check for a specific robot
		return array_key_exists($key, $this->robots) and $this->robot === $this->robots[$key];
	}

	// --------------------------------------------------------------------

	/**
	 * is mobile
	 *
	 * @access	public
	 * @return	bool
	 */
	public function is_mobile($key = null)
	{
		if ( ! $this->is_mobile)
		{
			return false;
		}

		// no need to be specific, it's a mobile
		if ($key === null)
		{
			return true;
		}

		// check for a specific robot
		return array_key_exists($key, $this->mobiles) and $this->mobile === $this->mobiles[$key];
	}

	// --------------------------------------------------------------------

	/**
	 * is this a referral from another site?
	 *
	 * @access	public
	 * @return	bool
	 */
	public function is_referral()
	{
		if ( ! isset($_server['http_referer']) or $_server['http_referer'] == '')
		{
			return false;
		}
		return true;
	}

	// --------------------------------------------------------------------

	/**
	 * agent string
	 *
	 * @access	public
	 * @return	string
	 */
	public function agent_string()
	{
		return $this->agent;
	}

	// --------------------------------------------------------------------

	/**
	 * get platform
	 *
	 * @access	public
	 * @return	string
	 */
	public function platform()
	{
		return $this->platform;
	}

	// --------------------------------------------------------------------

	/**
	 * get browser name
	 *
	 * @access	public
	 * @return	string
	 */
	public function browser()
	{
		return $this->browser;
	}

	// --------------------------------------------------------------------

	/**
	 * get the browser version
	 *
	 * @access	public
	 * @return	string
	 */
	public function version()
	{
		return $this->version;
	}

	// --------------------------------------------------------------------

	/**
	 * get the robot name
	 *
	 * @access	public
	 * @return	string
	 */
	public function robot()
	{
		return $this->robot;
	}
	// --------------------------------------------------------------------

	/**
	 * get the mobile device
	 *
	 * @access	public
	 * @return	string
	 */
	public function mobile()
	{
		return $this->mobile;
	}

	// --------------------------------------------------------------------

	/**
	 * get the referrer
	 *
	 * @access	public
	 * @return	bool
	 */
	public function referrer()
	{
		return ( ! isset($_server['http_referer']) or $_server['http_referer'] == '') ? '' : trim($_server['http_referer']);
	}

	// --------------------------------------------------------------------

	/**
	 * get the accepted languages
	 *
	 * @access	public
	 * @return	array
	 */
	public function languages()
	{
		if (count($this->languages) == 0)
		{
			$this->_set_languages();
		}

		return $this->languages;
	}

	// --------------------------------------------------------------------

	/**
	 * get the accepted character sets
	 *
	 * @access	public
	 * @return	array
	 */
	public function charsets()
	{
		if (count($this->charsets) == 0)
		{
			$this->_set_charsets();
		}

		return $this->charsets;
	}

	// --------------------------------------------------------------------

	/**
	 * test for a particular language
	 *
	 * @access	public
	 * @return	bool
	 */
	public function accept_lang($lang = 'en')
	{
		return (in_array(strtolower($lang), $this->languages(), true));
	}

	// --------------------------------------------------------------------

	/**
	 * test for a particular character set
	 *
	 * @access	public
	 * @return	bool
	 */
	public function accept_charset($charset = 'utf-8')
	{
		return (in_array(strtolower($charset), $this->charsets(), true));
	}

}


/* end of file user_agent.php */
/* location: ./system/libraries/user_agent.php */

