<?php

namespace SiCMS;

use Silex\Application;

/**
 * Class CmsController
 *
 * @package SiCMS
 */
class Controller
{

	/**
	 * @var \Silex\Application
	 */
	protected   $_app;

	/**
	 * @var string
	 */
	protected   $_typeOfView = 'twig';

	/**
	 * @param \Silex\Application $app
	 */
	public function __construct(Application $app)
	{
		$this->_app     = $app;
	}

    /**
     * Checks if the params are not empty for vital usages
     *
     * @return bool
     */
    protected function _checkVitalParams()
    {
        $params = func_get_args();
        foreach ($params as $param)
        {
            if (empty($param))
            {
                return false;
            }
        }
        return true;
    }

	/**
	 * @param null $templateName
	 * @param null $args
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	protected function _render($templateName = null, $args = null)
    {
	    $response = null;
        switch ($this->_typeOfView)
        {
            case 'json':
                $response = $this->_app->json($args);
                break;

            case 'twig':
            default:
                $response = $this->_app['twig']->render($templateName, $args);
                break;
        }

	    return $response;
    }

	/**
	 * @param     $message
	 * @param int $errorCode
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	protected function _renderError($message, $errorCode = 500)
    {
        switch ($this->_typeOfView)
        {
            case 'json':
                return $this->_app->json($message, $errorCode);
                break;

            case 'twig':
            default:
                //Is this really necessary?
                break;
        }
    }

	/**
	 * @param $type
	 *
	 * @return bool
	 */
	public function setTypeOfView($type)
    {
        $this->_typeOfView = $type;
        return true;
    }

}