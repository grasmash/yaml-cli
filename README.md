[![Build Status](https://travis-ci.org/grasmash/yaml-cli.svg?branch=master)](https://travis-ci.org/grasmash/yaml-cli)

A command line tool for reading and manipulating yaml files.

Commands:


| Command      | Description                                         |
|--------------| ----------------------------------------------------|
| get:value    | Get a value for a specific key in a YAML file.      |
| update:value | Update the value for a specific key in a YAML file. |

Example usage:

    ./vendor/bin/yaml-cli get:value somefile.yml some-key