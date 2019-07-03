<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝ 
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗ 
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | DomainGetter.php
 * Creator: Nir Simionovich <nirs@cloudonix.io> | 2019-07-01
 */  

namespace Cloudonix\Helpers;

use Exception;
use Cloudonix\Client as Client;
use Cloudonix\Exceptions\MissingDomainIdException;
use Cloudonix\Exceptions\WorkflowViolation;

class DomainGetter
{
	public $baseFilter;
	public $baseQuery;
	public $client;
	public $name;
	public $id;

	private $domainIdent = false;
	private $action = false;

	public function __construct(Client $client, $action = 'get')
	{
		try {
			if (!$client)
				throw new Exception('Datamodel Helper construction error', 500, null);

			$this->client = $client;
			$this->action = $action;
			$this->baseFilter = "?";
			$this->baseQuery = '/tenants/' . $client->tenantId . '/domains';

		} catch (Exception $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		}
	}

	public function byDomainId($param) {
		$this->domainIdent = (int)$param;
		return $this;
	}

	public function byDomainName($param) {
		$this->domainIdent = $param;
		return $this;
	}

	public function byDomain($param) {
		return (is_numeric($param)) ? $this->byDomainId($param) : $this->byDomainName($param);
	}

	public function run() {
		switch (strtolower($this->action)) {
			case "get":
				$result = $this->client->httpRequest('GET', $this->baseQuery . ((strlen($this->domainIdent)) ? '/' . $this->domainIdent : ''));
				break;
			case "listaliases":
				if ((!$this->domainIdent) || (!strlen($this->domainIdent)))
					throw new MissingDomainIdException('`byDomainId|byDomainName|byDomain` MUST be called before `run`', 500, null);
				$result = $this->client->httpRequest('GET', $this->baseQuery . '/' . $this->domainIdent . '/aliases');
				break;
			case "getapikeys":
				if ((!$this->domainIdent) || (!strlen($this->domainIdent)))
					throw new MissingDomainIdException('`byDomainId|byDomainName|byDomain` MUST be called before `run`', 500, null);
				$result = $this->client->httpRequest('GET', $this->baseQuery . '/' . $this->domainIdent . '/apikeys');
				break;
		}
		return $result;
	}

}