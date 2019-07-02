<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | Tenants.php
 * Creator: nirs | 2019-06-27
 */

namespace Cloudonix\Datamodels;

use Cloudonix\Helpers\TenantGetter;
use Cloudonix\Helpers\TenantSetter;
use Cloudonix\Client as Client;
use Cloudonix\LazyDatamodel as LazyDatamodel;
use Exception;

/**
 * Cloudonix API.Core Client - Tenants Datamodel CRUD Interface
 *
 * @package Cloudonix
 */
class Tenants implements LazyDatamodel
{
	public $client;
	public $name;
	public $id;

	private $tenantGetter;
	private $tenantSetter;

	public function __construct(Client $client)
	{
		if (!$client)
			throw new Exception('Datamodel construction error', 500, null);
		$this->client = $client;
	}

	/**
	 * Create a new Cloudonix Tenant
	 *
	 * @return TenantSetter The created Cloudonix Tenant Object
	 */
	public function create(): TenantSetter
	{
		$this->tenantSetter = new TenantSetter($this->client, 'create');
		return $this->tenantSetter;
	}

	/**
	 * Update an a Cloudonix Tenant object
	 *
	 * @return TenantSetter The updated Cloudonix Tenant Object
	 */
	public function update(): TenantSetter
	{
		$this->tenantSetter = new TenantSetter($this->client, 'update');
		return $this->tenantSetter;
	}

	/**
	 * Get a tenant by Object (or list of)
	 *
	 * @return TenantGetter
	 */
	public function get(): TenantGetter
	{
		$this->tenantGetter = new TenantGetter($this->client);
		return $this->tenantGetter;
	}

	/**
	 * Delete a tenant by Object (Not supported)
	 *
	 * @return false
	 */
	public function delete()
	{
		return false;
	}

	/**
	 * Create a Tenant API key
	 *
	 * @return TenantSetter The created API key object
	 */
	public function createApikey(): TenantSetter
	{
		$this->tenantSetter = new TenantSetter($this->client, 'createApikey');
		return $this->tenantSetter;
	}

	/**
	 * Update a Tenant API key
	 *
	 * @return TenantSetter The update API key object
	 */
	public function updateApikey(): TenantSetter
	{
		$this->tenantSetter = new TenantSetter($this->client, 'updateApikey');
		return $this->tenantSetter;
	}

	/**
	 * Delete a Tenant API key
	 *
	 * @return true on success
	 */
	public function deleteApikey(): TenantSetter
	{
		$this->tenantSetter = new TenantSetter($this->client, 'deleteApikey');
		return $this->tenantSetter;
	}

	/**
	 * Get a Tenants API key (or list of)
	 *
	 * @return TenantGetter
	 */
	public function getApikeys(): TenantGetter
	{
		$this->tenantGetter = new TenantGetter($this->client, true);
		return $this->tenantGetter;
	}
}