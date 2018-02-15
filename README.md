# Installation

you need composer to install this package.
since contao version 3.5, you can install the composer-client direct
via contao install-tool (/contao/install.php).

If composer is installed, you have to add my git-repo to your sources.
To achieve this task, follow this steps:

1. click "Package management" -> settings -> Experts mode
2. add in section "repositories" this piece of code and click "save"


```
{
	"type": "git",
	"url": "https://github.com/HenryLamorski/isotope-rest-api.git"
}
```
or use the "vcs" type for not using  proc_open which might be not available on your PHP installation

        {
            "url": "https://github.com/HenryLamorski/isotope-rest-api",
            "type": "vcs"
        }


3. search in Package management in Backend for "henrylamorski/isotope-rest-api" and install it.

# recommended environment

the package php-curl (php.net/manual/de/book.curl.php) is recommended.

# Examples

### get existing product by primary key 123
https://your-domain.de/interface/product/123

### get all existing products 
https://your-domain.de/interface/product/

Please note: fetching the whole product-db consumes a lot of memory!
If you get a error 500, check your Webserver logfile and php.ini.

### update/create script with php-curl

if you provide the "id"-Key, a update will be performed.
Therfore, is there no "id"-Key, a new record will be saved.

``` 
<?php
$rest_endpoint 	= 'https://your-domain.de/interface/';
$controler		= 'product';
$httpMethod		= 'POST';
$payLoad		= array(

	array(
		/** id: optional, if provided a update is performed **/
		'id'			=> 123,
		'pid'			=> 0,
		/** gid: group_id **/
		'gid'			=> 2,
		/** product type **/
		'type'			=> 1,
		/** categorys (site structure ids) **/
		'orderPages'	=> array(1,2,3)
		'sku'			=> 'a-test-import',
		'name'			=> 'Product name',
		'teaser'		=> 'Teaser',
		'description'	=> 'Product text',
		'images'		=> array(
			array(
				/** src as absolute path **/
				'src'       => '/var/www/your-domain.de/isotope/import',
				'filename'  => 'product_mainpic.png',
			),
			array(
				'src'       => '/var/www/your-domain.de/isotope/import',
				'filename'  => 'product_pic_1.png',
			)
		),
		'variants'		=> array(
			array(
				/** depending on your isotope settings **/
				'name'		=> 'Product variant name',
				'pricetier'	=> array(
					'tax_class'	=> 1,
					'tiers'		=> array(
						array(
							'min'	=> 1,
							'price'	=> 12.99
						),
						array(
							'min'	=> 50,
							'price'	=> 6.99
						),

					)
				), 
				/** map all your variant attributes here **/
			),		
		)
	)
);

$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, $rest_endpoint . $controler);
curl_setopt($curl, CURLOPT_VERBOSE, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $httpMethod);
curl_setopt($curl, CURLOPT_POSTFIELDS, $payLoad);

curl_setopt($curl, CURLOPT_HEADER, FALSE);
curl_setopt($curl, CURLOPT_TIMEOUT, 1800);

$response = curl_exec($curl);

if (curl_errno($curl))
{
	throw new Exception(curl_error($curl));
}

$status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

curl_close($curl);

$status = array(
	'status'    => $status,
	'data'      => json_decode($response),
);

echo "<pre>"; print_r($status); echo "</pre>";
```
