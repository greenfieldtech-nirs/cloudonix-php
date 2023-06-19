<?php
    /**
     * @package cloudonix-php
     * @file    Entities/Subscriber.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=subscriber
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Entities;

    use Cloudonix\Helpers\UtilityHelper as UtilityHelper;

    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\Entities\Profile as EntityProfile;

    /**
     * Subscriber Data Model Entity
     *
     * This class represents the generalised form of a Cloudonix Subscriber object.
     *
     * @property-read int           $id                            Subscriber Numeric ID
     * @property-read string        $msisdn                        Subscriber MSISDN
     * @property-read int           $domainId                      Domain Numeric ID
     * @property      bool          $active                        Subscriber Status
     * @property      string        $sipPassword                   Subscriber SIP Password
     * @property-read string        $createdAt                     Subscriber Creation Date and time
     * @property-read string        $modifiedAt                    Subscriber Last Modification Date and time
     * @property-read string        $deletedAt                     Subscriber Deletion Date and time
     * @property      EntityProfile $profile                       Subscriber Profile Object
     */
    class Subscriber extends CloudonixEntity
    {
        protected mixed $client;
        protected string $parentBranch;
        protected string $canonicalPath;

        /**
         * Subscriber DataModel Object Constructor
         *
         * @param string      $subscriber             A Cloudonix Subscriber Name (or MSISDN)
         * @param mixed       $parentBranch           A reference to the previous data model node
         * @param object|null $subscriberObject       A Cloudonix Subscriber Object as stdClass
         *                                            If $subscriberObject is provided, it will be used to build the
         *                                            Domain Entity object
         */
        public function __construct(string $subscriber, mixed $parentBranch, object $subscriberObject = null)
        {
            $this->client = $parentBranch->getClient();
            parent::__construct($subscriberObject, $parentBranch);
            $this->setPath($subscriber, $parentBranch->canonicalPath);
            $this->buildEntityData($subscriberObject);
        }

        /**
         * Delete the subscriber
         *
         * Delete a subscriber from the domain. If $force is set to true, active sessions of the
         * subscriber will be terminated and the subscriber will be deleted.
         *
         * @param bool $force
         *
         * @return bool
         */
        public function delete(bool $force = false): bool
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath() . (($force) ? "?can-deactivate=true" : ""));
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " result: " . json_encode($result));
            if ($result->code == 204)
                return true;
            return false;
        }

        /**
         * Reset the subscriber SIP Password.
         *
         * Specifying "GEN" as the password input will auto-generate a secured password.
         * The new password will be returned as part of the updated subscriber Object.
         *
         * @param string|null $password
         *
         * @return $this
         */
        public function resetSipPassword(string $password = null): Subscriber
        {
            if ($password == "GEN") {
                $passwd = new UtilityHelper();
                $password = $passwd->generateSecuredPassword();
            }
            $result = $this->client->httpConnector->request("PATCH", $this->getPath(), ['sip-password' => $password]);
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " result: " . json_encode($result));
            $this->buildEntityData($result);
            return $this;
        }

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function setPath(string $string, string $branchPath): void
        {
            if (!isset($this->canonicalPath))
                $this->canonicalPath = $branchPath . URLPATH_SUBSCRIBERS . "/" . $string;
        }

        private function buildEntityData(mixed $input): void
        {
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " input: " . json_encode($input));
            foreach ($input as $key => $value) {
                if ($key == "profile") {
                    $this->profile = new EntityProfile($value, $this);
                } else {
                    $this->$key = $value;
                }
            }
        }

        protected function refresh(): Subscriber
        {
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        public function __toString(): string
        {
            return json_encode($this->refresh());
        }
    }