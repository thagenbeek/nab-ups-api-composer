#UPS PHP Api

## Installation

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
``` require 'vendor/autoload.php';
```
