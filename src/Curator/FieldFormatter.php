<?php

namespace Curator;

use Curator\InvalidConfigurationException;

class FieldFormatter
{
    /** @var string */
    private $pattern;
    /** @var string */
    private $replace;
    /** @var array */
    private $fields = [];

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        $this->assertConfigIsValid($config);
        $this->pattern = $config['pattern'];
        preg_match_all('/<(\w+)>/', $this->pattern, $fields);
        $this->fields = $fields[1];
        $this->replace = $config['replace'];
    }

    private function assertConfigIsValid($config)
    {
        $required = ['pattern', 'replace'];
        foreach ($required as $field) {
            if (!isset($config[$field])) {
                throw InvalidConfigurationException::create($field);
            }
        }
    }
    
    /**
     * @param string $value
     *
     * @return string
     */
    public function process($value)
    {
        $matches = [];
        if (preg_match_all($this->pattern, $value, $matches)) {
            foreach ($this->fields as $field) {
                foreach ($matches[$field] as $match) {
                    $replace = str_replace("<{$field}>", $match, $this->replace);
                    $value = str_replace($match, $replace, $value);
                }
            }
        }

        return $value;
    }
}