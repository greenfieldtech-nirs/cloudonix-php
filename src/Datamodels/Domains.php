<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | Domains.php
 * Creator: nirs | 2019-06-27
 */

namespace Cloudonix;

use Exception;

/**
 * Cloudonix API.Core Client - Domains Datamodel CRUD Interface
 *
 * @package Cloudonix
 */
class Domains implements Datamodel
{
	public $client;
	public $name;
	public $id;

	public function __construct(Client $client)
	{
		try {
			if (!$client)
				throw new Exception('Datamodel construction error', 500, null);
			$this->client = $client;

		} catch (Exception $e) {
			die("Exception: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
		}
	}

	/**
	 * Create a new Cloudonix Domain
	 *
	 * @param array $object A domain create object (represented as an array) as following:
	 * [
	 * 	'domain' => 'mandatory domain name to create'
	 * ]
	 * @return object $object The created Cloudonix Domain Object
	 */
	public function create($object)
	{
		$result = $this->client->httpRequest('POST',
			'/tenants/' . $this->client->tenantId .
			'/domains', $object);
		return json_decode((string)$result->getBody());
	}

	/**
	 * Update an existing Cloudonix Domain
	 *
	 * @param array $object A domain update object (represented as an array) as following:
	 * [
	 *  'id' => 'domain ID number to update',
	 *  'profile' => 'optional array of key value pairs (registration-free-control-endpoint|registration-free-control-endpoint-api-key|call-timeout)'
	 * ]
	 * @return object $object The created Cloudonix Domain Object
	 */
	public function update($object)
	{
		$domainId = $object['id'];
		unset($object['id']);
		$result = $this->client->httpRequest('PUT',
			'/tenants/' . $this->client->tenantId .
			'/domains/' . $domainId, $object);
		return json_decode((string)$result->getBody());
	}

	/**
	 * Get a domain by domain ID or domain name
	 *
	 * @param integer|string $object
	 * @return object
	 */
	public function get($object = null)
	{
		$filter = (($object != null) && (strlen($object))) ? "/" . $object : "";
		$result = $this->client->httpRequest('GET',
			'/tenants/' . $this->client->tenantId .
			'/domains' . $filter);
		return json_decode((string)$result->getBody());
	}

	/**
	 * Delete a domain by domain ID or domain name
	 *
	 * @param integer|string $object
	 * @return boolean
	 */
	public function delete($object)
	{
		$result = $this->client->httpRequest('DELETE',
			'/tenants/' . $this->client->tenantId .
			'/domains/' . $object);
		return true;
	}

	/**
	 * Create a Domain API key
	 *
	 * @param array $object An API key create object (represented as an array) as following:
	 * [
	 *  'domainId' => 'mandatory_domain_id_or_name'
	 * 	'name' => 'mandatory_name',
	 * ]
	 * @return object A Cloudonix API key datamodel object
	 */
	public function createApikey($object)
	{
		$result = $this->client->httpRequest('POST',
			'/tenants/' . $this->client->tenantId .
			'/domains/' . $object['domainId'] .
			'/apikeys', $object);
		return json_decode((string)$result->getBody());
	}

	/**
	 * Update a Domain API key
	 *
	 * @param array $object An API key update object (represented as an array) as following:
	 * [
	 *  'domainId' => 'mandatory_domain_id_or_name',
	 *  'apikeyId' => 'the_apikey_id_to_update',
	 * 	'name' => 'mandatory_name'
	 * ]
	 * @return object A Cloudonix API key datamodel object
	 */
	public function updateApikey($object)
	{
		$result = $this->client->httpRequest('PUT',
			'/tenants/' . $this->client->tenantId .
			'/domains/' . $object['domainId'] .
			'/apikeys/' . $object['apikeyId'], $object);
		return json_decode((string)$result->getBody());
	}

	/**
	 * Delete a Domain API key
	 *
	 * @param array $object An API key delete object (represented as an array) as following:
	 * [
	 *  'domainId' => 'mandatory_domain_id_or_name',
	 *  'apikeyId' => 'the_apikey_id_to_update',
	 * ]
	 * @return void
	 */
	public function deleteApikey($object)
	{
		$this->client->httpRequest('DELETE',
			'/tenants/' . $this->client->tenantId .
			'/domains/' . $object['domainId'] .
			'/apikeys/' . $object['apikeyId']);
	}

	/**
	 * Get a Domain API key list
	 *
	 * @param array $object An API key list object (represented as an array) as following:
	 * [
	 *  'domainId' => 'mandatory_domain_id_or_name',
	 * ]
	 * @return object
	 */
	public function getApikeys($object = null)
	{
		$result = $this->client->httpRequest('GET',
			'/tenants/' . $this->client->tenantId .
			'/domains/' . $object['domainId'] .
			'/apikeys');
		return json_decode((string)$result->getBody());
	}
}