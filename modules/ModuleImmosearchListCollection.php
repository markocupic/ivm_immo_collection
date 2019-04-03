<?php

/**
 * Class ModuleImmosearchListCollection
 */
class ModuleImmosearchListCollection extends Module
{
    protected $strTemplate = 'mod_immosearch_listcollection';
    protected $arrFeaturedItems;
    protected $showDetail = false;
    private $wohngebiet;
    private $zimmer_von;
    private $zimmer_bis;
    private $size_von;
    private $size_bis;
    private $kalt_von;
    private $kalt_bis;
    private $wohngebiet_angepasst = '';
    private $zimmer_von_angepasst = '';
    private $zimmer_bis_angepasst = '';
    private $size_von_angepasst = '';
    private $size_bis_angepasst = '';
    private $kalt_von_angepasst = '';
    private $kalt_bis_angepasst = '';
    private $warm_von;
    private $warm_bis;
    private $searchAdjustKey = 0;
    private $page_anzahl;
    private $wohnungen_count;

    /**
     * ModuleImmosearchListCollection constructor.
     */
    public function __construct()
    {
        error_reporting(E_ALL);
        ini_set("display_errors", 0);
        if (isset($_GET['wid']))
        {
            $result = Database::getInstance()
                ->query("SELECT * FROM is_details WHERE id = '" . $_GET['wid'] . "'");
            $details = $result->fetchAssoc();

            if ($details['id'] != null)
            {
                $this->showDetail = true;
                $this->strTemplate = 'mod_immosearch_listresultdetail';
            }
        }
    }

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
            // Remove unique or empty values
            setrawcookie('ivm-collection', implode(',', $arrFeaturedItems));
        }
        // Return empty string if there is no collection data
        if (count($arrFeaturedItems) < 1)
        {
            //return '';
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
            $this->Template->ergebniss = null;
            return;
        }

        if ($this->showDetail) $this->makeDetail();
        else $this->makeList();


    }

    /**
     *
     */
    private function makeDetail()
    {
        $result = Database::getInstance()
            ->query("SELECT * FROM is_wohnungen WHERE wid = '" . $_GET['wid'] . "'");
        $wohnungData = $result->fetchAssoc();

        $result = Database::getInstance()
            ->query("SELECT * FROM is_details WHERE id = '" . $_GET['wid'] . "'");
        $details = $result->fetchAssoc();

        $result = Database::getInstance()
            ->query("SELECT * FROM is_wohngebiete WHERE id = '" . $wohnungData['gid'] . "'");
        $wohngebiet = $result->fetchAssoc();

        $result = Database::getInstance()
            ->query("SELECT * FROM is_ansprechpartner WHERE id = '" . $wohnungData['aid'] . "'");
        $ansprechpartner = $result->fetchAssoc();

        $pics_temp = explode(';', $details['pics']);

        $pics = array();
        for ($i = 0; $i < count($pics_temp); $i++)
        {
            if ($pics_temp[$i] != '')
            {
                $size = getimagesize('./Wohnungsangebote/' . $pics_temp[$i]);
                $pics[$i] = array(
                    'name'   => $pics_temp[$i],
                    'width'  => $size[0],
                    'height' => $size[1],
                );
            }
        }

        $wohnung['id'] = $wohnungData['id'];
        $wohnung['zimmer'] = $wohnungData['zimmer'];
        $wohnung['flaeche'] = ceil($wohnungData['flaeche']) . ' m²';
        $wohnung['warm'] = $this->makeEuro($wohnungData['warm']);
        $wohnung['kalt'] = $this->makeEuro($wohnungData['kalt']);
        $wohnung['title'] = $details['title'];
        $wohnung['str'] = $details['strasse'];
        $wohnung['hnr'] = $details['hnr'];
        $wohnung['plz'] = $details['plz'];
        $wohnung['ort'] = $details['ort'];
        $wohnung['wohngebiet'] = $wohngebiet['wohngebiet'];
        $wohnung['nebenkosten'] = $this->makeEuro($details['nk']);
        if ($details['hk'] == 0) $wohnung['heizkosten'] = null;
        else                             $wohnung['heizkosten'] = $this->makeEuro($details['hk']);
        $wohnung['stellplatz'] = $this->makeEuro($details['stellplatz']);
        $wohnung['expose'] = $details['expose'];
        $wohnung['energieausweis'] = $details['energie'];
        $wohnung['heizkosten_enthalten'] = $details['hk_in'];
        $wohnung['beschr'] = nl2br($details['beschr']);
        $wohnung['beschr_lage'] = nl2br($details['beschr_lage']);
        $wohnung['sonstige'] = nl2br($details['sonstige']);
        $wohnung['typ'] = $this->getName('typ', $details['typ']);
        $wohnung['etage'] = $wohnungData['etage'];
        $wohnung['dusche'] = $this->getName('dusche', $wohnungData['dusche']);
        $wohnung['wanne'] = $this->getName('wanne', $wohnungData['wanne']);
        $wohnung['fenster'] = $this->getName('fenster', $details['fenster']);
        $wohnung['ebk'] = $this->getName('ebk', $wohnungData['ebk']);
        $wohnung['offen'] = $this->getName('offen', $details['offen']);
        $wohnung['fliesen'] = $this->getName('fliesen', $details['fliesen']);
        $wohnung['kunststoff'] = $this->getName('kunststoff', $details['kunststoff']);
        $wohnung['parkett'] = $this->getName('parkett', $details['parkett']);
        $wohnung['teppich'] = $this->getName('teppich', $details['teppich']);
        $wohnung['laminat'] = $this->getName('laminat', $details['laminat']);
        $wohnung['dielen'] = $this->getName('dielen', $details['dielen']);
        $wohnung['stein'] = $this->getName('stein', $details['stein']);
        $wohnung['estrich'] = $this->getName('estrich', $details['estrich']);
        $wohnung['doppelboden'] = $this->getName('doppelboden', $details['doppelboden']);
        $wohnung['fern'] = $this->getName('fern', $details['fern']);
        $wohnung['etage_heizung'] = $this->getName('etage_heizung', $details['etage_heizung']);
        $wohnung['zentral'] = $this->getName('zentral', $details['zentral']);
        $wohnung['gas'] = $this->getName('gas', $details['gas']);
        $wohnung['oel'] = $this->getName('oel', $details['oel']);
        $wohnung['keller'] = $this->getName('keller', $details['keller']);
        $wohnung['lift'] = $this->getName('lift', $wohnungData['lift']);
        $wohnung['balkon'] = $this->getName('balkon', $wohnungData['balkon']);
        $wohnung['balkon_anz'] = $wohnungData['balkon'];
        $wohnung['baeder'] = $wohnungData['baeder'];
        $wohnung['anz_schlafzimmer'] = $details['anz_schlafzimmer'];
        $wohnung['garten'] = $this->getName('garten', $wohnungData['garten']);
        $wohnung['barrierefrei'] = $this->getName('barrierefrei', $details['barrierefrei']);
        $wohnung['wg'] = $this->getName('wg', $details['wg']);
        $wohnung['baujahr'] = $details['baujahr'];
        $wohnung['zustand'] = $this->getName('zustand', $details['zustand']);
        $wohnung['verfuegbar'] = $details['verfuegbar'];
        $wohnung['objektnr'] = $details['objektnr'];
        $wohnung['ap']['anrede'] = $ansprechpartner['anrede'];
        $wohnung['ap']['vorname'] = $ansprechpartner['vorname'];
        $wohnung['ap']['name'] = $ansprechpartner['name'];
        $wohnung['ap']['pic'] = null;
        $apDir = './files/themes/bootstrap-contao/content/images/anprechpartner';
        foreach (glob($apDir . "/*" . strtolower($ansprechpartner['name']) . "*") as $filename)
        {
            $wohnung['ap']['pic'] = $filename;
        }
        $wohnung['ap']['email'] = $ansprechpartner['email'];
        $wohnung['ap']['tel'] = $ansprechpartner['tel'];
        $wohnung['ap']['fax'] = $ansprechpartner['fax'];
        $wohnung['ap']['mobile'] = $ansprechpartner['mobile'];
        $wohnung['haustiere'] = $details['haustiere'];
        $wohnung['moebliert'] = $this->getName('moebliert', $details['moebliert']);
        $wohnung['rollstuhlgerecht'] = $this->getName('rollstuhlgerecht', $details['rollstuhlgerecht']);
        $wohnung['raeume_veraenderbar'] = $this->getName('raeume_veraenderbar', $details['raeume_veraenderbar']);
        $wohnung['wbs_sozialwohnung'] = $this->getName('wbs_sozialwohnung', $details['wbs_sozialwohnung']);
        $wohnung['e_typ'] = $this->getName('e_typ', $details['e_typ']);
        $wohnung['e_wert'] = str_replace(".", ",", $details['e_wert']);
        $wohnung['altbau'] = $this->getName('altbau', $details['altbau']);
        $wohnung['neubau'] = $this->getName('neubau', $details['neubau']);
        $wohnung['reinigung'] = $this->getName('reinigung', $details['reinigung']);
        $wohnung['senioren'] = $this->getName('senioren', $details['senioren']);
        $wohnung['gen_anteile'] = str_replace(".", ",", $details['gen_anteile']);

        $jumpTo = str_replace(array('&wid=' . $wohnungData['wid'], '?wid=' . $wohnungData['wid']), '', $_SERVER['REQUEST_URI']);

        // count clicks
        $result = Database::getInstance()
            ->query("SELECT * FROM is_detail_clicks WHERE objektnr = '" . $details['objektnr'] . "'");
        $clicksData = $result->fetchAssoc();
        if ($clicksData)
        {
            $result = Database::getInstance()
                ->query("UPDATE `is_detail_clicks` SET `clicks` = '" . ($clicksData['clicks'] + 1) . "' WHERE `is_detail_clicks`.`id` = " . $clicksData['id']);
        }
        else
        {
            $result = Database::getInstance()
                ->query("INSERT INTO `is_detail_clicks` (`id`, `objektnr`, `clicks`) VALUES (NULL, '" . $details['objektnr'] . "', '1');");
        }

        $clicksShowResult = Database::getInstance()
            ->query("SELECT c.* FROM is_detail_clicks c LEFT JOIN is_details d ON c.objektnr = d.objektnr WHERE d.objektnr IS NOT NULL AND c.objektnr != '" . $details['objektnr'] . "' ORDER BY c.clicks DESC LIMIT 0,8");
        $clicksShowData = $clicksShowResult->fetchAllAssoc();

        $clickData = array();
        $i = 0;
        foreach ($clicksShowData as $click)
        {
            $result = Database::getInstance()
                ->query("SELECT id, title, pics, strasse, hnr, plz, ort FROM is_details WHERE objektnr = '" . $click['objektnr'] . "'");
            $detailsClick = $result->fetchAssoc();

            $result = Database::getInstance()
                ->query("SELECT * FROM is_wohnungen WHERE wid = '" . $detailsClick['id'] . "'");
            $wohnungClickData = $result->fetchAssoc();

            $pics2 = explode(';', $detailsClick['pics']);
            $clickData[$i]['id'] = $wohnungClickData['id'];
            $clickData[$i]['wid'] = $wohnungClickData['wid'];
            $clickData[$i]['zimmer'] = $wohnungClickData['zimmer'];
            $clickData[$i]['flaeche'] = ceil($wohnungClickData['flaeche']) . ' m²';
            $clickData[$i]['kalt'] = $wohnungClickData['kalt'] . " &euro;";
            $clickData[$i]['title'] = $detailsClick['title'];
            $clickData[$i]['strasse'] = $detailsClick['strasse'];
            $clickData[$i]['hnr'] = $detailsClick['hnr'];
            $clickData[$i]['plz'] = $detailsClick['plz'];
            $clickData[$i]['ort'] = $detailsClick['ort'];
            $clickData[$i]['startbild'] = $pics2[0];
            $i++;
        }

        // was wurde nach Suche geklickt
        if (isset($_GET['searchid']))
        {
            $result = Database::getInstance()
                ->query("INSERT INTO `is_searches_clicks` (`id`, `searchid`, `clickTime`, `objektid`, `title`, `adresse`) VALUES (NULL, " . $_GET['searchid'] . ", CURRENT_TIMESTAMP, '" . $details['objektnr'] . "', '" . $details['title'] . "', '" . $details['strasse'] . " " . $details['hnr'] . ", " . $details['plz'] . " " . $details['ort'] . "');");
        }

        $this->Template->jumpTo = htmlspecialchars($jumpTo);
        $this->Template->phpself = htmlspecialchars($jumpTo);
        $this->Template->pics = $pics;
        $this->Template->wohnung = $wohnung;
        $this->Template->clickData = $clickData;
    }

    /**
     * @return array
     */
    private function getSearchResult()
    {
        if (is_array($this->arrFeaturedItems) && !empty($this->arrFeaturedItems))
        {
            $sql = sprintf('SELECT * FROM is_wohnungen WHERE id IN(%s) AND ', implode(',', $this->arrFeaturedItems));
        }
        else
        {
            // Suchanfrage
            $sql = "SELECT * FROM is_wohnungen WHERE ";
        }

        if ($this->wohngebiet != 'alle' && $this->wohngebiet != 'alleAdjusted' && isset($this->wohngebiet))
        {
            $sql .= "gid = '" . $this->wohngebiet . "' 
					AND ";
        }

        if ($this->zimmer_von != '') $zimmer_von = $this->zimmer_von;
        else $zimmer_von = 0;

        if ($this->zimmer_bis != '') $zimmer_bis = $this->zimmer_bis;
        else $zimmer_bis = 20;

        $sql .= "zimmer >= '" . $zimmer_von . "'
					AND zimmer <= '" . $zimmer_bis . "' ";

        if ($this->size_von != '') $sql .= " AND flaeche >= '" . $this->size_von . "'";
        if ($this->size_bis != '') $sql .= " AND flaeche <= '" . $this->size_bis . "'";
        if ($this->kalt_von != '') $sql .= " AND kalt >= '" . $this->kalt_von . "'";
        if ($this->kalt_bis != '') $sql .= " AND kalt <= '" . $this->kalt_bis . "'";
        if ($this->warm_von != '') $sql .= " AND warm >= '" . $this->warm_von . "'";
        if ($this->warm_bis != '') $sql .= " AND warm <= '" . $this->warm_bis . "'";

        switch ($_GET['sort_kind'])
        {
            case 'zimmer_up':
                $sql .= " ORDER BY zimmer ASC";
                break;
            case 'zimmer_down':
                $sql .= " ORDER BY zimmer DESC";
                break;
            case 'size_up':
                $sql .= " ORDER BY flaeche ASC";
                break;
            case 'size_down':
                $sql .= " ORDER BY flaeche DESC";
                break;
            case 'kalt_up':
                $sql .= " ORDER BY kalt ASC";
                break;
            case 'kalt_down':
                $sql .= " ORDER BY kalt DESC";
                break;
            case 'warm_up':
                $sql .= " ORDER BY warm ASC";
                break;
            case 'warm_down':
                $sql .= " ORDER BY warm DESC";
                break;
            default:
                $sql .= " ORDER BY kalt DESC";
                break;
        }

        $sql .= ", flaeche DESC";
        $result = Database::getInstance()
            ->query($sql);
        $wohnungen_count = count($result->fetchAllAssoc());
        $this->wohnungen_count = $wohnungen_count;
        $this->page_anzahl = ceil($wohnungen_count / 10);

        if (isset($_GET['page']) && $_GET['page'] <= $this->page_anzahl && $_GET['page'] > 0 && $_GET['page'] != "")
        {
            $this->Template->page = htmlspecialchars($_GET['page']);
            $sql .= " LIMIT " . ($_GET['page'] * 10 - 10) . ", 10";
        }
        else
        {
            $this->Template->page = 1;
            $sql .= " LIMIT 0, 10";
        }

        $result = Database::getInstance()
            ->query($sql);
        return $result->fetchAllAssoc();
    }

    /**
     *
     */
    private function makeList()
    {
        $this->wohngebiet = $_GET['wohngebiet'];
        $this->zimmer_von = $_GET['zimmer_von'];
        $this->zimmer_bis = $_GET['zimmer_bis'];
        $this->size_von = $_GET['size_von'];
        $this->size_bis = $_GET['size_bis'];
        $this->kalt_von = $_GET['kalt_von'];
        $this->kalt_bis = $_GET['kalt_bis'];

        $wohnungen_temp = $this->getSearchResult();
        while (count($wohnungen_temp) == 0)
        {
            $this->searchAdjustKey++;
            switch ($this->searchAdjustKey)
            {
                case 1:
                    if ($this->kalt_von != '')
                    {
                        $this->kalt_von -= $this->kalt_von * 0.2;
                        $this->kalt_von_angepasst = $this->kalt_von;
                    }
                    if ($this->kalt_bis != '')
                    {
                        $this->kalt_bis = $this->kalt_bis * 1.2;
                        $this->kalt_bis_angepasst = $this->kalt_bis;
                    }
                    $wohnungen_temp = $this->getSearchResult();
                    break;
                case 2:
                    if ($this->size_von != '')
                    {
                        $this->size_von -= $this->size_von * 0.2;
                        $this->size_von_angepasst = $this->size_von;
                    }
                    if ($this->size_bis != '')
                    {
                        $this->size_bis = $this->size_bis * 1.2;
                        $this->size_bis_angepasst = $this->size_bis;
                    }
                    $wohnungen_temp = $this->getSearchResult();
                    break;
                case 3:
                    if ($this->zimmer_von != '')
                    {
                        $this->zimmer_von--;
                        $this->zimmer_von_angepasst = $this->zimmer_von;
                    }
                    if ($this->zimmer_bis != '')
                    {
                        $this->zimmer_bis++;
                        $this->zimmer_bis_angepasst = $this->zimmer_bis;
                    }
                    $wohnungen_temp = $this->getSearchResult();
                    break;
                case 4:
                    if ($this->wohngebiet != 'alle')
                    {
                        $this->wohngebiet = 'alleAdjusted';
                        $this->wohngebiet_angepasst = 'alle';
                    }
                    $wohnungen_temp = $this->getSearchResult();
                    break;
                default:
                    break 2;
            }
        }

        $wohnungen = array();
        $ergebniss = false;
        foreach ($wohnungen_temp as $wohnung)
        {
            $ergebniss = true;
            $result = Database::getInstance()
                ->query("SELECT title, pics, strasse, hnr, plz, ort FROM is_details WHERE id = '" . $wohnung['wid'] . "'");
            $details = $result->fetchAssoc();

            $pics = explode(';', $details['pics']);

            $wohnungen[$wohnung['id']]['id'] = $wohnung['id'];
            $wohnungen[$wohnung['id']]['wid'] = $wohnung['wid'];
            $wohnungen[$wohnung['id']]['zimmer'] = $wohnung['zimmer'];
            $wohnungen[$wohnung['id']]['flaeche'] = ceil($wohnung['flaeche']) . ' m²';
            $wohnungen[$wohnung['id']]['warm'] = $this->makeEuro($wohnung['warm']);
            $wohnungen[$wohnung['id']]['kalt'] = $wohnung['kalt'] . " &euro;";
            $wohnungen[$wohnung['id']]['title'] = $details['title'];
            $wohnungen[$wohnung['id']]['strasse'] = $details['strasse'];
            $wohnungen[$wohnung['id']]['hnr'] = $details['hnr'];
            $wohnungen[$wohnung['id']]['plz'] = $details['plz'];
            $wohnungen[$wohnung['id']]['ort'] = $details['ort'];
            $wohnungen[$wohnung['id']]['startbild'] = $pics[0];
            $wohnungen[$wohnung['id']]['lift'] = $this->getName('lift', $wohnung['lift']);
            $wohnungen[$wohnung['id']]['balkon'] = $this->getName('balkon', $wohnung['balkon']);
            $wohnungen[$wohnung['id']]['garten'] = $this->getName('garten', $wohnung['garten']);
            $wohnungen[$wohnung['id']]['ebk'] = $this->getName('ebk2', $wohnung['ebk']);
            $wohnungen[$wohnung['id']]['etage'] = $wohnung['etage'] . '. Etage';
            $wohnungen[$wohnung['id']]['dusche'] = $this->getName('dusche', $wohnung['dusche']);
            $wohnungen[$wohnung['id']]['wanne'] = $this->getName('wanne', $wohnung['wanne']);
        }

        $this->Template->sort_kind = htmlspecialchars($_GET['sort_kind']);
        $jumpTo = $_SERVER['REQUEST_URI'];
        $jumpTo = str_replace('&sort_kind=warm_up', '', $jumpTo);
        $jumpTo = str_replace('&sort_kind=warm_down', '', $jumpTo);
        $jumpTo = str_replace('&sort_kind=kalt_up', '', $jumpTo);
        $jumpTo = str_replace('&sort_kind=kalt_down', '', $jumpTo);
        $jumpTo = str_replace('&sort_kind=size_up', '', $jumpTo);
        $jumpTo = str_replace('&sort_kind=size_down', '', $jumpTo);
        $jumpTo = str_replace('&sort_kind=zimmer_up', '', $jumpTo);
        $jumpTo = str_replace('&sort_kind=zimmer_down', '', $jumpTo);
        for ($i = 1; $i <= $this->page_anzahl; $i++) $jumpTo = str_replace('&page=' . $i, '', $jumpTo);

        if ($_GET['searchid'] == 'new')
        {
            $wohngebiet = 'alle';
            if ($_GET['wohngebiet'] != 'alle')
            {
                $result = Database::getInstance()
                    ->query("SELECT wohngebiet FROM is_wohngebiete WHERE id = " . $_GET['wohngebiet']);
                $wohngebiet_temp = $result->fetchAssoc();
                $wohngebiet = $wohngebiet_temp['wohngebiet'];
            }
            $searchid = Database::getInstance()
                ->query("INSERT INTO `is_searches` (`id`, `kalt_von`, `kalt_bis`, `size_von`, `size_bis`, `zimmer_von`, `zimmer_bis`, `wohngebiet`, `kalt_von_angepasst`, `kalt_bis_angepasst`, `size_von_angepasst`, `size_bis_angepasst`, `zimmer_von_angepasst`, `zimmer_bis_angepasst`, `wohngebiet_angepasst`, `ergebnisse`, `created`) VALUES (NULL, '" . $_GET['kalt_von'] . "', '" . $_GET['kalt_bis'] . "', '" . $_GET['size_von'] . "', '" . $_GET['size_bis'] . "', '" . $_GET['zimmer_von'] . "', '" . $_GET['zimmer_bis'] . "', '" . $wohngebiet . "', '" . $this->kalt_von_angepasst . "', '" . $this->kalt_bis_angepasst . "', '" . $this->size_von_angepasst . "', '" . $this->size_bis_angepasst . "', '" . $this->zimmer_von_angepasst . "', '" . $this->zimmer_bis_angepasst . "', '" . $this->wohngebiet_angepasst . "', '" . $this->wohnungen_count . "', " . time() . ");")
                ->__get(insertId);
            $jumpTo = str_replace('&searchid=new', '&searchid=' . $searchid, $jumpTo);
        }

        $this->Template->page_anz = $this->page_anzahl;
        $this->Template->phpself = htmlspecialchars($jumpTo);
        $this->Template->wohnungen = $wohnungen;
        $this->Template->ergebniss = $ergebniss;
        $this->Template->searchAdjustKey = $this->searchAdjustKey;
        $this->Template->kalt_von = $this->kalt_von;
        $this->Template->kalt_bis = $this->kalt_bis;
        $this->Template->size_von = $this->size_von;
        $this->Template->size_bis = $this->size_bis;
        $this->Template->zimmer_von = $this->zimmer_von;
        $this->Template->zimmer_bis = $this->zimmer_bis;
        $this->Template->wohngebiet = $this->wohngebiet;
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
        if (strlen($temp[1]) == 2) $price .= $temp[1];
        if (strlen($temp[1]) == 1) $price .= $temp[1] . '0';
        if (strlen($temp[1]) == 0) $price .= '00';
        if ($quick) $price .= ' &euro;';
        else $price .= ' Euro';
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
                'SOUTERRAIN'          => 'Souterrain',
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

        if ($wert == '' || $wert == 'false') return null;
        else return $names[$typ][$wert];
    }
}
