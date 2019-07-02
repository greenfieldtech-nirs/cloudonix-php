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
 * Creator: Nir Simionovich <nirs@cloudonix.io> | 2019-06-28
 */

namespace Cloudonix\Datamodels;

use Cloudonix\Helpers\TrunkGetter;
use Cloudonix\Helpers\TrunkSetter;
use Cloudonix\Client as Client;
use Cloudonix\LazyDatamodel as LazyDatamodel;
use Exception;

/**
 * Cloudonix API.Core Client - Trunks Datamodel CRUD Interface
 *
 * @package Cloudonix
 */
class Trunks implements LazyDatamodel
{
	public $client;
	public $name;
	public $id;

	protected $trunkGetter;
	protected $trunkSetter;

	public function __construct(Client $client)
	{
		if (!$client)
			throw new Exception('Datamodel construction error', 500, null);
		$this->client = $client;
	}

	/**
	 * Create a new trunk
	 *
	 * @return TrunkSetter The newly created trunk object
	 */
	public function create(): TrunkSetter
	{
		$this->trunkSetter = new TrunkSetter($this->client, 'create');
		return $this->trunkSetter;
	}

	/**
	 * Update an existing trunk
	 *
	 * @return TrunkSetter The updated trunk object
	 */
	public function update(): TrunkSetter
	{
		$this->trunkSetter = new TrunkSetter($this->client, 'update');
		return $this->trunkSetter;
	}

	/**
	 * Retrieve trunk information
	 *
	 * @return TrunkGetter The trunk (or list of) object (or objects)
	 */
	public function get(): TrunkGetter
	{
		$this->trunkGetter = new TrunkGetter($this->client);
		return $this->trunkGetter;
	}

	/**
	 * Delete a trunk
	 *
	 * @return TrunkSetter Returns True on success
	 */
	public function delete(): TrunkSetter
	{
		$this->trunkSetter = new TrunkSetter($this->client, 'delete');
		return $this->trunkSetter;
	}

	/**
	 * Create a new API key in the data model - Not Applicable
	 *
	 * @return bool
	 */
	public function createApikey()
	{
		return false;
	}

	/**
	 * Update an existing API key object in the data model - Not Applicable
	 *
	 * @return bool
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