<?php
    /**
     * @package cloudonix-php
     * @file    Entities/Profile.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Entities;

    use ArrayIterator;
    use Traversable;

    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;

    /**
     * Profile Data Model Entity
     *
     * This class represents the generalised form of a Cloudonix Profile object.
     * Profiles are used in Cloudonix with various data models - and are free formed.
     */
    class Profile extends CloudonixEntity implements \IteratorAggregate, \ArrayAccess
    {
        protected mixed $client;
        protected string $canonicalPath;

        /**
         * Profile DataModel Object Constructor
         *
         * @param CloudonixEntity $parent      The parent object that created this object
         * @param ?object         $inputObject Cloudonix Profile as Standard Object
         */
        public function __construct(CloudonixEntity $parent, mixed $inputObject = null)
        {
            $this->client = $parent->getClient();
            parent::__construct($inputObject, $parent);
            $this->setPath($parent->getPath());
            $this->buildEntityData($inputObject);
        }

        /**
         * Set an array of key-value pairs to the profile
         *
         * @param array $data
         *
         * @return void
         */
        public function set(array $data): void
        {
            $result = $this->client->httpConnector->request("PATCH", $this->getPath(), [
                "profile" => $data
            ]);
            $this->buildEntityData($result->profile);
        }

        public function offsetExists(mixed $offset): bool
        {
            return isset($this->$offset);
        }

        public function offsetGet(mixed $offset): mixed
        {
            return $this->$offset;
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            if ($value == null) {
                $this->offsetUnset($offset);
            } else {
                $result = $this->client->httpConnector->request("PATCH", $this->getPath(), [
                    "profile" => [$offset => $value]
                ]);
                $this->$offset = $value;
            }
        }

        public function offsetUnset(mixed $offset): void
        {
            $result = $this->client->httpConnector->request("PATCH", $this->getPath(), [
                "profile" => [$offset => null]
            ]);
            unset($this->$offset);
        }

        public function getIterator(): Traversable
        {
            return new ArrayIterator($this);
        }

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function setPath(string $string): void
        {
            if (!isset($this->canonicalPath)) {
                $this->canonicalPath = $string;
            }
        }

        public function refresh(): self
        {
            $result = $this->client->httpConnector->request("GET", $this->getPath());
            $this->buildEntityData($result->profile);
            return $this;
        }

        protected function buildEntityData(object|array|null $input): void
        {
            foreach ($input as $key => $value) {
                $this->$key = $value;
            }
        }

    }