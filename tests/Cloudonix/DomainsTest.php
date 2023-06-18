<?php
    /**
     * @package cloudonix-php
     * @file    tests/DomainTest.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix;

    use Cloudonix\TestConfiguration as TestConfiguration;
    use Cloudonix\CloudonixClient;

    use PHPUnit\Framework\TestCase;
    use Shalvah\Clara\Clara;

    class DomainsTest extends TestCase
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
            self::$testConfiguration = new TestConfiguration();
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
            $this->consoleLogger->debug("[" . get_class() . "] testTenantObject is " . $this->testTenantObject);

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
        public function testDomainFunctions()
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
        public function testNewDomain()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Creating new domain with name: " . self::$testConfiguration->newDomain);
            self::$testDomainObject = self::$testTenantObject->newDomain(self::$testConfiguration->newDomain);
            $this->consoleLogger->debug("[" . get_class() . "] New Domain object is " . self::$testDomainObject);
            $this->assertIsObject(self::$testDomainObject);
        }

        public function testGetLastDomainCreated()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Retrieving the domain created... ");
            $lastDomainCreated = self::$testTenantObject->domain(self::$testConfiguration->newDomain);
            $this->consoleLogger->debug("[" . get_class() . "] Last Created Domain object is " . $lastDomainCreated);
            $this->assertIsObject($lastDomainCreated);
        }

        public function testSetDomainAlias()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Set domain alias domain created... ");
            $lastDomainCreated = self::$testTenantObject->domain(self::$testConfiguration->newDomain);
            $setAliasResult = $lastDomainCreated->setAlias("Alias_" . self::$testConfiguration->newDomain);
            $this->consoleLogger->debug("[" . get_class() . "] New domain information is now: " . $setAliasResult);
            $this->assertIsObject($setAliasResult);
            $this->assertIsInt($setAliasResult->aliases[0]->id);
            $this->assertIsString($setAliasResult->aliases[0]->alias);
            $this->assertEquals("Alias_" . self::$testConfiguration->newDomain, $setAliasResult->aliases[0]->alias);
        }

        public function testUnsetDomainAlias()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Unset domain alias domain created... ");
            $lastDomainCreated = self::$testTenantObject->domain(self::$testConfiguration->newDomain);
            $unsetAliasResult = $lastDomainCreated->unsetAlias("Alias_" . self::$testConfiguration->newDomain);
            $this->consoleLogger->debug("[" . get_class() . "] New domain information is now: " . $unsetAliasResult);
            $this->assertIsObject($unsetAliasResult);
            $this->assertObjectNotHasProperty("aliases", $unsetAliasResult);
        }

        /**
         * @depends test__construct
         * @depends testGetPath
         * @return void
         */
        public function testDomainSetActiveFalse()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Setting Domain " . self::$testConfiguration->newDomain . " setActive(false)");
            $domainPostPatchObject = self::$testDomainObject->setActive(false);
            $this->assertIsBool($domainPostPatchObject->active);
            $this->assertTrue($domainPostPatchObject->active == false);
        }

        public function testDomainSetActiveTrue()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Setting Domain " . self::$testConfiguration->newDomain . " setActive(true)");
            $domainPostPatchObject = self::$testDomainObject->setActive(true);
            $this->assertIsBool($domainPostPatchObject->active);
            $this->assertTrue($domainPostPatchObject->active == true);
        }

        public function testDomainSetBorderToBorderTrue()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Setting Domain setBorderToBorder(true)");
            $domainPostPatchObject = self::$testDomainObject->setBorderToBorder(true);
            $this->assertIsObject($domainPostPatchObject);
            $this->assertTrue($domainPostPatchObject->profile['allowed-border']);
        }

        public function testDomainSetBorderToBorderFalse()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Setting Domain setBorderToBorder(false)");
            $domainPostPatchObject = self::$testDomainObject->setBorderToBorder(false);
            $this->assertIsObject($domainPostPatchObject);
            $this->assertFalse($domainPostPatchObject->profile['allowed-border']);
        }

        /**
         * @depends test__construct
         * @depends testGetPath
         * @return void
         */
        public function testDomainApikeyCreate()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Create a new Domain API key in domain: " . self::$testDomainObject->domain);
            self::$testDomainApikeyObject = self::$testDomainObject->newApikey(self::$testConfiguration->newDomainApikey);
            $this->consoleLogger->debug("[" . get_class() . "] Received API Key Object " . self::$testDomainApikeyObject);
            $this->assertIsObject(self::$testDomainApikeyObject);
            $this->assertIsString(self::$testDomainApikeyObject->keyId);
        }

        public function testDomainApikeyGet()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Get the previously created API Key for domain: " . self::$testDomainObject->domain);
            $myDomainApikeyObject = self::$testDomainObject->apikey(self::$testDomainApikeyObject->keyId);
            $this->consoleLogger->debug("[" . get_class() . "] Obtained API Key Object for " . self::$testDomainApikeyObject->keyId . " as " . $myDomainApikeyObject);
            $this->assertIsObject($myDomainApikeyObject);
            $this->assertIsString($myDomainApikeyObject->keyId);
        }

        public function testDomainApikeyDelete()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Delete the previously created API Key for domain: " . self::$testDomainObject->domain);
            $deleteResult = self::$testDomainObject->apikey(self::$testDomainApikeyObject->keyId)->delete();
            $this->consoleLogger->debug("[" . get_class() . "] Delete result code is: " . (boolean)$deleteResult);
            $this->assertTrue($deleteResult);
        }

        public function testDomainCreateSubscriberWithoutPassword()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Create a new Subscriber in domain: " . self::$testDomainObject->domain . " with null password");
            self::$testSubscriberObject = self::$testDomainObject->newSubscriber(self::$testConfiguration->newDomainSubscriber);
            $this->consoleLogger->debug("[" . get_class() . "] Received Subscriber Object " . self::$testSubscriberObject);
            $this->assertIsObject(self::$testSubscriberObject);
            $this->assertIsString(self::$testSubscriberObject->msisdn);
            $this->assertNull(self::$testSubscriberObject->sipPassword);
        }

        public function testDomainCreateSubscriberWithPassword()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Create a new Subscriber in domain: " . self::$testDomainObject->domain . " with preset password");
            self::$testSubscriberObject = self::$testDomainObject->newSubscriber(self::$testConfiguration->newDomainSubscriber . "_A", "password_" . self::$testConfiguration->newDomainSubscriber);
            $this->consoleLogger->debug("[" . get_class() . "] Received Subscriber Object " . self::$testSubscriberObject);
            $this->assertIsObject(self::$testSubscriberObject);
            $this->assertIsString(self::$testSubscriberObject->msisdn);
            $this->assertEquals("password_" . self::$testConfiguration->newDomainSubscriber, self::$testSubscriberObject->sipPassword);
        }

        public function testDomainCreateSubscriberWithAutogenPassword()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Create a new Subscriber in domain: " . self::$testDomainObject->domain . " with autogenerated password");
            self::$testSubscriberObject = self::$testDomainObject->newSubscriber(self::$testConfiguration->newDomainSubscriber . "_B", "GEN");
            $this->consoleLogger->debug("[" . get_class() . "] Received Subscriber Object " . self::$testSubscriberObject . " with password " . self::$testSubscriberObject->sipPassword);
            $this->assertIsObject(self::$testSubscriberObject);
            $this->assertIsString(self::$testSubscriberObject->msisdn);
            $this->assertIsString(self::$testSubscriberObject->sipPassword);
        }

        public function testDomainResetSubscriberPassword()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Reset SIP Password in domain: " . self::$testDomainObject->domain . " with autogenerator password");
            self::$testSubscriberObject = self::$testDomainObject->subscriber(self::$testConfiguration->newDomainSubscriber)->resetSipPassword("GEN");
            $this->consoleLogger->debug("[" . get_class() . "] Received Subscriber Object " . self::$testSubscriberObject);
            $this->assertIsObject(self::$testSubscriberObject);
            $this->assertIsString(self::$testSubscriberObject->msisdn);
            $this->assertIsString(self::$testSubscriberObject->sipPassword);
        }

        public function testDomainSubscriberSetProfileKV()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Set subscriber profile KV pair for " . self::$testConfiguration->newDomainSubscriber . "@" . self::$testDomainObject->domain);
            self::$testSubscriberObject->profile['setup_test_key'] = 'key_value';
            $updatedSubscriberObject = self::$testDomainObject->subscriber(self::$testConfiguration->newDomainSubscriber);
            $this->consoleLogger->debug("[" . get_class() . "] Received Subscriber Object " . $updatedSubscriberObject);
            $this->assertIsObject($updatedSubscriberObject);
            $this->assertIsObject($updatedSubscriberObject->profile);
            $this->assertIsString($updatedSubscriberObject->profile['setup_test_key']);
            $this->assertEquals('key_value', $updatedSubscriberObject->profile['setup_test_key']);
        }

        public function testDomainInboundTrunkCreate()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Create Inbound Trunk in domain: " . self::$testDomainObject->domain);
            self::$testInboundTrunkObject = self::$testDomainObject->newInboundTrunk("inbound_" . date("Hi"), "10.0.0.1");
            $this->consoleLogger->debug("[" . get_class() . "] Result trunk is:  " . self::$testInboundTrunkObject);

            $this->assertIsObject(self::$testInboundTrunkObject);
            $this->assertIsInt(self::$testInboundTrunkObject->id);
            $this->assertEquals(self::$testInboundTrunkObject->ip, "10.0.0.1");
            $this->assertEquals(self::$testInboundTrunkObject->port, 5060);
            $this->assertEquals(self::$testInboundTrunkObject->direction, "public-inbound");
        }

        public function testDomainInboundTrunkUpdate()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Update Inbound Trunk in domain: " . self::$testDomainObject->domain);
            $myUpdatedInboundTrunk = self::$testInboundTrunkObject->setEndpoint("10.10.10.10", 5060);
            $this->consoleLogger->debug("[" . get_class() . "] Result trunk is:  " . $myUpdatedInboundTrunk);
            $this->assertIsInt($myUpdatedInboundTrunk->id);
            $this->assertEquals($myUpdatedInboundTrunk->ip, "10.10.10.10");
            $this->assertEquals($myUpdatedInboundTrunk->port, 5060);
            $this->assertEquals($myUpdatedInboundTrunk->direction, "public-inbound");
        }

        public function testDomainInboundTrunkDelete()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Delete Inbound Trunk in domain: " . self::$testDomainObject->domain);
            $deleteInboundTrunk = self::$testInboundTrunkObject->delete();
            $this->assertTrue($deleteInboundTrunk);
        }

        public function testDomainOutboundTrunkFunctions()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Create Outbound Trunk in domain: " . self::$testDomainObject->domain);
            self::$testOutboundTrunkObject = self::$testDomainObject->newOutboundTrunk("outobound_" . date("Hi"), "192.168.1.1");
            $this->consoleLogger->debug("[" . get_class() . "] Result trunk is:  " . self::$testOutboundTrunkObject);

            $this->assertIsObject(self::$testOutboundTrunkObject);
            $this->assertIsInt(self::$testOutboundTrunkObject->id);
            $this->assertEquals(self::$testOutboundTrunkObject->ip, "192.168.1.1");
            $this->assertEquals(self::$testOutboundTrunkObject->port, 5060);
            $this->assertEquals(self::$testOutboundTrunkObject->direction, "public-outbound");
        }

        public function testDomainOutboundTrunkUpdate()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Update Outbound Trunk in domain: " . self::$testDomainObject->domain);
            $myUpdatedOutboundTrunk = self::$testOutboundTrunkObject->setEndpoint("10.20.20.20", 5060);
            $this->consoleLogger->debug("[" . get_class() . "] Result trunk is:  " . $myUpdatedOutboundTrunk);
            $this->assertIsInt($myUpdatedOutboundTrunk->id);
            $this->assertEquals($myUpdatedOutboundTrunk->ip, "10.20.20.20");
            $this->assertEquals($myUpdatedOutboundTrunk->port, 5060);
            $this->assertEquals($myUpdatedOutboundTrunk->direction, "public-outbound");
        }

        public function testDomainOutboundTrunkDelete()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Delete Inbound Trunk in domain: " . self::$testDomainObject->domain);
            $deleteOutboundTrunk = self::$testOutboundTrunkObject->delete();
            $this->assertTrue($deleteOutboundTrunk);
        }

        /**
         * @depends test__construct
         * @depends testGetPath
         * @return void
         */
        public function testDomainVoiceApplicationCollectionFunctions()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Retrieving VoiceApplications for domain" . self::$testConfiguration->newDomain);
            $domainApplicationsCollection = self::$testDomainObject->voiceApplications()->refresh();
            $this->consoleLogger->debug("[" . get_class() . "] Obtained  " . $domainApplicationsCollection . " objects ");
            $this->assertIsObject($domainApplicationsCollection);
            $this->assertIsIterable($domainApplicationsCollection);
            $this->assertIsInt($domainApplicationsCollection->count());
        }

        public function testDomainOutboundCallSession()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Starting outbound call in domain " . self::$testConfiguration->newDomain);
            $domainSessions = self::$testDomainObject->sessions();
            $this->consoleLogger->debug("[" . get_class() . "] Sessions object is " . $domainSessions);
            $newSession = $domainSessions->startOutboundCall('972546982826');
            $this->consoleLogger->debug("[" . get_class() . "] New session response is " . $newSession);
            $this->assertIsObject($newSession);
        }

        public function testDomainSubscriberNewSession()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Starting outbound call in domain " . self::$testConfiguration->newDomain);
            $domainSessions = self::$testDomainObject->sessions();
            $newSession = $domainSessions->startSubscriberSession(self::$testConfiguration->newDomainSubscriber, '972546982826', "https://api64.ipify.org/");
            $this->consoleLogger->debug("[" . get_class() . "] New session response is " . $newSession);
            $this->assertIsObject($newSession);
        }

        public function testDomainSubscriberSessionNotifyRinging()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Setting RegFree domain " . self::$testConfiguration->newDomain);
            $setRegFree = self::$testDomainObject->setRegFree("https://api64.ipify.org/", "this.is.a.super.secret.key");
            $this->consoleLogger->debug("[" . get_class() . "] Starting outbound call in domain " . self::$testConfiguration->newDomain);
            $domainSessions = self::$testDomainObject->sessions();
            $newSession = $domainSessions->startSubscriberSession(self::$testConfiguration->newDomainSubscriber, '972732557799', "https://api64.ipify.org/");
            $this->consoleLogger->debug("[" . get_class() . "] New session response is " . $newSession);
            $newSession = $newSession->notifyRinging(self::$testConfiguration->newDomainSubscriber, $newSession->token);
            $this->consoleLogger->debug("[" . get_class() . "] Post ringing update response is " . $newSession);
            $this->assertIsObject($newSession);
        }

        public function testDomainSubscriberSessionTimeLimitUpdate()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Starting outbound call in domain " . self::$testConfiguration->newDomain);
            $domainSessions = self::$testDomainObject->sessions();
            $newSession = $domainSessions->startSubscriberSession(self::$testConfiguration->newDomainSubscriber, '972546982826', "https://api64.ipify.org/");
            $this->consoleLogger->debug("[" . get_class() . "] New session response is " . $newSession);
            $this->assertIsObject($newSession);
            $this->consoleLogger->debug("[" . get_class() . "] Updating time limit to 600 seconds");
            $newSession = $newSession->updateTimeLimit(600);
            $this->consoleLogger->debug("[" . get_class() . "] New session response is " . $newSession);
            $this->assertIsObject($newSession);
        }

        public function testDomainSessions()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Retrieving Sessions for domain" . self::$testConfiguration->newDomain);
            $domainSessions = self::$testDomainObject->sessions();
            $this->consoleLogger->debug("[" . get_class() . "] Obtained  " . $domainSessions . " session objects ");
            $this->assertIsObject($domainSessions);
        }

        public function testDomainOutgoingSessions()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Retrieving Outgoing Sessions for domain" . self::$testConfiguration->newDomain);
            $domainSessions = self::$testDomainObject->sessions()->outgoing();
            $this->consoleLogger->debug("[" . get_class() . "] Obtained  " . $domainSessions . " session objects ");
            $this->assertIsObject($domainSessions);
        }

        public function testDomainApplicationSessions()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Retrieving Outgoing Sessions for domain" . self::$testConfiguration->newDomain);
            $domainSessions = self::$testDomainObject->sessions()->application();
            $this->consoleLogger->debug("[" . get_class() . "] Obtained  " . $domainSessions . " session objects ");
            $this->assertIsObject($domainSessions);
        }

        /**
         * @depends test__construct
         * @depends testGetPath
         * @return void
         */
        public function testNewDomainVoiceApplicationFunctions()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Create new VoiceApplications for domain " . self::$testConfiguration->newDomain);
            self::$testApplicationObject = self::$testDomainObject->newVoiceApplicationFromUrl(
                self::$testConfiguration->newDomainVoiceApplication,
                "https://my.remote.test.url/my.remote.script",
                "POST");
            $this->consoleLogger->debug("[" . get_class() . "] Obtained  " . self::$testApplicationObject . " voice application object ");
            $this->assertIsObject(self::$testApplicationObject);
            $this->assertIsString(self::$testApplicationObject->name);
            $this->assertIsInt(self::$testApplicationObject->id);
        }

        public function testNewDomainVoiceApplicationSetProfile()
        {
            $newProfileValue = "testvalue_" . date("His");
            $this->consoleLogger->debug("[" . get_class() . "] Setting VoiceApplication profile for " . self::$testApplicationObject->name);
            $this->consoleLogger->debug("[" . get_class() . "] VoiceApplication Object is now " . self::$testApplicationObject);
            $this->consoleLogger->debug("[" . get_class() . "] VoiceApplication CanonicalPath is now " . self::$testApplicationObject->canonicalPath);
            $this->consoleLogger->debug("[" . get_class() . "] VoiceApplication Profile is now " . self::$testApplicationObject->profile);
            self::$testApplicationObject->profile['test-key'] = $newProfileValue;
            $this->consoleLogger->debug("[" . get_class() . "] Setting application " . self::$testApplicationObject->name . " profile with 'test-key' with value  " . self::$testApplicationObject->profile['test-key']);
            $this->consoleLogger->debug("[" . get_class() . "] Updated voice application object is " . self::$testApplicationObject);
            $this->assertIsString(self::$testApplicationObject->profile['test-key']);
            $this->assertEquals($newProfileValue, self::$testApplicationObject->profile['test-key']);
        }

        public function testNewDomainVoiceApplicationUnsetProfile()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Unsetting application " . self::$testApplicationObject->name . " profile with 'test-key' ");
            unset(self::$testApplicationObject->profile['test-key']);
            $this->consoleLogger->debug("[" . get_class() . "] Updated voice application object is " . self::$testApplicationObject->profile);
            $this->assertObjectNotHasProperty('test-key', self::$testApplicationObject->profile);
        }

        public function testNewDomainVoiceApplicationSetUrl()
        {
            $newUrl = "https://some.server." . date("His") . ".net/some.script";
            $this->consoleLogger->debug("[" . get_class() . "] Setting application " . self::$testApplicationObject->name . " URL to " . $newUrl);
            $updatedVoiceApplication = self::$testDomainObject->voiceApplication(self::$testApplicationObject->name)->setApplicationUrl($newUrl, 'GET');
            $this->consoleLogger->debug("[" . get_class() . "] Updated voice application object is " . $updatedVoiceApplication);
            $this->assertEquals($newUrl, $updatedVoiceApplication->url);
        }

        public function testDomainVoiceApplicationSetSubscriberDataSet()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Set subscriber application data in domain: " . self::$testDomainObject->domain);
            $mySubscriberMSISDN = self::$testConfiguration->newDomainSubscriber;
            $this->consoleLogger->debug("[" . get_class() . "] Setting subscriber " . $mySubscriberMSISDN . " with data: newkey=new_value");
            $subscriberData = self::$testDomainObject->voiceApplication(self::$testApplicationObject->name)->subscriberData($mySubscriberMSISDN);
            $this->consoleLogger->debug("[" . get_class() . "] Subscriber Data object is now " . $subscriberData);
            $subscriberData['new_data_key1'] = "new_data_value1";
            $subscriberData['new_data_key2'] = "new_data_value2";
            $subscriberData['new_data_key3'] = "new_data_value3";
            $this->consoleLogger->debug("[" . get_class() . "] Subscriber Data object is now " . $subscriberData);
            $this->assertIsIterable($subscriberData);
            $this->assertCount(3, $subscriberData);
        }

        public function testDomainVoiceApplicationSetSubscriberDataGet()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Get subscriber application data in domain: " . self::$testDomainObject->domain);
            $mySubscriberMSISDN = self::$testConfiguration->newDomainSubscriber;
            $subscriberData = self::$testDomainObject->voiceApplication(self::$testApplicationObject->name)->subscriberData($mySubscriberMSISDN);
            $this->consoleLogger->debug("[" . get_class() . "] Subscriber Data object is now " . $subscriberData);
            $this->assertIsIterable($subscriberData);
            $this->assertCount(3, $subscriberData);
            $this->consoleLogger->debug("[" . get_class() . "] new_data_key1 is " . $subscriberData['new_data_key1']->data);
            $this->consoleLogger->debug("[" . get_class() . "] new_data_key2 is " . $subscriberData['new_data_key2']->data);
            $this->consoleLogger->debug("[" . get_class() . "] new_data_key3 is " . $subscriberData['new_data_key3']->data);

            $this->assertEquals('new_data_value1', $subscriberData['new_data_key1']->data);
            $this->assertEquals('new_data_value2', $subscriberData['new_data_key2']->data);
            $this->assertEquals('new_data_value3', $subscriberData['new_data_key3']->data);
        }

        public function testDomainVoiceApplicationSetSubscriberDataDelete()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Set subscriber data in domain: " . self::$testDomainObject->domain);
            $mySubscriberMSISDN = self::$testConfiguration->newDomainSubscriber;
            $subscriberData = self::$testDomainObject->voiceApplication(self::$testApplicationObject->name)->subscriberData($mySubscriberMSISDN);
            $this->consoleLogger->debug("[" . get_class() . "] Subscriber Data object is now " . $subscriberData);
            unset($subscriberData['new_data_key1']);
            unset($subscriberData['new_data_key2']);
            unset($subscriberData['new_data_key3']);
            $this->consoleLogger->debug("[" . get_class() . "] Subscriber Data object is now " . $subscriberData);
            $this->assertIsIterable($subscriberData);
            $this->assertCount(0, $subscriberData);
        }

        public function testDomainDnidCollection()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Get DNID Collection for domain: " . self::$testDomainObject->domain);
            $dnids = self::$testDomainObject->dnids();
            $this->consoleLogger->debug("[" . get_class() . "] Received collection: " . $dnids);
            $this->assertIsObject($dnids);
            $this->assertIsIterable($dnids);
        }

        public function testDomainVoiceApplicationNewDnidPattern()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Create a new DNID for application " . self::$testDomainObject->domain);
            $dnid = self::$testDomainObject->voiceApplication(self::$testApplicationObject->name)->newDnidPattern(date("HisY") . "*");
            $this->consoleLogger->debug("[" . get_class() . "] Response is: " . $dnid);
            $this->assertIsObject($dnid);
        }

        public function testDomainVoiceApplicationNewDnidRegex()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Create a new DNID for application " . self::$testDomainObject->domain);
            $dnid = self::$testDomainObject->voiceApplication(self::$testApplicationObject->name)->newDnidPattern("^" . date("HisY") . "$");
            $this->consoleLogger->debug("[" . get_class() . "] Response is: " . $dnid);
            $this->assertIsObject($dnid);
            $this->assertObjectHasProperty("id", $dnid);
        }

        public function testDomainVoiceApplicationNewDnidAsterisk()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Create a new DNID for application " . self::$testDomainObject->domain);
            $dnid = self::$testDomainObject->voiceApplication(self::$testApplicationObject->name)->newDnidAsterisk("_" . date("HisY") . ".");
            $this->consoleLogger->debug("[" . get_class() . "] Response is: " . $dnid);
            $this->assertIsObject($dnid);
            $this->assertObjectHasProperty("id", $dnid);
        }

        public function testDomainVoiceApplicationNewDnidPrefix()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Create a new DNID for application " . self::$testDomainObject->domain);
            $dnid = self::$testDomainObject->voiceApplication(self::$testApplicationObject->name)->newDnidPrefix(date("HisY"));
            $this->consoleLogger->debug("[" . get_class() . "] Response is: " . $dnid);
            $this->assertIsObject($dnid);
            $this->assertObjectHasProperty("id", $dnid);
        }

        public function testDomainVoiceApplicationDnidDeleteAll()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Get all DNIDs for application in domain " . self::$testDomainObject->domain);
            $dnids = self::$testDomainObject->voiceApplication(self::$testApplicationObject->name)->dnids();
            $this->consoleLogger->debug("[" . get_class() . "] Response is: " . $dnids);
            foreach ($dnids as $id => $object) {
                $this->consoleLogger->debug("[" . get_class() . "] Deleting DNID: " . $object->source . " at ID: " . $id);
                $this->assertTrue(self::$testDomainObject->dnid($id)->delete());
            }
        }

        public function testNewDomainVoiceApplicationDelete()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Delete Voice Application " . self::$testApplicationObject->name);
            $deleteVoiceApplicationResult = self::$testApplicationObject->delete();
            $this->consoleLogger->debug("[" . get_class() . "] Delete Voice Application is " . (boolean)$deleteVoiceApplicationResult);
            $this->assertTrue($deleteVoiceApplicationResult);
        }

        public function testDomainSubscribersDelete()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Delete subscribers created in domain: " . self::$testDomainObject->domain);
            $delete = self::$testDomainObject->subscriber(self::$testConfiguration->newDomainSubscriber)->delete(true);
            $this->assertTrue($delete);
            $delete = self::$testDomainObject->subscriber(self::$testConfiguration->newDomainSubscriber . "_A")->delete(true);
            $this->assertTrue($delete);
            $delete = self::$testDomainObject->subscriber(self::$testConfiguration->newDomainSubscriber . "_B")->delete(true);
            $this->assertTrue($delete);
        }

        public function testDeleteDomain()
        {
            $this->consoleLogger->info("");
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);

            $deleteDomainResult = self::$testDomainObject->delete();
            $this->consoleLogger->debug("[" . get_class() . "] Delete Domain is " . (boolean)$deleteDomainResult);

            $this->assertTrue($deleteDomainResult);
        }
    }
