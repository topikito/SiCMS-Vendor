<?php

namespace SiCMS;

use SiCMS\Core\OmniClass;

/**
 * Class CmsRouter
 *
 * @package SiCMS
 */
abstract class Router extends OmniClass
{

	abstract public function load();

}