<?php
    /**
     * @package cloudonixPhp
     * @file    Entities/VoiceApplicationSubscriberData.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Entities;

    use ArrayIterator;
    use Traversable;

    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\Entities\VoiceApplication as EntityVoiceApplication;

    /**
     * VoiceApplication Subscriber Data Model Entity
     *
     * Cloudonix voice applications allow the developer to securely store subscriber related information, in a
     * similar fashion to how web applications can store data in cookies. Data is stored in string format, providing
     * the developer with maximum flexibility in storing JSON or other encoded data.
     *
     * @property      string $key                    Subscriber data key
     * @property      string $data                   Subscriber data storage
     * @property-read string $createdAt              Subscriber data Creation Date and time
     * @property-read string $modifiedAt             Subscriber data Last Modification Date and ti,e
     */
    class VoiceApplicationSubscriberData extends CloudonixEntity implements \IteratorAggregate, \ArrayAccess
    {
        protected mixed $client;
        protected string $canonicalPath = "";
        protected array $subscriberData;

        /**
         * VoiceApplication Subscriber Data DataModel Object Constructor
         *
         * @param EntityVoiceApplication $voiceApplicationObject Cloudonix Voice Application Object
         * @param string                 $subscriber             Cloudonix Subscriber ID or MSISDN
         */
        public function __construct(EntityVoiceApplication $voiceApplicationObject, string $subscriber)
        {
            $this->client = $voiceApplicationObject->client;
            parent::__construct($this);
            $this->setPath($voiceApplicationObject->canonicalPath, $subscriber);
        }

        /**
         * Return the entity REST API canonical path
         *
         * @return string
         */
        public function getPath()
        {
            return $this->canonicalPath;
        }

        /**
         * Set the entity REST API canonical path
         *
         * @param string $string
         * @param string $subscriber
         *
         * @return void
         */
        protected function setPath(string $string, string $subscriber): void
        {
            if (!strlen($this->canonicalPath))
                $this->canonicalPath = $string . "/subscriber-data/" . $subscriber;
        }

        /**
         * Refresh the local storage with the remote data
         *
         * @return $this
         */
        public function refresh(): VoiceApplicationSubscriberData
        {
            $this->subscriberData = [];
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        public function delete(): bool
        {
            return false;
        }

        /**
         * Build the voice application subscribers data
         *
         * @param mixed $inputObject
         *
         * @return void
         */
        protected function buildEntityData(mixed $inputObject): void
        {
            if (!is_null($inputObject))
                foreach ($inputObject as $key => $value) {
                    $this->subscriberData[$key] = $value;
                }
        }

        public function offsetExists(mixed $offset): bool
        {
            $this->refresh();
            return isset($this->subscriberData[$offset]);
        }

        public function offsetGet(mixed $offset): mixed
        {
            $this->refresh();
            return $this->subscriberData[$offset];
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            if (!is_null($offset)) {
                $this->buildEntityData($this->client->httpConnector->request("PUT", $this->getPath() . "/" . $offset, $value));
            }
        }

        public function offsetUnset(mixed $offset): void
        {
            $this->buildEntityData($this->client->httpConnector->request("PUT", $this->getPath() . "/" . $offset, null));
        }

        public function set(array $data): void
        {
            foreach ($data as $key => $value) {
                $this->buildEntityData($this->client->httpConnector->request("PUT", $this->getPath() . "/" . $key, $value));
            };
        }

        /**
         * @inheritDoc
         */
        public function getIterator(): Traversable
        {
            $this->refresh();
            return new ArrayIterator($this->subscriberData);
        }

        public function __toString(): string
        {
            $this->refresh();
            return json_encode($this->subscriberData);
        }

    }