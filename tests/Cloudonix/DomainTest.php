<?php declare(strict_types=1);
    /**
     * @package cloudonix-php
     * @file    tests/DomainTest.php
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

    class DomainTest extends TestCase
    {
        private CloudonixClient $cxClientTester;
        private Clara $consoleLogger;
        private static $testConfiguration;
        private static $testTenantObject;
        private static $testDomainObject;

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
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            self::$testTenantObject = $this->cxClientTester->tenant();
            $canonicalPath = self::$testTenantObject->getPath();
            $this->consoleLogger->debug("[" . get_class() . "] testTenantObject is " . self::$testTenantObject);
            $this->consoleLogger->debug("[" . get_class() . "] canonicalPath is " . $canonicalPath);
            $this->assertInstanceOf('Cloudonix\Entities\Tenant', self::$testTenantObject);
            $this->assertStringContainsString("tenants", $canonicalPath);
            $this->assertIsString($canonicalPath);
        }

        public function testExistingDomain()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            self::$testDomainObject = self::$testTenantObject->domain(self::$testConfiguration->newDomain);
            $this->consoleLogger->debug("[" . get_class() . "] testDomainObject is " . self::$testDomainObject);
            if (!is_null(self::$testDomainObject)) {
                self::$testConfiguration->newDomain .= rand(100,1000);
            }
            $this->assertTrue(true);
        }

        /**
         * @depends testGetPath
         * @return void
         */
        public function testCreateNewDomain()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            self::$testDomainObject = self::$testTenantObject->newDomain(self::$testConfiguration->newDomain);
            $this->consoleLogger->debug("[" . get_class() . "] testDomainObject is " . self::$testDomainObject);
            $this->assertInstanceOf('Cloudonix\Entities\Domain', self::$testDomainObject);
            $this->assertIsString(self::$testDomainObject->domain);
        }

        /**
         * @depends testCreateNewDomain
         * @return void
         */
        public function testSetDomainInActive()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            self::$testDomainObject->setActive(false);
            $this->consoleLogger->debug("[" . get_class() . "] testDomainObject is " . self::$testDomainObject);
            $this->assertFalse(self::$testDomainObject->active);
        }

        /**
         * @depends testCreateNewDomain
         * @return void
         */
        public function testSetDomainActive()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            self::$testDomainObject->setActive(true);
            $this->consoleLogger->debug("[" . get_class() . "] testDomainObject is " . self::$testDomainObject);
            $this->assertTrue(self::$testDomainObject->active);
        }

        /**
         * @depends testCreateNewDomain
         * @return void
         */
        public function testSetDomainProfile()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            self::$testDomainObject->profile['myProfileKey'] = 'myProfileValue';
            $this->consoleLogger->debug("[" . get_class() . "] testDomainObject->profile is " . self::$testDomainObject->profile);

            $this->consoleLogger->debug("[" . get_class() . "] testDomainObject is " . self::$testDomainObject);
            $this->assertObjectHasProperty('myProfileKey', self::$testDomainObject->profile);
        }

        /**
         * @depends testSetDomainProfile
         * @return void
         */
        public function testUnsetDomainProfile()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            self::$testDomainObject->profile['myProfileKey'] = null;
            $this->consoleLogger->debug("[" . get_class() . "] testDomainObject is " . self::$testDomainObject);
            $this->assertObjectNotHasProperty('myProfileKey', self::$testDomainObject->profile);
        }

        /**
         * @depends testCreateNewDomain
         * @return void
         */
        public function testDeleteCreatedDomain()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $this->assertTrue(self::$testDomainObject->delete());
        }

    }
