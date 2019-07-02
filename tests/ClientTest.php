<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | ClientTest.php
 * Creator: Nir Simionovich <nirs@cloudonix.io> | 2019-06-26
 */

require '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
	public $cloudonixClient;

	public function testCanBeInitiatedDefault()
	{
		$this->cloudonixClient = new Cloudonix\Client();
		$this->assertClassHasAttribute('clientCacheHandler', 'Cloudonix\Client');
	}

	public function testCacheInitiatedCorrectly()
	{
		$this->cloudonixClient = new Cloudonix\Client();
		$this->assertNotFalse($this->cloudonixClient->cacheHandler);
	}
}
