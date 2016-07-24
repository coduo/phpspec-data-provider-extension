<?php

namespace Coduo\PhpSpec\DataProvider;

use Coduo\PhpSpec\DataProvider\Listener\DataProviderListener;
use Coduo\PhpSpec\DataProvider\Runner\Maintainer\DataProviderMaintainer;
use PhpSpec\Extension as PhpSpecExtension;
use PhpSpec\ServiceContainer;

class DataProviderExtension implements PhpSpecExtension
{
    /**
     * @param ServiceContainer $container
     * @param array $params
     */
    public function load(ServiceContainer $container, array $params)
    {
        $container->define('event_dispatcher.listeners.data_provider', function ($c) {
            return new DataProviderListener();
        }, ['event_dispatcher.listeners']);

        $container->define('runner.maintainers.data_provider', function ($c) {
            return new DataProviderMaintainer();
        }, ['runner.maintainers']);
    }
}
