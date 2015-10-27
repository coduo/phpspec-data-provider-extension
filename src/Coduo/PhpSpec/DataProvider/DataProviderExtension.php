<?php

namespace Coduo\PhpSpec\DataProvider;

use Coduo\PhpSpec\DataProvider\Listener\DataProviderListener;
use Coduo\PhpSpec\DataProvider\Runner\Maintainer\DataProviderMaintainer;
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
