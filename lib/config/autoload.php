<?php

ClassLoader::addNamespaces(array
(
	'IsotopeRest',
    'IsotopeRest\Webservice',
    'IsotopeRest\Model',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    'IsotopeRest\Model\Generic' => 'system/modules/isotope_rest/src/Model/Generic.php',
    'IsotopeRest\Webservice\WebserviceProducts' => 'system/modules/isotope_rest/src/Webservice/WebserviceProducts.php',
));
