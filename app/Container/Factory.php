<?php namespace App\Container;

use Interop\Container\ContainerInterface;
use Limoncello\ContainerLight\Container;

/**
 * @package App
 */
trait Factory
{
    use AppSetUpRouter, SetUpConfig, SetUpCors, SetUpDatabase, SetUpLogs, SetUpTemplates;

    /**
     * @return ContainerInterface
     */
    protected function createContainer()
    {
        $container = new Container();

        $this->setUpConfig($container);

        $this->setUpCors($container);

        $this->setUpDatabase($container);

        $this->setUpFileLogs($container);
        // or
        //$this->setUpNetworkLogs($container);

        // Uncomment if you need to have database access classes in container
        //$this->setUpDatabase($container);

        // Uncomment if you need to have Twig in container
        //$this->setUpTwig($container);

        // Uncomment if you need to have Router in container.
        // Note you can add router to container only here as the method expects
        // it would be called from Application.
        //$this->appSetUpRouter($container);

        return $container;
    }
}
