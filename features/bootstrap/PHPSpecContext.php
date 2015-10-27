<?php

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\Filesystem\Filesystem;
use PhpSpec\Console\Application;

class PHPSpecContext implements SnippetAcceptingContext
{
    /**
     * @var string
     */
    private $workDir;

    /**
     * @var ApplicationTester
     */
    private $applicationTester;

    /**
     * @BeforeScenario
     */
    public function createWorkDir()
    {

        $this->workDir = sprintf(
            '%s/%s/',
            sys_get_temp_dir(),
            uniqid('PHPSpecDataProviderExtension')
        );
        $fs = new Filesystem();
        $fs->mkdir($this->workDir, 0777);
        chdir($this->workDir);
    }

    /**
     * @AfterScenario
     */
    public function removeWorkDir()
    {
        $fs = new Filesystem();
        $fs->remove($this->workDir);
    }

    /**
     * @Given /^the PhpSpecDataProviderExtension is enabled$/
     */
    public function thePhpspecdataproviderextensionIsEnabled()
    {
        $phpspecyml = <<<YML
extensions:
  - Coduo\PhpSpec\DataProvider\DataProviderExtension
YML;

        file_put_contents($this->workDir.'phpspec.yml', $phpspecyml);
    }

    /**
     * @When /^I write a (?:spec|class) "([^"]*)" with following code$/
     */
    public function iWriteASpecWithFollowingCode($file, PyStringNode $codeContent)
    {
        $dirname = dirname($file);
        if (!file_exists($dirname)) {
            mkdir($dirname, 0777, true);
        }

        file_put_contents($file, $codeContent->getRaw());

        require_once($file);
    }

    /**
     * @Given /^I run phpspec$/
     */
    public function iRunPhpspec()
    {
        $application = new Application('2.0-dev');
        $application->setAutoExit(false);

        $this->applicationTester = new Console\ApplicationTester($application);
        $this->applicationTester->run('run --no-interaction -f pretty');
    }

    /**
     * @Then /^it should pass$/
     */
    public function itShouldPass()
    {
        expect($this->applicationTester->getResult())->toBe(0);
    }

    /**
     * @Given /^I should see "([^"]*)"$/
     */
    public function iShouldSee($message)
    {
        expect($this->applicationTester->getDisplay())->toMatch('/'.preg_quote($message, '/').'/sm');
    }
}
