<?php

namespace Grasmash\YamlCli\Tests;

use Symfony\Component\Console\Application;

/**
 * Class BltTestBase.
 *
 * Base class for all tests that are executed for BLT itself.
 */
abstract class TestBase extends \PHPUnit_Framework_TestCase
{

    /** @var Application */
    protected $application;

    /**
     * {@inheritdoc}
     *
     * @see https://symfony.com/doc/current/console.html#testing-commands
     */
    public function setUp()
    {
        parent::setUp();

        $this->application = new Application();
    }
}
