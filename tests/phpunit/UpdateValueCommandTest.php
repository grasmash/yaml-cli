<?php

namespace Grasmash\YamlCli\Tests\Command;

use Dflydev\DotAccessData\Data;
use Grasmash\YamlCli\Command\UpdateValueCommand;
use Grasmash\YamlCli\Tests\TestBase;
use Symfony\Component\Console\Tester\CommandTester;

class UpdateValueCommandTest extends TestBase
{

    /** @var string */
    protected $temp_file;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->setupTemporaryConfigFiles();
    }

    /**
     * Tests the 'update:value' command.
     *
     * @dataProvider getValueProvider
     */
    public function testUpdateValue($file, $key, $value, $expected_value, $expected_output, $expected_exit_code)
    {
        $commandTester = $this->runCommand($file, $key, $value);
        $output = $commandTester->getDisplay();
        $this->assertContains($expected_output, $output);

        $contents = $this->getCommand()->loadYamlFile($file);
        $data = new Data($contents);
        $this->assertEquals($expected_value, $data->get($key));
        $this->assertEquals($expected_exit_code, $commandTester->getStatusCode());
    }

    /**
     * Tests that passing a missing file outputs expected error.
     */
    public function testMissingFile()
    {
        $commandTester = $this->runCommand('missing.yml', 'not-real', 'still-not-real');
        $this->assertContains("The file missing.yml does not exist.", $commandTester->getDisplay());
    }

    /**
     * Gets the update:value command.
     *
     * @return UpdateValueCommand
     */
    protected function getCommand()
    {
        $this->application->add(new UpdateValueCommand());
        $command = $this->application->find('update:value');

        return $command;
    }

    /**
     * Runs the update:value commnd.
     *
     * @param string $file
     *   The filename.
     * @param string $key
     *   The key for which to update the value.
     * @param string $value
     *   The new value.
     *
     * @return \Symfony\Component\Console\Tester\CommandTester
     */
    protected function runCommand($file, $key, $value)
    {
        $command = $this->getCommand();
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'filename' => $file,
            'key' => $key,
            'value' => $value,
        ));

        return $commandTester;
    }

    /**
     * Provides values to testUpdateValue().
     *
     * @return array
     *   An array of values to test.
     */
    public function getValueProvider()
    {
        $file = 'tests/resources/temp.yml';

        return [
            [$file, 'deep-array.second.third.fourth', 'goodbye world', 'goodbye world', "The value for key 'deep-array.second.third.fourth' was set to 'goodbye world' in $file.", 0],
            [$file, 'flat-array.0', 'goodbye world', 'goodbye world', "The value for key 'flat-array.0' was set to 'goodbye world' in $file.", 0],
            [$file, 'inline-array.0', 'goodbye world', 'goodbye world', "The value for key 'inline-array.0' was set to 'goodbye world' in $file.", 0],
            [$file, 'new-key.sub-key', 'hello world', 'hello world', "The value for key 'new-key.sub-key' was set to 'hello world' in $file.", 0],
            [$file, 'boolean.0', 'false', false, "The value for key 'boolean.0' was set to 'false' in $file.", 0],
            [$file, 'boolean.1', 'true', true, "The value for key 'boolean.1' was set to 'true' in $file.", 0],
        ];
    }
}
