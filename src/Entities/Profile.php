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
     * Profiles are used in Cloudonix with various data models - and is free formed.
     */
    class Profile extends CloudonixEntity implements \IteratorAggregate, \ArrayAccess
    {
        protected mixed $client;
        protected string $canonicalPath;

        protected array $profile = [];

        /**
         * Profile DataModel Object Constructor
         *
         * @param CloudonixEntity $parent      The parent object that created this object
         * @param ?object          $inputObject Cloudonix Profile as Standard Object
         */
        public function __construct(CloudonixEntity $parent, mixed $inputObject = null)
        {
            $this->client = $parent->getClient();
            parent::__construct($inputObject, $parent);
            $this->setPath($parent->getPath());
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " parentBranch canonical path is now: " . $parent->getPath());
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
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " result: " . json_encode($result));
            $this->buildEntityData($result->profile);
        }

        public function offsetExists(mixed $offset): bool
        {
            return isset($this->profile[$offset]);
        }

        public function offsetGet(mixed $offset): mixed
        {
            $this->refresh();
            return $this->profile[$offset];
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            if (!is_null($offset)) {
                $result = $this->client->httpConnector->request("PATCH", $this->getPath(), [
                    "profile" => [$offset => $value]
                ]);
                $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " result: " . json_encode($result));
                $this->buildEntityData($result->profile);
            }
        }

        public function offsetUnset(mixed $offset): void
        {
            $result = $this->client->httpConnector->request("PATCH", $this->getPath(), [
                "profile" => [$offset => null]
            ]);
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " result: " . json_encode($result));
            $this->buildEntityData($result->profile);
        }

        public function getIterator(): Traversable
        {
            $this->refresh();
            return new ArrayIterator($this->profile);
        }

        public function __toString(): string
        {
            $this->refresh();
            return json_encode($this->profile);
        }

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function setPath(string $string): void
        {
            if (!isset($this->canonicalPath))
                $this->canonicalPath = $string;
        }

        public function refresh(): Profile
        {
            $this->profile = [];
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        protected function buildEntityData(object|array|null $input): void
        {
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " input: " . json_encode($input));
            if (!is_null($input)) {
                foreach ($input->profile as $key => $value) {
                    $this->profile[$key] = $value;
                }
            }
        }

    }