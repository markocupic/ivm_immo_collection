<?php
/**
 * Created by PhpStorm.
 * User: Marko
 * Date: 03.04.2019
 * Time: 12:47
 */


if(TL_MODE == 'FE')
{
    $GLOBALS['TL_CSS'][] = 'system/modules/ivm_immo_collection/assets/css/stylesheet.css';
    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/ivm_immo_collection/assets/js/ivm_immo_collection.js';
}

// Frontend modules
$GLOBALS['FE_MOD']['immosearchcollection']['immosearch_listcollection'] = 'ModuleImmosearchListCollection';

// Hooks
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('IvmImmoCollection\ReplaceInsertTags', 'replaceInsertTags');
