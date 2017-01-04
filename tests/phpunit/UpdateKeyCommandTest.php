<?php

namespace Grasmash\YamlCli\Tests\Command;

use Dflydev\DotAccessData\Data;
use Grasmash\YamlCli\Command\UpdateKeyCommand;
use Grasmash\YamlCli\Command\UpdateValueCommand;
use Grasmash\YamlCli\Tests\TestBase;
use Symfony\Component\Console\Tester\CommandTester;

class UpdateKeyCommandTest extends TestBase
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
     * Tests the 'update:key' command.
     *
     * @dataProvider getValueProvider
     */
    public function testUpdateKey($file, $key, $new_key, $expected) {
        $this->application->add(new UpdateKeyCommand());

        /** @var UpdateKeyCommand $command */
        $command = $this->application->find('update:key');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            'filename' => $file,
            'key' => $key,
            'new-key' => $new_key,
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains($expected, $output);

        // If the command was successful, also make sure that the file actually
        // contains the new key. This conditional is necessary because we
        // pass in a "missing" file as part of the test data set.
        if ($commandTester->getStatusCode() == 0) {
            $contents = $command->loadYamlFile($file);
            $data = new Data($contents);
            $this->assertTrue($data->has($new_key), "The file $file does not contain the new key $new_key. It should.");
            $this->assertNotTrue($data->has($key), "The file $file contains the old key $key. It should not.");
        }
    }

    /**
     * Provides values to testUpdateKey().
     *
     * @return array
     *   An array of values to test.
     */
    public function getValueProvider()
    {

        $file = 'tests/resources/temp.yml';

        return [
            [$file, 'deep-array.second.third.fourth', 'deep-array.second.third.fifth', "The key 'deep-array.second.third.fourth' was changed to 'deep-array.second.third.fifth' in tests/resources/temp.yml."],
            ['missing.yml', 'not-real', 'still-not-real', "The file missing.yml does not exist."],
        ];
    }
}
