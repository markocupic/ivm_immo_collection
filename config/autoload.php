<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2018 Leo Feyer
 *
 * @license LGPL-3.0+
 */

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    // Modules
    'ModuleImmosearchListCollection'      => 'system/modules/ivm_immo_collection/modules/ModuleImmosearchListCollection.php',

    // Classes
    'IvmImmoCollection\ReplaceInsertTags' => 'system/modules/ivm_immo_collection/classes/ReplaceInsertTags.php',
));

/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    'toggleCollection'              => 'system/modules/ivm_immo_collection/templates',
    'mod_immosearch_listcollection' => 'system/modules/ivm_immo_collection/templates',

));
