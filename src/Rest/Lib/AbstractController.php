<?php

namespace Rest\Lib;

use Slim\Container;

abstract class AbstractController
{
    /**
     * Dependency injection container
     *
     * @var Container
     */
    private $container;

    /**
     * NewsController constructor
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return Container
     */
    public final function getCI()
    {
        return $this->container;
    }
}