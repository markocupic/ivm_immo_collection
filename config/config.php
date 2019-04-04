<?php
/*
 * This file is part of Contao Ivm Immo Collection.
 *
 * (c) Marko Cupic, april 2019
 * @author Marko Cupic <https://github.com/markocupic/ivm_immo_collection>
 * @license MIT
 */


if(TL_MODE == 'FE')
{
    $GLOBALS['TL_CSS'][] = 'system/modules/ivm_immo_collection/assets/css/stylesheet.css|static';
    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/ivm_immo_collection/assets/js/ivm_immo_collection.js|static';
}

// Frontend modules
$GLOBALS['FE_MOD']['immosearchcollection']['immosearch_listcollection'] = 'IvmImmoCollection\ModuleImmosearchListCollection';

// Hooks
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('IvmImmoCollection\ReplaceInsertTags', 'replaceInsertTags');
