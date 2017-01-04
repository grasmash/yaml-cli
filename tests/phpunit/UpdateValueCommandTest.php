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
    public function testUpdateValue($file, $key, $value, $expected)
    {
        $commandTester = $this->runCommand($file, $key, $value);
        $output = $commandTester->getDisplay();
        $this->assertContains($expected, $output);

        $contents = $this->getCommand()->loadYamlFile($file);
        $data = new Data($contents);
        $this->assertEquals($value, $data->get($key));
    }

    /**
     * Tests that passing a missing file outputs expected error.
     */
    public function testMissingFile() {
        $commandTester = $this->runCommand('missing.yml', 'not-real', 'still-not-real');
        $this->assertContains("The file missing.yml does not exist.", $commandTester->getDisplay());
    }

    /**
     * Gets the update:value command.
     *
     * @return UpdateValueCommand
     */
    protected function getCommand() {
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
    protected function runCommand($file, $key, $value) {
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
            [$file, 'deep-array.second.third.fourth', 'goodbye world', "The value for key 'deep-array.second.third.fourth' was set to 'goodbye world' in tests/resources/temp.yml."],
            [$file, 'flat-array.0', 'goodbye world', "The value for key 'flat-array.0' was set to 'goodbye world' in tests/resources/temp.yml."],
            [$file, 'inline-array.0', 'goodbye world', "The value for key 'inline-array.0' was set to 'goodbye world' in tests/resources/temp.yml."],
            [$file, 'new-key.sub-key', 'hello world', "The value for key 'new-key.sub-key' was set to 'hello world' in tests/resources/temp.yml."],
        ];
    }
}
