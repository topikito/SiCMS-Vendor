<?php

namespace SiCMS\Core;

use Silex\Application;

/**
 *
 * One class, to control them all... haha :P Just for initializing the $_app
 *
 * Class OmniClass
 *
 * @package SiCMS\Core
 */
class OmniClass
{

	/**
	 * @var \Silex\Application
	 */
	protected   $_app;

	/**
	 * @param \Silex\Application $app
	 */
	public function __construct(Application $app)
	{
		$this->_app     = $app;
	}

}