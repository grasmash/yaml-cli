<?php

namespace Grasmash\YamlCli\Tests\Command;

use Dflydev\DotAccessData\Data;
use Grasmash\YamlCli\Command\UnsetKeyCommand;
use Grasmash\YamlCli\Tests\TestBase;
use Symfony\Component\Console\Tester\CommandTester;

class UnsetKeyCommandTest extends TestBase
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
     * Tests the 'unset:key' command.
     *
     * @dataProvider getValueProvider
     */
    public function testUnsetKey($filename, $key, $expected) {
        $commandTester = $this->runCommand($filename, $key);
        $output = $commandTester->getDisplay();
        $this->assertContains($expected, $output);

        $contents = $this->getCommand()->loadYamlFile($filename);
        $data = new Data($contents);
        $this->assertNotTrue($data->has($key), "The file $filename contains the old key $key. It should not.");
    }

    /**
     * Tests that passing a missing file outputs expected error.
     */
    public function testMissingFile() {
        $commandTester = $this->runCommand('missing.yml', 'not-real');
        $this->assertContains("The file missing.yml does not exist.", $commandTester->getDisplay());
    }

    /**
     * Gets the unset:key command.
     *
     * @return UnsetKeyCommand
     */
    protected function getCommand() {
        $this->application->add(new UnsetKeyCommand());
        $command = $this->application->find('unset:key');

        return $command;
    }

    /**
     * Runs the unset:key command.
     *
     * @param string $filename
     *   The filename.
     * @param string $key
     *   The key to unset.
     *
     * @return \Symfony\Component\Console\Tester\CommandTester
     */
    protected function runCommand($filename, $key) {
        $command = $this->getCommand();
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            'filename' => $filename,
            'key' => $key
        ));

        return $commandTester;
    }

    /**
     * Provides values to testUnsetKey().
     *
     * @return array
     *   An array of values to test.
     */
    public function getValueProvider()
    {

        $filename = 'tests/resources/temp.yml';

        return [
            [$filename, 'deep-array.second.third.fourth', "The key 'deep-array.second.third.fourth' was removed from $filename."],
        ];
    }
}
