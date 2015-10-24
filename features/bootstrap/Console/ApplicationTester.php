<?php

namespace Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * A console application tester heavily inspired by/proudly stolen from \Symfony\Component\Console\Tester\ApplicationTester.
 */
class ApplicationTester
{
    /**
     * @var Application $application
     */
    private $application;

    /**
     * @var StringInput $input
     */
    private $input;

    /**
     * @var StreamOutput $output
     */
    private $output;

    /**
     * @var resource $inputStream
     */
    private $inputStream;

    /**
     * @var int
     */
    private $result;

    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @param string $input
     *
     * @return integer
     */
    public function run($input)
    {
        $this->input = new StringInput($input);

        $this->output = new StreamOutput(fopen('php://memory', 'w', false));

        $inputStream = $this->getInputStream();
        rewind($inputStream);
        $this->application->getHelperSet()
            ->get('dialog')
            ->setInputStream($inputStream);

        $this->result = $this->application->run($this->input, $this->output);
    }

    /**
     * @param boolean
     *
     * @return string
     */
    public function getDisplay($normalize = false)
    {
        rewind($this->output->getStream());

        $display = stream_get_contents($this->output->getStream());

        if ($normalize) {
            $display = str_replace(PHP_EOL, "\n", $display);
        }

        return $display;
    }

    /**
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param string $input
     */
    public function putToInputStream($input)
    {
        fputs($this->getInputStream(), $input);
    }

    /**
     * @return resource
     */
    private function getInputStream()
    {
        if (null === $this->inputStream) {
            $this->inputStream = fopen('php://memory', 'r+', false);
        }

        return $this->inputStream;
    }

    /**
     * @return int
     */
    public function getResult()
    {
        return $this->result;
    }
}
