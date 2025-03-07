<?php
    /**
     * @package cloudonix-php
     * @file    tests/TestConfiguration.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix;

    echo "My Dir: " . __DIR__ . "\n";

    require_once __DIR__ . '/../../src/Helpers/ConfigHelper.php';

    use Dotenv\Dotenv;

    /**
     * @property string $apiKey                                                     Cloudonix API Key for Testing
     * @property string $endpoint                                                   Cloudonix API Endpoint
     * @property float  $endpointTimeout                                            Cloudonix API Endpoint Timeout
     * @property int    $endpointDebug                                              Cloudonix API Endpoint Debug
     *
     * @property string $newTenantApikey                                            New Tenant Apikey
     * @property array  $newTenantProfileKV                                         New Tenant Profile Key-Value Paid
     * @property string $newDomainApikey                                            New Domain Apikey
     * @property string $newApplicationApikey                                       New Application Apikey
     * @property string $newSubscriberApikey                                        New Subscriber Apikey
     *
     * @property string $newDomain                                                  New Domain Name
     * @property array  $newDomainProfileKV                                         New Domain Profile Key-Value Paid
     *
     * @property string $newDomainVoiceApplication                                  New Domain Application
     * @property array  $newDomainVoiceApplicationKV                                New Domain ApplicationProfile
     *           Key-Value Paid
     *
     * @property string $newDomainSubscriber                                        New Domain Subscriber
     * @property array  $newDomainSubscriberProfileKV                               New Tenant Profile Key-Value Paid
     *
     * @property string $newDomainInboundTrunk                                      New Domain Inbound Trunk
     * @property string $newDomainOutboundTrunk                                     New Domain Outbound Trunk
     *
     * @property string $newDomainDNID                                              New Domain DNID
     *
     * @property string $newContainerApplication                                    New Container Application
     */
    class TestConfiguration
    {
        private string $randomSeed;

        public function __construct(string $randomSeed = null)
        {

            if (is_null($randomSeed)) {
                $this->randomSeed = date("U");
            } else {
                $this->randomSeed = $randomSeed;
            }

            $this->loadConfig();
            $this->loadTestingData();
        }

        public function loadConfig(): void
        {
            $dotenv = Dotenv::createImmutable(__DIR__)->load();

            $this->apiKey = $_ENV['APIKEY'];
            $this->endpoint = $_ENV['ENDPOINT'];
            $this->endpointTimeout = 60;
            $this->endpointDebug = LOGGER_DEBUG;
        }

        private function loadTestingData(): void
        {
            $tsString = date("Ymdhis") . "_" . $this->randomSeed;
            $this->newTenantApikey = "TenantKey_Test_" . $tsString;
            $this->newDomainApikey = "DomainKey_Test_" . $tsString;
            $this->newApplicationApikey = "ApplicattionKey_Test_" . $tsString;
            $this->newSubscriberApikey = "SubscriberKey_Test_" . $tsString;
            $this->newDomain = "DomainName_Test_" . $tsString;
            $this->newDomainVoiceApplication = "VoiceApplication_Test_" . $tsString;
            $this->newDomainSubscriber = "Subscriber_Test_" . $tsString;
            $this->newContainerApplication = "ContainerApplication_Test_" . $tsString;
            $this->newDomainInboundTrunk = "InboundTrunk_Test_" . $tsString;
            $this->newDomainOutboundTrunk = "OutboundTrunk_Test_" . $tsString;
            $this->newDomainProfileKV = ["test_key_" . $tsString => $tsString];
            $this->newDomainVoiceApplicationKV = $this->newDomainProfileKV;
            $this->newDomainSubscriberProfileKV = $this->newDomainProfileKV;
            $this->newDomainDNID = "DNID_" . $tsString;
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