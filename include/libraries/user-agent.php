<?php
// namespace WPLRP\Inc\user_agent;
class wplrp_user_agent {

	/**
	 * Current user-agent
	 *
	 * @var string
	 */
	public $agent = NULL;

	/**
	 * Flag for if the user-agent belongs to a browser
	 *
	 * @var bool
	 */
	public $is_browser = FALSE;

	/**
	 * Flag for if the user-agent is a robot
	 *
	 * @var bool
	 */
	public $is_robot = FALSE;

	/**
	 * Flag for if the user-agent is a mobile browser
	 *
	 * @var bool
	 */
	public $is_mobile = FALSE;

	/**
	 * Languages accepted by the current user agent
	 *
	 * @var array
	 */
	public $languages = array();

	/**
	 * Character sets accepted by the current user agent
	 *
	 * @var array
	 */
	public $charsets = array();

	/**
	 * List of platforms to compare against current user agent
	 *
	 * @var array
	 */
	public $platforms = array();

	/**
	 * List of browsers to compare against current user agent
	 *
	 * @var array
	 */
	public $browsers = array();

	/**
	 * List of mobile browsers to compare against current user agent
	 *
	 * @var array
	 */
	public $mobiles = array();

	/**
	 * List of robots to compare against current user agent
	 *
	 * @var array
	 */
	public $robots = array();

	/**
	 * Current user-agent platform
	 *
	 * @var string
	 */
	public $platform = '';

	/**
	 * Current user-agent browser
	 *
	 * @var string
	 */
	public $browser = '';

	/**
	 * Current user-agent version
	 *
	 * @var string
	 */
	public $version = '';

	/**
	 * Current user-agent mobile name
	 *
	 * @var string
	 */
	public $mobile = '';

	/**
	 * Current user-agent robot name
	 *
	 * @var string
	 */
	public $robot = '';

	/**
	 * HTTP Referer
	 *
	 * @var	mixed
	 */
	public $referer;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * Sets the User Agent and runs the compilation routine
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->_load_agent_file();

		if (isset($_SERVER['HTTP_USER_AGENT']))
		{
			$this->agent = trim($_SERVER['HTTP_USER_AGENT']);
			$this->_compile_data();
		}

	}

	// --------------------------------------------------------------------

	/**
	 * Compile the User Agent Data
	 *
	 * @return	bool
	 */
	protected function _load_agent_file()
	{
		$this->platforms = array(
											'windows nt 10.0'	=> 'Windows',
											'windows nt 6.3'	=> 'Windows',
											'windows nt 6.2'	=> 'Windows',
											'windows nt 6.1'	=> 'Windows',
											'windows nt 6.0'	=> 'Windows',
											'windows nt 5.2'	=> 'Windows',
											'windows nt 5.1'	=> 'Windows',
											'windows nt 5.0'	=> 'Windows',
											'windows nt 4.0'	=> 'Windows',
											'winnt4.0'			=> 'Windows',
											'winnt 4.0'			=> 'Windows',
											'winnt'				=> 'Windows',
											'windows 98'		=> 'Windows',
											'win98'				=> 'Windows',
											'windows 95'		=> 'Windows',
											'win95'				=> 'Windows',
											'windows phone'			=> 'Windows Phone',
											'windows'			=> 'Unknown Windows OS',
											'android'			=> 'Android',
											'blackberry'		=> 'BlackBerry',
											'iphone'			=> 'iOS',
											'ipad'				=> 'iOS',
											'ipod'				=> 'iOS',
											'os x'				=> 'Mac OS X',
											'ppc mac'			=> 'Power PC Mac',
											'freebsd'			=> 'FreeBSD',
											'ppc'				=> 'Macintosh',
											'linux'				=> 'Linux',
											'debian'			=> 'Debian',
											'sunos'				=> 'Sun Solaris',
											'beos'				=> 'BeOS',
											'apachebench'		=> 'ApacheBench',
											'aix'				=> 'AIX',
											'irix'				=> 'Irix',
											'osf'				=> 'DEC OSF',
											'hp-ux'				=> 'HP-UX',
											'netbsd'			=> 'NetBSD',
											'bsdi'				=> 'BSDi',
											'openbsd'			=> 'OpenBSD',
											'gnu'				=> 'GNU/Linux',
											'unix'				=> 'Unknown Unix OS',
											'symbian' 			=> 'Symbian OS'
										);
		$this->browsers = array(
										'OPR'			=> 'Opera',
										'Firefox'	=> 'Firefox',
										'Opera'		=> 'Opera',
										'Opera.*?Version'	=> 'Opera',
										'Edg'			=> 'Microsoft Edge',
										
										'Flock'			=> 'Flock',
										'Edge'			=> 'Spartan',
										'Chrome'		=> 'Chrome',
										// Opera 10+ always reports Opera/9.80 and appends Version/<real version> to the user agent string
										
										
										'MSIE'			=> 'Internet Explorer',
										'Internet Explorer'	=> 'Internet Explorer',
										'Trident.* rv'	=> 'Internet Explorer',
										'Shiira'		=> 'Shiira',
										
										'Chimera'		=> 'Chimera',
										'Phoenix'		=> 'Phoenix',
										'Firebird'		=> 'Firebird',
										'Camino'		=> 'Camino',
										'Netscape'		=> 'Netscape',
										'OmniWeb'		=> 'OmniWeb',
										'Safari'		=> 'Safari',
										'Mozilla'		=> 'Mozilla',
										'Konqueror'		=> 'Konqueror',
										'icab'			=> 'iCab',
										'Lynx'			=> 'Lynx',
										'Links'			=> 'Links',
										'hotjava'		=> 'HotJava',
										'amaya'			=> 'Amaya',
										'IBrowse'		=> 'IBrowse',
										'Maxthon'		=> 'Maxthon',
										'Ubuntu'		=> 'Ubuntu Web Browser'
									);
		return true;
	}

	// --------------------------------------------------------------------

	/**
	 * Compile the User Agent Data
	 *
	 * @return	bool
	 */
	protected function _compile_data()
	{
		$this->_set_platform();

		foreach (array('_set_robot', '_set_browser', '_set_mobile') as $function)
		{
			if ($this->$function() === TRUE)
			{
				break;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Platform
	 *
	 * @return	bool
	 */
	protected function _set_platform()
	{
		if (is_array($this->platforms) && count($this->platforms) > 0)
		{
			foreach ($this->platforms as $key => $val)
			{
				if (preg_match('|'.preg_quote($key).'|i', $this->agent))
				{
					$this->platform = $val;
					return TRUE;
				}
			}
		}

		$this->platform = 'Unknown Platform';
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Browser
	 *
	 * @return	bool
	 */
	protected function _set_browser()
	{
		if (is_array($this->browsers) && count($this->browsers) > 0)
		{
			foreach ($this->browsers as $key => $val)
			{
				if (preg_match('|'.$key.'.*?([0-9\.]+)|i', $this->agent, $match))
				{
					$this->is_browser = TRUE;
					$this->version = $match[1];
					$this->browser = $val;
					$this->_set_mobile();
					return TRUE;
				}
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Robot
	 *
	 * @return	bool
	 */
	protected function _set_robot()
	{
		if (is_array($this->robots) && count($this->robots) > 0)
		{
			foreach ($this->robots as $key => $val)
			{
				if (preg_match('|'.preg_quote($key).'|i', $this->agent))
				{
					$this->is_robot = TRUE;
					$this->robot = $val;
					$this->_set_mobile();
					return TRUE;
				}
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Mobile Device
	 *
	 * @return	bool
	 */
	protected function _set_mobile()
	{
		if (is_array($this->mobiles) && count($this->mobiles) > 0)
		{
			foreach ($this->mobiles as $key => $val)
			{
				if (FALSE !== (stripos($this->agent, $key)))
				{
					$this->is_mobile = TRUE;
					$this->mobile = $val;
					return TRUE;
				}
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the accepted languages
	 *
	 * @return	void
	 */
	protected function _set_languages()
	{
		if ((count($this->languages) === 0) && ! empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			$this->languages = explode(',', preg_replace('/(;\s?q=[0-9\.]+)|\s/i', '', strtolower(trim($_SERVER['HTTP_ACCEPT_LANGUAGE']))));
		}

		if (count($this->languages) === 0)
		{
			$this->languages = array('Undefined');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set the accepted character sets
	 *
	 * @return	void
	 */
	protected function _set_charsets()
	{
		if ((count($this->charsets) === 0) && ! empty($_SERVER['HTTP_ACCEPT_CHARSET']))
		{
			$this->charsets = explode(',', preg_replace('/(;\s?q=.+)|\s/i', '', strtolower(trim($_SERVER['HTTP_ACCEPT_CHARSET']))));
		}

		if (count($this->charsets) === 0)
		{
			$this->charsets = array('Undefined');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Is Browser
	 *
	 * @param	string	$key
	 * @return	bool
	 */
	public function is_browser($key = NULL)
	{
		if ( ! $this->is_browser)
		{
			return FALSE;
		}

		// No need to be specific, it's a browser
		if ($key === NULL)
		{
			return TRUE;
		}

		// Check for a specific browser
		return (isset($this->browsers[$key]) && $this->browser === $this->browsers[$key]);
	}

	// --------------------------------------------------------------------

	/**
	 * Is Robot
	 *
	 * @param	string	$key
	 * @return	bool
	 */
	public function is_robot($key = NULL)
	{
		if ( ! $this->is_robot)
		{
			return FALSE;
		}

		// No need to be specific, it's a robot
		if ($key === NULL)
		{
			return TRUE;
		}

		// Check for a specific robot
		return (isset($this->robots[$key]) && $this->robot === $this->robots[$key]);
	}

	// --------------------------------------------------------------------

	/**
	 * Is Mobile
	 *
	 * @param	string	$key
	 * @return	bool
	 */
	public function is_mobile($key = NULL)
	{
		if ( ! $this->is_mobile)
		{
			return FALSE;
		}

		// No need to be specific, it's a mobile
		if ($key === NULL)
		{
			return TRUE;
		}

		// Check for a specific robot
		return (isset($this->mobiles[$key]) && $this->mobile === $this->mobiles[$key]);
	}

	// --------------------------------------------------------------------

	/**
	 * Is this a referral from another site?
	 *
	 * @return	bool
	 */
	public function is_referral()
	{
		if ( ! isset($this->referer))
		{
			if (empty($_SERVER['HTTP_REFERER']))
			{
				$this->referer = FALSE;
			}
			else
			{
				$referer_host = @parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
				$own_host = parse_url(config_item('base_url'), PHP_URL_HOST);

				$this->referer = ($referer_host && $referer_host !== $own_host);
			}
		}

		return $this->referer;
	}

	// --------------------------------------------------------------------

	/**
	 * Agent String
	 *
	 * @return	string
	 */
	public function agent_string()
	{
		return $this->agent;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Platform
	 *
	 * @return	string
	 */
	public function platform()
	{
		return $this->platform;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Browser Name
	 *
	 * @return	string
	 */
	public function browser()
	{
		return $this->browser;
	}

	// --------------------------------------------------------------------

	/**
	 * Get the Browser Version
	 *
	 * @return	string
	 */
	public function version()
	{
		return $this->version;
	}

	// --------------------------------------------------------------------

	/**
	 * Get The Robot Name
	 *
	 * @return	string
	 */
	public function robot()
	{
		return $this->robot;
	}
	// --------------------------------------------------------------------

	/**
	 * Get the Mobile Device
	 *
	 * @return	string
	 */
	public function mobile()
	{
		return $this->mobile;
	}

	// --------------------------------------------------------------------

	/**
	 * Get the referrer
	 *
	 * @return	bool
	 */
	public function referrer()
	{
		return empty($_SERVER['HTTP_REFERER']) ? '' : trim($_SERVER['HTTP_REFERER']);
	}

	// --------------------------------------------------------------------

	/**
	 * Get the accepted languages
	 *
	 * @return	array
	 */
	public function languages()
	{
		if (count($this->languages) === 0)
		{
			$this->_set_languages();
		}

		return $this->languages;
	}

	// --------------------------------------------------------------------

	/**
	 * Get the accepted Character Sets
	 *
	 * @return	array
	 */
	public function charsets()
	{
		if (count($this->charsets) === 0)
		{
			$this->_set_charsets();
		}

		return $this->charsets;
	}

	// --------------------------------------------------------------------

	/**
	 * Test for a particular language
	 *
	 * @param	string	$lang
	 * @return	bool
	 */
	public function accept_lang($lang = 'en')
	{
		return in_array(strtolower($lang), $this->languages(), TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Test for a particular character set
	 *
	 * @param	string	$charset
	 * @return	bool
	 */
	public function accept_charset($charset = 'utf-8')
	{
		return in_array(strtolower($charset), $this->charsets(), TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Parse a custom user-agent string
	 *
	 * @param	string	$string
	 * @return	void
	 */
	public function parse($string)
	{
		// Reset values
		$this->is_browser = FALSE;
		$this->is_robot = FALSE;
		$this->is_mobile = FALSE;
		$this->browser = '';
		$this->version = '';
		$this->mobile = '';
		$this->robot = '';

		// Set the new user-agent string and parse it, unless empty
		$this->agent = $string;

		if ( ! empty($string))
		{
			$this->_compile_data();
		}
	}

}
