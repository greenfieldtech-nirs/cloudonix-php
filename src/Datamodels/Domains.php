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

namespace Cloudonix\Datamodels;

use Cloudonix\Helpers\DnidGetter;
use Cloudonix\Helpers\DnidSetter;
use Cloudonix\Client as Client;
use Cloudonix\Datamodel as Datamodel;
use Cloudonix\LazyDatamodel as LazyDatamodel;
use Exception;

/**
 * Cloudonix API.Core Client - Domains Datamodel CRUD Interface
 *
 * @package Cloudonix
 */
class Domains implements LazyDatamodel
{
	public $client;
	public $name;
	public $id;

	public function __construct(Client $client)
	{
		if (!$client)
			throw new Exception('Datamodel construction error', 500, null);
		$this->client = $client;
	}

	/**
	 * Create a new Cloudonix Domain
	 *
	 * @return object $object The created Cloudonix Domain Object
	 */
	public function create()
	{
	}

	/**
	 * Update an existing Cloudonix Domain
	 *
	 * @return object $object The created Cloudonix Domain Object
	 */
	public function update()
	{
	}

	/**
	 * Get a domain by domain ID or domain name
	 *
	 * @return object
	 */
	public function get()
	{
	}

	/**
	 * Delete a domain by domain ID or domain name
	 *
	 * @return boolean
	 */
	public function delete()
	{
	}

	/**
	 * Create a Domain API key
	 *
	 * @return object A Cloudonix API key datamodel object
	 */
	public function createApikey()
	{
	}

	/**
	 * Update a Domain API key
	 *
	 * @return object A Cloudonix API key datamodel object
	 */
	public function updateApikey()
	{
	}

	/**
	 * Delete a Domain API key
	 *
	 * @return void
	 */
	public function deleteApikey()
	{
	}

	/**
	 * Get a Domain API key list
	 *
	 * @return object
	 */
	public function getApikeys()
	{
	}
}