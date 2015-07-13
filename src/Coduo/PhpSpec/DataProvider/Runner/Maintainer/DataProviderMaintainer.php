<?php

namespace Coduo\PhpSpec\DataProvider\Runner\Maintainer;

use Coduo\PhpSpec\DataProvider\Annotation\Parser;
use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Runner\Maintainer\MaintainerInterface;
use PhpSpec\SpecificationInterface;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\Runner\CollaboratorManager;

class DataProviderMaintainer implements MaintainerInterface
{
    const EXAMPLE_NUMBER_PATTERN = '/^(\d+)\)/';

    /**
     * @param ExampleNode $example
     *
     * @return bool
     */
    public function supports(ExampleNode $example)
    {
        return $this->haveValidDataProvider($example);
    }

    /**
     * @param ExampleNode            $example
     * @param SpecificationInterface $context
     * @param MatcherManager         $matchers
     * @param CollaboratorManager    $collaborators
     */
    public function prepare(ExampleNode $example, SpecificationInterface $context,
                            MatcherManager $matchers, CollaboratorManager $collaborators)
    {
        $exampleNum = $this->getExampleNumber($example->getTitle());
        $providedData = $this->getDataFromProvider($example);

        if (! array_key_exists($exampleNum, $providedData)) {
            return ;
        }

        $data = $providedData[$exampleNum];

        foreach ($example->getFunctionReflection()->getParameters() as $position => $parameter) {
            if (!isset($data[$position])) {
                continue;
            }
            $collaborators->set($parameter->getName(), $data[$position]);
        }
    }

    /**
     * @param ExampleNode            $example
     * @param SpecificationInterface $context
     * @param MatcherManager         $matchers
     * @param CollaboratorManager    $collaborators
     */
    public function teardown(ExampleNode $example, SpecificationInterface $context,
                             MatcherManager $matchers, CollaboratorManager $collaborators)
    {
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return 50;
    }

    private function haveValidDataProvider(ExampleNode $example)
    {
        $parser = new Parser();
        $dataProviderMethod = $parser->getDataProvider($example->getFunctionReflection());

        if (!isset($dataProviderMethod)) {
            return false;
        }

        if (!$example->getSpecification()->getClassReflection()->hasMethod($dataProviderMethod)) {
            return false;
        }

        $subject = $example->getSpecification()->getClassReflection()->newInstance();
        $providedData = $example->getSpecification()->getClassReflection()->getMethod($dataProviderMethod)->invoke($subject);

        if (!is_array($providedData)) {
            return false;
        }

        $exampleParamsCount = count($example->getFunctionReflection()->getParameters());
        foreach ($providedData as $dataRow) {
            if (!is_array($dataRow)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param ExampleNode $example
     * @return bool|mixed
     */
    private function getDataFromProvider(ExampleNode $example)
    {
        $parser = new Parser();
        $dataProviderMethod = $parser->getDataProvider($example->getFunctionReflection());

        if (!isset($dataProviderMethod)) {
            return array();
        }

        if (!$example->getSpecification()->getClassReflection()->hasMethod($dataProviderMethod)) {
            return array();
        }

        $subject = $example->getSpecification()->getClassReflection()->newInstance();
        $providedData = $example->getSpecification()->getClassReflection()->getMethod($dataProviderMethod)->invoke($subject);

        return (is_array($providedData)) ? $providedData : array();
    }

    /**
     * @param $title
     * @return int
     */
    private function getExampleNumber($title)
    {
        if (0 === preg_match(self::EXAMPLE_NUMBER_PATTERN, $title, $matches)) {
            return 0;
        }

        return (int) $matches[1] - 1;
    }
}
