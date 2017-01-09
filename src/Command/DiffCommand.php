<?php

namespace Grasmash\YamlCli\Command;

use Dflydev\DotAccessData\Data;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class CreateProjectCommand
 *
 * @package Grasmash\YamlCli\Command
 */
class DiffCommand extends CommandBase
{
  /**
   * {inheritdoc}
   */
    protected function configure()
    {
        $this
            ->setName('diff')
            ->setDescription('Compare two YAML files in order to find differences between them.')
            ->addArgument(
                'yaml-left',
                InputArgument::REQUIRED,
                'YAML file used as base to compare'
            )
            ->addArgument(
                'yaml-right',
                InputArgument::REQUIRED,
                'YAML file used to find missing parts or differences with the base YAML file'
            )
            ->addOption(
                'stats',
                false,
                InputOption::VALUE_NONE,
                'YAML file used to find missing parts or differences with the base YAML file'
            )
            ->addOption(
                'negate',
                false,
                InputOption::VALUE_NONE,
                'Define mode diff or equal comparison, possible values TRUE/FALSE or 0/1'
            )
            ->addOption(
                'limit',
                null,
                InputOption::VALUE_OPTIONAL,
                'Limit results to a specific number'
            )
            ->addOption(
                'offset',
                null,
                InputOption::VALUE_OPTIONAL,
                'Starting point of a limit'
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

        $yaml_left = $input->getArgument('yaml-left');
        $yaml_right = $input->getArgument('yaml-right');
        $stats = $input->getOption('stats');
        $negate = $input->getOption('negate');
        $limit = $input->getOption('limit');
        $offset = $input->getOption('offset');
        if ($negate == 1 || $negate == 'TRUE') {
            $negate = true;
        } else {
            $negate = false;
        }

        $yaml_left_parsed = $this->loadYamlFile($yaml_left);
        if (!$yaml_left_parsed) {
            return 1;
        }
        $yaml_right_parsed = $this->loadYamlFile($yaml_right);
        if (!$yaml_right_parsed) {
            return 1;
        }

        $statistics = ['total' => 0, 'equal'=> 0 , 'diff' => 0];
        $diff = $this->arrayDiff($yaml_left_parsed, $yaml_right_parsed, $negate, $statistics);
        print_r($diff);

        if ($stats) {
            $this->writeInfo(
                sprintf(
                    'Total: "%s"',
                    $statistics['total']
                )
            );
            $this->writeInfo(
                sprintf(
                    'Diff: "%s"',
                    $statistics['diff']
                )
            );
            $this->writeInfo(
                sprintf(
                    'Equal: "%s"',
                    $statistics['equal']
                )
            );
            return 0;
        }

        // FLAT YAML file to display full yaml to be used with command yaml:update:key or yaml:update:value
        $diffFlatten = array();
        $keyFlatten = '';
        $this->yamlFlattenArray($diff, $diffFlatten, $keyFlatten);
        if ($limit !== null) {
            if (!$offset) {
                $offset = 0;
            }
            $diffFlatten = array_slice($diffFlatten, $offset, $limit);
        }
        $tableHeader = [
            "Key",
            "Value",
        ];
        $tableRows = [];
        foreach ($diffFlatten as $yamlKey => $yamlValue) {
            $tableRows[] = [
                $yamlKey,
                $yamlValue
            ];
            print $yamlKey . "\n";
            print $yamlValue . "\n";
        }
        $this->table($tableHeader, $tableRows, 'compact');
    }

    /**
     * Calculates the differences between two arrays.
     *
     * @param $array1
     * @param $array2
     * @param bool   $negate if Negate is true only if values are equal are returned.
     * @param$$statistics mixed array
     * @return array
     */
    public function arrayDiff($array1, $array2, $negate = false, &$statistics)
    {
        $result = array();
        foreach ($array1 as $key => $val) {
            if (isset($array2[$key])) {
                if (is_array($val) && $array2[$key]) {
                    $result[$key] = $this->arrayDiff($val, $array2[$key], $negate, $statistics);
                    if (empty($result[$key])) {
                        unset($result[$key]);
                    }
                } else {
                    $statistics['total'] += 1;
                    if ($val == $array2[$key] && $negate) {
                        $result[$key] = $array2[$key];
                        $statistics['equal'] += 1;
                    } elseif ($val != $array2[$key] && $negate) {
                        $statistics['diff'] += 1;
                    } elseif ($val != $array2[$key] && !$negate) {
                        $result[$key] = $array2[$key];
                        $statistics['diff'] += 1;
                    } elseif ($val == $array2[$key] && !$negate) {
                        $result[$key] = $array2[$key];
                        $statistics['equal'] += 1;
                    }
                }
            } else {
                if (is_array($val)) {
                    $statistics['diff'] += count($val, COUNT_RECURSIVE);
                    $statistics['total'] += count($val, COUNT_RECURSIVE);
                } else {
                    $statistics['diff'] +=1;
                    $statistics['total'] += 1;
                }
            }
        }
        return $result;
    }

    /**
     * Flat a yaml file
     * @param array  $array
     * @param array  $flatten_array
     * @param string $key_flatten
     */
    public function yamlFlattenArray(array &$array, &$flatten_array, &$key_flatten = '')
    {
        foreach ($array as $key => $value) {
            if (!empty($key_flatten)) {
                $key_flatten.= '.';
            }
            $key_flatten.= $key;
            if (is_array($value)) {
                $this->yamlFlattenArray($value, $flatten_array, $key_flatten);
            } else {
                if (!empty($value)) {
                    $flatten_array[$key_flatten] = $value;
                    $key_flatten = substr($key_flatten, 0, strrpos($key_flatten, "."));
                } else {
                    // Return to previous key
                    $key_flatten = substr($key_flatten, 0, strrpos($key_flatten, "."));
                }
            }
        }
        // Start again with flatten key after recursive call
        $key_flatten = substr($key_flatten, 0, strrpos($key_flatten, "."));
    }

    /**
     * @param array  $headers
     * @param array  $rows
     * @param string $style
     */
    public function table(array $headers, array $rows, $style = 'symfony-style-guide')
    {
        $headers = array_map(
            function ($value) {
                return sprintf('<info>%s</info>', $value);
            }, $headers
        );
        if (!is_array(current($rows))) {
            $rows = array_map(
                function ($row) {
                    return [$row];
                },
                $rows
            );
        }
        $table = new Table($this->output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->setStyle($style);
        $table->render();
    }
}
