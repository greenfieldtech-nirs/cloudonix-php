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

    class DomainVoiceApplicationsTest extends TestCase
    {
        private CloudonixClient $cxClientTester;
        private Clara $consoleLogger;
        private static $testConfiguration;
        private static $testTenantObject;
        private static $testDomainObject;
        private static $testVoiceApplicationObject;

        public function __construct(string $name)
        {
            $this->consoleLogger = new Clara("TenantTest");
            self::$testConfiguration = new TestConfiguration("2");
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

        public function testCreateNewVoiceApplication()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);

            self::$testVoiceApplicationObject = self::$testDomainObject->newVoiceApplicationFromUrl(self::$testConfiguration->newDomainVoiceApplication, "https://some.server/some.script", "POST");
            $this->consoleLogger->debug("[" . get_class() . "] Created new application object " . self::$testVoiceApplicationObject);

            $this->assertInstanceOf('Cloudonix\Entities\VoiceApplication', self::$testVoiceApplicationObject);
            $this->assertIsString(self::$testVoiceApplicationObject->name);
            $this->assertEquals("https://some.server/some.script", self::$testVoiceApplicationObject->url);
            $this->assertEquals("POST", self::$testVoiceApplicationObject->method);
        }

        public function testSetVoiceApplicationUrl()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);

            self::$testVoiceApplicationObject->setApplicationUrl("https://new.server/new.script", "GET");

            $this->assertInstanceOf('Cloudonix\Entities\VoiceApplication', self::$testVoiceApplicationObject);
            $this->assertIsString(self::$testVoiceApplicationObject->name);
            $this->assertEquals("https://new.server/new.script", self::$testVoiceApplicationObject->url);
            $this->assertEquals("GET", self::$testVoiceApplicationObject->method);
        }

        public function testSetVoiceApplicationStatus()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);

            self::$testVoiceApplicationObject->setActive(false);
            $this->assertInstanceOf('Cloudonix\Entities\VoiceApplication', self::$testVoiceApplicationObject);
            $this->assertIsBool(self::$testVoiceApplicationObject->active);
            $this->assertFalse(self::$testVoiceApplicationObject->active);

            self::$testVoiceApplicationObject->setActive(true);
            $this->assertInstanceOf('Cloudonix\Entities\VoiceApplication', self::$testVoiceApplicationObject);
            $this->assertTrue(self::$testVoiceApplicationObject->active);
        }

        public function testVoiceApplicationAddAsteriskDnid()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);

            $randomDnidSeed = date("His");

            $newDnid = self::$testVoiceApplicationObject->newDnidAsterisk("_" . $randomDnidSeed . "1.");
            $this->consoleLogger->debug("[" . get_class() . "] Created DNID " . $newDnid);
            $this->assertInstanceOf('Cloudonix\Entities\Dnid', $newDnid);
            $this->assertEquals("_" . $randomDnidSeed . "1.", $newDnid->source);

            $this->assertTrue($newDnid->delete());
        }

        public function testVoiceApplicationAddRegexDnid()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);

            $randomDnidSeed = date("His");

            $newDnid = self::$testVoiceApplicationObject->newDnidRegex("^" . $randomDnidSeed);
            $this->consoleLogger->debug("[" . get_class() . "] Created DNID " . $newDnid);
            $this->assertInstanceOf('Cloudonix\Entities\Dnid', $newDnid);
            $this->assertEquals("^" . $randomDnidSeed, $newDnid->source);

            $this->assertTrue($newDnid->delete());
        }

        public function testVoiceApplicationAddPatternDnid()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);

            $randomDnidSeed = date("His");

            $newDnid = self::$testVoiceApplicationObject->newDnidPattern($randomDnidSeed . "*");
            $this->consoleLogger->debug("[" . get_class() . "] Created DNID " . $newDnid);
            $this->assertInstanceOf('Cloudonix\Entities\Dnid', $newDnid);
            $this->assertEquals($randomDnidSeed . "*", $newDnid->source);

            $this->assertTrue($newDnid->delete());
        }

        public function testVoiceApplicationAddPrefixDnid()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);

            $randomDnidSeed = date("His");

            $newDnid = self::$testVoiceApplicationObject->newDnidPrefix($randomDnidSeed . "*");
            $this->consoleLogger->debug("[" . get_class() . "] Created DNID " . $newDnid);
            $this->assertInstanceOf('Cloudonix\Entities\Dnid', $newDnid);
            $this->assertEquals($randomDnidSeed . "*", $newDnid->source);

            $this->assertTrue($newDnid->delete());
        }

        public function testVoiceApplicationDelete()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            $this->assertTrue(self::$testVoiceApplicationObject->delete());
        }

        public function testVoiceApplicationsCollection()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);
            for ($i=0; $i < 10; $i++) {
                $lastApplicationCreated = self::$testDomainObject->newVoiceApplicationFromUrl(self::$testConfiguration->newDomainVoiceApplication . date("His"), "https://some.server/some.script", "POST");
                $this->consoleLogger->debug("[" . get_class() . "] lastApplicationCreated " . $lastApplicationCreated);
            }

            $applicationCollection = self::$testDomainObject->voiceApplications()->list();
            $this->consoleLogger->debug("[" . get_class() . "] voiceApplication Collection is: " . $applicationCollection);
            $this->assertInstanceOf('Cloudonix\Collections\VoiceApplications', $applicationCollection);
            $this->assertCount(12, $applicationCollection);
        }

        public function testHostedApplicationProvisioning()
        {
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__);

            $newHostedApplicationObject = self::$testTenantObject->newHostedApplication(self::$testConfiguration->newContainerApplication, 'static', "<Response><Hangup/></Response>");
            $this->assertInstanceOf('Cloudonix\Entities\HostedApplication', $newHostedApplicationObject);

            $this->consoleLogger->debug("[" . get_class() . "] newHostedApplication Result is: " . $newHostedApplicationObject);
            $newVoiceApplicationObject = self::$testDomainObject->newVoiceApplicationFromUrl(self::$testConfiguration->newDomainVoiceApplication, $newHostedApplicationObject->url, "POST");
            $this->assertInstanceOf('Cloudonix\Entities\VoiceApplication', $newVoiceApplicationObject);
            $this->assertIsString($newVoiceApplicationObject->name);
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
