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

    class DomainTrunksTest extends TestCase
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
        public function testCreateInboundTrunk()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $fqdn = 'random_domain_name_' . date("His") . 'gmail.com';
            $newTrunk = self::$testDomainObject->newInboundTrunk(self::$testConfiguration->newDomainInboundTrunk, $fqdn);

            $this->consoleLogger->debug("[" . get_class() . "] newInboundTrunk is " . $newTrunk);
            $this->assertInstanceOf('Cloudonix\Entities\Trunk', $newTrunk);

            $this->consoleLogger->debug("[" . get_class() . "] Deleting the new Trunk " . self::$testConfiguration->newDomainInboundTrunk);
            $this->assertTrue($newTrunk->delete());
        }

        /**
         * @depends testCreateNewDomain
         * @return void
         */
        public function testUpdateInboundTrunk()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $fqdn = 'random_domain_name_' . date("His") . 'gmail.com';
            $newTrunk = self::$testDomainObject->newInboundTrunk(self::$testConfiguration->newDomainInboundTrunk, $fqdn);

            $this->consoleLogger->debug("[" . get_class() . "] newInboundTrunk is " . $newTrunk);
            $this->assertInstanceOf('Cloudonix\Entities\Trunk', $newTrunk);

            $this->consoleLogger->debug("[" . get_class() . "] Deleting the new Trunk " . self::$testConfiguration->newDomainInboundTrunk);
            $this->assertTrue($newTrunk->delete());
        }

        /**
         * @depends testCreateNewDomain
         * @return void
         */
        public function testCreateOutboundTrunk()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $fqdn = 'random_domain_name_' . date("His") . 'gmail.com';
            $newTrunk = self::$testDomainObject->newOutboundTrunk(self::$testConfiguration->newDomainOutboundTrunk, $fqdn);

            $this->consoleLogger->debug("[" . get_class() . "] newOutboundTrunk is " . $newTrunk);
            $this->assertInstanceOf('Cloudonix\Entities\Trunk', $newTrunk);

            $this->consoleLogger->debug("[" . get_class() . "] Deleting the new Trunk " . self::$testConfiguration->newDomainOutboundTrunk);
            $this->assertTrue($newTrunk->delete());
        }

        /**
         * @depends testCreateNewDomain
         * @return void
         */
        public function testUpdateOutboundTrunk()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $fqdn = 'random_domain_name_' . date("His") . 'gmail.com';
            $newTrunk = self::$testDomainObject->newOutboundTrunk(self::$testConfiguration->newDomainOutboundTrunk, $fqdn);

            $this->consoleLogger->debug("[" . get_class() . "] newOutboundTrunk is " . $newTrunk);
            $this->assertInstanceOf('Cloudonix\Entities\Trunk', $newTrunk);

            $this->consoleLogger->debug("[" . get_class() . "] setting Trunk metric to 100");

            $newTrunk->setOutboundMetric(100);
            $this->consoleLogger->debug("[" . get_class() . "] updated outbound trunk is " . $newTrunk);

            $this->assertEquals(100, $newTrunk->metric);

            $this->consoleLogger->debug("[" . get_class() . "] Deleting the new Trunk " . self::$testConfiguration->newDomainOutboundTrunk);
            $this->assertTrue($newTrunk->delete());
        }

        public function testDomainTrunksCollection()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);

            for ($i=0; $i<10; $i++) {
                $fqdn = 'random_domain_name' . $i . '_' . date("His") . '.gmail.com';
                $newTrunk = self::$testDomainObject->newOutboundTrunk($fqdn, $fqdn);

                $this->consoleLogger->debug("[" . get_class() . "] created new trunk " . $newTrunk);
                $this->assertInstanceOf('Cloudonix\Entities\Trunk', $newTrunk);
            }

            $domainTrunks = self::$testDomainObject->trunks()->list();
            $this->consoleLogger->debug("[" . get_class() . "] Domain trunks collection is " . $domainTrunks);

            $this->assertInstanceOf('Cloudonix\Collections\Trunks', $domainTrunks);
        }


        public function testDomainTrunksCollectionDelete()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $domainTrunks = self::$testDomainObject->trunks()->list();
            $this->consoleLogger->debug("[" . get_class() . "] Domain trunks collection is " . $domainTrunks);

            foreach ($domainTrunks as $domainTrunk) {
                $this->consoleLogger->debug("[" . get_class() . "] About to delete trunk: " . $domainTrunk->name);
                unset($domainTrunks[0]);
            }
            $this->consoleLogger->debug("[" . get_class() . "] Domain trunks collection is " . $domainTrunks);

            $this->assertInstanceOf('Cloudonix\Collections\Trunks', $domainTrunks);
            $this->assertCount(0, $domainTrunks);
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
