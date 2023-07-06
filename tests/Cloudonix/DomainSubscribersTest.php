<?php declare(strict_types=1);
    /**
     * @package cloudonix-php
     * @file    tests/DomainSubscribersTest.php
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

    class DomainSubscribersTest extends TestCase
    {
        private CloudonixClient $cxClientTester;
        private Clara $consoleLogger;
        private static $testConfiguration;
        private static $testTenantObject;
        private static $testDomainObject;
        private static $testSubscriberApiKey;

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
        public function testSubscriberWithNullPassword()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $newMsisdn = self::$testConfiguration->newDomainSubscriber . "_" . date("i");
            $newSubscriber = self::$testDomainObject->newSubscriber($newMsisdn);
            $this->consoleLogger->debug("[" . get_class() . "] New Subscriber Object " . $newSubscriber);
            $this->assertInstanceOf('Cloudonix\Entities\Subscriber', $newSubscriber);
            $this->assertEquals($newMsisdn, $newSubscriber->msisdn);
            $this->consoleLogger->debug("[" . get_class() . "] Deleting Subscriber Object for " . $newSubscriber->msisdn);
            $this->assertTrue($newSubscriber->delete());
        }

        /**
         * @depends testCreateNewDomain
         * @return void
         */
        public function testSubscriberWithAutoGen()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $newMsisdn = self::$testConfiguration->newDomainSubscriber . "_" . date("i");
            $newSubscriber = self::$testDomainObject->newSubscriber($newMsisdn, 'GEN');
            $this->consoleLogger->debug("[" . get_class() . "] New Subscriber Object " . $newSubscriber);
            $this->assertInstanceOf('Cloudonix\Entities\Subscriber', $newSubscriber);
            $this->assertEquals($newMsisdn, $newSubscriber->msisdn);
            $this->consoleLogger->debug("[" . get_class() . "] Deleting Subscriber Object for " . $newSubscriber->msisdn);
            $this->assertTrue($newSubscriber->delete());
        }

        /**
         * @depends testCreateNewDomain
         * @return void
         */
        public function testSubscriberWithPassword()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $newMsisdn = self::$testConfiguration->newDomainSubscriber . "_" . date("i");
            $newSubscriber = self::$testDomainObject->newSubscriber($newMsisdn, 'TEST-NEW-PASSWORD-THAT-SILLY');
            $this->consoleLogger->debug("[" . get_class() . "] New Subscriber Object " . $newSubscriber);
            $this->assertInstanceOf('Cloudonix\Entities\Subscriber', $newSubscriber);
            $this->assertEquals($newMsisdn, $newSubscriber->msisdn);
            $this->consoleLogger->debug("[" . get_class() . "] Deleting Subscriber Object for " . $newSubscriber->msisdn);
            $this->assertTrue($newSubscriber->delete());
        }

        /**
         * @depends testCreateNewDomain
         * @return void
         */
        public function testSubscriberResetPassword()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $newMsisdn = self::$testConfiguration->newDomainSubscriber . "_" . date("i");
            $newSubscriber = self::$testDomainObject->newSubscriber($newMsisdn, 'TEST-NEW-PASSWORD-THAT-SILLY');
            $this->consoleLogger->debug("[" . get_class() . "] New Subscriber Object " . $newSubscriber);
            $this->assertInstanceOf('Cloudonix\Entities\Subscriber', $newSubscriber);
            $this->assertEquals($newMsisdn, $newSubscriber->msisdn);

            $this->consoleLogger->debug("[" . get_class() . "] Resetting subscriber password to null for: " . $newSubscriber->msisdn);
            $newSubscriber->resetSipPassword();
            $this->consoleLogger->debug("[" . get_class() . "] New Subscriber Object " . $newSubscriber);
            $this->assertEquals(null, $newSubscriber->password);

            $this->consoleLogger->debug("[" . get_class() . "] Resetting subscriber password to fixed password for: " . $newSubscriber->msisdn);
            $newSubscriber->resetSipPassword('YET-ANOTHER-SILLY-PASSWORD');
            $this->consoleLogger->debug("[" . get_class() . "] New Subscriber Object " . $newSubscriber);
            $this->assertEquals('YET-ANOTHER-SILLY-PASSWORD', $newSubscriber->sipPassword);

            $this->consoleLogger->debug("[" . get_class() . "] Resetting subscriber password to auto password for: " . $newSubscriber->msisdn);
            $newSubscriber->resetSipPassword('GEN');
            $this->consoleLogger->debug("[" . get_class() . "] New Subscriber Object " . $newSubscriber);
            $this->assertIsString($newSubscriber->sipPassword);

            $this->consoleLogger->debug("[" . get_class() . "] Deleting Subscriber Object for " . $newSubscriber->msisdn);
            $this->assertTrue($newSubscriber->delete());
        }

        /**
         * @depends testCreateNewDomain
         * @return void
         */
        public function testSubscriberSetProfile()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $newMsisdn = self::$testConfiguration->newDomainSubscriber . "_" . date("i");
            $newSubscriber = self::$testDomainObject->newSubscriber($newMsisdn);
            $this->consoleLogger->debug("[" . get_class() . "] New Subscriber Object " . $newSubscriber);
            $this->assertInstanceOf('Cloudonix\Entities\Subscriber', $newSubscriber);
            $this->assertEquals($newMsisdn, $newSubscriber->msisdn);

            $this->consoleLogger->debug("[" . get_class() . "] Setting subscriber profile with key and value");
            $newSubscriber->profile['newkey'] = "new_value";

            $this->consoleLogger->debug("[" . get_class() . "] Refreshing the subscriber object");
            $this->consoleLogger->debug("[" . get_class() . "] Updated Subscriber Object " . $newSubscriber);

            $this->assertInstanceOf('Cloudonix\Entities\Profile', $newSubscriber->profile);
            $this->assertObjectHasProperty('newkey', $newSubscriber->profile);
            $this->assertEquals('new_value', $newSubscriber->profile['newkey']);

            $this->consoleLogger->debug("[" . get_class() . "] Unsetting subscriber profile with key and value");
            $newSubscriber->profile['newkey'] = null;

            $this->consoleLogger->debug("[" . get_class() . "] Refreshing the subscriber object");
            $this->consoleLogger->debug("[" . get_class() . "] Updated Subscriber Object " . $newSubscriber);

            $this->assertInstanceOf('Cloudonix\Entities\Profile', $newSubscriber->profile);
            $this->assertObjectNotHasProperty('newkey', $newSubscriber->profile);

            $this->consoleLogger->debug("[" . get_class() . "] Deleting Subscriber Object for " . $newSubscriber->msisdn);
            $this->assertTrue($newSubscriber->delete());
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
