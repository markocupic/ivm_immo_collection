<?php
/**
 * Created by PhpStorm.
 * User: Marko
 * Date: 03.04.2019
 * Time: 12:49
 */

namespace IvmImmoCollection;


/**
 * Class ReplaceInsertTags
 * @package IvmImmoCollection
 */
class ReplaceInsertTags
{
    /**
     * @param $strTag
     * @return bool|string
     */
    public function replaceInsertTags($strTag)
    {
        if (strpos($strTag, 'ivmImmoCollectionToggleCollection') !== false)
        {
            $arrFeaturedItems = array();
            if (isset($_COOKIE['ivm-collection']))
            {
                if ($_COOKIE['ivm-collection'] != '')
                {
                    $arrFeaturedItems = explode(',', $_COOKIE['ivm-collection']);
                }
                // Remove unique or empty values
                setrawcookie('ivm-collection', implode(',', array_filter(array_unique($arrFeaturedItems))));
            }

            $arrTag = explode('::', $strTag);
            $wid = $arrTag[1];




            $objTemplate = new \FrontendTemplate('toggleCollection');
            $objTemplate->wid = $wid;
            $objTemplate->icon = 'unfeatured';
            $objTemplate->title = $GLOBALS['TL_LANG']['MSC']['ivmAddToCollection'];
            $objTemplate->moduleId = $GLOBALS['TL_LANG']['MSC']['ivmAddToCollection'];
            $objTemplate->itemSelector = '';
            if(isset($arrTag[2]))
            {
                $objTemplate->itemSelector = $arrTag[2];
            }

            if (in_array($wid, $arrFeaturedItems))
            {
                $objTemplate->featureClass = 'featured';
                $objTemplate->icon = 'featured';
                $objTemplate->title = $GLOBALS['TL_LANG']['MSC']['ivmRemoveFromCollection'];
            }
            return $objTemplate->parse();
        }

        return false;
    }
}