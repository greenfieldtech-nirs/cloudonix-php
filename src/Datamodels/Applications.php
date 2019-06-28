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
 * Cloudonix API.Core Client - Applications Datamodel CRUD Interface
 *
 * @package Cloudonix
 */
class Applications implements Datamodel
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
	 * Create a new Application in a Domain
	 *
	 * @param array $object A domain create object (represented as an array) as following:
	 * [
	 * 	'domainId' => 'The domain ID the application will be created in',
	 * 	'name' => 'name of the new application',
	 * 	'type' => 'Application language type (applicable values are cloudonix|twilio',
	 * 	'url' => 'Remote URL where the application is hosted - normally this will the first application script',
 	 *  'profile' => 'optional array of key value pairs'
	 * ]
	 * @return object $object The created Cloudonix Domain Object
	 */
	public function create($object)
	{
		$domainId = $object['domainId'];
		unset($object['domainId']);
		$result = $this->client->httpRequest('POST',
			'/tenants/' . $this->client->tenantId .
			'/domains/' . $domainId .
			'/applications',
			$object);
		return json_decode((string)$result->getBody());
	}

	/**
	 * Update an existing Cloudonix Tenant
	 *
	 * @param array $object A domain update object (represented as an array) as following:
	 * [
	 * 	'domainId' => 'The domain ID the application will be created in',
	 * 	'type' => 'Application language type (applicable values are cloudonix|twilio',
	 * 	'url' => 'Remote URL where the application is hosted - normally this will the first application script',
	 *  'profile' => 'optional array of key value pairs'
	 * ]
	 * @return object $object The created Cloudonix Domain Object
	 */
	public function update($object)
	{
		$domainId = $object['domainId'];
		unset($object['domainId']);
		unset($object['name']);
		$result = $this->client->httpRequest('PUT',
			'/tenants/' . $this->client->tenantId .
			'/domains/' . $domainId .
			'/applications',
			$object);
		return json_decode((string)$result->getBody());
	}

	/**
	 * Get applications by domain ID or domain name
	 *
	 * @param array $object A domain update object (represented as an array) as following:
	 * [
	 * 	'domainId' => '(Mandatory) The domain ID the application will be created in',
	 * 	'applicationIdent' => '(Optional) Application ID number or application name',
	 * ]
	 * @return object
	 */
	public function get($object = null)
	{
		$filter = ((array_key_exists('applicationIdent', $object)) && ($object['applicationIdent'] != null) && (strlen($object['applicationIdent']))) ? "/" . $object['applicationIdent'] : "";

		$result = $this->client->httpRequest('GET',
			'/tenants/' . $this->client->tenantId .
			'/domains/' . $object['domainId'] .
			'/applications' . $filter);
		return json_decode((string)$result->getBody());
	}

	/**
	 * Delete an application from a domain ID
	 *
	 * @param array $object A domain delete object (represented as an array) as following:
	 * [
	 * 	'domainId' => '(Mandatory) The domain ID the application will be created in',
	 * 	'applicationIdent' => '(Mandaotry) Application ID number or application name',
	 * ]
	 * @return boolean
	 */
	public function delete($object)
	{
		$result = $this->client->httpRequest('DELETE',
			'/tenants/' . $this->client->tenantId .
			'/domains/' . $object['domainId'] .
			'/applications/' . $object['applicationIdent']);
		return true;
	}

	/**
	 * Create an Application API key
	 *
	 * @param array $object An API key create object (represented as an array) as following:
	 * [
	 *  'domainId' => 'mandatory domain ID or name',
	 *  'applicationId' => 'mandatory application ID to create an API key for',
	 * 	'name' => 'mandatory_name',
	 * ]
	 * @return object A Cloudonix API key datamodel object
	 */
	public function createApikey($object)
	{
		$domainId = $object['domainId'];
		$applicationId = $object['applicationId'];
		unset($object['domainId']);
		unset($object['applicationId']);

		$result = $this->client->httpRequest('POST',
			'/tenants/' . $this->client->tenantId .
			'/domains/' . $domainId .
			'/applications/' . $applicationId .
			'/apikeys', $object);
		return json_decode((string)$result->getBody());
	}

	/**
	 * Update an Application API key
	 *
	 * @param array $object An API key update object (represented as an array) as following:
	 * [
	 *  'domainId' => 'mandatory domain ID or name',
	 *  'applicationId' => 'mandatory application ID to udpate an API key for',
	 *  'apikeyId' => 'the_apikey_id_to_update',
	 * 	'name' => 'mandatory_name'
	 * ]
	 * @return object A Cloudonix API key datamodel object
	 */
	public function updateApikey($object)
	{
		$apikeyId = $object['apikeyId'];
		$domainId = $object['domainId'];
		$applicationId = $object['applicationId'];
		unset($object['domainId']);
		unset($object['applicationId']);
		unset($object['apikeyId']);

		$result = $this->client->httpRequest('PUT',
			'/tenants/' . $this->client->tenantId .
			'/domains/' . $domainId .
			'/applications/' . $applicationId .
			'/apikeys/' . $apikeyId, $object);
		return json_decode((string)$result->getBody());
	}

	/**
	 * Delete an Application API key
	 *
	 * @param array $object An API key delete object (represented as an array) as following:
	 * [
	 *  'domainId' => 'mandatory domain ID or name',
	 *  'applicationId' => 'mandatory application ID to delete an API key for',
	 *  'apikeyId' => 'the apikey ID to delete',
	 * ]
	 * @return void
	 */
	public function deleteApikey($object)
	{
		$apikeyId = $object['apikeyId'];
		$domainId = $object['domainId'];
		$applicationId = $object['applicationId'];

		$this->client->httpRequest('DELETE',
			'/tenants/' . $this->client->tenantId .
			'/domains/' . $domainId .
			'/applications/' . $applicationId .
			'/apikeys/' . $apikeyId);
	}

	/**
	 * Get an Application API key list
	 *
	 * @param array $object An API key list object (represented as an array) as following:
	 * [
	 *  'domainId' => 'mandatory domain ID or name',
	 *  'applicationId' => 'mandatory application ID to get the API key list for',
	 * ]
	 * @return object
	 */
	public function getApikeys($object = null)
	{
		$domainId = $object['domainId'];
		$applicationId = $object['applicationId'];

		$result = $this->client->httpRequest('GET',
			'/tenants/' . $this->client->tenantId .
			'/domains/' . $domainId .
			'/applications/' . $applicationId .
			'/apikeys');
		return json_decode((string)$result->getBody());
	}
}