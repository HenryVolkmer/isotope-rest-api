<?php
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
