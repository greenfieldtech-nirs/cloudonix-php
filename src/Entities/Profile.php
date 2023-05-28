<?php

    namespace Cloudonix\Entities;

    use ArrayIterator;
    use Traversable;

    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;

    /**
     * <code>
     *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
     * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
     * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
     * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
     * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
     *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
     * </code>
     *
     * Profile Data Model Entity
     * This class represents the generalised form of a Cloudonix Profile object.
     * Profiles are used in Cloudonix with various data models - and is free formed.
     *
     * @package cloudonixPhp
     * @file    Entities/Profile.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */
    class Profile extends CloudonixEntity implements \IteratorAggregate, \ArrayAccess
    {
        protected mixed $client;
        protected string $canonicalPath = "";
        protected array $profile;

        /**
         * Profile DataModel Object Constructor
         *
         * @param object $profileStdObject Cloudonix Profile as Standard Object
         * @param mixed  $parentBranch     A reference to the previous data model node
         */
        public function __construct(mixed $profileStdObject, mixed $parentBranch)
        {
            $this->client = $parentBranch->client;
            $this->setPath($parentBranch->canonicalPath);
            $this->buildEntityData($profileStdObject);
            parent::__construct($profileStdObject);
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
         *
         * @return void
         */
        protected function setPath(string $string): void
        {
            if (!strlen($this->canonicalPath))
                $this->canonicalPath = $string;
        }

        /**
         * Refresh the local profile storage with the remote profile data
         *
         * @return $this
         */
        public function refresh(): Profile
        {
            $this->profile = [];
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath())->profile);
            return $this;
        }

        /**
         * Build the local profile properties
         *
         * @param mixed $profileStdObject
         *
         * @return void
         */
        protected function buildEntityData(mixed $profileStdObject): void
        {
            if (!is_null($profileStdObject))
                foreach ($profileStdObject as $key => $value) {
                    $this->profile[$key] = $value;
                }
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
                $this->buildEntityData($this->client->httpConnector->request("PATCH", $this->getPath(), [
                    "profile" => [ $offset => $value ]
                ])->profile);
            }
        }

        public function offsetUnset(mixed $offset): void
        {
            $this->buildEntityData($this->client->httpConnector->request("PATCH", $this->getPath(), [
                "profile" => [ $offset => null ]
            ])->profile);
        }

        public function set(array $data): void
        {
            $this->buildEntityData($this->client->httpConnector->request("PATCH", $this->getPath(), [
                "profile" => $data
            ])->profile);
        }

        /**
         * @inheritDoc
         */
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

    }