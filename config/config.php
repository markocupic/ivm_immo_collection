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

// Labels used in IvmImmoCollection\ModuleImmosearchListCollection
$GLOBALS['IVM_LABELS'] = array(
    'typ'                 => array(
        'DACHGESCHOSS'        => 'Dachgeschosswohnung',
        'ETAGE'               => 'Etagenwohnung',
        'ERDGESCHOSS'         => 'Erdgeschosswohnung',
        'LOFT-STUDIO-ATELIER' => 'Loft-Studio-Atelier',
        'PENTHOUSE'           => 'Penthouse',
        'MAISONETTE'          => 'Maisonette',
        'SOUTERRAIN'          => 'Souterrainwohnung',
        'TERRASSEN'           => 'Terrassenwohnung',
        'KEINE_ANGABE'        => 'Wohnung',
    ),
    'zustand'             => array(
        'ERSTBEZUG'                => 'Erstbezug',
        'TEIL_VOLLSANIERT'         => 'Saniert',
        'TEIL_VOLLRENOVIERUNGSBED' => 'Renovierungsbedürftig',
        'NEUWERTIG'                => 'Neuwertig',
        'BAUFAELLIG'               => 'Baufällig',
        'TEIL_VOLLRENOVIERT'       => 'Renoviert',
        'MODERNISIERT'             => 'Modernisiert',
        'NACH_VEREINBARUNG'        => 'Nach Vereinbarung',
        'GEPFLEGT'                 => 'Gepflegt',
    ),
    'dusche'              => array('true' => 'Dusche'),
    'wanne'               => array('true' => 'Wanne'),
    'fenster'             => array('true' => 'Fenster im Bad'),
    'ebk'                 => array('true' => 'Einbauküche'),
    'ebk2'                => array('true' => 'EBK'),
    'offen'               => array('true' => 'offene Küche'),
    'fliesen'             => array('true' => 'Fliesenböden'),
    'kunststoff'          => array('true' => 'PVC - Belag'),
    'parkett'             => array('true' => 'Parkettböden'),
    'teppich'             => array('true' => 'Teppich'),
    'laminat'             => array('true' => 'Laminat'),
    'dielen'              => array('true' => 'Dielen'),
    'stein'               => array('true' => 'Stein'),
    'estrich'             => array('true' => 'Estrich'),
    'doppelboden'         => array('true' => 'Doppelboden'),
    'fern'                => array('true' => 'Fernheizung'),
    'etage_heizung'       => array('true' => 'Etagenheizung'),
    'zentral'             => array('true' => 'Zentralheizung'),
    'gas'                 => array('true' => 'Gasfeuerung'),
    'oel'                 => array('true' => 'Ölbefeuerung'),
    'wg'                  => array('true' => 'WG geeignet'),
    'keller'              => array('true' => 'Keller vorhanden'),
    'lift'                => array('true' => 'Aufzug'),
    'barrierefrei'        => array('true' => 'barrierefrei'),
    'garten'              => array('true' => 'Garten'),
    'moebliert'           => array('true' => 'Möbliert'),
    'rollstuhlgerecht'    => array('true' => 'Rollstuhlgerecht'),
    'raeume_veraenderbar' => array('true' => 'Räume veränderbar'),
    'wbs_sozialwohnung'   => array('true' => 'Wohnberechtigungsschein'),
    'balkon'              => array('true' => 'Balkon'),
    'e_typ'               => array('VERBRAUCH' => 'Energieverbrauch'),
    'altbau'              => array('1' => 'Altbau'),
    'neubau'              => array('1' => 'Neubau'),
    'reinigung'           => array('1' => 'Reinigungsservice'),
    'senioren'            => array('true' => 'Seniorengerecht'),
);
