<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | TenantConnectorTest.php
 * Creator: Nir Simionovich <nirs@cloudonix.io> | 2019-06-26
 */

require '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class TenantConnectorTest extends TestCase
{
	public $cloudonixClient;
	public $cloudonixTenantConnector;

	public function testTenanntConnectorCanBeInitiated()
	{
		$this->cloudonixClient = new Cloudonix\Client();
		$this->cloudonixTenantConnector = new Cloudonix\Tenants\Connector('mymockapikey', $this->cloudonixClient);
		$this->assertClassHasAttribute('clientCacheHandler', 'Cloudonix\Tenants\Connector');
	}

}
