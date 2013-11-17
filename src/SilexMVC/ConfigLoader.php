<?php

namespace SilexMVC;

use SilexMVC\Core\OmniClass;
use Symfony\Component\Yaml\Yaml;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\DoctrineServiceProvider;

/**
 * Class CmsConfig
 *
 * @package SilexMVC
 */
class ConfigLoader extends OmniClass
{

	protected $_baseDir;

	/**
	 * @return $this
	 */
	protected function _loadTranslator()
	{
		/* TRANSLATOR */
		$this->_app->register(new TranslationServiceProvider(), [
			'translator.messages' => []
		]);

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function _loadDB()
	{
		$this->_app->register(new DoctrineServiceProvider(), [
			'db.options' => [
				'driver' => 'pdo_mysql',
				'host' => $this->_app['config']['database']['host'],
				'dbname' => $this->_app['config']['database']['name'],
				'user' => $this->_app['config']['database']['user'],
				'password' => $this->_app['config']['database']['password'],
			],
		]);

		return $this;
	}

	/**
	 *
	 */
	protected function _loadDebug()
	{
		$debugMode = false;
		if (isset($this->_app['config']['debug']['mode']))
		{
			$debugMode = $this->_app['config']['debug']['mode'];
		}
		$this->_app['debug'] = $debugMode;
	}

	/**
	 * @return $this
	 */
	protected function _loadViews()
	{
		$viewsFolder = '/src/views';
		if (isset($this->_app['config']['cms']['views']['rootFolder']))
		{
			$viewsFolder = $this->_app['config']['cms']['views']['rootFolder'];
		}

		$this->_app->register(new TwigServiceProvider(), [
			'twig.path' => $this->_baseDir . $viewsFolder
		]);

		return $this;
	}

	/**
	 * @param $configA
	 * @param $configB
	 *
	 * @return mixed
	 */
	private function _mergeConfigs(&$configA, &$configB)
	{
		$merged = $configA;

		foreach ($configB as $key => &$value)
		{
			if (is_array($value) && isset ($merged[$key]) && is_array ($merged[$key]))
			{
				$merged[$key] = $this->_mergeConfigs($merged[$key], $value);
			}
			else
			{
				$merged[$key] = $value;
			}
		}

		return $merged;
	}

	/**
	 * @param string $resource
	 *
	 * @return array
	 */
	public function parseFile($resource)
	{
		$configValues = Yaml::parse($resource);

		defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'dev'));
		$common = $configValues['common'];
		$env = $configValues[APPLICATION_ENV]? $configValues[APPLICATION_ENV] : [];

		$config = $this->_mergeConfigs($common,$env);
		$this->_app['config'] = $config;

		return $config;
	}

	public function loadModules()
	{
		$this->_baseDir = $this->getBaseDir();

		$this->_loadViews();
		$this->_loadTranslator();
		$this->_loadDB();
		$this->_loadDebug();

		return $this;
	}

	public function getBaseDir()
	{
		return dirname(__DIR__);
	}

}