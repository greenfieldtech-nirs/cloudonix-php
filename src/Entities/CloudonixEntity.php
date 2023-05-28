<?php

    namespace Cloudonix\Entities;

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
     * Cloudonix Entity Class
     * This class represents the generalised form of a Cloudonix data model entity.
     *
     * @package cloudonixPhp
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @file    Entities/CloudonixEntity.php
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */
    abstract class CloudonixEntity
    {
        protected string $canonicalPath;

        public function __construct(mixed $client)
        {
            if (!is_null($client))
                foreach ($client as $key => $value) {
                    if (!is_object($value)) $this->$key = $value;
                }
        }

        abstract public function getPath();

        abstract protected function buildEntityData(mixed $input);

        abstract protected function refresh();

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