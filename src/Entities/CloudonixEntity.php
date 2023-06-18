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
        protected string $canonicalPath = "";
        public function __construct(mixed $client, mixed $parentBranch = null)
        {
            if (is_object($parentBranch)) {
                $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " Construction with parent " . get_class($parentBranch));
                $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " Parent canoncialPath: " . json_encode($parentBranch));
            } else {
                $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " Construction without parent");
            }

            if (!is_null($client))
                foreach ($client as $key => $value) {
                    if ($key=="canonicalPath") continue;
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

        public function __toString(): string
        {
            return json_encode($this);
        }

        public function __get(mixed $name)
        {
            return $this->$name;
        }

        public function __set(string $name, mixed $value)
        {
            $this->$name = $value;
        }
    }