<?php
/*
 * This file is part of Contao Ivm Immo Collection.
 *
 * (c) Marko Cupic, april 2019
 * @author Marko Cupic <https://github.com/markocupic/ivm_immo_collection>
 * @license MIT
 */

namespace IvmImmoCollection;

use Contao\Database;
use Contao\Widget;

/**
 * Class ValidateFormFieldHook
 * @package IvmImmoCollection
 */
class ValidateFormFieldHook
{
    /**
     * Send is_details.objektnr
     * @param Widget $objWidget
     * @param $intId
     * @param $arrForm
     * @return Widget
     */
    public function validateFormField(Widget $objWidget, $intId, $arrForm)
    {
        // @see contao?do=form&table=tl_form_field&id=171&act=edit
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

                $arrItems = array_map(function ($id) {
                    $objRow = Database::getInstance()->prepare('SELECT * FROM is_details WHERE id=?')->limit(1)->execute($id);
                    if ($objRow->numRows)
                    {
                        return $objRow->objektnr;
                    }
                    else
                    {
                        return '';
                    }
                }, $arrFeaturedItems);

                $arrItems = array_filter(array_unique($arrItems));
                if (count($arrItems > 0))
                {
                    $objWidget->value = sprintf('Folgende Wohnungen befinden sich in der Merkliste: %s', implode(', ', $arrItems));
                }
                else
                {
                    $objWidget->value = sprintf('Es befinden sich keine Wohnungen in der Merkliste.');
                }
            }
        }

        return $objWidget;
    }
}
