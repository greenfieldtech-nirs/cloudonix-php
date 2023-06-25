<?php
    /**
     * @package cloudonix-php
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
    use Cloudonix\Entities\VoiceApplication;

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
        protected mixed $client;
        protected mixed $parent;
        protected string $canonicalPath;

        protected string $msisdn;

        public function __construct(VoiceApplication $parent, string $msisdn)
        {
            $this->client = $parent->getClient();
            $this->parent = $parent;
            $this->msisdn = $msisdn;
            $this->setPath($parent->getPath());
            parent::__construct($this);
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
         * @param string $parentCanonicalPath
         *
         * @return void
         */
        protected function setPath(string $parentCanonicalPath): void
        {
            $this->canonicalPath = $parentCanonicalPath . "/subscribers/" . $this->msisdn;
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
                    $this->collection[$value->key] = new EntitySubscriberDataKey($this->parent, $value->subscriber->msisdn, $value);
                }
            return $this->collection;
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            if (!is_null($offset)) {
                $result = $this->client->httpConnector->request("PUT", $this->getPath() . "/" . $offset, $value);
                $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " result: " . json_encode($result));
            }
        }

        public function offsetUnset(mixed $offset): void
        {
            if (!is_null($offset)) {
                $result = $this->client->httpConnector->request("DELETE", $this->getPath() . "/" . $offset);
                $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " result: " . json_encode($result));
            }
        }

        public function offsetGet(mixed $offset): mixed
        {
            $this->refresh();
            return parent::offsetGet($offset);
        }

        public function getIterator(): Traversable
        {
            $this->refresh();
            return parent::getIterator();
        }

        public function __toString(): string
        {
            $this->refresh();
            return parent::__toString();
        }
    }