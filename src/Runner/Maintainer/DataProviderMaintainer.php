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
        $providedData = $this->getDataFromProvider($example);

        if (!$providedData) {
            return false;
        }

        foreach ($providedData as $dataRow) {
            if (!is_array($dataRow)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param ExampleNode            $example
     * @param SpecificationInterface $context
     * @param MatcherManager         $matchers
     * @param CollaboratorManager    $collaborators
     */
    public function prepare(
        ExampleNode $example,
        SpecificationInterface $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ) {
        $exampleNum = $this->getExampleNumber($example->getTitle());
        $providedData = $this->getDataFromProvider($example);

        if (!array_key_exists($exampleNum, $providedData)) {
            return;
        }

        $data = $providedData[$exampleNum];

        foreach ($example->getFunctionReflection()->getParameters() as $position => $parameter) {
            if (!array_key_exists($position, $data)) {
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
    public function teardown(
        ExampleNode $example,
        SpecificationInterface $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ) {
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return 50;
    }

    /**
     * @param ExampleNode $example
     *
     * @return array|null
     */
    private function getDataFromProvider(ExampleNode $example)
    {
        $dataProviderMethod = Parser::getDataProvider($example->getFunctionReflection());

        if (null === $dataProviderMethod) {
            return null;
        }

        $classReflection = $example->getSpecification()->getClassReflection();

        if (!$classReflection->hasMethod($dataProviderMethod)) {
            return null;
        }

        $subject = $classReflection->newInstance();
        $providedData = $classReflection->getMethod($dataProviderMethod)->invoke($subject);

        return is_array($providedData) ? $providedData : null;
    }

    /**
     * @param $title
     *
     * @return int
     */
    private function getExampleNumber($title)
    {
        if (0 === preg_match(self::EXAMPLE_NUMBER_PATTERN, $title, $matches)) {
            return 0;
        }

        return (int)$matches[1] - 1;
    }
}
