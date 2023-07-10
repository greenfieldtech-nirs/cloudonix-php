<?php
    /**
     * @package cloudonix-php
     * @file    tests/TenantTest.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */
    namespace Cloudonix;

    require_once 'TestConfiguration.php';

    use Cloudonix\TestConfiguration as TestConfiguration;
    use Cloudonix\CloudonixClient;

    use PHPUnit\Framework\TestCase;
    use Shalvah\Clara\Clara;

    class TenantTest extends TestCase
    {
        private static $testTenantApikeyObject;
        private static $testDomainApikeyObject;
        private CloudonixClient $cxClientTester;
        private Clara $consoleLogger;
        private static $testConfiguration;
        private static $testTenantObject;
        private static $testDomainObject;
        private static $testApplicationObject;
        private static $testSubscriberObject;
        private static $testInboundTrunkObject;
        private static $testOutboundTrunkObject;
        private static $testDnidObject;
        private static $testApikeysCollection;
        private static $testApplicationsCollection;

        public function __construct(string $name)
        {
            $this->consoleLogger = new Clara("TenantTest");
            self::$testConfiguration = new TestConfiguration("1");
            $this->cxClientTester = new CloudonixClient(
                self::$testConfiguration->apiKey,
                self::$testConfiguration->endpoint,
                self::$testConfiguration->endpointTimeout,
                self::$testConfiguration->endpointDebug
            );
            parent::__construct($name);
        }

        private function dumpConfiguration($testConfiguration)
        {
            $this->consoleLogger->info("");
            $this->consoleLogger->info("[" . get_class() . "] Current configuration...");
            foreach ($testConfiguration as $key => $value) {
                if (is_array($value) or is_object($value)) {
                    foreach ($value as $kk => $kv) {
                        $this->consoleLogger->info("[" . get_class() . "] [[{$key}]] {$kk} => {$kv}");
                    }
                } else {
                    $this->consoleLogger->info("[" . get_class() . "] {$key} => {$value}");
                }
            }
        }

        public function test__construct()
        {
            $this->consoleLogger->info("");
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $this->assertTrue(isset($this->cxClientTester->httpConnector));
        }

        /**
         * @depends test__construct
         * @return void
         */
        public function testGetPath()
        {
            $this->consoleLogger->info("");
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);

            self::$testTenantObject = $this->cxClientTester->tenant();
            $this->consoleLogger->debug("[" . get_class() . "] testTenantObject is " . self::$testTenantObject);

            $canonicalPath = self::$testTenantObject->getPath();
            $this->consoleLogger->debug("[" . get_class() . "] canonicalPath is " . $canonicalPath);

            $this->assertIsString($canonicalPath);
            $this->assertStringContainsString("tenants", $canonicalPath);
        }

        /**
         * @depends test__construct
         * @depends testGetPath
         * @return void
         */
        public function testTenantFunctions()
        {
            $this->consoleLogger->info("");
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $this->assertTrue(true);
        }

        /**
         * @depends test__construct
         * @depends testGetPath
         * @return void
         */
        public function testTenantSetActiveFalse()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $this->consoleLogger->debug("[" . get_class() . "] Setting Tenant status to setActive(false)");
            self::$testTenantObject->setActive(false);
            $this->assertIsBool(self::$testTenantObject->active);
            $this->assertTrue(self::$testTenantObject->active == false);
            $this->consoleLogger->debug("[" . get_class() . "] Tenant object is now: " . self::$testTenantObject);

        }

        public function testTenantSetActiveTrue()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $this->consoleLogger->debug("[" . get_class() . "] Setting Tenant status to setActive(true)");
            self::$testTenantObject->setActive(true);
            $this->assertIsBool(self::$testTenantObject->active);
            $this->assertTrue(self::$testTenantObject->active == true);
            $this->consoleLogger->debug("[" . get_class() . "] Tenant object is now: " . self::$testTenantObject);
        }

        /**
         * @depends test__construct
         * @depends testGetPath
         * @return void
         */
        public function testTenantApikeysCollection()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $this->consoleLogger->debug("[" . get_class() . "] Get Tenant API Keys Collection");
            self::$testApikeysCollection = self::$testTenantObject->apikeys();
            $this->consoleLogger->debug("[" . get_class() . "] Received " . count(self::$testApikeysCollection) . " objects ");
            $this->consoleLogger->debug("[" . get_class() . "] Collection Received " . self::$testApikeysCollection);
            $this->assertIsObject(self::$testApikeysCollection);
            $this->assertIsIterable(self::$testApikeysCollection);
            $this->assertIsInt(self::$testApikeysCollection->count());
        }

        public function testTenantApikeyCreateAndDelete()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $this->consoleLogger->debug("[" . get_class() . "] Creating 11 API Keys");
            for ($i=0; $i < 10; $i++) {
                self::$testTenantApikeyObject = self::$testTenantObject
                    ->newApikey(self::$testConfiguration->newTenantApikey . date("His") . $i);
            }
            self::$testTenantApikeyObject = self::$testTenantObject
                ->newApikey(self::$testConfiguration->newTenantApikey);
            $this->consoleLogger->debug("[" . get_class() . "] Received API Key Object " . self::$testTenantApikeyObject);

            $this->consoleLogger->debug("[" . get_class() . "] Total API Keys: " . count(self::$testTenantObject->apikeys()) . " objects ");

            $this->assertIsObject(self::$testTenantApikeyObject);
            $this->assertIsString(self::$testTenantApikeyObject->keyId);
            $this->assertTrue(self::$testTenantApikeyObject->delete());
        }

        public function testTenantApikeyUnsetFirst()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $this->consoleLogger->debug("[" . get_class() . "] Getting API Keys collection for Tenant " . self::$testTenantObject->name);
            $apikeys = self::$testTenantObject->apikeys();
            $this->consoleLogger->debug("[" . get_class() . "] apiKeys collection is: " . $apikeys);
            $this->consoleLogger->debug("[" . get_class() . "] First API key is: " . $apikeys[1]);
            $firstKeyId = $apikeys[1]->keyId;
            $this->consoleLogger->debug("[" . get_class() . "] firstKeyId is: " . $firstKeyId . " setting up to unset");
            unset($apikeys[1]);
            $this->consoleLogger->debug("[" . get_class() . "] apiKeys collection is now: " . $apikeys);
            $this->consoleLogger->debug("[" . get_class() . "] apikey[1] is now: " . $apikeys[1]->keyId);
            $this->assertNotEquals($firstKeyId, $apikeys[1]->keyId);
            $this->consoleLogger->debug("[" . get_class() . "] Refreshing DomainsCollection");
            $apikeys->list();
            $this->consoleLogger->debug("[" . get_class() . "] firstKeyId is now: " . $apikeys[1]->keyId);
            $this->assertNotEquals($firstKeyId, $apikeys[1]->keyId);
        }

        public function testTenantApikeyGet()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $this->consoleLogger->debug("[" . get_class() . "] Create a new Tenant API Key");
            self::$testTenantApikeyObject = self::$testTenantObject
                ->newApikey(self::$testConfiguration->newTenantApikey);
            $this->consoleLogger->debug("[" . get_class() . "] Create result is: " . self::$testTenantApikeyObject);

            $myNewApiKey = self::$testTenantObject->apikey(self::$testTenantApikeyObject->keyId);
            $this->consoleLogger->debug("[" . get_class() . "] Got API Key: " . $myNewApiKey);
            $this->assertIsObject($myNewApiKey);
            $this->assertIsString($myNewApiKey->keyId);
            $this->assertEquals(self::$testTenantApikeyObject->keyId, $myNewApiKey->keyId);
            $this->assertTrue($myNewApiKey->delete());
        }

        public function testTenantNewHostedApplication()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $newHostedApplicationObject = self::$testTenantObject->newHostedApplication(self::$testConfiguration->newContainerApplication, 'static', "<Response><Hangup/></Response>");
            $this->consoleLogger->debug("[" . get_class() . "] newHostedApplication Result is: " . $newHostedApplicationObject);
            $this->assertIsObject($newHostedApplicationObject);
            $this->assertEquals(self::$testConfiguration->newContainerApplication, $newHostedApplicationObject->name);
        }
        public function testTenantGetHostedApplication()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $getHostedApplication = self::$testTenantObject->hostedApplication(self::$testConfiguration->newContainerApplication);
            $this->consoleLogger->debug("[" . get_class() . "] getHostedApplication Result is: " . $getHostedApplication);
            $this->assertIsObject($getHostedApplication);
            $this->assertEquals(self::$testConfiguration->newContainerApplication, $getHostedApplication->name);
        }

        public function testTenantGetHostedApplicationMainBlock()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $getHostedApplicationBlockMain = self::$testTenantObject->hostedApplication(self::$testConfiguration->newContainerApplication)->getBlockByName('main');
            $this->consoleLogger->debug("[" . get_class() . "] getHostedApplicationBlockMain Result is: " . $getHostedApplicationBlockMain);
            $this->assertIsObject($getHostedApplicationBlockMain);
            $this->assertEquals('main', $getHostedApplicationBlockMain->name);
        }

        public function testTenantHostedApplications()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $getHostedApplications = self::$testTenantObject->hostedApplications();
            $this->consoleLogger->debug("[" . get_class() . "] getHostedApplications Result is: " . $getHostedApplications);
            $this->assertIsObject($getHostedApplications);
            $this->assertInstanceOf('Cloudonix\Collections\HostedApplications', $getHostedApplications);
        }

        public function testTenantHostedApplicationDelete()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $this->assertTrue(self::$testTenantObject->hostedApplication(self::$testConfiguration->newContainerApplication)->delete());
        }
    }
