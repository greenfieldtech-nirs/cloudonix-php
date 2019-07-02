<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | Dnids.php
 * Creator: nirs | 2019-06-28
 */

namespace Cloudonix\Datamodels;

use Cloudonix\Helpers\DnidGetter;
use Cloudonix\Helpers\DnidSetter;
use Cloudonix\Client as Client;
use Cloudonix\LazyDatamodel as LazyDatamodel;
use Exception;

/**
 * Cloudonix API.Core Client - DNIDs Datamodel CRUD Interface
 *
 * @package Cloudonix
 */
class Dnids implements LazyDatamodel
{
	public $client;
	public $name;
	public $id;

	protected $dnidGetter;
	protected $dnidSetter;

	public function __construct(Client $client)
	{
		if (!$client)
			throw new Exception('Datamodel construction error', 500, null);
		$this->client = $client;

	}

	/**
	 * Create a DNID
	 *
	 * @return DnidSetter The created DNID object
	 */
	public function create(): DnidSetter
	{
		$this->dnidSetter = new DnidSetter($this->client, 'create');
		return $this->dnidSetter;
	}

	/**
	 * Update a DNID
	 *
	 * @return DnidSetter The updated DNID object
	 */
	public function update(): DnidSetter
	{
		$this->dnidSetter = new DnidSetter($this->client, 'update');
		return $this->dnidSetter;
	}

	/**
	 * Get DNID information
	 *
	 * @return DnidGetter A DNID (or list of) object (or objects)
	 */
	public function get(): DnidGetter
	{
		$this->dnidGetter = new DnidGetter($this->client);
		return $this->dnidGetter;
	}

	/**
	 * Delete a DNID
	 *
	 * @return DnidSetter True on success
	 */
	public function delete(): DnidSetter
	{
		$this->dnidSetter = new DnidSetter($this->client, 'delete');
		return $this->dnidSetter;
	}

	/**
	 * Create a new API key in the data model - Not Applicable
	 *
	 * @return false
	 */
	public function createApikey()
	{
		return false;
	}

	/**
	 * Update an existing API key object in the data model - Not Applicable
	 *
	 * @return false
	 */
	public function updateApikey()
	{
		return false;
	}

	/**
	 * Delete an existing API key object from the data model - Not Applicable
	 *
	 * @return bool
	 */
	public function deleteApikey()
	{
		return false;
	}

	/**
	 * Get a list of currently available API keys in the data model - Not Applicable
	 *
	 * @return bool
	 */
	public function getApikeys()
	{
		return false;
	}
}