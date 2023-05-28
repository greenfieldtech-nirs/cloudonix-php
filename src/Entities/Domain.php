<?php

    namespace Cloudonix\Entities;

    use Cloudonix\Collections\VoiceApplications as CollectionVoiceApplications;
    use Cloudonix\Collections\Apikeys as CollectionApikeys;

    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\Entities\Dnid as EntityDnid;
    use Cloudonix\Entities\Profile as EntityProfile;
    use Cloudonix\Entities\VoiceApplication as EntityApplication;
    use Cloudonix\Entities\Apikey as EntityApikey;

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
     * Domain Data Model Entity
     * This class represents the generalised form of a Cloudonix Domain object.
     *
     * @package  cloudonixPhp
     * @filename Entities/Domain.php
     * @author   Nir Simionovich <nirs@cloudonix.io>
     * @see      https://dev.docs.cloudonix.io/#/platform/api-core/models?id=domains
     * @license  MIT License (https://choosealicense.com/licenses/mit/)
     * @created  2023-05-14
     *
     * @property-read int               $id                                  Domain Numeric ID
     * @property-read int               $tenantId                            Tenant Numeric ID
     * @property      int               $defaultApplicationId                Domain Default Application ID
     * @property      string            $domain                              Domain name, usually an FQDN
     * @property-read string            $createdAt                           Domain Creation Date and time
     * @property-read string            $modifiedAt                          Domain Last Modification Date and time
     * @property-read string            $deletedAt                           Domain Deletion Date and time
     * @property-read string            $uuid                                Domain UUID
     * @property      bool              $active                              Domain Status
     * @property      bool              $registrationFree                    Domain RegFree Dialing Status
     * @property      EntityProfile     $profile                             Domain Profile Object
     * @property-read EntityApplication $defaultApplication                  Domain Default Application Object
     */
    class Domain extends CloudonixEntity
    {
        protected mixed $client;
        protected string $canonicalPath = "";
        public CollectionVoiceApplications $collectionVoiceApplications;
        public CollectionApikeys $collectionApikeys;

        /**
         * Domain DataModel Object Constructor
         *
         * @param string      $domain                 A Cloudonix Domain Name
         * @param mixed       $parentBranch           A reference to the previous data model node
         * @param object|null $domainObject           A Cloudonix Domain Object as stdClass
         *                                            If $domainObject is provided, it will be used to build the Domain
         *                                            Entity object
         */
        public function __construct(string $domain, mixed $parentBranch, object $domainObject = null)
        {
            $this->client = $parentBranch->client;
            parent::__construct($this->client);
            if (!is_null($domainObject)) {
                $this->buildEntityData($domainObject);
                $this->setPath($domainObject->domain, $parentBranch->canonicalPath);
            } else {
                $this->setPath($domain, $parentBranch->canonicalPath);
            }
        }

        /**
         * Return a new DNID Entity object
         *
         * @param string $dnid DNID Number or ID
         *
         * @return Dnid
         */
        public function dnid(string $dnid): EntityDnid
        {
            return new EntityDnid($dnid, $this);
        }

        /**
         * Return a new Voice Application Entity object
         *
         * @param string $voiceapplication
         *
         * @return VoiceApplication
         */
        public function voiceApplication(string $voiceapplication): EntityApplication
        {
            return new EntityApplication($voiceapplication, $this);
        }

        public function voiceApplications(): CollectionVoiceApplications
        {
            if (!isset($this->collectionVoiceApplications))
                $this->collectionVoiceApplications = new CollectionVoiceApplications($this);

            return $this->collectionVoiceApplications->refresh();
        }

        /**
         * Create a new voice application in the domain from a remote URL resource
         *
         * @param string $name      Voice application name
         * @param string $url       Voice application URL
         * @param string $method    Voice application HTTP Method (GET/POST)
         *
         * @return VoiceApplication
         */
        public function newVoiceApplicationFromUrl(string $name, string $url, string $method): EntityApplication
        {
            $canonicalPath = $this->getPath() . URLPATH_APPLICATIONS;
            $newApplicationObject = $this->client->httpConnector->request('POST', $canonicalPath, [
                'name' => $name,
                'url' => $url,
                'method' => $method
            ]);
            return new EntityApplication($name, $this, $newApplicationObject);
        }

        public function apikeys(): CollectionApikeys
        {
            if (!isset($this->collectionApikeys))
                $this->collectionApikeys = new CollectionApikeys($this);

            return $this->collectionApikeys->refresh();
        }

        public function apikey(string $keyId): EntityApikey
        {
            return new EntityApikey($keyId, $this);
        }

        public function newApikey(string $name): EntityApikey
        {
            $canonicalPath = $this->getPath() . URLPATH_APIKEYS;
            $newDomainApikey = $this->client->httpConnector->request('POST', $canonicalPath, [
                'name' => $name
            ]);
            return new EntityApikey($name, $this, $newDomainApikey);
        }


        /**
         * Set RegFree Dialing information for the Domain
         *
         * @param string      $regFreeEndpoint
         * @param string|null $regFreeApikey
         *
         * @return $this
         */
        public function setRegFree(string $regFreeEndpoint, string $regFreeApikey = null): Domain
        {
            $this->profile->set([
                'registration-free-control-endpoint' => $regFreeEndpoint,
                'registration-free-control-endpoint-api-key' => $regFreeApikey
            ]);
            return $this->refresh();
        }

        public function setNoAnswerTimeout(int $timeout): Domain
        {
            $this->profile['call-timeout'] = $timeout;
            return $this->refresh();
        }

        public function setBorderToBorder(bool $status): Domain
        {
            $this->profile['allowed-border'] = $status;
            return $this->refresh();
        }

        public function setRedirectToBorder(bool $status): Domain
        {
            $this->profile['redirect-unknown-to-border'] = $status;
            return $this->refresh();
        }

        public function setPlayRingtone(bool $status): Domain
        {
            $this->profile['subscribers-auto-progress	'] = $status;
            return $this->refresh();
        }

        public function setSessionUpdateCallback(string $string): Domain
        {
            $this->profile['session-update-endpoint'] = $string;
            return $this->refresh();
        }

        public function setLeastCostRoutingEndpoint(string $string): Domain
        {
            $this->profile['lcr-address'] = $string;
            return $this->refresh();
        }

        public function setMessagingProfile(string $messagingEndpoint, string $messagingBucket = null): Domain
        {
            $this->profile->set([
                'messaging-notification-url' => $messagingEndpoint,
                'messaging-archive-bucket-name' => $messagingBucket
            ]);
            return $this->refresh();
        }

        public function setCdrDeliveryEndpoint(string $string): Domain
        {
            $this->profile['cdr-endpoint'] = $string;
            return $this->refresh();
        }

        public function setWavRecordingFormat(): Domain
        {
            $this->profile['recording-media-type'] = "wav";
            return $this->refresh();
        }

        public function setMp3RecordingFormat(): Domain
        {
            $this->profile['recording-media-type'] = "mp3";
            return $this->refresh();
        }

        public function setDefaultRingingTimeout(int $timeout = 60): Domain
        {
            $this->profile['call-timeout'] = $timeout;
            return $this->refresh();
        }

        public function setOutboundSessionTimeouts(int $connection = 10, int $provisional = 2): Domain
        {
            $this->profile->set([
                'connection-timeout' => $connection,
                'provisional-timeout' => $provisional
            ]);
            return $this->refresh();
        }

        public function setCallerIdPassthrough(bool $status): Domain
        {
            $this->profile['allow-passthrough-caller-name'] = $status;
            return $this->refresh();
        }

        public function setActive(bool $status): Domain
        {
            $this->client->httpConnector->request("PATCH", $this->getPath(), ['active' => $status]);
            return $this->refresh();
        }

        public function setDefaultApplication(int $application): Domain
        {
            $this->client->httpConnector->request("PATCH", $this->getPath(), ['defaultApplication' => $application]);
            return $this->refresh();
        }

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function setPath(string $string, string $branchPath): void
        {
            if (!strlen($this->canonicalPath))
                $this->canonicalPath = $branchPath . URLPATH_DOMAINS . "/" . $string;
        }

        public function refresh(): Domain
        {
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        public function __toString(): string
        {
            return json_encode($this->refresh());
        }

        protected function buildEntityData(mixed $input): void
        {
            foreach ($input as $key => $value) {
                if ($key == "profile") {
                    $this->profile = new EntityProfile($value, $this);
                } else if ($key == "defaultApplication") {
                    $this->defaultApplicationId = $value;
                } else if ($key == "application") {
                    $this->defaultApplication = new EntityApplication($value->id, $this, $value);
                } else if ($key == "tenant") {
                    continue;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }