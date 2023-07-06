<?php
    /**
     * @package cloudonix-php
     * @file    tests/ClientTest.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */
    namespace Cloudonix;

    require_once 'TestConfiguration.php';

    use PHPUnit\Framework\TestCase;
    use Shalvah\Clara\Clara;
    use Cloudonix\CloudonixClient ;
    use Cloudonix\TestConfiguration as TestConfiguration;

    class _ClientTest extends TestCase
    {
        private CloudonixClient $cxClientTester;
        private Clara $consoleLogger;
        private static $testConfiguration;

        public function __construct(string $name)
        {
            $this->consoleLogger = new Clara("UnitClientTest");
            self::$testConfiguration = new TestConfiguration();
            $this->cxClientTester = new CloudonixClient(
                self::$testConfiguration->apiKey,
                self::$testConfiguration->endpoint,
                self::$testConfiguration->endpointTimeout,
                self::$testConfiguration->endpointDebug
            );
            parent::__construct($name);
        }

        public function test__construct(): void
        {
            $this->consoleLogger->info("");
            $this->consoleLogger->debug("[" . get_class() . "] Executing " . __FUNCTION__ );
            $this->assertTrue(isset($this->cxClientTester->httpConnector));
        }
    }
