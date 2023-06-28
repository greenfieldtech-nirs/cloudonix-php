<?php
    /**
     * @package cloudonix-php
     * @file    Helpers/LogHelper.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Helpers;

    use Ramsey\Uuid\Uuid;
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;

    /**
     * LogHelper Class
     *
     * The LogHelper class provides a wrapper interface to Monolog Logger, allowing simpler and clear logging.
     */
    class LogHelper
    {
        public Logger $logger;
        public mixed $level;
        public \Ramsey\Uuid\UuidInterface $uuid;

        public function __construct($logChannelLevel = LOGGER_DISABLE, string $logChannelName = 'cloudonix-php', string $logChannelStream = __DIR__ . '/cloudonix-php.log')
        {
            $this->logger = new Logger($logChannelName);
            $streamHandler = new StreamHandler($logChannelStream);

            $this->uuid = Uuid::uuid4();
            $streamFormat = "%datetime% %channel% [" . $this->uuid . "] [%level_name%]: %message% %context% %extra%\n";
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
            if (!$this->severityFilter(LOGGER_INFO)) return;
            $array = (is_object($array)) ? (array)$array : $array;
            $this->logger->info($param, $array);
            return;
        }

        public function debug(string $param, $array = [])
        {
            if (!$this->severityFilter(LOGGER_DEBUG)) return;
            $array = (is_object($array)) ? (array)$array : $array;
            $this->logger->debug($param, $array);
            return;
        }

        public function error(string $param, $array = [])
        {
            if (!$this->severityFilter(LOGGER_ERROR)) return;
            $array = (is_object($array)) ? (array)$array : $array;
            $this->logger->error($param, $array);
            return;
        }

        public function critical(string $param, $array = [])
        {
            if (!$this->severityFilter(LOGGER_CRITICAL)) return;
            $array = (is_object($array)) ? (array)$array : $array;
            $this->logger->critical($param, $array);
            return;
        }

        public function alert(string $param, $array = [])
        {
            if (!$this->severityFilter(LOGGER_ALERT)) return;
            $array = (is_object($array)) ? (array)$array : $array;
            $this->logger->alert($param, $array);
            return;
        }

        public function notice(string $param, $array = [])
        {
            if (!$this->severityFilter(LOGGER_NOTICE)) return;
            $array = (is_object($array)) ? (array)$array : $array;
            $this->logger->notice($param, $array);
            return;
        }

        public function warning(string $param, $array = [])
        {
            if (!$this->severityFilter(LOGGER_WARNING)) return;
            $array = (is_object($array)) ? (array)$array : $array;
            $this->logger->warning($param, $array);
            return;
        }

        public function emergency(string $param, $array = [])
        {
            if (!$this->severityFilter(LOGGER_EMERGENCY)) return;
            $array = (is_object($array)) ? (array)$array : $array;
            $this->logger->emergency($param, $array);
            return;
        }

    }