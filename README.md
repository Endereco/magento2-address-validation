**Endereco Address Validation Module for Magento 2.4+**
GDPR compliant International Address Validation for over 180 countries for Magento 2.4, using Endereco as Webservice for validation in realtime.

**IMPORTANT NOTE**
A separate Endereco account ist required to receive an API-Key. This key is required to activate and use the module.
(https://www.endereco.de/magento/)

Endereco Services are free of charge for small businesses with up to 250 address-validations per month
For a higher amount of requests ( > 250 address validations per month), we offer contracts billed monthly following the real usage of the service. All information is available here: 
(https://www.endereco.de/magento/)

**Content**
**- Feature description**
**- Installation**
**- new installation**
**- updating the module**
**- Adding autocomplete configuration to Webserver**
**- Configuration**
**- Testing**

Features
 Purpose:
Endereco Adressmanagement-Services are developed to reduce manual address corrections and delivery problems for Magento Shop operators. With automated validations and proposals, customers are enforced to correct mistyped addresses with a click.
This module supports the following features:
- International address validation with automated correction proposals for over 180 countries for
	- billing address 	- shipping address 
International address autocomplete for ZIP-Code, City, and Street
Automatically corrects small typos without user interactions
Proposes alternatives in case of address-errors
-  E-Mail address validation
-  Phone number validation and formatting
Name validation and formatting (Firstname + Lastname)
A demo installation is available here: 
https://magento.endereco-qa.de

Installation
Install via composer:

composer require devcccc/magento2-address-validation

php bin/magento module:enable Endereco_Addressvalidation
php bin/magento setup:upgrade

//optional on production servers:

bin/magento setup:di:compile
bin/magento setup:static-content:deploy

2.2 Update module
To update module over composer:

//download modul
composer update devcccc/magento2-address-validation

//activate module
php bin/magento setup:upgrade

//optional on production servers:
bin/magento setup:di:compile
bin/magento setup:static-content:deploy

After installation:
php bin/magento module:enable Endereco_Addressvalidation
php bin/magento setup:upgrade

If your Magento installation is running into production mode you have also to deploy the static content files:
php bin/magento setup:static-content:deploy
For development systems we suggest a cleanup of the generated and pub/static/frontend-folders:
rm -Rf generated pub/static/frontend

Direct requests  (strongly recommend)
Important: to use the faster direct requests additional webserver configuration is required.
For nginx you have to add a new location-block within your nginx-Site-Configuration. For Apache you have to copy the DirectProxy.php-file and add a Rewrite in the .htaccess.
For further instructions see below.

Apache Webserver
After installation/update copy the DirectProxy-File to the pub-directory:
cd {{YOUR MAGENTO MAIN DIRECTORY}}
cp vendor/devcccc/magento2-address-validation/Controller/Proxy/DirectProxy.php pub/
Edit the .htaccess in the pub-folder. Add a rewrite for the DirectProxy.php. Its required that this rewrite is done before the index.php rewrite.
Add the following line to pub/.htaccess:
RewriteRule cccc_adressvalidation/direct DirectProxy.php [L]
To do this before the index.php-rewrite your pub/.htaccess might look like:
....
RewriteRule cccc_adressvalidation/direct DirectProxy.php [L]

############################################
## Never rewrite for existing files, directories and links
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-l
....
Now enable the direct requests in the settings of the extension within the Magento Admin-Backend.
Menu: Stores => Configuration. Select "Endereco Config" => Address validation with Endereco => "Use direct requests" => Yes

nginx Webserver
For nginx a new location-Block within your nginx-site-configuration is required. Add this location:
location /cccc_adressvalidation/direct {
    root $MAGE_ROOT/app/code/Endereco/Addressvalidation/Controller/Proxy;
    index DirectProxy.php;
    fastcgi_pass   fastcgi_backend;
    fastcgi_index  DirectProxy.php;
    fastcgi_param  SCRIPT_FILENAME  $document_root/DirectProxy.php;
    include        fastcgi_params;
}
This location-Block should be placed directly in the server-block for the Magento-shop.
Now enable the direct requests in the settings of the extension within the Magento Admin-Backend.
Menu: Stores => Configuration. Select "Endereco Config" => Address validation with Endereco => "Use direct requests" => Yes

—-

If you need further assistance, contact us at info@endereco.de or by phone: +49 931 6639 839 0

Endereco UG (Haftungsbeschränkt) – 
Gesellschaft für Master Data Quality Management
Balthasar-Neumann-Str. 4b
97236 Randersacker
Deutschland
