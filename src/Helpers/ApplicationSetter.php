<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝ 
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗ 
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | ApplicationSetter.php
 * Creator: Nir Simionovich <nirs@cloudonix.io> | 2019-07-02
 */  
namespace Cloudonix\Helpers;

use Exception;
use Cloudonix\Client as Client;
use Cloudonix\Exceptions\WorkflowViolation;
use Cloudonix\Exceptions\MissingDomainIdException;
use Cloudonix\Exceptions\MissingApplicationIdException;

class ApplicationSetter
{
	public $baseFilter = "?";
	public $baseQuery = false;
	public $client;
	public $name;
	public $id;

	private $action = false;
	private $actionData = [];
	private $domainIdent = false;
	private $apikeyKeyIdent = false;

	public function __construct(Client $client, $action)
	{
		try {
			if (!$client)
				throw new Exception('Datamodel Helper construction error', 500, null);

			$this->client = $client;
			$this->action = $action;
			$this->baseQuery = '/tenants/' . $client->tenantId;

		} catch (Exception $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		}
	}

	public function byId($param)
	{
		$this->id = (int)$param;
		return $this;
	}

	public function byName($param)
	{
		$this->id = $param;
		return $this;
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

	public function setDomainId($param)
	{
		$this->domainIdent = (int)$param;
		return $this;
	}

	public function setDomainName($param)
	{
		$this->domainIdent = $param;
		return $this;
	}

	public function setDomain($param)
	{
		return (is_numeric($param)) ? $this->setDomainId($param) : $this->setDomainName($param);
	}

	public function setApplicationId($param)
	{
		$this->id = (int)$param;
		return $this;
	}

	public function setName($param) {
		$this->actionData['name'] = $param;
		return $this;
	}

	public function setEndpoint($param) {
		$this->actionData['url'] = $param;
		return $this;
	}

	public function setType($param) {
		$this->actionData['type'] = $param;
		return $this;
	}

	public function setLangCXML() {
		return $this->setType('cloudonix');
	}

	public function setLangTwiml() {
		return $this->setType('twiml');
	}

	public function setActive($param)
	{
		$this->actionData['active'] = (boolean)$param;
		return $this;
	}

	public function setProfileKey($key, $value)
	{
		$this->actionData['profile'][$key] = $value;
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

		try {

			if ((!$this->domainIdent) || (!strlen($this->domainIdent)))
				throw new MissingDomainIdException('`setDomainId|setDomainName|setDomain` MUST be called before `run`', 500, null);

			switch (strtolower($this->action)) {
				case "create":
					$result = $this->client->httpRequest('POST',
						$this->baseQuery . '/domains/' . $this->domainIdent . '/applications',
						$this->actionData);
					break;
				case "update":
					if ((!$this->id) || (!strlen($this->id)))
						throw new MissingApplicationIdException('`byId` MUST be called before `run`', 500, null);

					$result = $this->client->httpRequest('PUT',
						$this->baseQuery . '/domains/' . $this->domainIdent . '/applications/' . $this->id,
						$this->actionData);
					break;
				case "delete":
					if ((!$this->id) || (!strlen($this->id)))
						throw new MissingApplicationIdException('`byId` MUST be called before `run`', 500, null);

					$result = $this->client->httpRequest('DELETE',
						$this->baseQuery . '/domains/' . $this->domainIdent . '/applications/' . $this->id);
					break;
				case "createapikey":
					if ((!$this->id) || (!strlen($this->id)))
						throw new MissingApplicationIdException('`setApplicationId` MUST be called before `run`', 500, null);

					if (!$this->actionData['name'])
						throw new WorkflowViolation('`setName` MUST be called before `run`', 500, null);

					$this->cleanActionData(['name']);
					$result = $this->client->httpRequest('POST',
						$this->baseQuery . '/domains/' . $this->domainIdent . '/applications/'. $this->id . '/apikeys',
						$this->actionData);

					break;
				case "updateapikey":
					if ((!$this->id) || (!strlen($this->id)))
						throw new MissingApplicationIdException('`byId` MUST be called before `run`', 500, null);

					if (!$this->actionData['name'])
						throw new WorkflowViolation('`setName` MUST be called before `run`', 500, null);

					$this->cleanActionData(['name','active']);
					$result = $this->client->httpRequest('PUT',
						$this->baseQuery . '/domains/' . $this->domainIdent . '/applications/'. $this->id . '/apikeys',
						$this->actionData);

					break;
				case "deleteapikey":
					if (!$this->apikeyKeyIdent)
						throw new WorkflowViolation('`byApikeyId|byApikeyName` MUST be called before `run`', 500, null);

					$result = $this->client->httpRequest('DELETE',
						$this->baseQuery . '/domains/' . $this->domainIdent . '/apikeys/' . $this->apikeyKeyIdent);
					break;
			}
			return $result;

		} catch (Exception $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		}
	}


}