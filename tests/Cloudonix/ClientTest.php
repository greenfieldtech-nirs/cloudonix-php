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
        private TestConfiguration $testConfiguration;

        public function __construct(string $name)
        {
            $this->consoleLogger = new Clara("UnitClientTest");
            $testConfiguration = new TestConfiguration();
            $this->cxClientTester = new CloudonixClient(
                $testConfiguration->apiKey,
                $testConfiguration->endpoint,
                $testConfiguration->endpointTimeout,
                $testConfiguration->endpointDebug
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
