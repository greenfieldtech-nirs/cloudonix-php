<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | SubscriberGetter.php
 * Creator: Nir Simionovich <nirs@cloudonix.io> | 2019-06-29
 */

namespace Cloudonix\Helpers;

use Exception;
use Cloudonix\Client as Client;
use Cloudonix\Exceptions\MissingDomainIdException;

class SubscriberGetter
{
	public $baseFilter = false;
	public $baseQuery = false;
	public $filterBySubscriberId = false;
	public $domain = false;
	public $client;
	public $name;
	public $id;

	public function __construct(Client $client)
	{
		if (!$client)
			throw new Exception('Datamodel Helper construction error', 500, null);

		$this->client = $client;
		$this->baseQuery = '/tenants/' . $client->tenantId;
		$this->baseFilter = "?";
	}

	public function byDomain($domain)
	{
		$this->domain = $domain;
		$this->baseQuery .= '/domains/' . $domain;
		return $this;
	}

	public function byDomainId($domainId)
	{
		return $this->byDomain($domainId);
	}

	public function bySubscriber($subscriber)
	{
		if (!$this->filterBySubscriberId) {
			$this->baseFilter .= 'by_msisdn=' . $subscriber;
			return $this;
		}
	}

	public function byMSISDN($msisdn)
	{
		return $this->bySubscriber($msisdn);
	}

	public function bySubscriberId($id)
	{
		$this->filterBySubscriberId = true;
		$this->baseFilter = "?";
		$this->baseQuery .= '/subscribers/' . $id;
		return $this;
	}

	public function run()
	{
		if ((!$this->domain) || (!$this->baseQuery))
			throw new MissingDomainIdException('`byDomainId|byDomain` MUST be called before `run`', 500, null);

		if ($this->filterBySubscriberId) {
			$result = $this->client->httpRequest('GET', $this->baseQuery . $this->baseFilter);
		} else {
			$result = $this->client->httpRequest('GET', $this->baseQuery . '/subscribers' . $this->baseFilter);
		}
		return $result;
	}

}