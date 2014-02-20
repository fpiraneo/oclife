<?php
/*
 * Copyright 2014 by Francesco PIRANEO G. (fpiraneo@gmail.com)
 * 
 * This file is part of oclife.
 * 
 * oclife is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * oclife is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with oclife.  If not, see <http://www.gnu.org/licenses/>.
 */

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
