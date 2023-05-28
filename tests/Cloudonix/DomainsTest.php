<?php
    /**
     *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
     * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
     * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
     * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
     * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
     *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
     *
     * @project :  cloudonix-php
     * @filename: TenantTest.php
     * @author  :   nirs
     * @created :  2023-05-27
     */

    namespace Cloudonix;

    use Cloudonix\TestConfiguration as TestConfiguration;
    use Cloudonix\CXClient as CXClient;

    use PHPUnit\Framework\TestCase;
    use Shalvah\Clara\Clara;

    class DomainsTest extends TestCase
    {
        private static $testTenantApikeyObject;
        private static $testDomainApikeyObject;
        private CXClient $cxClientTester;
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
            $this->cxClientTester = new CXClient(
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

            $this->consoleLogger->debug("[" . get_class() . "] Retrieving the domain created... ");
            $lastDomainCreated = self::$testTenantObject->domain(self::$testConfiguration->newDomain);
            $this->consoleLogger->debug("[" . get_class() . "] Last Created Domain object is " . $lastDomainCreated);
            $this->assertIsObject($lastDomainCreated);
        }

        /**
         * @depends test__construct
         * @depends testGetPath
         * @return void
         */
        public function testDomainProfileFunctions()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Setting Domain " . self::$testConfiguration->newDomain . " setActive(false)");
            $domainPostPatchObject = self::$testDomainObject->setActive(false);
            $this->assertIsBool($domainPostPatchObject->active);
            $this->assertTrue($domainPostPatchObject->active == false);

            $this->consoleLogger->debug("[" . get_class() . "] Setting Domain " . self::$testConfiguration->newDomain . " setActive(true)");
            $domainPostPatchObject = self::$testDomainObject->setActive(true);
            $this->assertIsBool($domainPostPatchObject->active);
            $this->assertTrue($domainPostPatchObject->active == true);

            $this->consoleLogger->debug("[" . get_class() . "] Setting Domain setBorderToBorder(true)");
            $domainPostPatchObject = self::$testDomainObject->setBorderToBorder(true);
            $this->assertIsObject($domainPostPatchObject);
            $this->assertTrue($domainPostPatchObject->profile['allowed-border']);

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
        public function testDomainApikeyFunctions()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Create a new Domain API key in domain: " . self::$testDomainObject->domain);
            self::$testDomainApikeyObject = self::$testDomainObject->newApikey(self::$testConfiguration->newDomainApikey);
            $this->consoleLogger->debug("[" . get_class() . "] Received API Key Object " . self::$testDomainApikeyObject);
            $this->assertIsObject(self::$testDomainApikeyObject);
            $this->assertIsString(self::$testDomainApikeyObject->keyId);

            $this->consoleLogger->debug("[" . get_class() . "] Get the previously created API Key for domain: " . self::$testDomainObject->domain);
            $myDomainApikeyObject = self::$testDomainObject->apikey(self::$testDomainApikeyObject->keyId);
            $this->consoleLogger->debug("[" . get_class() . "] Obtained API Key Object for " . self::$testDomainApikeyObject->keyId . " as " . $myDomainApikeyObject);
            $this->assertIsObject($myDomainApikeyObject);
            $this->assertIsString($myDomainApikeyObject->keyId);

            $this->consoleLogger->debug("[" . get_class() . "] Delete the previously created API Key for domain: " . self::$testDomainObject->domain);
            $deleteResult = self::$testDomainObject->apikey(self::$testDomainApikeyObject->keyId)->delete();
            $this->consoleLogger->debug("[" . get_class() . "] Delete result code is: " . (boolean)$deleteResult);
            $this->assertTrue($deleteResult);
        }

        public function testDomainSubscriberFunctions()
        {

        }

        public function testDomainInboundTrunkFunctions()
        {

        }

        public function testDomainOutboundTrunkFunctions()
        {

        }

        public function testDomainDnidFunctions()
        {

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

            $newProfileValue = "testvalue_" . date("His");
            $this->consoleLogger->debug("[" . get_class() . "] Setting VoiceApplication profile for " . self::$testApplicationObject->name);
            self::$testApplicationObject->profile['test-key'] = $newProfileValue;
            $this->consoleLogger->debug("[" . get_class() . "] Setting application " . self::$testApplicationObject->name . " profile with 'test-key' with value  " . self::$testApplicationObject->profile['test-key']);
            $updatedVoiceApplication = self::$testDomainObject->voiceApplication(self::$testApplicationObject->name);
            $this->consoleLogger->debug("[" . get_class() . "] Updated voice application object is " . $updatedVoiceApplication);
            $this->assertIsString($updatedVoiceApplication->profile['test-key']);
            $this->assertEquals($newProfileValue, $updatedVoiceApplication->profile['test-key']);

            $this->consoleLogger->debug("[" . get_class() . "] Unsetting application " . self::$testApplicationObject->name . " profile with 'test-key' ");
            unset(self::$testApplicationObject->profile['test-key']);
            $updatedVoiceApplication = self::$testDomainObject->voiceApplication(self::$testApplicationObject->name);
            $this->consoleLogger->debug("[" . get_class() . "] Updated voice application object is " . $updatedVoiceApplication);
            $this->assertObjectNotHasProperty('test-key', $updatedVoiceApplication->profile);

            $this->consoleLogger->debug("[" . get_class() . "] Delete Voice Application " . self::$testApplicationObject->name);
            $deleteVoiceApplicationResult = self::$testApplicationObject->delete();
            $this->consoleLogger->debug("[" . get_class() . "] Delete Voice Application is " . (boolean)$deleteVoiceApplicationResult);
            $this->assertTrue($deleteVoiceApplicationResult);
        }

    }
