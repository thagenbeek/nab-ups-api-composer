#UPS PHP Api

##Requirements
1. In order to use any of these apis you must first sign up for a UPS Developer account. (https://www.ups.com/upsdeveloperkit?loc=en_US)
2. For the Shipping rates API you need to open a UPS account. No credit card information is needed you just need an account number from UPS. (http://www.ups.com)

## Installation

### Manual Installation
1. Download and extract the project into an appropriate place in your application
2. Require the appropriate class in your code.
```require '/src/UpsApi/Tracking.php';```
```require '/src/UpsApi/Rate.php';```
```require '/src/UpsApi/Package.php'```
```require '/src/UpsApi/AddressValidation.php'```

### Installing via Composer
Composer is a dependency management tool for PHP that allows you to declare the dependencies your project needs and installs them into your project. In order to use the Constant Contact PHP SDK through composer, you must do the 
following

1. Add "itsjustalinkwebdesigns/ups" as a dependency in your project's composer.json file. 
```javascript
 {
        "require": {
            "itsjustalinkwebdesigns/ups": "dev"
        }
 }
``` 

2. Download and Install Composer. 
``` curl -s "http://getcomposer.org/installer" | php ``` 

3. Install your dependencies by executing the following in your project root. 
``` php composer.phar install ``` 

4. Require Composer's autoloader. Composer also prepares an autoload file that's capable of autoloading all of the classes in any of the libraries that it downloads. To use it, just add the following line to your code's bootstrap process. 
``` require 'vendor/autoload.php';```

##Usage

###Address Validation
```php
use UpsApi/AddressValidation;
$valid = new AddressValidation("UPS API Key", "UPS User Name", "UPS Password");
$valid->set_cosignee('Elvis Presley');
$valid->set_address('3734 Elvis Presley Blvd');
$valid->set_city('Memphis');
$valid->set_state('Tennessee');
$valid->set_zip('38116');
$valid->set_country('US');

$valid->validate();
	
if($valid->isValid())
{
	echo "Valid Address";
}else
{
	echo "Not a valid address";
}
```

###Shipping Rates
```php
use UpsApi\Rate;
use UpsApi\Package;

$my_rate = new Rate("UPS Api Key", "UPS User Name", "UPS Password");
$my_rate->setPickupType(1);
$my_rate->setCustomerClass(1);
	
//Shipper Information
$my_rate->setName(1, "Robert Evans");
$my_rate->setShipperNumber("FW9428");
$my_rate->setAddress(1, "705 Gazania Lane", "", "");
$my_rate->setCity(1, "Myrtle Beach");
$my_rate->setState(1, "SC");
$my_rate->setZip(1, "29579");
$my_rate->setCountry(1, "US");
$my_rate->setCountry(1, "US");
	
//From information
$my_rate->setState(2, "CA");
$my_rate->setZip(2, "92656");
$my_rate->setCountry(2, "US");
	
//To Information
$my_rate->setState(3, "MD");
$my_rate->setZip(3, "21093");
$my_rate->setCountry(3, "US");
	
$my_rate->setService(3);
	
$package = new Package();
	
$package->setType(2);
$package->setMeasurement(5,4,10,"IN");
$package->setWeight(1,"LBS");
$my_rate->addPackage($package);
	
$package->setType(2);
$package->setMeasurement(3,5,8,"IN");
$package->setWeight(2,"LBS");
$my_rate->addPackage($package);
	
$my_rate->get_rates();

echo "<pre>";
print_r($my_rate->toArray());
echo "</pre>";
```

###Tracking
```php
use UpsApi\Tracking;

$tracker = new Tracking("UPS API Key","UPS User Name","UPS Password");
	
$tracker->setTrackingNumber("UPS Tracking Number");

$tracker->track();

echo "<pre>";
print_r($tracker->toArray());
echo "</pre>";
```

##Minimum System Requirements
Use of this library requires PHP 5.3+, and PHP SOAP extension (http://php.net/manual/en/book.soap.php)
