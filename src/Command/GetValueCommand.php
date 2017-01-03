<?php

namespace Grasmash\YamlCli\Command;

use Dflydev\DotAccessData\Data;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @var int
 */
const MACOSX = 33;

/**
 * Class CreateProjectCommand
 *
 * @package Grasmash\YamlCli\Command
 */
class GetValueCommand extends CommandBase
{
  /**
   * {inheritdoc}
   */
    protected function configure()
    {
        $this
        ->setName('get:value')
        ->setDescription('Get a value for a specific key in a YAML file.')
        ->addArgument(
            'file',
            InputArgument::REQUIRED
        )
         ->addArgument(
             'key',
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
        $yaml_file = $input->getArgument('file');
        $yaml_key = $input->getArgument('key');

        if (!file_exists($yaml_file)) {
            $output->writeln("<error>The file $yaml_file does not exist.</error>");

            return 1;
        }

        try {
            $yaml_parsed = Yaml::parse(file_get_contents($yaml_file));
        } catch (\Exception $e) {
            $output->writeln("<error>There was an error parsing $yaml_file.</error>");
            $output->writeln($e->getMessage());

            return 1;
        }

        if (empty($yaml_parsed)) {
            $output->writeln("<error>The file $yaml_file is empty.");

            return 1;
        }

        $data = new Data($yaml_parsed);
        if (!$data->has($yaml_key)) {
            $output->writeln("<error>The key $yaml_key does not exist in $yaml_file.");

            return 1;
        }

        $value = $data->get($yaml_key);
        $output->writeln(trim(Yaml::dump($value)));
    }
}
