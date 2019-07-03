<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | TrunkSetter.php
 * Creator: Nir Simionovich <nirs@cloudonix.io> | 2019-06-30
 */

namespace Cloudonix\Helpers;

use Exception;
use Cloudonix\Client as Client;

class TrunkSetter
{
	public $baseFilter = false;
	public $baseQuery = false;
	public $domain = false;
	public $client = false;
	public $name = false;
	public $id = false;

	private $action;
	private $actionData = ['profile' => []];
	private $trunkId = false;

	public function __construct(Client $client, $action)
	{
		if (!$client)
			throw new DatamodelBuilderException('Datamodel Helper construction error', 500, null);

		$this->client = $client;
		$this->action = $action;
		$this->baseQuery = '/tenants/' . $client->tenantId;
	}

	public function setDomain($domain)
	{
		$this->domain = $domain;
		$this->baseQuery .= '/domains/' . $domain;
		return $this;
	}

	public function setDomainId($domainId)
	{
		return $this->setDomain($domainId);
	}

	public function byDomain($domainId)
	{
		return $this->setDomain($domainId);
	}

	public function byDomainId($domainId)
	{
		return $this->setDomain($domainId);
	}

	public function byTrunkId($trunkId)
	{
		$this->trunkId = $trunkId;
		return $this;
	}

	public function setTrunkId($trunkId)
	{
		return $this->byTrunkId($trunkId);
	}

	public function setName($name)
	{
		$this->actionData['name'] = $name;
		return $this;
	}

	public function setDirection($direction)
	{
		switch (strtolower($direction)) {
			case "public-inbound":
				$this->actionData['direction'] = 'public-inbound';
				break;
			case "public-outbound":
			default:
				$this->actionData['direction'] = 'public-outbound';
				break;
		}
		return $this;
	}

	public function setIPAddress($ipaddress)
	{
		$this->actionData['ip'] = $ipaddress;
		return $this;
	}

	public function setIPPort($ipport)
	{
		$this->actionData['port'] = $ipport;
		return $this;
	}

	public function setIP($string)
	{
		list($ipaddress, $ipport) = explode(":", $string);
		$this->actionData['ip'] = $ipaddress;
		$this->actionData['port'] = $ipport;
		return $this;
	}

	public function setPrefix($string)
	{
		$this->actionData['prefix'] = $string;
		return $this;
	}

	public function setTransport($string)
	{
		switch (strtolower($string)) {
			case "udp":
			case "tcp":
			case "tls":
				$this->actionData['transport'] = strtolower($string);
				break;
			default:
				$this->actionData['transport'] = 'udp';
				break;
		}
		return $this;
	}

	public function setProfileKey($key, $value)
	{
		$this->actionData['profile'][$key] = $value;
		return $this;
	}

	public function run()
	{
		if ((!$this->domain) || (!$this->baseQuery))
			throw new MissingDomainIdException('`setDomainId|byDomainId` MUST be called before `run`', 500, null);

		switch (strtolower($this->action)) {
			case "create":
			case "update":
				$httpAction = (strtolower($this->action) == "create") ? "POST" : "PUT";
				$result = $this->client->httpRequest($httpAction, $this->baseQuery . '/trunks', $this->actionData);
				break;
			case "delete":

				if (!$this->trunkId)
					throw new MissingTrunkIdException('`setTrunkId|byTrunkId` MUST be called before `run`', 500, null);

				$result = $this->client->httpRequest('DELETE', $this->baseQuery . '/trunks/' . $this->trunkId);
				break;
			default:
				return false;
				break;
		}
		return $result;
	}


}