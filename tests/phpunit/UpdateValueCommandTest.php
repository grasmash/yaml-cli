<?php

namespace Grasmash\YamlCli\Tests\Command;

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

        // Make a temporary copy of good.yml so that we can update a value
        // without destroying the original.
        $source = __DIR__ . '/../resources/good.yml';
        $this->temp_file = __DIR__ . '/../resources/temp.yml';
        if (file_exists($source)) {
            copy($source, $this->temp_file);
        }
    }

    /**
     * Removes temporary file.
     */
    public function tearDown() {
        parent::tearDown();

        unlink($this->temp_file);
    }

    /**
     * Tests the 'update:value' command.
     *
     * @dataProvider getValueProvider
     */
    public function testGetValue($file, $key, $value, $expected)
    {
        $this->application->add(new UpdateValueCommand());

        $command = $this->application->find('update:value');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'filename' => $file,
            'key' => $key,
            'value' => $value,
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains($expected, $output);

        // If the command was successful, also make sure that the file actually
        // contains the value.
        // @todo Use get:value to check the specific array key?
        if ($commandTester->getStatusCode() == 0) {
            $contents = file_get_contents($file);
            $this->assertContains($value, $contents);
        }
    }

    /**
     * Provides values to testGetValue().
     *
     * @return array
     *   An array of values to test.
     */
    public function getValueProvider()
    {

        $file = 'tests/resources/temp.yml';

        return [
            [$file, 'deep-array.second.third.fourth', 'goodbye world', "The key 'deep-array.second.third.fourth' was changed to 'goodbye world' in tests/resources/temp.yml."],
            ['missing.yml', 'not-real', 'whatever', "The file missing.yml does not exist."],
        ];
    }
}
