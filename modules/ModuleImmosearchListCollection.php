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
                // Get $arrFeaturedItems by decoding $_COOKIE['ivm-collection']
                $arrFeaturedItems = explode(',', base64_decode($_COOKIE['ivm-collection']));

                // Clean array from invalid values
                $arrFeaturedItems = array_map(function ($el) {
                    if (is_numeric($el))
                    {
                        return $el;
                    }
                    else
                    {
                        return '';
                    }
                }, $arrFeaturedItems);
                $arrFeaturedItems = array_filter(array_unique($arrFeaturedItems));
            }

            // Remove unique or empty values and reset cookie
            $strCookie = implode(',', $arrFeaturedItems);
            // Base64 encode cookie string
            $strCookie = $strCookie != '' ? base64_encode($strCookie) : '';
            setrawcookie('ivm-collection', $strCookie);
        }

        $this->arrFeaturedItems = $arrFeaturedItems;

        return parent::generate();
    }

    /**
     * Generate the frontend module
     */
    protected function compile()
    {
        $this->Template->flats = null;

        if (count($this->arrFeaturedItems) < 1)
        {
            return;
        }

        $this->generateList();
    }

    /**
     * Generate the list
     */
    private function generateList()
    {
        $result = \Database::getInstance()->query('SELECT * FROM is_wohnungen WHERE id IN(' . implode(',', $this->arrFeaturedItems) . ') ORDER BY kalt DESC');

        if (!$result->numRows)
        {
            return;
        }

        $flats = array();
        $hasItems = false;
        foreach ($result->fetchAllAssoc() as $flat)
        {
            $hasItems = true;
            $result = \Database::getInstance()->prepare("SELECT * FROM is_details WHERE id = ?")->execute($flat['wid']);
            $details = $result->fetchAssoc();

            // Get pics
            $pics = explode(';', $details['pics']);
            // Get jumpTo url
            $url = '';
            $objPage = \PageModel::findByPk($this->jumpTo);
            if ($objPage !== null)
            {
                $url = $objPage->getFrontendUrl() . '?wid=' . $flat['wid'];
            }

            $flats[$flat['id']] = array(
                'id'        => $flat['id'],
                'wid'       => $flat['wid'],
                'zimmer'    => $flat['zimmer'],
                'flaeche'   => ceil($flat['flaeche']) . ' m²',
                'warm'      => $this->formatEuro($flat['warm']),
                'kalt'      => $flat['kalt'] . " &euro;",
                'title'     => $details['title'],
                'strasse'   => $details['strasse'],
                'hnr'       => $details['hnr'],
                'plz'       => $details['plz'],
                'ort'       => $details['ort'],
                'startbild' => $pics[0],
                'lift'      => $this->getLabel('lift', $flat['lift']),
                'balkon'    => $this->getLabel('balkon', $flat['balkon']),
                'garten'    => $this->getLabel('garten', $flat['garten']),
                'ebk'       => $this->getLabel('ebk2', $flat['ebk']),
                'etage'     => $flat['etage'] . '. Etage',
                'dusche'    => $this->getLabel('dusche', $flat['dusche']),
                'wanne'     => $this->getLabel('wanne', $flat['wanne']),
                'jumpTo'    => $url
            );
        }
        if ($hasItems && count($flats) > 0)
        {
            $this->Template->flats = $flats;
        }
    }

    /**
     * @param $value
     * @param bool $quick
     * @return string
     */
    private function formatEuro($value, $quick = true)
    {
        $temp = explode('.', $value);
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
     * @param $value
     * @return null
     */
    private function getLabel($type, $value)
    {
        if ($value == '' || $value == 'false')
        {
            return null;
        }

        // $GLOBALS['IVM_LABELS'] is stored in system/modules/ivm_immo_collection/config/config.php
        if (!isset($GLOBALS['IVM_LABELS'][$type][$value]))
        {
            return '';
        }
        else
        {
            return $GLOBALS['IVM_LABELS'][$type][$value];
        }
    }
}
