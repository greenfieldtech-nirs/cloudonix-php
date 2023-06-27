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

    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;
    use Cloudonix\Entities\SubscriberDataKey as EntitySubscriberDataKey;
    use Cloudonix\Entities\VoiceApplication;

    /**
     * Subscriber Data Keys Collection
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

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function setPath(string $parentCanonicalPath): void
        {
            $this->canonicalPath = $parentCanonicalPath . "/subscribers/" . $this->msisdn;
        }

        public function refresh(): SubscriberDataKeys
        {
            $this->refreshCollectionData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        protected function refreshCollectionData(mixed $param): array
        {
            $this->collection = [];
            foreach ($param as $value) {
                $this->collection[] = new EntitySubscriberDataKey($this->parent, $value->subscriber->msisdn, $value);
            }
            $this->collectionCount = count($this->collection);
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
                if ($result->code == 204) {
                    parent::offsetUnset($offset);
                }
            }
        }
    }