<?php
    /**
     * @package cloudonix-php
     * @filename: Entities/Application.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=apikeys
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created :  2023-05-14
     */

    namespace Cloudonix\Entities;

    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;

    /**
     * API Key Data Model Entity
     *
     * This class represents the generalised form of a Cloudonix API Key object.
     *
     * @property-read int    $id                     Apikey Numeric ID
     * @property-read string $name                   Apikey Name
     * @property-read int    $tenantId               Tenant Numeric ID
     * @property-read int    $domainId               Domain Numeric ID
     * @property-read int    $applicationId          Application Numeric ID
     * @property-read int    $subscriberId           Subscriber Numeric ID
     * @property-read string $userId                 Cockpit User ID
     * @property      bool   $active                 API Key Status
     * @property-read string $keyId                  API Key String
     * @property-read string $secret                 API Key Secret
     * @property-read string $type                   API Key Type (Informational Only)
     * @property-read string $accessRights           API Key Access Rights
     * @property-read string $createdAt              API Key Creation Date and time
     * @property-read string $modifiedAt             API Key Last Modification Date and time
     * @property-read string $deletedAt              API Key Deletion Date and time
     */
    class Apikey extends CloudonixEntity
    {
        protected mixed $client;
        protected string $parentBranch;
        protected string $canonicalPath;

        /**
         * Domain DataModel Object Constructor
         *
         * @param string      $keyId                  A Cloudonix Apikey (Designated as XI....)
         * @param mixed       $parentBranch           A reference to the previous data model node
         * @param object|null $apikeyObject           A Cloudonix Apikey Object as stdClass
         *                                            If $apikeyObject is provided, it will be used to build the Domain
         *                                            Entity object
         */
        public function __construct(string $keyId, mixed $parentBranch, mixed $apikeyObject = null)
        {
            $this->client = $parentBranch->getClient();
            parent::__construct($this, $parentBranch);
            $this->parentBranch = $parentBranch;
            $this->setPath($keyId, $parentBranch->canonicalPath);
            if (!is_null($apikeyObject)) {
                $this->buildEntityData($apikeyObject);
                $this->setPath($apikeyObject->id, $parentBranch->canonicalPath);
            } else {
                $this->setPath($keyId, $parentBranch->canonicalPath);
                $this->refresh();
            }
        }

        /**
         * Delete the API Key
         *
         * @return bool     True on success, false on failure
         */
        public function delete(): bool
        {
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " " . $this->getPath());
            $result = $this->client->httpConnector->request("DELETE", $this->getPath());
            if ($result->code == 204)
                return true;

            return false;
        }

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function setPath(string $string, string $branchPath): void
        {
            if (!isset($this->canonicalPath))
                $this->canonicalPath = $branchPath . URLPATH_APIKEYS . "/" . $string;
        }

        protected function refresh(): Apikey
        {
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        private function buildEntityData(mixed $input): void
        {
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " input: " . json_encode($input));
            if (!is_null($input))
                foreach ($input as $key => $value) {
                    if ($key == "subscriber") {
                        $this->accessRights = "subscriber";
                    } else if ($key == "application") {
                        $this->accessRights = "application";
                    } else if ($key == "domain") {
                        $this->accessRights = "domain";
                    } else if ($key == "tenant") {
                        $this->accessRights = "tenant";
                    } else {
                        $this->$key = $value;
                    }
                }
        }
    }