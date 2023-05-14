<?php
    /**
     * <code>
     *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
     * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
     * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
     * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
     * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
     *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
     * </code>
     *
     * @Project cloudonix-php | LogHelper.php
     * @author  Nir Simionovich <nirs@cloudonix.io> | 2023-05-04
     */

    namespace Cloudonix\Helpers;

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;
    class LogHelper
    {
        public $logger;
        public $level;

        public function __construct($logChannelLevel = DISABLE, string $logChannelName = 'cloudonix-php', string $logChannelStream = 'cloudonix-php.log')
        {
            $this->logger = new Logger($logChannelName);
            $streamHandler = new StreamHandler($logChannelStream);

            $streamFormat = "%datetime% %channel% [%level_name%]: %message% %context% %extra%\n";
            $streamDateFormat = "M d H:i:s";
            $streamHandler->setFormatter(new LineFormatter($streamFormat, $streamDateFormat, false, true));
            $this->logger->pushHandler($streamHandler);
            $this->level = $logChannelLevel;
        }

        /**
         * @param int $param
         *
         * @return bool
         */
        private function severityFilter(int $param): bool
        {
            $result = false;
            if ($this->level >= $param)
                $result = true;

            return $result;
        }

        /**
         * @param string $param
         * @param array  $array
         *
         * @return void
         */
        public function info(string $param, $array = [])
        {
            if (!$this->severityFilter(INFO)) return;
            $array = (is_object($array)) ? (array)$array : $array;
            $this->logger->info($param, $array);
            return;
        }

        public function debug(string $param, $array = [])
        {
            if (!$this->severityFilter(DEBUG)) return;
            $array = (is_object($array)) ? (array)$array : $array;
            $this->logger->debug($param, $array);
            return;
        }

        public function error(string $param, $array = [])
        {
            if (!$this->severityFilter(ERROR)) return;
            $array = (is_object($array)) ? (array)$array : $array;
            $this->logger->error($param, $array);
            return;
        }

        public function critical(string $param, $array = [])
        {
            if (!$this->severityFilter(CRITICAL)) return;
            $array = (is_object($array)) ? (array)$array : $array;
            $this->logger->critical($param, $array);
            return;
        }

        public function alert(string $param, $array = [])
        {
            if (!$this->severityFilter(ALERT)) return;
            $array = (is_object($array)) ? (array)$array : $array;
            $this->logger->alert($param, $array);
            return;
        }

        public function notice(string $param, $array = [])
        {
            if (!$this->severityFilter(NOTICE)) return;
            $array = (is_object($array)) ? (array)$array : $array;
            $this->logger->notice($param, $array);
            return;
        }

        public function warning(string $param, $array = [])
        {
            if (!$this->severityFilter(WARNING)) return;
            $array = (is_object($array)) ? (array)$array : $array;
            $this->logger->warning($param, $array);
            return;
        }

        public function emergency(string $param, $array = [])
        {
            if (!$this->severityFilter(EMERGENCY)) return;
            $array = (is_object($array)) ? (array)$array : $array;
            $this->logger->emergency($param, $array);
            return;
        }

    }