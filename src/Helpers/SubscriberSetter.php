<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | SubscriberSetter.php
 * Creator: nirs | 2019-06-29
 */

namespace Cloudonix;


use Exception;

class SubscriberSetter
{
	public $baseFilter = false;
	public $baseQuery = false;
	public $domain = false;
	public $client = false;
	public $name = false;
	public $id = false;

	private $action = false;
	private $actionData = [];

	public function __construct(Client $client, $action)
	{
		if (!$client)
			throw new Exception('Datamodel Helper construction error', 500, null);

		$this->client = $client;
		$this->action = $action;
		$this->baseQuery = '/tenants/' . $client->tenantId;

	}

	public function setDomain($domain)
	{
		$this->domain = $domain;
		$this->baseQuery .= '/domains/' . $domain;
		return $this;
	}

	public function setDomainId($domainId)
	{
		return $this->setDomain($domainId);
	}

	public function setMsisdn($msdisn)
	{
		$this->actionData['msisdn'] = $msdisn;
		return $this;
	}

	public function setSubscriberNumber($msisdn)
	{
		return $this->setMsisdn($msisdn);
	}

	public function setPhoneNumber($msisdn)
	{
		return $this->setMsisdn($msisdn);
	}

	public function bySubscriberId($id)
	{
		$this->id = $id;
		return $this;
	}

	public function setProfileKey($key, $value)
	{
		$this->actionData['profile']['key'] = $value;
		return $this;
	}

	public function setActive($active)
	{
		$this->actionData['active'] = (int)$active;
		return $this;
	}

	public function setSipPassword($password)
	{
		$this->actionData['sip-password'] = $password;
		return $this;
	}

	public function setSipRandomPassword()
	{
		$this->actionData['sip-password'] = $this->generateRandomString(16);
		return $this;
	}

	private function generateRandomString($length = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!#$%^&*()[]{}-_=+';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public function run()
	{
		if ((!$this->domain) || (!$this->baseQuery))
			throw new MissingDomainIdException('`setDomainId|setDomain` MUST be called before `run`', 500);

		switch (strtolower($this->action)) {
			case "create":
				$result = $this->client->httpRequest('POST', $this->baseQuery . '/subscribers', $this->actionData);
				break;
			case "update":
				if (!$this->id)
					throw new MissingSubscriberIdException('`setSubscriberId|bySubscriberId` MUST be called before `run`', 500);
				$result = $this->client->httpRequest('PUT', $this->baseQuery . '/subscribers/' . $this->id, $this->actionData);
				break;
			case "delete":
				if (!$this->id)
					throw new MissingSubscriberIdException('`setSubscriberId|bySubscriberId` MUST be called before `run`', 500);
				$result = $this->client->httpRequest('DELETE', $this->baseQuery . '/subscribers/' . $this->id);
				return true;
				break;
			default:
				return false;
				break;
		}
		return json_decode((string)$result->getBody());
	}


}