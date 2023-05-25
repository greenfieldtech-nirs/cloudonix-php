<?php

    namespace Cloudonix\Entities;

    use Cloudonix\DataModel\CloudonixEntity as CloudonixEntity;

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
     * API Key Data Model Entity
     * This class represents the generalised form of a Cloudonix Application object.
     *
     * @package cloudonixPhp
     * @filename: Entities/Application.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=apikeys
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created :  2023-05-14
     *
     * @property-read int              $id                     Application Numeric ID
     * @property-read int              $domainId               Domain Numeric ID
     * @property-read string           $name                   Application Name
     * @property      bool             $active                 Application Status
     * @property-read string           $createdAt              Application Creation Date and time
     * @property-read string           $modifiedAt             Application Last Modification Date and time
     * @property-read string           $deletedAt              Application Deletion Date and time
     * @property-read string           $type                   Application type
     * @property      string           $url                    Application URL
     * @property      string           $method                 Application Method (GET or POST)
     * @property      CloudonixProfile $profile                Application Profile Object
     */
    class Apikey extends CloudonixEntity
    {
        protected mixed $client;
        protected string $canonicalPath = "";

        /**
         * Domain DataModel Object Constructor
         *
         * @param string $apikey            Cloudonix Apikey ID
         * @param mixed  $parentBranch A reference to the previous data model node
         */
        public function __construct(string $apikey, mixed $parentBranch)
        {
            parent::__construct($this);
            $this->client = $parentBranch->client;
            $this->parentBranch = $parentBranch;
            $this->setPath($apikey, $parentBranch->canonicalPath);
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
         * @param string $string
         *
         * @return void
         */
        protected function setPath(string $string, string $branchPath): void
        {
            if (!strlen($this->canonicalPath))
                $this->canonicalPath = $branchPath . URLPATH_APIKEYS . "/" . $string;
        }

        protected function refresh(): Dnid
        {
            $this->buildApikey($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        /**
         * Build the local Dnid properties
         *
         * @param mixed $dnidStdObject
         *
         * @return void
         */
        private function buildApikey(mixed $apikeyStdObject): void
        {
            if (!is_null($apikeyStdObject))
                foreach ($apikeyStdObject as $key => $value) {
                    if ($key == "application") {
                        continue;
                    } else {
                        $this->$key = $value;
                    }
                }
        }
    }