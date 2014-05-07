<?php

namespace Coduo\PHPSpec;

use Coduo\PHPSpec\Listener\DataProviderListener;
use Coduo\PHPSpec\Runner\Maintainer\DataProviderMaintainer;
use PhpSpec\Extension\ExtensionInterface;
use PhpSpec\ServiceContainer;

class DataProviderExtension implements ExtensionInterface
{
    /**
     * @param ServiceContainer $container
     */
    public function load(ServiceContainer $container)
    {
        $container->setShared('event_dispatcher.listeners.data_provider', function ($c) {
            return new DataProviderListener();
        });

        $container->set('runner.maintainers.data_provider', function ($c) {
            return new DataProviderMaintainer();
        });
    }
}
