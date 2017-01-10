<?php

namespace Grasmash\YamlCli\Tests\Command;

use Grasmash\YamlCli\Command\GetValueCommand;
use Grasmash\YamlCli\Command\LintCommand;
use Grasmash\YamlCli\Tests\TestBase;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Tester\CommandTester;

class LintCommandTest extends TestBase
{

    /**
     * Tests the 'lint' command.
     *
     * @dataProvider getValueProvider
     */
    public function testLint($file, $expected)
    {
        $this->application->add(new LintCommand());

        $command = $this->application->find('lint');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'filename' => $file
        ), ['verbosity' => Output::VERBOSITY_VERBOSE]);

        $output = $commandTester->getDisplay();
        $this->assertContains($expected, $output);
    }

    /**
     * Provides values to testLint().
     *
     * @return array
     *   An array of values to test.
     */
    public function getValueProvider()
    {

        return [
            ['tests/resources/good.yml', "The file tests/resources/good.yml contains valid YAML."],
            ['tests/resources/bad.yml', "There was an error parsing tests/resources/bad.yml. The contents are not valid YAML."],
            ['missing.yml', "The file missing.yml does not exist."],
        ];
    }
}
