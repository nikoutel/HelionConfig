HelionConfig
===========

**A versatile configuration parser. Can handle multiple configuration formats.** 

*HelionConfig* is a tool for reading (and soon, writing) different configuration types (INI, XML, JSON, apache, PHP arrays, generic conf), returning the whole configuration, a section, or a specific value.    

## Usage ##
```php
$helionConfig = new HelionConfig($options);
$configReader = $helionConfig->getConfigReader(ConfigType::XML);
$configSrc = 'file.xml';
$config = $configReader->getConfig($configSrc);
```
```php
$value = $configReader->getConfigValue($key, $config);
```
```php
$configAsArray = $config->asArray();
$configAsArrayFlat = $config->asArrayFlat();
```


### Parameters ###

***ConfigType***

A `ConfigType` parameter.

Available types are:
* `ConfigType::INI`
* `ConfigType::XML`
* `ConfigType::JSON`
* `ConfigType::PHPARRAY` *(configuration stored in a php array)* <sup>*</sup> 
* `ConfigType::APACHE` *(`apache2.conf` / `httpd.conf` type configuration)*
* `ConfigType::CONF` *(configurable generic configuration type, see options and examples)*

\* The PHPArray file should only include an array with the configuration data, and a `return` statement returning the array.

Method `$helionConfig->listConfigTypes()` returns all available config types.

***configSrc***

The configuration source could be a file path, URL, or a configuration string.

***options*** *(optional)*
  
An options array can be passed to the constructor of `HelionConfig`.  

Array elements:  
* `rootName`: name of the root object element  -  *Default: `configRoot`*  
* `sectionSeparator`: symbol used to denote the separation of subsections and value  -  *Default: `.`*  
* `libxmlOptions`: used to specify optional *libxml* parameters  -  *e.g.:* `LIBXML_PARSEHUGE, LIBXML_NOBLANKS` 
* `jsonOptions`: used to specify optional *json* constants  -  *e.g.:* `JSON_PRETTY_PRINT, JSON_NUMERIC_CHECK` 
* `curlOptions`: an array specifying additional *curlopt* constants -  *e.g.:* 
    * `CURLOPT_HTTPAUTH => CURLAUTH_BASIC,`
    * `CURLOPT_USERPWD => 'AzureDiamond:hunter2'` 
* `genericConf`: used to specify the symbols for the configurable generic configuration format  -  *Default:*    
    * `'sectionStart' => "["`
    * `'sectionEnd' => "]"`
    * `'equals' => "="`
    * `'multiLineSeparator' => "\\"`
    * `'commentStart' => ";"`
    
    
*Options Example:*    
```php
$options = array(
    'sectionSeparator' => ':',
    'rootName' => 'myConf',
    'libxmlOptions' => array(
        LIBXML_PARSEHUGE, LIBXML_NOBLANKS
    ),
    'jsonOptions' => array(
        JSON_PRETTY_PRINT, JSON_NUMERIC_CHECK
    ),
    'curlOptions' => array(
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD => 'AzureDiamond:hunter2'
    ),
    'genericConf' => array(
        'sectionStart' => "[",
        'sectionEnd' => "]",
        'equals' => "=",
        'multiLineSeparator' => "\\",
        'commentStart' => "#",
    )
);
```

### Return values ###

*HelionConfig* returns a `HelionConfigValue` Object representing configuration data.
All data are enclosed in a root object element named `configRoot` (can be changed through the options array).

Each directive and each section, is represented by an `HelionConfigValue` Object, and multiple subsections and values, are grouped in arrays of `HelionConfigValue` Objects.

Specific values or subsections can be returned with the `getConfigValue()` method, of the `HelionConfigValue` Object. 
Nested sections can be accessed by chaining the subsections with the `sectionSeparator` options array element 
(default is `.`) (*e.g.:* `section1.section2.key`)

To convert a `HelionConfigValue` Object to an array, the method `asArray()` is provided. 
Additionally the method `asArrayFlat()` returns a flat Array. 
Sections and value are separated with `sectionSeparator`.


## Requirements ##

* PHP 7 (min)
* SimpleXML php extension
* libxml php extension
* JavaScript Object Notation php extension (json)
* Client URL Library Object Notation php extension (curl)


## Install ##

**composer:**  
```
composer require nikoutel/helionconfig
```

## Examples ##
```php
require '/../vendor/autoload.php';
use Nikoutel\HelionConfig\HelionConfig;
use Nikoutel\HelionConfig\ConfigType\ConfigType;
```

<br />

**Example #1** (simple .ini file):

```ini
; db.ini
[owner]
name=John Doe
organization=Acme Widgets Inc.

[database]
server=192.0.2.62
port=143
```

```php
$configSrc = 'db.ini';

$helionConfig = new HelionConfig($options);
$configReader = $helionConfig->getConfigReader(ConfigType::INI);
$config = $configReader->getConfig($configSrc);

$port = $configReader->getConfigValue('database.port', $config);

```
*will output:*
```
$config:
Nikoutel\HelionConfig\HelionConfigValue Object
(
    [helionConfigName] => configRoot
    [helionConfigValue] => Array
        (
            [owner] => Nikoutel\HelionConfig\HelionConfigValue Object
                (
                    [helionConfigName] => owner
                    [helionConfigValue] => Array
                        (
                            [name] => Nikoutel\HelionConfig\HelionConfigValue Object
                                (
                                    [helionConfigName] => name
                                    [helionConfigValue] => John Doe
                                    [helionConfigAttributes] => 
                                )
                            [organization] => Nikoutel\HelionConfig\HelionConfigValue Object
                                (
                                    [helionConfigName] => organization
                                    [helionConfigValue] => Acme Widgets Inc.
                                    [helionConfigAttributes] => 
                                )
                        )
                    [helionConfigAttributes] => 
                )
            [database] => Nikoutel\HelionConfig\HelionConfigValue Object
                (
                    [helionConfigName] => database
                    [helionConfigValue] => Array
                        (
                            [server] => Nikoutel\HelionConfig\HelionConfigValue Object
                                (
                                    [helionConfigName] => server
                                    [helionConfigValue] => 192.0.2.62
                                    [helionConfigAttributes] => 
                                )
                            [port] => Nikoutel\HelionConfig\HelionConfigValue Object
                                (
                                    [helionConfigName] => port
                                    [helionConfigValue] => 143
                                    [helionConfigAttributes] => 
                                )
                        )
                    [helionConfigAttributes] => 
                )
        )
    [helionConfigAttributes] => 
)
```
and:

```php
$port = $configReader->getConfigValue('database.port', $config);
```
*will output:*
```
$port: 143
```

<br />

**Example #2** (config-like json passed as a string):

```php
$configSrc = '{ "isbn": "0-13-110362-8",
                "author": [
                     {"firstname": "Brian", "lastname": "Kernighan"},
                     {"firstname": "Dennies", "lastname": "Ritchie"}],
                 "title": "The C Programming Language",
                 "category": ["Programming", "Technology"]}';

$helionConfig = new HelionConfig($options);
$configReader = $helionConfig->getConfigReader(ConfigType::JSON);

$array = $configReader->getConfig($configSrc)->asArrayFlat();
```
*will output:*
```
Array
(
    [isbn] => 0-13-110362-8
    [author.0.firstname] => Brian
    [author.0.lastname] => Kernighan
    [author.1.firstname] => Dennies
    [author.1.lastname] => Ritchie
    [title] => The C Programming Language
    [category.0] => Programming
    [category.1] => Technology
)
```

<br />

**Example #3** (configurable generic configuration type):

```
# /etc/mysql/my.cnf
[mysqld_safe]
socket		= /var/run/mysqld/mysqld.sock
nice		= 0

[mysqld]
# * Basic Settings
user		= mysql
port		= 3306
```
MySQL uses INI type configuration files but with comments marked with the non-standard *‘#’*
```php
$options = array(
    'genericConf' => array(
        'commentStart' => "#",
    )
);

$helionConfig = new HelionConfig($options);
$configReader = $helionConfig->getConfigReader(ConfigType::CONF);
$config = $configReader->getConfig('/etc/mysql/my.cnf');

// get only the 'mysqld' section
$mysqldCofigSection = $configReader->getConfigValue('mysqld', $config);
```
*will output:*
```
$mysqldCofigSection:
Nikoutel\HelionConfig\HelionConfigValue Object
(
    [helionConfigName] => mysqld
    [helionConfigValue] => Array
        (
            [user] => Nikoutel\HelionConfig\HelionConfigValue Object
                (
                    [helionConfigName] => user
                    [helionConfigValue] => mysql
                    [helionConfigAttributes] => 
                )
            [port] => Nikoutel\HelionConfig\HelionConfigValue Object
                (
                    [helionConfigName] => port
                    [helionConfigValue] => 3306
                    [helionConfigAttributes] => 
                )
        )
    [helionConfigAttributes] => 
)
```


and:
```php
// get the 'port' from the 'mysqld' section
$port = $configReader->getConfigValue('port', $mysqldCofigSection);
```
*will output:*
```
$port: 3306
```
<br />

**Example #4** (apache vhost conf):

```ApacheConf
<VirtualHost *:80>
    DocumentRoot /var/www/api
    ServerName api.example.com
    <Directory /var/www/api>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

```php
$helionConfig = new HelionConfig();
$configReader = $helionConfig->getConfigReader(ConfigType::APACHE);
$config = $configReader->getConfig($configSrc);

$array = $config->asArray();
$arrayFlat = $config->asArrayFlat()
```
*will output:*
```
$array:
Array
(
    [helionConfigName] => configRoot
    [helionConfigValue] => Array
        (
            [VirtualHost] => Array
                (
                    [helionConfigName] => VirtualHost
                    [helionConfigValue] => Array
                        (
                            [DocumentRoot] => Array
                                (
                                    [helionConfigName] => DocumentRoot
                                    [helionConfigValue] => /var/www/api
                                )

                            [ServerName] => Array
                                (
                                    [helionConfigName] => ServerName
                                    [helionConfigValue] => api.example.com
                                )

                            [Directory] => Array
                                (
                                    [helionConfigName] => Directory
                                    [helionConfigValue] => Array
                                        (
                                            [AllowOverride] => Array
                                                (
                                                    [helionConfigName] => AllowOverride
                                                    [helionConfigValue] => All
                                                )
                                            [Require] => Array
                                                (
                                                    [helionConfigName] => Require
                                                    [helionConfigValue] => all granted
                                                )
                                        )
                                    [helionConfigAttributes] => /var/www/api
                                )
                        )
                    [helionConfigAttributes] => *:80
                )
        )
)

$arrayFlat:
Array
(
    [VirtualHost.DocumentRoot] => /var/www/api
    [VirtualHost.ServerName] => api.example.com
    [VirtualHost.Directory.AllowOverride] => All
    [VirtualHost.Directory.Require] => all granted
    [VirtualHost.Directory.@attribute] => /var/www/api
    [VirtualHost.@attribute] => *:80
)
```

<br />

## Todo ##
* YAML
* TOML
* Add writing/editing capabilities (getConfigWriter())
* Combine sources


## License ##
This software is licensed under the [MPL](http://www.mozilla.org/MPL/2.0/) 2.0:
```
    This Source Code Form is subject to the terms of the Mozilla Public
    License, v. 2.0. If a copy of the MPL was not distributed with this
    file, You can obtain one at http://mozilla.org/MPL/2.0/.
```
