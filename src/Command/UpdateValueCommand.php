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
        ->addArgument(
            'filename',
            InputArgument::REQUIRED
        )
         ->addArgument(
             'key',
             InputArgument::REQUIRED
         )
        ->addArgument(
            'value',
            InputArgument::REQUIRED
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
        if (!$yaml_parsed) {
            // Exit with a status of 1.
            return 1;
        }

        $data = new Data($yaml_parsed);
        if (!$this->checkKeyExists($data, $key)) {
            return 1;
        }
        $data->set($key, $value);

        try {
            $yaml = Yaml::dump($data->export());
        } catch (\Exception $e) {
            $this->output->writeln("<error>There was an error dumping the yaml contents for $filename.</error>");
            $this->output->writeln($e->getMessage());

            return false;
        }

        try {
            file_put_contents($filename, $yaml);
        } catch (\Exception $e) {
            $this->output->writeln("<error>There was an writing to $filename.</error>");
            $this->output->writeln($e->getMessage());

            return false;
        }

        $this->output->writeln("<info>The key '$key' was changed to '$value' in $filename.</info>");
    }
}
