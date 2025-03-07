<?php
    /**
     * @package  cloudonix-php
     * @filename Entities/Domain.php
     * @author   Nir Simionovich <nirs@cloudonix.io>
     * @see      https://dev.docs.cloudonix.io/#/platform/api-core/models?id=domains
     * @license  MIT License (https://choosealicense.com/licenses/mit/)
     * @created  2023-05-14
     */

    namespace Cloudonix\Entities;

    use Cloudonix\Collections\VoiceApplications as CollectionVoiceApplications;
    use Cloudonix\Collections\Apikeys as CollectionApikeys;
    use Cloudonix\Collections\Subscribers as CollectionSubscribers;
    use Cloudonix\Collections\Dnids as CollectionDnids;
    use Cloudonix\Collections\Trunks as CollectionTrunks;
    use Cloudonix\Collections\Sessions as CollectionSessions;

    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\Entities\Dnid as EntityDnid;
    use Cloudonix\Entities\Profile as EntityProfile;
    use Cloudonix\Entities\VoiceApplication as EntityVoiceApplication;
    use Cloudonix\Entities\Apikey as EntityApikey;
    use Cloudonix\Entities\Subscriber as EntitySubscriber;
    use Cloudonix\Entities\Trunk as EntityTrunk;
    use Cloudonix\Entities\Session as EntitySession;

    /**
     * Domain Data Model Entity
     *
     * This class represents the generalised form of a Cloudonix Domain object.
     *
     * @property-read int                    $id                                  Domain Numeric ID
     * @property-read int                    $tenantId                            Tenant Numeric ID
     * @property      int                    $defaultApplicationId                Domain Default Application ID
     * @property      string                 $domain                              Domain name, usually an FQDN
     * @property-read string                 $createdAt                           Domain Creation Date and time
     * @property-read string                 $modifiedAt                          Domain Last Modification Date and time
     * @property-read string                 $deletedAt                           Domain Deletion Date and time
     * @property-read string                 $uuid                                Domain UUID
     * @property      bool                   $active                              Domain Status
     * @property      bool                   $registrationFree                    Domain RegFree Dialing Status
     * @property      EntityProfile          $profile                             Domain Profile Object
     * @property-read EntityVoiceApplication $defaultApplication                  Domain Default Application Object
     */
    class Domain extends CloudonixEntity
    {
        protected mixed $client;
        protected string $canonicalPath;

        private CollectionVoiceApplications $collectionVoiceApplications;
        private CollectionApikeys $collectionApikeys;
        private CollectionSubscribers $collectionSubscribers;
        private CollectionDnids $collectionDnids;
        private CollectionTrunks $collectionTrunks;
        private CollectionSessions $collectionSessions;

        /**
         * Domain DataModel Object Constructor
         *
         * @param string  $domain                     A Cloudonix Domain Name
         * @param Tenant  $parent                     The parent object that created this object
         * @param ?object $inputObject                A Cloudonix Domain Object as stdClass
         *                                            If $domainObject is provided, it will be used to build the Domain
         *                                            Entity object
         */
        public function __construct(string $domain, Tenant $parent, ?object $inputObject = null)
        {
            $this->client = $parent->getClient();
            parent::__construct($this, $parent);
            if (!is_null($inputObject)) {
                $this->setPath($inputObject->domain, $parent->getPath());
                $this->buildEntityData($inputObject);
            } else {
                $this->setPath($domain, $parent->getPath());
            }
        }

        /**
         * Return a new Trunk Entity object
         *
         * @param string $trunk Trunk Name or ID
         *
         * @return EntityTrunk
         */
        public function trunk(string $trunk): EntityTrunk
        {
            return new EntityTrunk($trunk, $this);
        }

        /**
         * Return the domains' Trunks Collection
         *
         * @return CollectionTrunks
         */
        public function trunks(): CollectionTrunks
        {
            if (!isset($this->collectionTrunks))
                $this->collectionTrunks = new CollectionTrunks($this);

            return $this->collectionTrunks;
        }

        /**
         * Create a new inbound Trunk in the domain
         *
         * @param string $name      Name of the inbound Trunk
         * @param string $ip        IP or FQDN of the inbound Trunk
         * @param int    $port      PORT Number of the inbound Trunk (Default: 5060)
         * @param string $transport Transport of the inbound Trunk (UDP/TCP/TLS)
         * @param string $prefix    Technical prefix to match against inbound INVITE from this Trunk
         *
         * @return EntityTrunk
         */
        public function newInboundTrunk(string $name,
                                        string $ip,
                                        int    $port = 5060,
                                        string $transport = "udp",
                                        string $prefix = ""): EntityTrunk
        {
            $result = $this->client->httpConnector->request("POST", $this->getPath() . URLPATH_TRUNKS, [
                'name' => $name,
                'ip' => $ip,
                'port' => $port,
                'prefix' => $prefix,
                'transport' => $transport,
                'direction' => 'public-inbound'
            ]);
            return new EntityTrunk($name, $this, $result);
        }

        /**
         * Create a new outbound Trunk in the domain
         *
         * @param string $name      Name of the outbound Trunk
         * @param string $ip        IP number or FQDN of the outbound Trunk
         * @param int    $port      Port number of the outbound Trunk (Default: 5060)
         * @param string $transport Transport of the outbound Trunk (UDP/TCP/TLS)
         * @param string $prefix    Technical prefix to prepend to outbound calls from this trunk
         * @param int    $metric    Assigned metric for this trunk, the lower the number - the higher the priority
         *
         * @return EntityTrunk
         */
        public function newOutboundTrunk(string $name,
                                         string $ip,
                                         int    $port = 5060,
                                         string $transport = "udp",
                                         string $prefix = "",
                                         int    $metric = 10): EntityTrunk
        {
            $result = $this->client->httpConnector->request("POST", $this->getPath() . URLPATH_TRUNKS, [
                'name' => $name,
                'ip' => $ip,
                'port' => $port,
                'prefix' => $prefix,
                'transport' => $transport,
                'metric' => $metric,
                'direction' => 'public-outbound'
            ]);
            return new EntityTrunk($name, $this, $result);
        }

        /**
         * Return a new DNID Entity object
         *
         * @param string $dnid DNID Number or ID
         *
         * @return EntityDnid
         */
        public function dnid(string $dnid): EntityDnid
        {
            return new EntityDnid($dnid, $this);
        }

        /**
         * Return a collection of domain associated DNID objects
         *
         * @return CollectionDnids
         */
        public function dnids(): CollectionDnids
        {
            if (!isset($this->collectionDnids))
                $this->collectionDnids = new CollectionDnids($this);

            return $this->collectionDnids;
        }

        /**
         * Return a new Voice Application Entity object
         *
         * @param string $voiceApplication
         *
         * @return EntityVoiceApplication
         */
        public function voiceApplication(string $voiceApplication): EntityVoiceApplication
        {
            return new EntityVoiceApplication($voiceApplication, $this);
        }

        /**
         * Return a collection of domain associated Voice Application objects
         *
         * @return CollectionVoiceApplications
         */
        public function voiceApplications(): CollectionVoiceApplications
        {
            if (!isset($this->collectionVoiceApplications))
                $this->collectionVoiceApplications = new CollectionVoiceApplications($this);

            return $this->collectionVoiceApplications;
        }

        /**
         * Create a new voice application in the domain from a remote URL resource
         *
         * @param string $name   Voice application name
         * @param string $url    Voice application URL
         * @param string $method Voice application HTTP Method (GET/POST)
         *
         * @return EntityVoiceApplication
         */
        public function newVoiceApplicationFromUrl(string $name, string $url, string $method): EntityVoiceApplication
        {
            $canonicalPath = $this->getPath() . URLPATH_APPLICATIONS;
            $result = $this->client->httpConnector->request('POST', $canonicalPath, [
                'name' => $name,
                'url' => $url,
                'method' => $method
            ]);
            return new EntityVoiceApplication($name, $this, $result);
        }


        /**
         * Return a collection of domain associated API Key objects
         *
         * @return CollectionApikeys
         */
        public function apikeys(): CollectionApikeys
        {
            if (!isset($this->collectionApikeys))
                $this->collectionApikeys = new CollectionApikeys($this);

            return $this->collectionApikeys;
        }

        /**
         * Return a new Voice Application Entity object
         *
         * @param string $keyId
         *
         * @return EntityApikey
         */
        public function apikey(string $keyId): EntityApikey
        {
            return new EntityApikey($keyId, $this);
        }

        /**
         * Create a new API key in the domain
         *
         * @param string $name API Key Name
         *
         * @return EntityApikey
         */
        public function newApikey(string $name): EntityApikey
        {
            $apikeysCollection = (!isset($this->collectionApikeys)) ? $this->apikeys() : $this->collectionApikeys;
            return $apikeysCollection->create($name);
        }

        /**
         * Return a collection of domain associated Subscriber objects
         *
         * @return CollectionSubscribers
         */
        public function subscribers(): CollectionSubscribers
        {
            if (!isset($this->collectionSubscribers))
                $this->collectionSubscribers = new CollectionSubscribers($this);

            return $this->collectionSubscribers;
        }

        /**
         * Return a new Subscriber Entity object
         *
         * @param string $subscriber
         *
         * @return EntitySubscriber
         */
        public function subscriber(string $subscriber): EntitySubscriber
        {
            return new EntitySubscriber($subscriber, $this);
        }

        /**
         * Create a new Subscriber in the domain
         *
         * @param string      $subscriber  Subscriber MSISDN or other unique identified
         * @param string|null $sipPassword Subscriber SIP password
         *
         * @return EntitySubscriber
         */
        public function newSubscriber(string $subscriber, string $sipPassword = null): EntitySubscriber
        {
            $subscribersCollection = (!isset($this->collectionSubscribers)) ? $this->subscribers() : $this->collectionSubscribers;
            return $subscribersCollection->newSubscriber($subscriber, $sipPassword);
        }

        /**
         * Create a new Sessions object in the domain, based upon a predefined token
         *
         * @param string $token
         *
         * @return Session
         */
        public function session(string $token): EntitySession
        {
            return new EntitySession($token, $this);
        }

        /**
         * Return a collection of domain associated session objects
         *
         * @return CollectionSessions
         */
        public function sessions(): CollectionSessions
        {
            if (!isset($this->collectionSessions))
                $this->collectionSessions = new CollectionSessions($this);

            return $this->collectionSessions;
        }

        /**
         * Set RegFree Dialing information for the Domain
         *
         * @param string      $regFreeEndpoint
         * @param string|null $regFreeApikey
         *
         * @return $this
         */
        public function setRegFree(string $regFreeEndpoint, string $regFreeApikey = null): self
        {
            $this->profile->set([
                'registration-free-control-endpoint' => $regFreeEndpoint,
                'registration-free-control-endpoint-api-key' => $regFreeApikey
            ]);
            return $this->refresh();
        }

        public function setNoAnswerTimeout(int $timeout): self
        {
            $this->profile['call-timeout'] = $timeout;
            return $this->refresh();
        }

        public function setBorderToBorder(bool $status): self
        {
            $this->profile['allowed-border'] = $status;
            return $this->refresh();
        }

        public function setRedirectToBorder(bool $status): self
        {
            $this->profile['redirect-unknown-to-border'] = $status;
            return $this->refresh();
        }

        public function setPlayRingtone(bool $status): self
        {
            $this->profile['subscribers-auto-progress	'] = $status;
            return $this->refresh();
        }

        public function setSessionUpdateCallback(string $string): self
        {
            $this->profile['session-update-endpoint'] = $string;
            return $this->refresh();
        }

        public function setLeastCostRoutingEndpoint(string $string): self
        {
            $this->profile['lcr-address'] = $string;
            return $this->refresh();
        }

        public function setMessagingProfile(string $messagingEndpoint, string $messagingBucket = null): self
        {
            $this->profile->set([
                'messaging-notification-url' => $messagingEndpoint,
                'messaging-archive-bucket-name' => $messagingBucket
            ]);
            return $this->refresh();
        }

        public function setCdrDeliveryEndpoint(string $string): self
        {
            $this->profile['cdr-endpoint'] = $string;
            return $this->refresh();
        }

        public function setWavRecordingFormat(): self
        {
            $this->profile['recording-media-type'] = "wav";
            return $this->refresh();
        }

        public function setMp3RecordingFormat(): self
        {
            $this->profile['recording-media-type'] = "mp3";
            return $this->refresh();
        }

        public function setDefaultRingingTimeout(int $timeout = 60): self
        {
            $this->profile['call-timeout'] = $timeout;
            return $this->refresh();
        }

        public function setOutboundSessionTimeouts(int $connection = 10, int $provisional = 2): self
        {
            $this->profile->set([
                'connection-timeout' => $connection,
                'provisional-timeout' => $provisional
            ]);
            return $this->refresh();
        }

        public function setCallerIdPassthrough(bool $status): self
        {
            $this->profile['allow-passthrough-caller-name'] = $status;
            return $this->refresh();
        }

        public function setActive(bool $status): self
        {
            $this->client->httpConnector->request("PATCH", $this->getPath(), ['active' => $status]);
            return $this->refresh();
        }

        public function setDefaultApplication(int $application): self
        {
            $this->client->httpConnector->request("PATCH", $this->getPath(), ['defaultApplication' => $application]);
            return $this->refresh();
        }

        public function setAlias(string $alias): self
        {
            $this->client->httpConnector->request("POST", $this->getPath() . "/aliases", ['alias' => $alias]);
            return $this->refresh();
        }

        public function unsetAlias(string $alias): self
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath() . "/aliases/" . $alias);
            return $this->refresh();
        }

        public function delete(): bool
        {
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
            if (!isset($this->canonicalPath)) {
                $this->canonicalPath = $branchPath . URLPATH_DOMAINS . "/" . $string;
            }
        }

        protected function buildEntityData(object|array $input): void
        {
            foreach ($input as $key => $value) {
                if ($key == "profile") {
                    $this->profile = new EntityProfile($this, $value);
                } else if ($key == "defaultApplication") {
                    $this->defaultApplicationId = $value;
                } else if ($key == "application") {
                    $this->defaultApplication = new EntityVoiceApplication($value->id, $this, $value);
                } else if ($key == "tenant") {
                    continue;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }