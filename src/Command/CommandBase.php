<?php

namespace Grasmash\YamlCli\Command;

use Grasmash\YamlCli\Loader\JsonFileLoader;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class CommandBase
 *
 * @package Grasmash\YamlCli\Command
 */
abstract class CommandBase extends Command
{
    /** @var Filesystem */
    protected $fs;
    /**
     * @var InputInterface
     */
    protected $input;
    /**
     * @var OutputInterface
     */
    protected $output;

    /** @var FormatterHelper */
    protected $formatter;

  /**
   * Initializes the command just after the input has been validated.
   *
   * @param InputInterface  $input  An InputInterface instance
   * @param OutputInterface $output An OutputInterface instance
   */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->formatter = $this->getHelper('formatter');
        $this->fs = new Filesystem();
    }

    /**
     * Loads a yaml file.
     *
     * @param $filename
     *   The file name.
     *
     * @return array|bool
     *   The parsed content of the yaml file. FALSE if an error occured.
     */
    protected function loadYamlFile($filename)
    {
        if (!file_exists($filename)) {
            $this->output->writeln("<error>The file $filename does not exist.</error>");

            return false;
        }

        try {
            $contents = Yaml::parse(file_get_contents($filename));
        } catch (\Exception $e) {
            $this->output->writeln("<error>There was an error parsing $filename. The contents are not valid YAML.</error>");
            $this->output->writeln($e->getMessage());

            return false;
        }

        if (empty($contents)) {
            $this->output->writeln("<error>The file $filename is empty.");

            return false;
        }

        return $contents;
    }

    /**
     * Checks if a key exists in an array.
     *
     * Supports dot notation for keys. E.g., first.second.parts.
     *
     * @param array $data
     *   The array of data that may contain key.
     * @param string $key
     *   The array key, optionally in dot notation format.
     *
     * @return bool
     *
     */
    protected function checkKeyExists($data, $key)
    {
        if (!$data->has($key)) {
            $this->output->writeln("<error>The key $key does not exist.");

            return false;
        }

        return true;
    }
}
