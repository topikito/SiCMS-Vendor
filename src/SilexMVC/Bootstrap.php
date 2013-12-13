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
	 * @param $configFile   string
	 * @param $paths        string|array
	 *
	 * @return $this
	 */
	public function loadConfig($configFile, $paths)
	{
		$paths = (array) $paths;

		$fileLocator = new FileLocator($paths);

		$configLoader = $this->getConfigLoader();
		$configLoader->parseFile($fileLocator->locate($configFile));
		$configLoader->loadModules();

		return $this;
	}

	/**
	 * @return \Silex\Application
	 */
	public function getApplication()
	{
		return $this->_app;
	}

	public function before() {}
	public function customLoad() {}

	/**
	 * @param array $params
	 *
	 * @return mixed
	 */
	static public function load($params = [])
	{
		$configFile = null;
		$configPaths = [];

		extract($params);

		$me = new static();
		$me->loadConfig($configFile, $configPaths);
		$me->loadDispatcher();
		$me->customLoad();
		$me->before();

		return $me->getApplication();
	}

	/**
	 * @return ConfigLoader
	 */
	public function getConfigLoader()
	{
		return new \SilexMVC\ConfigLoader($this->_app);
	}

	abstract function loadDispatcher();

}