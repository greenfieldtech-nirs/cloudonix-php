<?php
    /**
     * @package cloudonixPhp
     * @file    Collections/SubscriberDataKeys.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=voice-application-subscriber-data
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Collections;

    use ArrayIterator;
    use Traversable;

    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;
    use Cloudonix\Entities\SubscriberDataKey as EntitySubscriberDataKey;

    /**
     * Subscriber Data Keys Collection
     *
     * Cloudonix voice applications allow the developer to securely store subscriber related information, in a
     * similar fashion to how web applications can store data in cookies. Data is stored in string format, providing
     * the developer with maximum flexibility in storing JSON or other encoded data.
     *
     * @property      string $key                    Subscriber data key
     * @property      string $data                   Subscriber data storage
     * @property-read string $createdAt              Subscriber data Creation Date and time
     * @property-read string $modifiedAt             Subscriber data Last Modification Date and time
     * @property-read string $deletedAt              Subscriber data Last Deletion Date and time
     */
    class SubscriberDataKeys extends CloudonixCollection implements \IteratorAggregate, \ArrayAccess
    {
        public mixed $client;
        private mixed $parent;

        public function __construct(mixed $parent)
        {
            $this->client = $parent->client;
            $this->parent = $parent;
            $this->setPath($parent->domain);
            parent::__construct();
        }

        /**
         * Return the entity REST API canonical path
         *
         * @return string
         */
        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        /**
         * Set the entity REST API canonical path
         *
         * @param string $domain
         *
         * @return void
         */
        protected function setPath(string $domain): void
        {
            if (!strlen($this->canonicalPath))
                $this->canonicalPath = URLPATH_CALLS . "/" . $domain . URLPATH_SESSIONS;
        }

        /**
         * Refresh the collection
         *
         * @return $this
         */
        public function refresh(): SubscriberDataKeys
        {
            $this->refreshCollectionData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        /**
         * Build the local collection data storage
         *
         * @param mixed $param
         *
         * @return array
         */
        protected function refreshCollectionData(mixed $param): array
        {
            $this->collection = [];
            if (!is_null($param))
                foreach ($param as $key => $value) {
                    $this->collection[$value->id] = new EntitySubscriberDataKey($value->application, $value->subscriber->msisdn, $value);
                }
            return $this->collection;
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            return;
        }

        public function offsetUnset(mixed $offset): void
        {
            return;
        }

        public function offsetGet(mixed $offset): mixed
        {
            if (!count($this->collection)) $this->refresh();
            return parent::offsetGet($offset);
        }

        public function getIterator(): Traversable
        {
            if (!count($this->collection)) $this->refresh();
            return parent::getIterator();
        }

        public function __toString(): string
        {
            if (!count($this->collection)) $this->refresh();
            return parent::__toString();
        }
    }