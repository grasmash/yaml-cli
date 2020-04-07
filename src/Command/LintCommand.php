<?php

namespace Grasmash\YamlCli\Command;

use Dflydev\DotAccessData\Data;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * Class CreateProjectCommand
 *
 * @package Grasmash\YamlCli\Command
 */
class LintCommand extends CommandBase
{
    /**
     * {inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('lint')
            ->setDescription('Validates that a given YAML file has valid syntax.')
            ->addUsage('path/to/file.yml')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'The path or directory of the YAML file'
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        $yaml_parsed = $this->loadYamlFile($path);
        if (!$yaml_parsed) {
            // Exit with a status of 1.
            return 1;
        }

        if (is_dir($path)) {
            $finder = new Finder();
            $finder->files()->in($path);
            foreach ($finder as $file) {
                $yaml_parsed = $this->loadYamlFile($file->getRealPath());
                if (!$yaml_parsed) {
                    // Exit with a status of 1.
                    return 1;
                }
            }
        } else {
            $yaml_parsed = $this->loadYamlFile($path);
            if (!$yaml_parsed) {
                // Exit with a status of 1.
                return 1;
            }
        }

        if (OutputInterface::VERBOSITY_VERBOSE === $output->getVerbosity()) {
            if (is_dir($path)) {
                $output->writeln("<info>The directory $path contains valid YAML.</info>");
            } else {
                $output->writeln("<info>The file $path contains valid YAML.</info>");
            }
        }

        return 0;
    }
}
