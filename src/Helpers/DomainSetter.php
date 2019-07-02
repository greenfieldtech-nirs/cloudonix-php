<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | DomainSetter.php
 * Creator: Nir Simionovich <nirs@cloudonix.io> | 2019-07-01
 */
namespace Cloudonix\Helpers;

use Exception;
use Cloudonix\Client as Client;
use Cloudonix\Exceptions\MissingDomainIdException;
use Cloudonix\Exceptions\WorkflowViolation;

class DomainSetter
{
	public $baseFilter = "?";
	public $baseQuery = false;
	public $client = false;
	public $name = false;
	public $id = false;

	private $action;
	private $actionData = [];
	private $domainIdent = false;
	private $alias = false;
	private $apikeyKeyIdent = false;

	public function __construct(Client $client, $action)
	{
		try {
			if (!$client)
				throw new Exception('Datamodel Helper construction error', 500, null);

			$this->client = $client;
			$this->action = $action;
			$this->baseQuery = '/tenants/' . $client->tenantId . '/domains';

		} catch (Exception $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		}
	}

	public function setName($param)
	{
		$this->actionData['domain'] = $param;
		$this->actionData['name'] = $param;
		return $this;
	}

	public function setActive($param)
	{
		$this->actionData['active'] = (boolean)$param;
		return $this;
	}

	public function setHostedApplicationId($param)
	{
		$this->actionData['application'] = (int)$param;
		return $this;
	}

	public function setHostedApplicationName($param)
	{
		$this->actionData['application'] = $param;
		return $this;
	}

	public function setApplicationEndpoint($param)
	{
		return $this->setHostedApplicationName($param);
	}

	public function setCallTimeout($param)
	{
		$this->actionData['profile']['call-timeout'] = (int)$param;
		return $this;
	}

	public function setRegfreeControlEndpoint($param)
	{
		$this->actionData['profile']['registration-free-control-endpoint'] = $param;
		return $this;
	}

	public function setRegfreeControlApikey($param)
	{
		$this->actionData['profile']['registration-free-control-endpoint-api-key'] = $param;
		return $this;
	}

	public function setBorderToBorderCalls($param = false)
	{
		$this->actionData['profile']['allowed-border'] = (int)$param;
		return $this;
	}

	public function setUnknownToBorderCalls($param = true)
	{
		$this->actionData['profile']['redirect-unknown-to-border'] = (int)$param;
		return $this;
	}

	public function setSubscribersProgress($param = true)
	{
		$this->actionData['profile']['subscribers-auto-progress'] = (int)$param;
		return $this;
	}

	public function setLeastCallRoutingEndpoint($param)
	{
		$this->actionData['profile']['lcr-address'] = $param;
		return $this;
	}

	public function setCallerIdNamePassthrough($param = true)
	{
		$this->actionData['profile']['allow-passthrough-caller-name'] = (int)$param;
		return $this;
	}

	public function setDomainAlias($param)
	{
		$this->actionData['aliases'][] = $param;
		$this->actionData['alias'] = $param;
		return $this;
	}

	public function byDomainId($param)
	{
		$this->domainIdent = (int)$param;
		return $this;
	}

	public function byDomainName($param)
	{
		$this->domainIdent = $param;
		return $this;
	}

	public function byDomain($param)
	{
		return (is_numeric($param)) ? $this->byDomainId($param) : $this->byDomainName($param);
	}

	public function byAliasId($param)
	{
		$this->alias = (int)$param;
		return $this;
	}

	public function byAliasName($param)
	{
		$this->alias = $param;
		return $this;
	}

	public function byAlias($param)
	{
		return (is_numeric($param)) ? $this->byAliasId($param) : $this->byAliasName($param);
	}

	public function byApikeyId($param)
	{
		$this->apikeyKeyIdent = (int)$param;
		return $this;
	}

	public function byApikeyName($param)
	{
		$this->apikeyKeyIdent = $param;
		return $this;
	}

	public function byApikey($param)
	{
		return (is_numeric($param)) ? $this->byApikeyId($param) : $this->byApikeyName($param);
	}

	/**
	 * Clean the `actionData` from keys that may create an issue upon create or update methods
	 *
	 * @param array $keys An array of keys that are either allowed (only these will be in the actionData) or not-allowed (filtered out)
	 * @param bool $allowed TRUE for 'allowed keys' or FALSE for 'filtered keys'
	 */
	private function cleanActionData($keys, $allowed = true)
	{
		foreach ($this->actionData as $key => $value) {
			if (!$allowed) {
				if (in_array($key, $keys))
					unset($this->actionData[$key]);
			} else {
				if (!in_array($key, $keys))
					unset($this->actionData[$key]);
			}
		}
	}

	public function run()
	{
		switch (strtolower($this->action)) {
			case "create":
				if ((!array_key_exists('domain', $this->actionData)) || (!strlen($this->actionData['domain'])))
					throw new MissingDomainIdException('`setName` MUST be called before `run`', 500);
				$this->cleanActionData(['alias', 'name'], false);

				$result = $this->client->httpRequest('POST', $this->baseQuery, $this->actionData);
				break;
			case "update":
				if (!$this->domainIdent)
					throw new WorkflowViolation('`byDomain|byDomainId|byDomain` MUST be called before `run`', 500);
				$this->cleanActionData(['alias', 'name'], false);

				$result = $this->client->httpRequest('PUT', $this->baseQuery . '/' . $this->domainIdent, $this->actionData);
				break;
			case "delete":
				if (!$this->domainIdent)
					throw new WorkflowViolation('`byDomain|byDomainId|byDomain` MUST be called before `run`', 500);
				$result = $this->client->httpRequest('DELETE', $this->baseQuery . '/' . $this->domainIdent);
				break;
			case "createalias":
				if (!$this->domainIdent)
					throw new WorkflowViolation('`byDomain|byDomainId|byDomain` MUST be called before `run`', 500);
				$this->cleanActionData(['alias']);
				$result = $this->client->httpRequest('POST',
					$this->baseQuery . '/' . $this->domainIdent . '/aliases',
					$this->actionData);
				break;
			case "deletealias":
				if (!$this->domainIdent)
					throw new WorkflowViolation('`byDomain|byDomainId|byDomain` MUST be called before `run`', 500);
				if (!$this->alias)
					throw new WorkflowViolation('`byAlias|byAliasId|byAlias` MUST be called before `run`', 500);
				$result = $this->client->httpRequest('DELETE',
					$this->baseQuery . '/' . $this->domainIdent . '/aliases/' . $this->alias);
				break;
			case "createapikey":
				if (!$this->domainIdent)
					throw new WorkflowViolation('`byDomain|byDomainId|byDomain` MUST be called before `run`', 500);
				if (!$this->actionData['name'])
					throw new WorkflowViolation('`setName` MUST be called before `run`', 500);

				$this->cleanActionData(['name']);
				$result = $this->client->httpRequest('POST',
					$this->baseQuery . '/' . $this->domainIdent . '/apikeys',
					$this->actionData);

				break;
			case "updateapikey":
				if (!$this->domainIdent)
					throw new WorkflowViolation('`byDomain|byDomainId|byDomain` MUST be called before `run`', 500);
				if (!$this->actionData['name'])
					throw new WorkflowViolation('`setName` MUST be called before `run`', 500);

				$this->cleanActionData(['name']);
				$result = $this->client->httpRequest('PUT',
					$this->baseQuery . '/' . $this->domainIdent . '/apikeys',
					$this->actionData);

				break;
			case "deleteapikey":
				if (!$this->domainIdent)
					throw new WorkflowViolation('`byDomain|byDomainId|byDomain` MUST be called before `run`', 500);
				if (!$this->apikeyKeyIdent)
					throw new WorkflowViolation('`byApikeyId|byApikeyName|byApikey` MUST be called before `run`', 500);

				$result = $this->client->httpRequest('DELETE',
					$this->baseQuery . '/' . $this->domainIdent . '/apikeys/' . $this->apikeyKeyIdent);
				break;
			default:
				return false;
				break;
		}
		return $result;
	}


}