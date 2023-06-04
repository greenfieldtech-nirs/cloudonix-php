<?php
    /**
     * @package cloudonixPhp
     * @file    Entities/Dnid.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=dnid
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Entities;
    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;

    /**
     * DNID Data Model Entity
     *
     * This class represents the generalised form of a Cloudonix DNID object.
     *
     * @property-read int    $id                                     DNID Numeric ID
     * @property-read int    $domainId                               DNID Domain Numeric ID
     * @property-read int    $applicationId                          DNID Application Numeric ID
     * @property      bool   $active                                 DNID Status
     * @property-read string $dnid                                   DNID Translated Regular Expression String
     * @property      string $source                                 DNID Source Number
     * @property-read bool   $global                                 DNID Source Number string Global Status
     * @property      bool   $expression                             DNID Source Number string is regex based
     * @property      bool   $prefix                                 DNID Source Number string is prefix based
     * @property      bool   $asteriskCompatible                     DNID Source Number string is Asterisk based
     */
    class Dnid extends CloudonixEntity
    {
        protected mixed $client;
        protected string $canonicalPath = "";

        /**
         * DNID DataModel Object Constructor
         *
         * @param string      $dnid                   Cloudonix DNID ID
         * @param mixed       $parentBranch           A reference to the previous data model node
         * @param object|null $dnidObject             A Cloudonix DNID Object as stdClass
         *                                            If $dnidObject is provided, it will be used to build the Domain
         *                                            Entity object
         */
        public function __construct(string $dnid, mixed $parentBranch, object $dnidObject = null)
        {
            $this->client = $parentBranch->client;
            parent::__construct($this->client);
            if (!is_null($dnidObject)) {
                $this->buildEntityData($dnidObject);
                $this->setPath($dnidObject->id, $parentBranch->canonicalPath);
            } else {
                $this->setPath($dnid, $parentBranch->canonicalPath);
            }
        }

        /**
         * Set DNID status
         *
         * @param bool $status
         *
         * @return $this
         */
        public function setActive(bool $status): Dnid
        {
            $this->client->httpConnector->request("PATCH", $this->getPath(), ['status' => $status]);
            return $this->refresh();
        }

        /**
         * Set DNID based upon a RegEx string
         *
         * @param string $input
         *
         * @return $this
         */
        public function setDnidRegex(string $input): Dnid
        {
            $this->client->httpConnector->request("PATCH", $this->getPath(), [
                'source' => $input,
                'expression' => true,
                'prefix' => false,
                'asteriskCompatible' => false
            ]);
            return $this->refresh();
        }

        /**
         * Set DNID based upon a Prefix string
         *
         * @param string $input
         *
         * @return $this
         */
        public function setDnidPrefix(string $input): Dnid
        {
            $this->client->httpConnector->request("PATCH", $this->getPath(), [
                'source' => $input,
                'expression' => false,
                'prefix' => true,
                'asteriskCompatible' => false
            ]);
            return $this->refresh();
        }

        /**
         * Set DNID based upon an Asterisk extensions.conf pattern
         *
         * @param string $input
         *
         * @return $this
         */
        public function setDnidAsterisk(string $input): Dnid
        {
            $this->client->httpConnector->request("PATCH", $this->getPath(), [
                'source' => $input,
                'expression' => false,
                'prefix' => false,
                'asteriskCompatible' => true
            ]);
            return $this->refresh();
        }

        /**
         * Delete a DNID
         *
         * @return bool
         */
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
        public function getPath(): string
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
        protected function setPath(string $string, string $branchPath): void
        {
            if (!strlen($this->canonicalPath))
                $this->canonicalPath = $branchPath . URLPATH_DNIDS . "/" . $string;
        }

        protected function refresh(): Dnid
        {
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        /**
         * Build the local Dnid properties
         *
         * @param mixed $dnidStdObject
         *
         * @return void
         */
        protected function buildEntityData(mixed $dnidStdObject): void
        {
            if (!is_null($dnidStdObject))

                foreach ($dnidStdObject as $key => $value) {
                    if ($key == "application") {
                        continue;
                    } else {
                        $this->$key = $value;
                    }
                }
        }
    }