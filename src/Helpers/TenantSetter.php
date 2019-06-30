<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | TenantSetter.php
 * Creator: nirs | 2019-06-30
 */

namespace Cloudonix;

use Exception;

class TenantSetter
{
	public $baseFilter = "?";
	public $baseQuery = false;
	public $client = false;
	public $name = false;
	public $id = false;

	private $apikeyId = false;
	private $action;
	private $actionData = [];

	public function __construct(Client $client, $action)
	{
		if (!$client)
			throw new Exception('Datamodel Helper construction error', 500, null);

		$this->client = $client;
		$this->action = $action;
		$this->baseQuery = '/tenants';
	}

	public function setName($name)
	{
		$this->name = $name;
		$this->actionData['name'] = $name;
		return $this;
	}

	public function setApikeyName($name)
	{
		$this->actionData['name'] = $name;
		return $this;
	}

	public function setProfileKey($key, $value)
	{
		$this->actionData['profile'][$key] = $value;
		return $this;
	}

	public function byId($id)
	{
		$this->id = $id;
		return $this;
	}

	public function byName($name)
	{
		$this->name = $name;
		return $this;
	}

	public function byApikeyId($id)
	{
		$this->apikeyId = $id;
		return $this;
	}

	public function run()
	{
		switch (strtolower($this->action)) {
			case "createapikey":
				if ((!$this->name) && (!$this->id))
					throw new MissingTenantNameException('`byTenantId|byTenantName` must be called before `run`');

				$result = $this->client->httpRequest('POST',
					$this->baseQuery . '/' . (($this->name) ? $this->name : $this->id) . '/apikeys',
					$this->actionData);
				break;
			case "updateapikey":
				if ((!$this->name) && (!$this->id))
					throw new MissingTenantNameException('`byTenantId|byTenantName` must be called before `run`');

				$result = $this->client->httpRequest('PUT',
					$this->baseQuery . '/' . (($this->name) ? $this->name : $this->id) . '/apikeys',
					$this->actionData);
				break;
			case "deleteapikey":
				if ((!$this->name) && (!$this->id))
					throw new MissingTenantNameException('`byTenantId|byTenantName` must be called before `run`');
				if (!$this->apikeyId)
					throw new MissinnApikeyIdException('`byApikeyId` must be called before `run`');

				$this->client->httpRequest('DELETE',
					$this->baseQuery . '/' . (($this->name) ? $this->name : $this->id) . '/apikeys/' . $this->apikeyId);
				return true;
				break;
			case "create":
				if (!$this->name)
					throw new MissingTenantNameException('`setName` must be called before `run`');

				$result = $this->client->httpRequest('POST', $this->baseQuery, $this->actionData);
				break;
			case "update":
				if ((!$this->id) && (!$this->name))
					throw new MissingTenantNameException('`byId|byName` must be called before `run`');

				$idOrName = ($this->id) ? $this->id : $this->name;
				$result = $this->client->httpRequest('PUT', $this->baseQuery . '/' . $idOrName, $this->actionData);
				break;
			case "delete":
			default:
				return false;
				break;
		}
		return json_decode((string)$result->getBody());

	}


}