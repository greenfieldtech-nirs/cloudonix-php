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

    class TenantTest extends TestCase
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
        public function testTenantDomainsCollection()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Getting domains collection for Tenant " . self::$testTenantObject->name);
            $domainsCollection = self::$testTenantObject->domains();
            $this->consoleLogger->debug("[" . get_class() . "] domainsCollection is: " . $domainsCollection);
            $this->assertIsIterable($domainsCollection);
        }

        /**
         * @depends test__construct
         * @depends testGetPath
         * @return void
         */
        public function testTenantSetActiveFalse()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Setting Tenant status to setActive(false)");
            $thisNewTenantObject = self::$testTenantObject->setActive(false);
            $this->assertIsBool($thisNewTenantObject->active);
            $this->assertTrue($thisNewTenantObject->active == false);
        }

        public function testTenantSetActiveTrue()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Setting Tenant status to setActive(true)");
            $thisNewTenantObject = self::$testTenantObject->setActive(true);
            $this->assertIsBool($thisNewTenantObject->active);
            $this->assertTrue($thisNewTenantObject->active == true);
        }

        /**
         * @depends test__construct
         * @depends testGetPath
         * @return void
         */
        public function testTenantApikeysCollection()
        {

            $this->consoleLogger->debug("[" . get_class() . "] Get Tenant API Keys Collection");
            self::$testApikeysCollection = self::$testTenantObject->apikeys();
            $this->consoleLogger->debug("[" . get_class() . "] Received " . self::$testApikeysCollection->count() . " objects ");
            $this->assertIsObject(self::$testApikeysCollection);
            $this->assertIsIterable(self::$testApikeysCollection);
            $this->assertIsInt(self::$testApikeysCollection->count());
        }

        public function testTenantApikeyCreate()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Create a new Tenant API Key");
            self::$testTenantApikeyObject = self::$testTenantObject
                ->newApikey(self::$testConfiguration->newTenantApikey);
            $this->consoleLogger->debug("[" . get_class() . "] Received API Key Object " . self::$testTenantApikeyObject);
            $this->assertIsObject(self::$testTenantApikeyObject);
            $this->assertIsString(self::$testTenantApikeyObject->keyId);
        }

        public function testTenantApikeyCreated()
        {
            $myApikeyObject = self::$testTenantObject->apikey(self::$testTenantApikeyObject->keyId);
            $this->consoleLogger->debug("[" . get_class() . "] Obtained API Key Object for " . self::$testTenantApikeyObject->keyId . " as " . $myApikeyObject);

            $this->assertIsObject($myApikeyObject);
            $this->assertIsString($myApikeyObject->keyId);
        }

        public function testTenantApikeyDelete() {
            $deleteResult = self::$testTenantObject->apikey(self::$testTenantApikeyObject->keyId)->delete();
            $this->consoleLogger->debug("[" . get_class() . "] Delete result code is: " . (boolean)$deleteResult);
            $this->assertTrue($deleteResult);
        }

        public function testContainerApplications()
        {
            $this->consoleLogger->info("");
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);

        }

        public function testContainerApplication()
        {
            $this->consoleLogger->info("");
            $this->consoleLogger->info("[" . get_class() . "] Executing " . __FUNCTION__);

        }
    }
