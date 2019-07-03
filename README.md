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
composer require cloudonix/cloudonix-php
```

## Quickstart

### Send an SMS

```php
// Connect to the Cloudonix platform and Create a new domain in your tenant account
<?php

use Cloudonix\Client;
use Cloudonix\Exceptions\MissingDomainIdException;
use Cloudonix\Exceptions\MissingTenantIdException;
use Cloudonix\Exceptions\MissingTenantNameException;

try {
	$myCloudonixClient = new Client("cloudonix_provided_apikey");
	
	/* Get my own tenant information */
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

[apidocs]: http://webinc.cloudonix.io/cloudonix-php/index.html
[documentation]: https://docs.cloudonix.io/