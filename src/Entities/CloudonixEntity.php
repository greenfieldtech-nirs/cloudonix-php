<?php
    /**
     * @package cloudonix-php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @file    Entities/CloudonixEntity.php
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Entities;

    /**
     * Cloudonix Entity Abstract Class
     *
     * This class represents the generalised form of a Cloudonix data model entity.
     */
    abstract class CloudonixEntity
    {
        public function __construct(object|null $child, object $parent = null)
        {
            if (!is_null($child))
                foreach ($child as $key => $value) {
                    if ($key == "canonicalPath") continue;
                    if (!is_object($value)) $this->$key = $value;
                }
        }

        abstract public function getPath();

        public function delete(): bool
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath());
            if ($result->code == 204)
                return true;

            return false;
        }

        /**
         * Return the client object
         *
         * @return mixed
         */
        public function getClient(): mixed
        {
            return $this->client;
        }

        public function __toString(): string
        {
            $this->refresh();
            return json_encode($this);
        }

        public function __get(mixed $name)
        {
            $this->refresh();
            return $this->$name;
        }

        public function __set(string $name, mixed $value)
        {
            $this->$name = $value;
        }

        protected function refresh(): self
        {
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        protected function buildEntityData(object|array $input): void
        {
            foreach ($input as $key => $value) {
                $this->$key = $value;
            }
        }
    }