[![Build Status](https://travis-ci.org/grasmash/yaml-cli.svg?branch=master)](https://travis-ci.org/grasmash/yaml-cli)

Yet another  command line tool for reading and manipulating yaml files, built on the [Symfony console component](http://symfony.com/doc/current/components/console.html).

Commands:


| Command      | Description                                         |
|--------------| ----------------------------------------------------|
| get:value    | Get a value for a specific key in a YAML file.      |
| update:value | Update the value for a specific key in a YAML file. |

Example usage:

    ./vendor/bin/yaml-cli get:value somefile.yml some-key
    ./vendor/bin/yaml-cli update:value somefile.yml some-key some-value

Similar tools:

- Javascript - https://github.com/pandastrike/yaml-cli
- Ruby - https://github.com/rubyworks/yaml_command
- Python - https://github.com/0k/shyaml