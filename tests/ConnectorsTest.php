<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * @Project cloudonix-php | ConnectorsTest.php
 * Creator: Nir Simionovich <nirs@cloudonix.io> | 2019-06-26
 */

require '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

$env = getenv();

class ConnectorsTest extends TestCase
{
	public $cloudonixClient;

	public function testTenantConnector()
	{
		global $env;

		$this->cloudonixClient = new Cloudonix\Client($env['CXKEY']);
		$this->assertClassHasAttribute('tenantId', 'Cloudonix\Client');
		$this->assertClassHasAttribute('httpEndpoint', 'Cloudonix\Client');
		$this->assertClassHasAttribute('httpConnector', 'Cloudonix\Client');

		$this->tenantDatamodel = $this->cloudonixClient->tenants();
		$this->assertClassHasAttribute('tenantGetter', 'Cloudonix\Datamodels\Tenants');
		$this->assertClassHasAttribute('tenantSetter', 'Cloudonix\Datamodels\Tenants');
	}

	public function testDomainConnector()
	{
		global $env;

		$this->cloudonixClient = new Cloudonix\Client($env['CXKEY']);
		$this->assertClassHasAttribute('tenantId', 'Cloudonix\Client');
		$this->assertClassHasAttribute('httpEndpoint', 'Cloudonix\Client');
		$this->assertClassHasAttribute('httpConnector', 'Cloudonix\Client');

		$this->domainsDatamodel = $this->cloudonixClient->domains();
		$this->assertClassHasAttribute('domainGetter', 'Cloudonix\Datamodels\Domains');
		$this->assertClassHasAttribute('domainSetter', 'Cloudonix\Datamodels\Domains');
	}

	public function testApplicationsConnectors()
	{
		global $env;

		$this->cloudonixClient = new Cloudonix\Client($env['CXKEY']);
		$this->assertClassHasAttribute('tenantId', 'Cloudonix\Client');
		$this->assertClassHasAttribute('httpEndpoint', 'Cloudonix\Client');
		$this->assertClassHasAttribute('httpConnector', 'Cloudonix\Client');

		$this->applicationsDatamodel = $this->cloudonixClient->applications();
		$this->assertClassHasAttribute('applicationGetter', 'Cloudonix\Datamodels\Applications');
		$this->assertClassHasAttribute('applicationSetter', 'Cloudonix\Datamodels\Applications');
	}

	public function testSubscribersConnectors()
	{
		global $env;

		$this->cloudonixClient = new Cloudonix\Client($env['CXKEY']);
		$this->assertClassHasAttribute('tenantId', 'Cloudonix\Client');
		$this->assertClassHasAttribute('httpEndpoint', 'Cloudonix\Client');
		$this->assertClassHasAttribute('httpConnector', 'Cloudonix\Client');

		$this->subscribersDatamodel = $this->cloudonixClient->subscribers();
		$this->assertClassHasAttribute('subscriberGetter', 'Cloudonix\Datamodels\Subscribers');
		$this->assertClassHasAttribute('subscriberSetter', 'Cloudonix\Datamodels\Subscribers');
	}

	public function testDnidsConnectors()
	{
		global $env;

		$this->cloudonixClient = new Cloudonix\Client($env['CXKEY']);
		$this->assertClassHasAttribute('tenantId', 'Cloudonix\Client');
		$this->assertClassHasAttribute('httpEndpoint', 'Cloudonix\Client');
		$this->assertClassHasAttribute('httpConnector', 'Cloudonix\Client');

		$this->dnidDatamodel = $this->cloudonixClient->dnids();
		$this->assertClassHasAttribute('dnidGetter', 'Cloudonix\Datamodels\Dnids');
		$this->assertClassHasAttribute('dnidSetter', 'Cloudonix\Datamodels\Dnids');
	}

	public function testTrunksConnectors()
	{
		global $env;

		$this->cloudonixClient = new Cloudonix\Client($env['CXKEY']);
		$this->assertClassHasAttribute('tenantId', 'Cloudonix\Client');
		$this->assertClassHasAttribute('httpEndpoint', 'Cloudonix\Client');

		$this->trunksDatamodel = $this->cloudonixClient->trunks();
		$this->assertClassHasAttribute('trunkGetter', 'Cloudonix\Datamodels\Trunks');
		$this->assertClassHasAttribute('trunkSetter', 'Cloudonix\Datamodels\Trunks');
		$this->assertClassHasAttribute('httpConnector', 'Cloudonix\Client');
	}
}

