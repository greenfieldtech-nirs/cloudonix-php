<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | Datamodel.php
 * Creator: nirs | 2019-06-27
 */

namespace Cloudonix;

/**
 * Generic Cloudonix Data Model CRUD (Access) Interface
 *
 * Interface Datamodel
 * @package Cloudonix
 */
interface Datamodel
{
	/**
	 * Create an object in the data model
	 *
	 * @param $object
	 * @return mixed
	 */
	public function create($object);

	/**
	 * Update an object in the data model
	 * @param $object
	 * @return mixed
	 */
	public function update($object);

	/**
	 * Get an object from the data model
	 * @param $object
	 * @return mixed
	 */
	public function get($object);

	/**
	 * Delete an object from the data model
	 *
	 * @param $object
	 * @return mixed
	 */
	public function delete($object);

	/**
	 * Create a new API key in the data model
	 *
	 * @param $object
	 * @return mixed
	 */
	public function createApikey($object);

	/**
	 * Update an existing API key object in the data model
	 *
	 * @param $object
	 * @return mixed
	 */
	public function updateApikey($object);

	/**
	 * Delete an existing API key object from the data model
	 *
	 * @param $object
	 * @return mixed
	 */
	public function deleteApikey($object);

	/**
	 * Get a list of currently available API keys in the data model
	 *
	 * @param $object
	 * @return mixed
	 */
	public function getApikeys($object);
}