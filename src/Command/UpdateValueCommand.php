<?php

namespace Grasmash\YamlCli\Command;

use Dflydev\DotAccessData\Data;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class CreateProjectCommand
 *
 * @package Grasmash\YamlCli\Command
 */
class UpdateValueCommand extends CommandBase
{
  /**
   * {inheritdoc}
   */
    protected function configure()
    {
        $this
            ->setName('update:value')
            ->setDescription('Update the value for a specific key in a YAML file.')
            ->addUsage("path/to/file.yml example.key 'new value for example.key'")
            ->addArgument(
                'filename',
                InputArgument::REQUIRED,
                "The filename of the YAML file"
            )
             ->addArgument(
                 'key',
                 InputArgument::REQUIRED,
                 "The key for the value to set, in dot notation"
             )
            ->addArgument(
                'value',
                InputArgument::REQUIRED,
                "The new value"
            );
    }

  /**
   * @param \Symfony\Component\Console\Input\InputInterface $input
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   *
   * @return bool
   */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');
        $key = $input->getArgument('key');
        $value = $input->getArgument('value');
        $yaml_parsed = $this->loadYamlFile($filename);
        if ($yaml_parsed === false) {
            // Exit with a status of 1.
            return 1;
        }

        $data = new Data($yaml_parsed);
        $data->set($key, $value);

        if ($this->writeYamlFile($filename, $data)) {
            $this->output->writeln("<info>The value for key '$key' was set to '$value' in $filename.</info>");
        }
    }
}
