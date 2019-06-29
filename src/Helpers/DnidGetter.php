<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | DnidsGetter.php
 * Creator: nirs | 2019-06-29
 */

namespace Cloudonix;

use Exception;
use Cloudonix\Exceptions\MissingDomainIdException;
use Cloudonix\Exceptions\DatamodelBuilderException;

class DnidGetter
{
	public $baseFilter;
	public $baseQuery;
	public $domainId;
	public $dnids;
	public $client;
	public $name;
	public $id;

	public function __construct(Client $client)
	{
		try {
			if (!$client)
				throw new DatamodelBuilderException('Datamodel Helper construction error', 500, null);

			$this->client = $client;
			$this->baseFilter = "?";
			$this->baseQuery = '/domains/' . $this->domainId;

		} catch (Exception $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		}
	}

	public function byApplication($applicationId) {
		$this->baseQuery .= '/applications/' . $applicationId;
		return $this;
	}

	public function byActive($active) {
		$this->baseFilter .= 'by_active=' . (int)$active . '&';
		return $this;
	}

	public function byPrefix($prefix) {
		$this->baseFilter .= 'by_prefix=' . $prefix . '&';
		return $this;
	}

	public function byAsteriskPrefix($asteriskPrefix) {
		$this->baseFilter .= 'by_asterisk_compatible=' . $asteriskPrefix . '&';
		return $this;
	}

	public function setDomainId($domainId) {
		$this->domainId = $domainId;
		return $this;
	}

	public function run() {

		try {
			if (!$this->domainId)
				throw new MissingDomainIdException('`setDomainId` MUST be called before `run`', 500);

			$result = $this->client->httpRequest('GET', $this->baseQuery . '/dnids' . $this->baseFilter);
			return json_decode((string)$result->getBody());
		} catch (MissingDomainIdException $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		} catch (Exception $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		}
	}

}