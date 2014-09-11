<?php

namespace Coduo\PhpSpec\DataProvider\Listener;

use Coduo\PhpSpec\DataProvider\Annotation\Parser;
use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Event\SpecificationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DataProviderListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'beforeSpecification' => array('beforeSpecification'),
        );
    }

    public function beforeSpecification(SpecificationEvent $event)
    {
        $examplesToAdd  = array();
        $parser         = new Parser();
        foreach ($event->getSpecification()->getExamples() as $example) {
            $dataProviderMethod = $parser->getDataProvider($example->getFunctionReflection());

            if (null !== $dataProviderMethod) {

                if (!$example->getSpecification()->getClassReflection()->hasMethod($dataProviderMethod)) {
                    return false;
                }

                $subject = $example->getSpecification()->getClassReflection()->newInstance();
                $providedData = $example->getSpecification()->getClassReflection()->getMethod($dataProviderMethod)->invoke($subject);

                if (is_array($providedData)) {
                    foreach ($providedData as $i => $dataRow) {
                        $examplesToAdd[] = new ExampleNode($i+1 . ') ' . $example->getTitle(), $example->getFunctionReflection());
                    }
                }

            }
        }
        foreach ($examplesToAdd as $example) {
            $event->getSpecification()->addExample($example);
        }
    }
}
