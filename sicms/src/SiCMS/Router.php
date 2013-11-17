<?php

namespace SiCMS;

/**
 * Class CmsRouter
 *
 * @package SiCMS
 */
abstract class Router
{

	/**
	 * @var
	 */
	protected $_app;

	/**
	 * @param $_app
	 */
	function __construct($_app)
	{
		$this->_app = $_app;
	}

	abstract public function load();

}