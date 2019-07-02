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
use Cloudonix\Exceptions\DatamodelBuilderException as DatamodelBuilderException;
use Cloudonix\Exceptions\MissingDomainIdException;

class ApplicationSetter
{
	public $baseFilter = "?";
	public $baseQuery = false;
	public $client;
	public $name;
	public $id;

	private $action = false;
	private $actionData = [];

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


	public function run()
	{

		try {

			switch (strtolower($this->action)) {
				case "create":
				case "update":

					$httpAction = (strtolower($this->action) == "create") ? "POST" : "PUT";
					$result = $this->client->httpRequest($httpAction, $this->baseQuery, $this->actionData);
					break;
				case "delete":

					$this->client->httpRequest('DELETE', $this->baseQuery);
					return true;
					break;
				default:
					return false;
					break;
			}
			return json_decode((string)$result->getBody());

		} catch (Exception $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		}
	}


}