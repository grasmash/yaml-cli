<?php

namespace Grasmash\YamlCli\Tests\Command;

use Grasmash\YamlCli\Command\GetValueCommand;
use Grasmash\YamlCli\Tests\TestBase;
use Symfony\Component\Console\Tester\CommandTester;

class GetValueCommandTest extends TestBase
{

    /**
     * Tests the 'get:value' command.
     *
     * @dataProvider getValueProvider
     */
    public function testGetValue($file, $key, $expected)
    {
        $this->application->add(new GetValueCommand());

        $command = $this->application->find('get:value');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'filename' => $file,
            'key' => $key
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains($expected, $output);
    }

    /**
     * Provides values to testGetValue().
     *
     * @return array
     *   An array of values to test.
     */
    public function getValueProvider()
    {

        $file = 'tests/resources/good.yml';

        return [
            [$file, 'not-real', "The key not-real does not exist."],
            ['missing.yml', 'not-real', "The file missing.yml does not exist."],
            [$file, 'deep-array.second.third.fourth', 'hello world'],
            [$file, 'flat-array', '- one
- two
- three'],
            [$file, 'inline-array', '- one
- two
- three'],
        ];
    }
}
