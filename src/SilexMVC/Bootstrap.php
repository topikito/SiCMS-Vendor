<?php

namespace SilexMVC;

use Silex\Application;
use Symfony\Component\Config\FileLocator;

/**
 * Class Bootstrap
 */
abstract class Bootstrap
{

	/**
	 * @var \Silex\Application
	 */
	protected $_app;

	private function __construct()
	{
		// Let the magic BEGIN!!
		$this->_app = new Application();
	}

	/**
	 * @param $configFile
	 *
	 * @return $this
	 */
	public function loadConfig($configFile)
	{
		$paths = [
			dirname(dirname(__DIR__)) . '/config',
			dirname(dirname(__DIR__)),
			__DIR__,
			dirname(__DIR__)
		];

		$fileLocator = new FileLocator($paths);
		$configLoader = new ConfigLoader($this->_app);
		$configLoader->load($fileLocator->locate($configFile));

		return $this;
	}

	/**
	 * @return \Silex\Application
	 */
	public function getApplication()
	{
		return $this->_app;
	}

	/**
	 * @param array $params
	 *
	 * @return mixed
	 */
	static public function load($params = [])
	{
		$configFile = null;

		extract($params);

		$me = new static();
		$me->loadConfig($configFile)->loadDispatcher();
		return $me->getApplication();
	}

	abstract function loadDispatcher();

}