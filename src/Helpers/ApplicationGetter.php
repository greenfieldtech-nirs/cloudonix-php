<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝ 
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗ 
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | ApplicationGetter.php
 * Creator: Nir Simionovich <nirs@cloudonix.io> | 2019-07-03
 */  
namespace Cloudonix\Helpers;

use Exception;
use Cloudonix\Client as Client;
use Cloudonix\Exceptions\DatamodelBuilderException as DatamodelBuilderException;
use Cloudonix\Exceptions\MissingDomainIdException;

class ApplicationGetter
{
	public $baseFilter;
	public $baseQuery;
	public $client;
	public $name;
	public $id;

	private $domainIdent = false;
	private $applicationIdent = false;
	private $applicationType = false;
	private $applicationUrlEndpoint = false;
	private $applicationName = false;
	private $applicationId = false;
	private $action = false;

	public function __construct(Client $client, $action = 'get')
	{
		try {
			if (!$client)
				throw new Exception('Datamodel Helper construction error', 500, null);

			$this->client = $client;
			$this->action = $action;
			$this->baseFilter = "?";
			$this->baseQuery = '/tenants/' . $client->tenantId . '/domains/';

		} catch (Exception $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		}
	}

	public function byId($param) {
		$this->applicationId = (int)$param;
		return $this;
	}

	public function byName($param) {
		$this->applicationName = $param;
		return $this;

	}

	public function byUrlEndpoint($param) {
		$this->applicationUrlEndpoint = $param;
		return $this;
	}

	public function byType($param) {
		$this->applicationType = $param;
		return $this;
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
				if ((!$this->domainIdent) || (!strlen($this->domainIdent)))
					throw new MissingDomainIdException('`byDomainId|byDomainName|byDomain` MUST be called before `run`', 500);

				$this->baseQuery .= $this->domainIdent . '/applications';
				$this->baseFilter .= (($this->applicationId) ? 'by_id=' . $this->applicationId : '') . '&';
				$this->baseFilter .= (($this->applicationName) ? 'by_name=' . $this->applicationName : '') . '&';
				$this->baseFilter .= (($this->applicationUrlEndpoint) ? 'by_url=' . $this->applicationUrlEndpoint : '') . '&';
				$this->baseFilter .= (($this->applicationType) ? 'by_type=' . $this->applicationType : '');

				$result = $this->client->httpRequest('GET',
					$this->baseQuery . $this->domainIdent . $this->baseFilter);
				break;
			case "getapikeys":
				if ((!$this->domainIdent) || (!strlen($this->domainIdent)))
					throw new MissingDomainIdException('`byDomainId|byDomainName|byDomain` MUST be called before `run`', 500);

				$result = $this->client->httpRequest('GET', $this->baseQuery . '/' . $this->domainIdent . '/apikeys');
				break;
		}
		return $result;
	}


}