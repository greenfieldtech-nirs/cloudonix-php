<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | Applications.php
 * Creator: Nir Simionovich <nirs@cloudonix.io> | 2019-06-27
 */

namespace Cloudonix\Datamodels;

use Cloudonix\Client as Client;
use Cloudonix\LazyDatamodel as LazyDatamodel;
use Cloudonix\Helpers\ApplicationGetter;
use Cloudonix\Helpers\ApplicationSetter;
use Exception;

/**
 * Cloudonix API.Core Client - Applications Datamodel CRUD Interface
 *
 * @package Cloudonix
 */
class Applications implements LazyDatamodel
{
	public $client;
	public $name;
	public $id;

	protected $applicationGetter;
	protected $applicationSetter;

	public function __construct(Client $client)
	{
		if (!$client)
			throw new Exception('Datamodel construction error', 500, null);
		$this->client = $client;

	}

	/**
	 * Create a new Application
	 * @return ApplicationSetter The created Cloudonix Domain Application Object
	 */
	public function create(): ApplicationSetter
	{
		$this->applicationSetter = new ApplicationSetter($this->client, 'create');
		return $this->applicationSetter;
	}

	/**
	 * Update an application in the data model
	 * @return ApplicationSetter The updated Cloudonix Domain Application Object
	 */
	public function update(): ApplicationSetter
	{
		$this->applicationSetter = new ApplicationSetter($this->client, 'update');
		return $this->applicationSetter;
	}

	/**
	 * Get an application from the data model
	 * @return ApplicationGetter A Cloudonix Domain Application Object (or list of)
	 */
	public function get(): ApplicationGetter
	{
		$this->applicationGetter = new ApplicationGetter($this->client);
		return $this->applicationGetter;
	}

	/**
	 * Delete an application from the data model
	 * @return ApplicationSetter
	 */
	public function delete(): ApplicationSetter
	{
		$this->applicationSetter = new ApplicationSetter($this->client, 'delete');
		return $this->applicationSetter;
	}

	/**
	 * Create a new application API key in the data model
	 * @return ApplicationSetter The created Cloudonix Domain Application API key Object
	 */
	public function createApikey(): ApplicationSetter
	{
		$this->applicationSetter = new ApplicationSetter($this->client, 'createApikey');
		return $this->applicationSetter;
	}

	/**
	 * Update an existing application API key object in the data model
	 * @return ApplicationSetter The updated Cloudonix Domain Application API key Object
	 */
	public function updateApikey(): ApplicationSetter
	{
		$this->applicationSetter = new ApplicationSetter($this->client, 'updateApikey');
		return $this->applicationSetter;
	}

	/**
	 * Delete an existing application API key object from the data model
	 * @return ApplicationSetter
	 */
	public function deleteApikey(): ApplicationSetter
	{
		$this->applicationSetter = new ApplicationSetter($this->client, 'deleteApikey');
		return $this->applicationSetter;
	}

	/**
	 * Get a list of currently available application API keys in the data model
	 * @return ApplicationGetter List of Cloudonix Application API keys
	 */
	public function getApikeys(): ApplicationGetter
	{
		$this->applicationGetter = new ApplicationGetter($this->client, 'getApikeys');
		return $this->applicationGetter;
	}
}