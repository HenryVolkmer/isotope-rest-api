<?php
/**
 * Isotope eCommerce for Contao Open Source CMS
 *
 * Copyright (C) 2009-2017 terminal42 gmbh & Isotope eCommerce Workgroup
 * 
 * RESTful API for Isotope eCommerce
 * 
 * Copyright (C) 2017 Henry Lamorski
 * 
 * @author Henry Lamorski <henry.lamorski@mailbox.org>
 *
 * @link       https://isotopeecommerce.org
 * @link       https://github.com/HenryLamorski/isotope-rest-api
 * @license    https://opensource.org/licenses/lgpl-3.0.html
 */
 
class Helper
{
    /**
     * Standardize a parameter (strip special characters and convert spaces)
     * @param string
     * @param boolean
     * @return string
     */
    public static function standardize($strString, $blnPreserveUppercase=false)
    {
        $arrSearch = array('/[^a-zA-Z0-9 _-]+/', '/ +/', '/\-+/');
        $arrReplace = array('', '-', '-');
        $strString = preg_replace($arrSearch, $arrReplace, $strString);

        if (is_numeric(substr($strString, 0, 1)))
        {
                $strString = 'id-' . $strString;
        }
 
        if (!$blnPreserveUppercase)
        {
                $strString = strtolower($strString);
        }

        return trim($strString, '-');
    }
}
