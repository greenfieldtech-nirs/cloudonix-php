<?php
    /**
     * @package cloudonix-php
     * @file    tests/ContainerApplicationTest.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix;

    require_once 'TestConfiguration.php';

    use \Cloudonix\TestConfiguration;
    use \Cloudonix\CloudonixClient;

    use PHPUnit\Framework\TestCase;
    use Shalvah\Clara\Clara;

    class ContainerApplicationTest extends TestCase
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
            $this->consoleLogger = new Clara("ContainerApplicationTest");
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
            $this->consoleLogger->debug("[" . get_class() . "] testTenantObject is " . self::$testTenantObject);

            $canonicalPath = self::$testTenantObject->getPath();
            $this->consoleLogger->debug("[" . get_class() . "] canonicalPath is " . $canonicalPath);

            $this->assertIsString($canonicalPath);
            $this->assertStringContainsString("tenants", $canonicalPath);
        }

        /**
         * @depends testGetPath
         * @return void
         */
        public function testNewContainerApplication()
        {
            $containerApplicationResult = self::$testTenantObject->newContainerApplication(self::$testConfiguration->newContainerApplication, 'static', '<Response><Hangup/></Response>');
            $this->consoleLogger->debug("[" . get_class() . "] New Container Application Result: " . $containerApplicationResult);
            $this->assertIsInt($containerApplicationResult->id);
            $this->assertIsString($containerApplicationResult->name);
            $this->assertEquals(self::$testConfiguration->newContainerApplication, $containerApplicationResult->name);
        }

        /**
         * @depends testGetPath
         * @return void
         */
        public function testContainerApplicationObject()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Get previously created container application: " . self::$testConfiguration->newContainerApplication);
            $myContainerApplication = self::$testTenantObject->containerApplication(self::$testConfiguration->newContainerApplication);
            $this->consoleLogger->debug("[" . get_class() . "] Container application object is: " . $myContainerApplication);
            $this->assertIsInt($myContainerApplication->id);
            $this->assertIsString($myContainerApplication->name);
            $this->assertEquals(self::$testConfiguration->newContainerApplication, $myContainerApplication->name);
        }

        /**
         * @depends testGetPath
         * @return void
         */
        public function testContainerApplicationAddBlock()
        {

        }

    }
