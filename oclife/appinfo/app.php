<?php

\OCP\App::addNavigationEntry(array(

    // the string under which your app will be referenced in owncloud
    'id' => 'oclife',

    // sorting weight for the navigation. The higher the number, the higher
    // will it be listed in the navigation
    'order' => 10,

    // the route that will be shown on startup
    'href' => \OCP\Util::linkToRoute('oclife_index'),

    // the icon that will be shown in the navigation
    // this file needs to exist in img/example.png
    'icon' => \OCP\Util::imagePath('oclife', 'nav-icon.svg'),

    // the title of your application. This will be used in the
    // navigation or on the settings page of your app
    'name' => 'Tags'
));

// Add what's needed by TagManager
/*
\OCP\Util::addStyle('oclife', 'tagmanager');
\OCP\Util::addStyle('oclife', 'typeahead');
\OCP\Util::addScript('oclife', 'typeahead');
\OCP\Util::addScript('oclife', 'tagmanager');
*/


//\OCP\Util::addStyle('oclife', 'bootstrap.min');
// \OCP\Util::addStyle('oclife', 'tokenfield-typeahead');
\OCP\Util::addStyle('oclife', 'bootstrap-tokenfield');
\OCP\Util::addStyle('oclife', 'pygments-manni');
\OCP\Util::addStyle('oclife', 'docs');
\OCP\Util::addStyle('oclife', 'oclife_fileinfo');

\OCP\Util::addScript('oclife', 'bootstrap-tokenfield');
// \OCP\Util::addScript('oclife', 'typeahead');
\OCP\Util::addScript('oclife', 'scrollspy');
\OCP\Util::addScript('oclife', 'docs.min');




OCP\App::registerAdmin('oclife', 'settings');
OCP\Util::addscript('oclife', 'filetagger');
