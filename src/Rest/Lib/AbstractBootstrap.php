<?php

namespace Rest\Lib;

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class AbstractBootstrap
{

    const BAD_HTTP_CODES = [400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411,
        412, 413, 414, 415, 416, 417, 500, 501, 502, 503, 504, 505];

    /**
     * Slim application
     *
     * @var App
     */
    private $app;

    /**
     * Bootstrap constructor
     *
     * @param App $app
     */
    public function __construct(App $app = null)
    {
        $this->app = is_null($app) ? new App($this->loadConfigs()) : $app;
        $this->app->add('Rest\Lib\AbstractBootstrap::processRequest');
        $this->setUpRoutes();
    }

    /**
     * @return array
     */
    public function loadConfigs()
    {
        return [];
    }

    public abstract function setUpRoutes();

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     */
    public static function processRequest(Request $request, Response $response, callable $next)
    {
        try {
            /** @var Response $response */
            $response = $next($request, $response);
        } catch (\Exception $e) {
            $json = [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'exception' => get_class($e)
            ];

            return $response
                ->withStatus(
                    in_array($e->getCode(), self::BAD_HTTP_CODES) ? $e->getCode() : 500)
                ->withJson($json);
        }

        return $response;
    }

    /**
     * Load config helper
     * @param string $path
     * @return mixed
     */
    public final function loadConfig($path)
    {
        $file = realpath($path);
        if ($file === false || !is_readable($file)) {
            throw new \InvalidArgumentException("Wrong config '{$path}'");
        }

        return require_once $file;
    }

    /**
     * @return App
     */
    public final function getApp()
    {
        return $this->app;
    }

    /**
     * Start application
     */
    public function run()
    {
        $this->app->run();
    }
}