<?php

namespace Grasmash\YamlCli\Tests\Command;

use Grasmash\YamlCli\Command\GetValueCommand;
use Grasmash\YamlCli\Command\LintCommand;
use Grasmash\YamlCli\Tests\TestBase;
use Symfony\Component\Console\Tester\CommandTester;

class ApplicationTest extends TestBase
{

    /**
     * Tests that all expected commands are available in the application.
     *
     * @dataProvider getValueProvider
     */
    public function testApplication($expected)
    {
        $bin = realpath(__DIR__ . '/../../bin/yaml-cli');
        $output = shell_exec("$bin list");
        $this->assertContains($expected, $output);
    }

    /**
     * Provides values to testApplication().
     *
     * @return array
     *   An array of values to test.
     */
    public function getValueProvider()
    {
        return [
            ['get:value'],
            ['lint'],
            ['unset:key'],
            ['update:key'],
            ['update:value'],
        ];
    }
}
