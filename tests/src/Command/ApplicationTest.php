<?php

namespace Grasmash\YamlCli\Tests\Command;

use PHPUnit\Framework\Attributes\DataProvider;
use Grasmash\YamlCli\Tests\TestBase;

class ApplicationTest extends TestBase
{

    /**
     * Tests that all expected commands are available in the application.
     */
    #[DataProvider('getValueProvider')]
    public function testApplication($expected)
    {
        $bin = realpath(__DIR__ . '/../../../bin/yaml-cli');
        $output = shell_exec("$bin list");
        $this->assertStringContainsString($expected, $output);
    }

    /**
     * Provides values to testApplication().
     *
     * @return array
     *   An array of values to test.
     */
    public static function getValueProvider(): array
    {
        return [
            ['get:value'],
            ['get:type'],
            ['lint'],
            ['unset:key'],
            ['update:key'],
            ['update:value'],
        ];
    }
}
