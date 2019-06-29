<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | Trunks.php
 * Creator: nirs | 2019-06-28
 */

namespace Cloudonix;

use Exception;

/**
 * Cloudonix API.Core Client - Trunks Datamodel CRUD Interface
 *
 * @package Cloudonix
 */
class Trunks implements Datamodel
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
	 * Create a new Trunk in a Domain
	 *
	 * @param array $object A domain create object (represented as an array) as following:
	 * [
	 *    'domainId' => 'The domain ID the application will be created in',
	 *    'name' => 'name of the new trunk',
	 *    'direction' => 'The trunk direction `public-inbound`|`public-outbound`',
	 *    'ip' => 'The IP number of the remote trunk',
	 *    'port' => 'The IP PORT number of the remote trunk',
	 *    'prefix' => 'An alphanumeric prefix, to be added to dialed numbers - prior to sending/receibing the call to/from the trunk',
	 *    'transport' => 'The trunk transport type udp|tcp|tls',
	 *  'profile' => 'optional array of key value pairs'
	 * ]
	 * @return object $object The created Cloudonix Trunk Object
	 */
	public function create($object)
	{
		$domainId = $object['domainId'];
		unset($object['domainId']);

		$result = $this->client->httpRequest('POST',
			'/tenants/' . $this->client->tenantId .
			'/domains/' . $domainId .
			'/trunks',
			$object);
		return json_decode((string)$result->getBody());
	}

	/**
	 * Update new Trunk in a Domain
	 *
	 * @param array $object A domain update object (represented as an array) as following:
	 * [
	 *    'domainId' => 'The domain ID the application will be created in',
	 *    'trunkId' => 'The trunk ID to be updated',
	 *    'name' => 'name of the updated trunk',
	 *    'direction' => 'The trunk direction `public-inbound`|`public-outbound`',
	 *    'ip' => 'The IP number of the remote trunk',
	 *    'port' => 'The IP PORT number of the remote trunk',
	 *    'prefix' => 'An alphanumeric prefix, to be added to dialed numbers - prior to sending/receibing the call to/from the trunk',
	 *    'transport' => 'The trunk transport type udp|tcp|tls',
	 *  'profile' => 'optional array of key value pairs'
	 * ]
	 * @return object $object The created Cloudonix Trunk Object
	 */
	public function update($object)
	{
		$domainId = $object['domainId'];
		$trunkId = $object['trunkId'];
		unset($object['domainId']);
		unset($object['trunkId']);

		$result = $this->client->httpRequest('PUT',
			'/tenants/' . $this->client->tenantId .
			'/domains/' . $domainId .
			'/trunks/' . $trunkId,
			$object);
		return json_decode((string)$result->getBody());
	}

	/**
	 * Get Trunk information from a Domain
	 *
	 * @param array $object A domain update object (represented as an array) as following:
	 * [
	 *    'domainId' => 'The domain ID the application will be created in',
	 *    'trunkId' => 'The trunk ID to be updated',
	 * ]
	 * @return object $object The created Cloudonix Trunk Object
	 */
	public function get($object)
	{
		$domainId = $object['domainId'];
		$trunkId = $object['trunkId'];
		unset($object['domainId']);
		unset($object['trunkId']);

		$result = $this->client->httpRequest('GET',
			'/tenants/' . $this->client->tenantId .
			'/domains/' . $domainId .
			'/trunks/' . $trunkId);
		return json_decode((string)$result->getBody());
	}

	/**
	 * Delete a Trunk from a domain ID
	 *
	 * @param array $object A trunk delete object (represented as an array) as following:
	 * [
	 *    'domainId' => 'The domain ID of the trunk to delete',
	 *    'trunkId' => 'The trunk ID to be deleted',
	 * ]
	 * @return boolean
	 */
	public function delete($object)
	{
		$domainId = $object['domainId'];
		$trunkId = $object['trunkId'];
		unset($object['domainId']);
		unset($object['trunkId']);

		$this->client->httpRequest('DELETE',
			'/tenants/' . $this->client->tenantId .
			'/domains/' . $domainId .
			'/trunks/' . $trunkId,
			$object);
		return true;
	}

	/**
	 * Create a new API key in the data model - Not Applicable
	 *
	 * @param $object
	 * @return bool
	 */
	public function createApikey($object)
	{
		return false;
	}

	/**
	 * Update an existing API key object in the data model - Not Applicable
	 *
	 * @param $object
	 * @return bool
	 */
	public function updateApikey($object)
	{
		return false;
	}

	/**
	 * Delete an existing API key object from the data model - Not Applicable
	 *
	 * @param $object
	 * @return bool
	 */
	public function deleteApikey($object)
	{
		return false;
	}

	/**
	 * Get a list of currently available API keys in the data model - Not Applicable
	 *
	 * @param $object
	 * @return bool
	 */
	public function getApikeys($object)
	{
		return false;
	}
}