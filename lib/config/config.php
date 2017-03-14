<?php
$GLOBALS['RESTFUL_WEBSERVICES']['ROUTING']['products'] = array
(
    // Define the webservice location (required definition)
    // Callable via http://localhost/contao-test/interface/products/12/my_token
    'pattern' => '/products/{token}/{id}',

    // Restrict methods (optional definition)
    // You can use GET, PUT, POST and DELETE
    #'methods' => array('GET', 'POST'),

    // Set requirements for the pattern values (optional definition)


    'requirements' => array
    (
        'id' => '\d+',
    ),
    

    // Restrict access by tokens (optional definition)
    'tokens' => array
    (
        'foobar',
    ),

    // Restrict access by ip addresses (optional definition)
    'ips' => array
    (
        '127.0.0.1',
    ),
);
