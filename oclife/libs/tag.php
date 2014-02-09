<?php

namespace OCA\OCLife;

/**
 * Define an object 'tag'
 *
 * @author fpiraneo
 */
class tag {
    public $value;
    public $label;
    
    /**
     * Constructor for class 'tag'
     * @param integer $value ID of tag
     * @param string $label Human readable tag description
     */
    function __construct($value, $label) {
        $this->value = $value;
        $this->label = $label;
    }

    public function getValue() {
        return $this->value;
    }

    public function getLabel() {
        return $this->label;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function setLabel($label) {
        $this->label = $label;
    }
}
