# cloudonix-php

## What is this about?
Cloudonix is not your typical communications platform, it is a Communications Infrastructure as a Service platform. As 
such, it exposes a comprehensive command and control API, allowing developers to directly interact with the Cloudonix
internal datamodels. These datamodels are the primary building blocks of the Cloudonix framework - and are the core of 
working with the platform. 

The `cloudonix-php` library provides a rapid development framework for PHP developers, to rapidly interact with the 
command and control API - and in accordance to the Cloudonix datamodels security paradigms.

## Installation

You can install **cloudonix-php** via composer or by downloading the source.

#### Via Composer:

**cloudonix-php** is available on Packagist as the
[`cloudonix/cloudonix-php`](http://packagist.org/packages/cloudonix/cloudonix-php) package:

```
composer require cloudonix/cloudonix-php @dev
```

## Quickstart
### before anything else
Before issuing any `primitive` (as describer below), the `->getSelf()` method MUST be called. This method will both validate
your API key credentials and populate the required tenant information for continued operations.

### cloudonix-php primitives
The library consists of several key `primitives` that are used across the entire library, these are:

- Primary data model primitives (applicable to `tenant`, `domain`, `subscriber`, `application`, `dnid` and `trunk` )
  - `create` - Create a data model object.
  - `delete` - Delete a data model object.
  - `update` - Update a data model object. 
  - `get` - Get a data model object. 
- Secondary data model primitives (these normally apply to API keys, created for a specific data model object)
  - `createApikey` - Create an API key, associated with a specific data model object.
  - `deleteApikey` - Delete an API key, associated with a specific data model object.
  - `updateApikey` - Update an API key, associated with a specific data model object.
  - `getApikeys` - Get a list of API keys, associated with a specific data model object.
- Special data model primitives
  - Special `domain` primitives
    - `createAlias` - Create a domain alias for a specific domain data model object.
    - `deleteAlias` - Delete a domain alias for a specific domain data model object.
    - `listAliases` - List the aliases for a specific domain data model object.

Primitives are invoked by declaring the primitive intention (eg. `->create()`), followed by a set of `setX` and/or `byX` 
rules. For example:

```php
$myNewDomain = $myCloudonixClient->domains()  // Invoke the `domains` data model
	->create()                                // Invoke the `create` primitive
	->setName('my-domain.com')                // Set the new domain name to 'my-domain.com'
	->setActive(true)                         // Set the new domain status to `active`
	->setDomainAlias('my-domain-alias.com')   // Set the new domain with an alias of 'my-domain-alias.com'
	->setCallTimeout(30)                      // Set the global domain ringing timeout to 30 seconds
	->setUnknownToBorderCalls(false)          // Set routing of unknown DNID to the border gateway
	->run();                                  // Execute the creation transaction
```

Each primitive ensures that invoking it will always be performed using a minimal set of rules. If the rules are not met,
the library will automatically generate an Exception. We recommend examining the library documentation, available [here][phpcloudonix].

### Create a new domain in your tenant account

```php
// Connect to the Cloudonix platform and Create a new domain in your tenant account
<?php

require 'vendor/autoload.php';  /* Change this to the location of your vendor */

use Cloudonix\Client;
use Cloudonix\Exceptions\MissingDomainIdException;
use Cloudonix\Exceptions\MissingTenantIdException;
use Cloudonix\Exceptions\MissingTenantNameException;

try {
	$myCloudonixClient = new Client("cloudonix_provided_apikey");
	
	/* Get my own tenant information */
	/* It is mandatory to issue `->getSelf()` before other operations - otherwise, this will result in a security violation */
	$mySelf = $myCloudonixClient->getSelf();
	var_dump($mySelf);
	
	/* Create a new domain in my own tenant */
	$myNewDomain = $myCloudonixClient->domains()
		->create()
		->setName('my-domain.com')
		->setActive(true)
		->setDomainAlias('my-domain-alias.com')
		->setCallTimeout(30)
		->setUnknownToBorderCalls(false)
		->run();
	var_dump($myNewDomain);
    
	/* Get a list of the domains associated with my tenant */
	$myDomains = $myCloudonixClient->domains()
		->get()
		->run();
	var_dump($myDomains);
		
} catch (Cloudonix\Exceptions\MissingDomainIdException $e) {
	die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
} catch (Cloudonix\Exceptions\MissingTenantIdException $e) {
	die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
} catch (Cloudonix\Exceptions\MissingTenantNameException $e) {
	die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
} catch (Cloudonix\Exceptions\WorkflowViolationBadResponse $e) {
	die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
} catch (Exception $e) {
	die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
}
```

### Get a list of my tenant domains

```php
...
	$myCloudonixClient = new Client("cloudonix_provided_apikey");	
	print_r($myCloudonixClient->domains()->get()->run());
...
```

### Create a new domain and generate a new API key for it

```php
...
	/* Create the domain */
	$myDomains = $myCloudonixClient->domains()
		->create()
		->setName('my-test-domain.domain.com')
		->setActive(false)
		->setDomainAlias('my-test-domain-alias.domain.com')
		->setCallTimeout(30)
		->setUnknownToBorderCalls(false)
		->run();
	print_r($myDomains);
	
	/* Update the domain to `active` */
	print_r($myCloudonixClient->domains()
		->update()
		->byDomainId($myDomains->id)
		->setActive(true)
		->run()
	);
	
	/* Create a domain alias */
	$myDomainAlias = ($myCloudonixClient->domains()
		->createAlias()
		->byDomainId($myDomains->id)
		->setDomainAlias('my-test-domain-alias-2.domain.com')
		->run());
	print_r($myDomainAlias);

	/* Delete the domain alias */
	print_r($myCloudonixClient->domains()
		->deleteAlias()
		->byDomainId($myDomains->id)
		->byAliasId($myDomainAlias->id)
		->run());

	/* Create a domain API key */
	$myDomainApikey = $myCloudonixClient->domains()
		->createApikey()
		->byDomainId($myDomains->id)
		->setName('my-domain-apikey-' . $myDomainTime)
		->run();
		
	print_r($myDomainApikey);
...
```
Please note that your assigned API key from Cloudonix is a tenant API key. API keys created via the various
`->createApikey()` methods will create keys that are associated to their respective data model. 

## Documentation

The documentation for the Cloudonix API is located [here][apidocs].

The PHP library documentation can be found [here][documentation].

## Prerequisites

* PHP >= 7.1
* The PHP JSON extension

## Getting help

If you require assistance or had identified a bug in the library, please open an `issue` via the issue tracker of the
project.

[documentation]: http://webinc.cloudonix.io/cloudonix-php/index.html
[apidocs]: https://docs.cloudonix.io/
[phpcloudonix]: http://webinc.cloudonix.io/php-cloudonix/index.html
