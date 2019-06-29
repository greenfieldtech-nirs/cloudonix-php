<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | DnidSetter.php
 * Creator: nirs | 2019-06-29
 */

namespace Cloudonix;

use Exception;
use Cloudonix\Exceptions\MissingAdditionalDataException;
use Cloudonix\Exceptions\MissingApplicationIdException;
use Cloudonix\Exceptions\MissingDnidIdException;
use Cloudonix\Exceptions\MissingDomainIdException;
use Cloudonix\Exceptions\DatamodelBuilderException;

class DnidSetter
{
	public $baseFilter;
	public $baseQuery;
	public $domainId = false;
	public $dnids;
	public $client;
	public $name;
	public $id;

	private $applicationId = false;
	private $dnidNumber = false;
	private $dnidSource = false;
	private $dnidId = false;
	private $action;
	private $actionData = [];

	public function __construct(Client $client, $action)
	{
		try {
			if (!$client)
				throw new DatamodelBuilderException('Datamodel Helper construction error', 500, null);

			$this->client = $client;
			$this->action = $action;
			$this->baseQuery = '/domains/' . $this->domainId;

		} catch (DatamodelBuilderException $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		} catch (Exception $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		}
	}

	public function setApplicationId($applicationId)
	{
		$this->applicationId = $applicationId;
		$this->baseQuery .= '/applications/' . $applicationId . '/dnids';
		return $this;
	}

	public function setDnid($dnid)
	{
		$this->dnidNumber = true;
		$this->actionData['dnid'] = $dnid;
		return $this;
	}

	public function setDnidId($dndId)
	{
		$this->dnidId = $dndId;
		return $this;
	}

	public function setSource($source)
	{
		$this->dnidSource = true;
		$this->actionData['source'] = $source;
		return $this;
	}

	public function setActive($active)
	{
		$this->actionData['active'] = (int)$active;
		return $this;
	}

	public function setPrefix($prefix)
	{
		$this->actionData['prefix'] = $prefix;
		return $this;
	}

	public function setExpression($expression)
	{
		$this->actionData['expression'] = $expression;
		return $this;
	}

	public function setAsteriskCompatible($expression)
	{
		$this->actionData['asteriskCompatible'] = $expression;
		return $this;
	}

	public function setDomainId($domainId) {
		$this->domainId = $domainId;
		return $this;
	}

	public function run()
	{

		try {
			if (!$this->domainId)
				throw new MissingDomainIdException('`setDomainId` MUST be called before `run`', 500);

			if (!$this->applicationId)
				throw new MissingApplicationIdException('`setApplicationId` MUST be called before `run`', 500);

			switch (strtolower($this->action)) {
				case "create":
				case "update":

					if ((!$this->dnidNumber) && (!$this->dnidSource))
						throw new MissingDnidIdException('`setDnid` and `setSource` MUST be called before `run`', 500);

					if ($this->dnidSource) {
						if ((!array_key_exists('prefix', $this->actionData)) &&
							(!array_key_exists('expression', $this->actionData)) &&
							(!array_key_exists('asteriskCompatible', $this->actionData))
						)
							throw new MissingAdditionalDataException('`setSource` MUST be followed by either `setPrefix`, `setExpression` or `setAsteriskCompatible` before `run`', 500);
					}

					$httpAction = (strtolower($this->action) == "create") ? "POST" : "PUT";
					$result = $this->client->httpRequest($httpAction, $this->baseQuery, $this->actionData);
					break;
				case "delete":

					if (!$this->dnidId)
						throw new MissingAdditionalDataException('`setDnidId` MUST be called on `delete` methods before `run`', 500);

					$this->client->httpRequest('DELETE', $this->baseQuery . '/' . $this->dnidId);
					return true;
					break;
				default:
					return false;
					break;
			}
			return json_decode((string)$result->getBody());

		} catch (MissingDomainIdException $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		} catch (MissingApplicationIdException $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		} catch (MissingAdditionalDataException $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		} catch (MissingDnidIdException $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		} catch (Exception $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		}
	}


}