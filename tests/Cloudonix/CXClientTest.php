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
     * @filename: CXClientTest.php
     * @author  :   nirs
     * @created :  2023-05-27
     */

    namespace Cloudonix;

    require_once 'TestConfiguration.php';

    use PHPUnit\Framework\TestCase;
    use Shalvah\Clara\Clara;
    use Cloudonix\CXClient as CXClient;
    use Cloudonix\TestConfiguration as TestConfiguration;

    class CXClientTest extends TestCase
    {
        private CXClient $cxClientTester;
        private Clara $consoleLogger;
        private TestConfiguration $testConfiguration;

        public function __construct(string $name)
        {
            $this->consoleLogger = new Clara("CXClientTest");
            $testConfiguration = new TestConfiguration();
            $this->cxClientTester = new CXClient(
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
