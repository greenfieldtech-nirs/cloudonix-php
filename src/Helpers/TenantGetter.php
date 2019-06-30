<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | TenantGetter.php
 * Creator: nirs | 2019-06-30
 */

namespace Cloudonix\Helpers;

use Exception;
use Cloudonix\Client as Client;
use Cloudonix\Exceptions\MissingTenantNameException;
use Cloudonix\Exceptions\WorkflowViolation;

class TenantGetter
{
	public $baseFilter = "?";
	public $baseQuery = false;
	public $client = false;
	public $name = false;
	public $id = false;

	private $setting = false;
	private $apikeys = false;

	public function __construct(Client $client, $apikeys = false)
	{
		if (!$client)
			throw new Exception('Datamodel Helper construction error', 500, null);

		$this->client = $client;
		$this->apikeys = $apikeys;
		$this->baseFilter = "?";
		$this->baseQuery = '/tenants';
	}

	public function byName($name)
	{
		$this->name = $name;
		return $this;
	}

	public function byId($id)
	{
		return $this->byName($id);
	}

	public function bySetting($setting)
	{
		$this->setting = $setting;
		return $this;
	}

	public function run()
	{
		if (($this->setting) && (!$this->name))
			throw new MissingTenantNameException('`byName|byId` MUST be called before `bySetting`');

		if (($this->setting) && ($this->apikeys))
			throw new WorkflowViolation('`bySettings` and `getApiKeys` are mutually exclusive', 500, null);

		if (($this->apikeys) && (!$this->name))
			throw new WorkflowViolation('`byId|byName` MUST be called before `run()`', 500, null);

		$result = $this->client->httpRequest('GET',
			$this->baseQuery
			. (($this->name) ? '/' . $this->name : '')
			. (($this->setting) ? '/settings/' . $this->setting : '')
			. (($this->apikeys) ? '/apikeys' : '')
		);

		return json_decode((string)$result->getBody());

	}

}