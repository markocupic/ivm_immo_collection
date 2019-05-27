<?php
/*
 * This file is part of Contao Ivm Immo Collection.
 *
 * (c) Marko Cupic, april 2019
 * @author Marko Cupic <https://github.com/markocupic/ivm_immo_collection>
 * @license MIT
 */

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    // Modules
    'IvmImmoCollection\ModuleImmosearchListCollection' => 'system/modules/ivm_immo_collection/modules/ModuleImmosearchListCollection.php',

    // Classes
    'IvmImmoCollection\ReplaceInsertTags'              => 'system/modules/ivm_immo_collection/classes/ReplaceInsertTags.php',

    // Hooks
    'IvmImmoCollection\ValidateFormFieldHook'          => 'system/modules/ivm_immo_collection/classes/ValidateFormFieldHook.php',

));

/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    'toggleCollection'              => 'system/modules/ivm_immo_collection/templates',
    'mod_immosearch_listcollection' => 'system/modules/ivm_immo_collection/templates',
));
