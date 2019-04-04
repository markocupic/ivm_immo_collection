<?php
/*
 * This file is part of Contao Ivm Immo Collection.
 *
 * (c) Marko Cupic, april 2019
 * @author Marko Cupic <https://github.com/markocupic/ivm_immo_collection>
 * @license MIT
 */

namespace IvmImmoCollection;

/**
 * Class ModuleImmosearchListCollection
 */
class ModuleImmosearchListCollection extends \Module
{
    /**
     * @var string
     */
    protected $strTemplate = 'mod_immosearch_listcollection';

    /**
     * @var
     */
    protected $arrFeaturedItems;

    /**
     * @var
     */
    protected $searchResults;

    /**
     * Display a wildcard in the back end
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['immosearch_listcollection'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;
            return $objTemplate->parse();
        }

        $arrFeaturedItems = array();
        if (isset($_COOKIE['ivm-collection']))
        {
            if ($_COOKIE['ivm-collection'] != '')
            {
                $arrFeaturedItems = explode(',', $_COOKIE['ivm-collection']);
            }
            $arrFeaturedItems = array_filter(array_unique($arrFeaturedItems));

            // Remove unique or empty values and reset cookie
            setrawcookie('ivm-collection', implode(',', $arrFeaturedItems));
        }

        $this->arrFeaturedItems = $arrFeaturedItems;

        return parent::generate();
    }

    /**
     *
     */
    protected function compile()
    {
        if (count($this->arrFeaturedItems) < 1)
        {
            $this->Template->ergebnis = null;
            return;
        }

        $result = \Database::getInstance()->query('SELECT * FROM is_wohnungen WHERE id IN(' . implode(',', $this->arrFeaturedItems) . ') ORDER BY kalt DESC');
        if (!$result->numRows)
        {
            $this->Template->ergebnis = null;
            return;
        }
        $this->searchResults = $result->fetchAllAssoc();
        $this->makeList();
    }

    /**
     *
     */
    private function makeList()
    {
        $wohnungen = array();
        $ergebnis = false;
        foreach ($this->searchResults as $wohnung)
        {
            $ergebnis = true;
            $result = \Database::getInstance()->prepare("SELECT * FROM is_details WHERE id = ?")->execute($wohnung['wid']);
            $details = $result->fetchAssoc();

            // Get pics
            $pics = explode(';', $details['pics']);

            // Get jumpTo url
            $url = '';
            $objPage = \PageModel::findByPk($this->jumpTo);
            if ($objPage !== null)
            {
                $url = $objPage->getFrontendUrl() . '?wid=' . $wohnung['wid'];
            }

            $wohnungen[$wohnung['id']] = array(
                'id'        => $wohnung['id'],
                'wid'       => $wohnung['wid'],
                'zimmer'    => $wohnung['zimmer'],
                'flaeche'   => ceil($wohnung['flaeche']) . ' m²',
                'warm'      => $this->makeEuro($wohnung['warm']),
                'kalt'      => $wohnung['kalt'] . " &euro;",
                'title'     => $details['title'],
                'strasse'   => $details['strasse'],
                'hnr'       => $details['hnr'],
                'plz'       => $details['plz'],
                'ort'       => $details['ort'],
                'startbild' => $pics[0],
                'lift'      => $this->getName('lift', $wohnung['lift']),
                'balkon'    => $this->getName('balkon', $wohnung['balkon']),
                'garten'    => $this->getName('garten', $wohnung['garten']),
                'ebk'       => $this->getName('ebk2', $wohnung['ebk']),
                'etage'     => $wohnung['etage'] . '. Etage',
                'dusche'    => $this->getName('dusche', $wohnung['dusche']),
                'wanne'     => $this->getName('wanne', $wohnung['wanne']),
                'jumpTo'    => $url
            );
        }

        $this->Template->wohnungen = $wohnungen;
        $this->Template->ergebnis = $ergebnis;
    }

    /**
     * @param $wert
     * @param bool $quick
     * @return string
     */
    private function makeEuro($wert, $quick = true)
    {
        $temp = explode('.', $wert);
        $price = $temp[0] . ',';

        if (strlen($temp[1]) == 2)
        {
            $price .= $temp[1];
        }
        elseif (strlen($temp[1]) == 1)
        {
            $price .= $temp[1] . '0';
        }
        elseif (strlen($temp[1]) == 0)
        {
            $price .= '00';
        }

        if ($quick)
        {
            $price .= ' &euro;';
        }
        else
        {
            $price .= ' Euro';
        }

        return $price;
    }

    /**
     * @param $typ
     * @param $wert
     * @return null
     */
    private function getName($typ, $wert)
    {
        $names = array(
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

        if ($wert == '' || $wert == 'false')
        {
            return null;
        }
        else
        {
            return $names[$typ][$wert];
        }
    }
}
