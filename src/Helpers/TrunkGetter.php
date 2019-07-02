<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | TrunkGetter.php
 * Creator: nirs | 2019-06-30
 */

namespace Cloudonix\Helpers;

use Exception;
use Cloudonix\Client as Client;

class TrunkGetter
{
	public $baseFilter = "?";
	public $baseQuery = false;
	public $domain = false;
	public $client;
	public $name;
	public $id;

	public function __construct(Client $client)
	{
		if (!$client)
			throw new Exception('Datamodel Helper construction error', 500, null);

		$this->client = $client;
		$this->baseFilter = "?";
		$this->baseQuery = '/tenants/' . $client->tenantId;
	}

	public function byDomain($domain)
	{
		$this->domain = $domain;
		$this->baseQuery .= '/domains/' . $domain;
		return $this;
	}

	public function byDomainId($domainId)
	{
		return $this->byDomain($domainId);
	}

	public function byTrunkId($trunkId)
	{
		$this->baseFilter .= '/' . $trunkId;
		return $this;
	}

	public function run()
	{
		$result = $this->client->httpRequest('GET', $this->baseQuery . '/trunks' . $this->baseFilter);
		return $result;
	}

}