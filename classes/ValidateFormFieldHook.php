<?php
/*
 * This file is part of Contao Ivm Immo Collection.
 *
 * (c) Marko Cupic, april 2019
 * @author Marko Cupic <https://github.com/markocupic/ivm_immo_collection>
 * @license MIT
 */

namespace IvmImmoCollection;

use Contao\Widget;

/**
 * Class ValidateFormFieldHook
 * @package IvmImmoCollection
 */
class ValidateFormFieldHook
{
    /**
     * @param Widget $objWidget
     * @param $intId
     * @param $arrForm
     * @return Widget
     */
    public function validateFormField(Widget $objWidget, $intId, $arrForm)
    {
        if ($objWidget->name === 'merkliste_wohnungen')
        {
            $arrFeaturedItems = array();
            if (isset($_COOKIE['ivm-collection']))
            {
                if ($_COOKIE['ivm-collection'] != '')
                {
                    // Get $arrFeaturedItems by decoding $_COOKIE['ivm-collection']
                    $arrFeaturedItems = explode(',', base64_decode($_COOKIE['ivm-collection']));

                    // Remove unique or empty values
                    $arrFeaturedItems = array_filter(array_unique($arrFeaturedItems));

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
                }

                $strCookie = implode(',', $arrFeaturedItems);

                $objWidget->value = $strCookie;
            }
        }

        return $objWidget;
    }
}
