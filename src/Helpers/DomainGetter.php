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
 * Creator: nirs | 2019-07-01
 */  

namespace Cloudonix\Helpers;

use Exception;
use Cloudonix\Client as Client;

class DomainGetter
{
	public $baseFilter;
	public $baseQuery;
	public $domainId;
	public $client;
	public $name;
	public $id;

	public function __construct(Client $client, $domainId)
	{
		try {
			if (!$client)
				throw new Exception('Datamodel Helper construction error', 500, null);

			$this->client = $client;
			$this->domainId = $domainId;
			$this->baseFilter = "?";
			$this->baseQuery = '/domains/' . $this->domainId;

		} catch (Exception $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		}
	}

	public function setDomainId($domainId) {
		$this->domainId = $domainId;
		return $this;
	}

	public function run() {
		$result = $this->client->httpRequest('GET', $this->baseQuery . '/domains' . $this->baseFilter);
		return $result;
	}

}