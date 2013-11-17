<?php

namespace SiCMS;

use Symfony\Component\Yaml\Yaml;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\DoctrineServiceProvider;

/**
 * Class CmsConfig
 *
 * @package SiCMS
 */
class ConfigLoader
{

	/**
	 * @var \Silex\Application
	 */
	protected $_app;

	/**
	 * @param \Silex\Application $_app
	 */
	function __construct(Application $_app)
	{
		$this->_app = $_app;
	}

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
			'twig.path' => __DIR__ . '/../..' . $viewsFolder
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
	public function load($resource)
	{
		$configValues = Yaml::parse($resource);

		defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'dev'));
		$common = $configValues['common'];
		$env = $configValues[APPLICATION_ENV]? $configValues[APPLICATION_ENV] : [];

		$config = $this->_mergeConfigs($common,$env);
		$this->_app['config'] = $config;

		$this->_loadViews();
		$this->_loadTranslator();
		$this->_loadDB();
		$this->_loadDebug();

		return $config;
	}

}