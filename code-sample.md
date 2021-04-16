---
description: Quick Usage
---

# Getting Started

### Install:

Use composer to install

```php
composer require mitmelon/guardtor
```

### Usage :

```php
require_once __DIR__."/vendor/autoload.php";

// Place GuardTor Class untop of your application
$guardTor = new GuardTor();
$guardTor->init();
//Your Application Code Here
```

### Custom Configuration Setup:

You can change GuardTor properties by calling it before calling the init\(\) method.

```php
//Allow GuardTor to create or modify .htaccess with added functionalities to prevent bad bots
//Default is false.
//Please make sure you only enable this on development for one request to prevent over-writeups
//Once request is complete from your browser, change $guardTor->createhtaccess = false;
//On production change to $guardTor->createhtaccess = false;
$guardTor->createhtaccess = true;
//Never block tor users
//Default is true.
$guardTor->blocktor = false;
//Set the block page url users will be redirected to once blocked
//Default is __DIR__.'/error.html';
$guardTor->blockLink = 'BLOCK_PAGE_URL';
//Prevent request block once limit is reached
//Default is true;
//Please note that setting this to true requires redis installed.
$guardTor->block_request = false;
//Set request limit per minute to reach before blocking request
//This could be used to prevent DDOS Attacks
//Default is 100 times per minutes
$guardTor->attempt = 100;
```

### Other Methods:

```php
/**
 * Validate IPV4 and IPV6 address
 * @param $ip string
 * @return boolean || string
 */
$guardTor->validate_ip($ip);
/**
 * Get device ID from every request including device fingerprint
 * @return array
 */
$guardTor->getDeviceInfo();
/**
 * Get request IP Address
 * @return string
 */
$guardTor->get_ip();
/**
 * Advance cleaning of strings from user inputs
 * @return string
 */
$guardTor->strip();
/**
 * Clean html inputs to prevent xss attacks
 * @return string
 */
$guardTor->filterHtml();
```

### Future Updates :

* Spam Detections/Blocker

## License

Released under the MIT license.

