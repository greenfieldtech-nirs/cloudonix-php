<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | ${FILE_NAME}
 * Creator: nirs | 2019-06-30
 */

namespace Cloudonix;

use Exception;

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
		try {
			if (!$client)
				throw new DatamodelBuilderException('Datamodel Helper construction error', 500, null);

			$this->client = $client;
			$this->action = $action;
			$this->baseQuery = '/tenants/' . $client->tenantId;

		} catch (DatamodelBuilderException $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		} catch (Exception $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		}
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
		try {

			if ((!$this->domain) || (!$this->baseQuery))
				throw new MissingDomainIdException('`setDomainId|byDomainId` MUST be called before `run`', 500);

			switch (strtolower($this->action)) {
				case "create":
				case "update":
					$httpAction = (strtolower($this->action) == "create") ? "POST" : "PUT";
					$result = $this->client->httpRequest($httpAction, $this->baseQuery . '/trunks', $this->actionData);
					break;
				case "delete":

					if (!$this->trunkId)
						throw new MissingTrunkIdException('`setTrunkId|byTrunkId` MUST be called before `run`', 500);

					$this->client->httpRequest('DELETE', $this->baseQuery . '/trunks/' . $this->trunkId);
					return true;
					break;
				default:
					return false;
					break;
			}
			return json_decode((string)$result->getBody());

		} catch (MissingDomainIdException $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		} catch (MissingTrunkIdException $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		} catch (Exception $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		}
	}


}