<?php

namespace SilexMVC;

use SilexMVC\Core\OmniClass;

/**
 * Class CmsRouter
 *
 * @package SilexMVC
 */
abstract class Router extends OmniClass
{

	abstract public function load();

}