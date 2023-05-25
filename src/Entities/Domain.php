<?php

    namespace Cloudonix\Entities;

    use Cloudonix\Collections\VoiceApplications as CollectionVoiceApplications;

    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\Entities\Dnid as EntityDnid;
    use Cloudonix\Entities\Domain as EntityDomain;
    use Cloudonix\Entities\Profile as EntityProfile;
    use Cloudonix\Entities\VoiceApplication as EntityApplication;
    use Cloudonix\Entities\ContainerApplication as EntityContainerApplication;

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
     * @property      string            $domain                              Domain name, usually an FQDN
     * @property      bool              $active                              Domain Status
     * @property-read string            $createdAt                           Domain Creation Date and time
     * @property-read string            $modifiedAt                          Domain Last Modification Date and time
     * @property-read string            $deletedAt                           Domain Deletion Date and time
     * @property      bool              $registrationFree                    Domain RegFree Dialing Status
     * @property-read EntityApplication $defaultApplication                  Domain Default Application ID
     * @property-read string            $uuid                                Domain UUID
     * @property      EntityProfile     $profile                             Domain Profile Object
     * @property      int               $defaultApplicationId                Domain Default Application Object
     */
    class Domain extends CloudonixEntity
    {
        protected mixed $client;
        protected string $canonicalPath = "";
        protected CollectionVoiceApplications $collectionVoiceApplications;
        protected CollectionContainerApplications $collectionContainerApplications;

        /**
         * Domain DataModel Object Constructor
         *
         * @param string      $domain                 A Cloudonix Domain Name
         * @param mixed       $parentBranch           A reference to the previous data model node
         * @param object|null $domainBlockBlockObject A Cloudonix Domain Object as stdClass
         *                                  If $domainObject is provided, it will be used to build the Domain Entity
         *                                  object
         */
        public function __construct(string $domain, mixed $parentBranch, object $domainBlockBlockObject = null)
        {
            $this->client = $parentBranch->client;
            if (!is_null($domainBlockBlockObject)) {
                $this->buildEntityData($domainBlockBlockObject);
                $this->setPath($domainBlockBlockObject->id, $parentBranch->canonicalPath);
            } else {
                $this->setPath($domain, $parentBranch->canonicalPath);
            }
            parent::__construct($this);
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

            return $this->collectionVoiceApplications;
        }

        public function newVoiceApplicationFromUrl(string $name, string $url, string $method): EntityApplication
        {
            $canonicalPath = $this->getPath() . URLPATH_APPLICATIONS;
            $newApplicationObject = $this->client->httpConnector->request('POST', $canonicalPath, [
                'name' => $name
            ]);
            return new EntityApplication($name, $this, $newApplicationObject);
        }

        public function newVoiceApplicationFromContainer(string $name, string $url, string $method): EntityApplication
        {
            $canonicalPath = $this->getPath() . URLPATH_APPLICATIONS;
            $newApplicationObject = $this->client->httpConnector->request('POST', $canonicalPath, [
                'name' => $name
            ]);
            return new EntityDomain($domain, $this, $newDomain);
        }

        /**
         * Set RegFree Dialing information for the Domain
         *
         * @param string $regFreeEndpoint
         * @param string $regFreeApikey
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