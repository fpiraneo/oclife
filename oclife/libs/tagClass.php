<?php

class tag {
    public $value;
    public $label;
    
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
