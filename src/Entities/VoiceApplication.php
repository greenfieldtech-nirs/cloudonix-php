<?php

    namespace Cloudonix\Entities;

    use Cloudonix\Collections\Dnids as CollectionDnids;

    use Cloudonix\Collections\Subscribers as CollectionSubscribers;
    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\Entities\Profile as EntityProfile;
    use Cloudonix\Entities\Dnid as EntityDnid;
    use Cloudonix\Helpers\UtilityHelper as UtilityHelper;

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
     * Voice Application Data Model Entity
     * This class represents the generalised form of a Cloudonix Application object.
     *
     * @package cloudonixPhp
     * @filename: Entities/Application.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=applications
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created :  2023-05-14
     *
     * @property-read int           $id                     Application Numeric ID
     * @property-read int           $domainId               Domain Numeric ID
     * @property-read string        $name                   Application Name
     * @property      bool          $active                 Application Status
     * @property-read string        $createdAt              Application Creation Date and time
     * @property-read string        $modifiedAt             Application Last Modification Date and time
     * @property-read string        $deletedAt              Application Deletion Date and time
     * @property-read string        $type                   Application type
     * @property      string        $url                    Application URL
     * @property      string        $method                 Application Method (GET or POST)
     * @property      EntityProfile $profile                Application Profile Object
     */
    class VoiceApplication extends CloudonixEntity
    {
        protected mixed $client;
        protected string $canonicalPath = "";
        public CollectionDnids $collectionDnids;

        /**
         * Application DataModel Object Constructor
         *
         * @param string      $containerApplicationBlockName   Cloudonix Voice Application Name or ID
         * @param mixed       $parentBranch                    A reference to the previous data model node
         * @param object|null $containerApplicationBlockObject A Cloudonix Application object
         *                                                     If $applicationObject is provided, it will be used to
         *                                                     build the Application Entity object
         */
        public function __construct(string $containerApplicationBlockName, mixed $parentBranch, object $containerApplicationBlockObject = null)
        {
            parent::__construct($this);
            $this->client = $parentBranch->client;
            $this->setPath($containerApplicationBlockName, $parentBranch->canonicalPath);
            if (is_null($containerApplicationBlockObject)) {
                $this->refresh();
            } else {
                $this->buildEntityData($containerApplicationBlockObject);
            }
        }

        public function dnid(string $dnid): EntityDnid
        {
            return new Dnid($dnid, $this);
        }

        public function dnids(): CollectionDnids
        {
            if (!isset($this->collectionDnids))
                $this->collectionDnids = new CollectionDnids($this);

            return $this->collectionDnids;
        }

        public function newDnidPrefix(string $dnid): EntityDnid
        {
            $dnidResult = $this->client->httpConnector->request("POST", $this->getPath() . "/dnids", [
                'source' => $dnid,
                'expression' => false,
                'prefix' => true,
                'asteriskCompatible' => false
            ]);
            return new EntityDnid($dnidResult->id, $this, $dnidResult);
        }

        public function newDnidRegex(string $dnid): EntityDnid
        {
            $dnidResult = $this->client->httpConnector->request("POST", $this->getPath() . "/dnids", [
                'source' => $dnid,
                'expression' => true,
                'prefix' => false,
                'asteriskCompatible' => false
            ]);
            return new EntityDnid($dnidResult->id, $this, $dnidResult);
        }

        public function newDnidAsterisk(string $dnid): EntityDnid
        {
            $dnidResult = $this->client->httpConnector->request("POST", $this->getPath() . "/dnids", [
                'source' => $dnid,
                'expression' => false,
                'prefix' => false,
                'asteriskCompatible' => true
            ]);
            return new EntityDnid($dnidResult->id, $this, $dnidResult);
        }

        public function newDnidPattern(string $dnid): EntityDnid
        {
            $dnidResult = $this->client->httpConnector->request("POST", $this->getPath() . "/dnids", [
                'source' => $dnid,
                'expression' => false,
                'prefix' => false,
                'asteriskCompatible' => false
            ]);
            return new EntityDnid($dnidResult->id, $this, $dnidResult);
        }

        /**
         * Set the Voice Application remote URL and method
         *
         * @param string $url    Remote Voice Application URL
         * @param string $method Remote Voice Application URL method [GET/POST]
         *
         * @return $this            A refreshed Voice Application object
         */
        public function setApplicationUrl(string $url, string $method = "POST"): VoiceApplication
        {
            if ((strtoupper($method) != "POST") && (strtoupper($method) != "GET"))
                return $this;

            $utilityHelper = new UtilityHelper();
            $validatedUrl = $utilityHelper->cleanUrl($url);

            if (!$validatedUrl)
                return $this;

            $this->client->httpConnector->request("PATCH", $this->getPath(), [
                'url' => $validatedUrl,
                'method' => strtoupper($method)
            ]);
            return $this->refresh();
        }

        public function setActive(bool $status): Domain
        {
            $this->client->httpConnector->request("PATCH", $this->getPath(), ['active' => $status]);
            return $this->refresh();
        }

        public function delete(): bool
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath());
            if ($result->code == 204)
                return true;
            return false;
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
         * @param string $branchPath
         *
         * @return void
         */
        protected function setPath(string $string, string $branchPath)
        {
            if (!strlen($this->canonicalPath))
                $this->canonicalPath = $branchPath . URLPATH_APPLICATIONS . "/" . $string;
        }

        protected function refresh(): VoiceApplication
        {
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        /**
         * Build the local Application properties
         *
         * @param mixed $applicationStdObject
         *
         * @return void
         */
        protected function buildEntityData(mixed $applicationStdObject): void
        {
            if (!is_null($applicationStdObject))
                foreach ($applicationStdObject as $key => $value) {
                    if ($key == "profile") {
                        $myProfile = new EntityProfile($value, $this);
                        $this->profile = $myProfile;
                    } else if ($key == "domain") {
                        continue;
                    } else {
                        $this->$key = $value;
                    }
                }
        }
    }