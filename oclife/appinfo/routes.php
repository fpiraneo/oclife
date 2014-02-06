<?php

$this->create('oclife_index', '/')->action(
    function($params){
        require __DIR__ . '/../index.php';
    }
);
