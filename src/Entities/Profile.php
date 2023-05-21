<?php

    namespace Cloudonix\Entities;

    use Cloudonix\Client as CloudonixClient;
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
    class Profile extends CloudonixEntity
    {
        protected mixed $client;
        protected string $canonicalPath = "";

        /**
         * Profile DataModel Object Constructor
         *
         * @param CloudonixClient $client A CloudonixClient HTTP Connector Object
         */
        public function __construct(mixed $profileStdObject, mixed $profileBranch)
        {
            parent::__construct($profileStdObject);
            $this->client = $profileBranch->client;
            $this->setPath($profileBranch->canonicalPath);
            $this->buildProfile($profileStdObject);
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
         * Build the local profile properties
         *
         * @param mixed $profileStdObject
         *
         * @return void
         */
        private function buildProfile(mixed $profileStdObject): void
        {
            foreach ($profileStdObject as $key => $value) {
                $this->$key = $value;
            }
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
            $this->buildProfile($this->client->httpConnector->request("GET", $this->getPath())->profile);
            return $this;
        }

        /**
         * Set or update a profile key value pair
         *
         * @param string      $key
         * @param string|null $value
         *
         * @return $this
         */
        public function setProperty(string $key, string $value = null): Profile
        {
            $this->client->httpConnector->request("PUT", $this->getPath(), ['profile' => [$key => $value]]);
            if (is_null($value))
                unset($this->$key);

            return $this->refresh();
        }

        /**
         * Unset a profile key
         *
         * @param string $key
         *
         * @return $this
         */
        public function unsetProperty(string $key): Profile
        {
            return $this->setProperty($key);
        }

    }