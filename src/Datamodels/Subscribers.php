<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | Subscribers.php
 * Creator: nirs | 2019-06-29
 */

namespace Cloudonix;

use Exception;

/**
 * Cloudonix API.Core Client - Subscribers Datamodel CRUD Interface
 *
 * @package Cloudonix
 */
class Subscribers implements LazyDatamodel
{
	public $client;
	public $name;
	public $id;

	protected $subscriberGetter;
	protected $subscriberSetter;

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
	 * Create a Subscriber in a the Domain
	 *
	 * @return SubscriberSetter $object The Subscriber (or list of) object created in the datamodel
	 */
	public function create(): SubscriberSetter
	{
		if (!$this->subscriberSetter) {
			$this->subscriberSetter = new SubscriberSetter($this->client, 'create');
		}
		return $this->subscriberSetter;
	}

	/**
	 * Update a Subscriber in a the Domain
	 *
	 * @return SubscriberSetter $object The Subscriber (or list of) object created in the datamodel
	 */
	public function update(): SubscriberSetter
	{
		if (!$this->subscriberSetter) {
			$this->subscriberSetter = new SubscriberSetter($this->client, 'update');
		}
		return $this->subscriberSetter;
	}

	/**
	 * Get Subscriber (or list of) in a the Domain
	 *
	 * @return SubscriberGetter $object The Subscriber (or list of) object created in the datamodel
	 */
	public function get(): SubscriberGetter
	{
		if (!$this->subscriberGetter) {
			$this->subscriberGetter = new SubscriberGetter($this->client);
		}
		return $this->subscriberGetter;
	}

	/**
	 * Delete a Subscriber in a the Domain
	 *
	 * @return SubscriberSetter $object The Subscriber (or list of) object created in the datamodel
	 */
	public function delete(): SubscriberSetter
	{
		if (!$this->subscriberSetter) {
			$this->subscriberSetter = new SubscriberSetter($this->client, 'delete');
		}
		return $this->subscriberSetter;
	}

	/**
	 * Create a Subscriber API key in a domain
	 *
	 * @return SubscriberSetter $object The Subscriber (or list of) object created in the datamodel
	 */
	public function createApikey()
	{
		if (!$this->subscriberSetter) {
			$this->subscriberSetter = new SubscriberSetter($this->client, 'delete');
		}
		return $this->subscriberSetter;
	}

	/**
	 * Update a Subscriber API key in a domain
	 *
	 * @return SubscriberSetter $object The Subscriber (or list of) object created in the datamodel
	 */
	public function updateApikey()
	{
		if (!$this->subscriberSetter) {
			$this->subscriberSetter = new SubscriberSetter($this->client, 'delete');
		}
		return $this->subscriberSetter;
	}

	/**
	 * Delete a Subscriber API key in a domain
	 *
	 * @return SubscriberSetter $object The Subscriber (or list of) object created in the datamodel
	 */
	public function deleteApikey()
	{
		if (!$this->subscriberSetter) {
			$this->subscriberSetter = new SubscriberSetter($this->client, 'delete');
		}
		return $this->subscriberSetter;
	}

	/**
	 * Get a Subscriber API key (or list of) in a domain
	 *
	 * @return SubscriberSetter $object The Subscriber (or list of) object created in the datamodel
	 */
	public function getApikeys()
	{
		if (!$this->subscriberGetter) {
			$this->subscriberGetter = new SubscriberGetter($this->client);
		}
		return $this->subscriberGetter;
	}
}