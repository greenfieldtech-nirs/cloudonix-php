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
 * Creator: nirs | 2019-06-29
 */  

namespace Cloudonix;


use Exception;

class SubscriberSetter
{
	public $baseFilter;
	public $baseQuery;
	public $domainId;
	public $client;
	public $name;
	public $id;

	private $action;
	private $actionData = [];

	public function __construct(Client $client, $action)
	{
		try {
			if (!$client)
				throw new Exception('Datamodel Helper construction error', 500, null);

			$this->client = $client;
			$this->action = $action;
			$this->baseQuery = '/domains/' . $this->domainId;

		} catch (Exception $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		}
	}

	public function setDomainId($domainId) {
		$this->domainId = $domainId;
		return $this;
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