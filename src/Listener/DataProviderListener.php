<?php

namespace Coduo\PhpSpec\DataProvider\Listener;

use Coduo\PhpSpec\DataProvider\Annotation\Parser;
use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Event\SpecificationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DataProviderListener implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'beforeSpecification' => ['beforeSpecification'],
        ];
    }

    /**
     * @param SpecificationEvent $event
     *
     * @return bool
     */
    public function beforeSpecification(SpecificationEvent $event)
    {
        $examplesToAdd = [];

        foreach ($event->getSpecification()->getExamples() as $example) {
            $dataProviderMethod = Parser::getDataProvider($example->getFunctionReflection());

            if (null === $dataProviderMethod) {
                continue;
            }

            $reflection = $example->getSpecification()->getClassReflection();

            if (!$reflection->hasMethod($dataProviderMethod)) {
                return false;
            }

            $subject = $reflection->newInstance();
            $providedData = $reflection->getMethod($dataProviderMethod)->invoke($subject);

            if (is_array($providedData)) {
                foreach ($providedData as $i => $dataRow) {
                    $examplesToAdd[] = new ExampleNode(
                        $i + 1 .') '.$example->getTitle(),
                        $example->getFunctionReflection()
                    );
                }
            }
        }

        foreach ($examplesToAdd as $example) {
            $event->getSpecification()->addExample($example);
        }
    }
}
