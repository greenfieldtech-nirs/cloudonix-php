<?php
    /**
     * @package cloudonix-php
     * @file    Helpers/UtilityHelper.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Helpers;

    use DOMDocument;
    use Hackzilla\PasswordGenerator\Generator\HybridPasswordGenerator;

    /**
     * UtilityHelper Class
     *
     * This helper is designed to provide various methods that assist in validating or generating data.
     */
    class UtilityHelper
    {
        public function __construct()
        {
        }

        /**
         * Generate a highly secured SIP Password
         *
         * @param bool   $setLowercase
         * @param bool   $setUppercase
         * @param bool   $setNumbers
         * @param int    $setSegmentCount
         * @param int    $setSegmentLength
         * @param string $setSegmentSeparator
         *
         * @return string
         */
        public function generateSecuredPassword(bool   $setLowercase = true,
                                                bool   $setUppercase = true,
                                                bool   $setNumbers = true,
                                                int    $setSegmentCount = 4,
                                                int    $setSegmentLength = 6,
                                                string $setSegmentSeparator = '-'): string
        {
            $generator = new HybridPasswordGenerator();
            $generator
                ->setLowercase($setLowercase)
                ->setUppercase($setUppercase)
                ->setNumbers($setNumbers)
                ->setSegmentCount($setSegmentCount)
                ->setSegmentLength($setSegmentLength)
                ->setSegmentSeparator($setSegmentSeparator);
            return $generator->generatePassword();
        }

        /**
         * Validate and Clean a given string to a valid URL - or false or invalid URL
         *
         * @param string $url
         *
         * @return mixed
         */
        public function cleanUrl(string $url): mixed
        {
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return false;
            }
            return filter_var($url, FILTER_SANITIZE_URL);
        }

        /**
         * Validate that the input source code doesn't contain well-known malicious elements is a valid JS or
         * Ruby source code
         *
         * Note: This code is really simple and should not be considered as "secure" - it's only a sanity check.
         *
         * @param string $code
         *
         * @return mixed
         */
        public function validateCode(string $code): mixed
        {
            $jsPattern = '/^\s*(?:var|let|const|function|if|else|for|while|switch|case|break|continue|return|try|catch)\b/';
            $rubyPattern = '/^\s*(?:def|if|elsif|else|unless|while|until|for|do|case|when|break|next|return|begin|rescue)\b/';

            // Check for specific patterns that may indicate malicious code
            $blacklistPatterns = [
                '/\bexec\s*\(/i',         // Matches 'exec(' case-insensitively
                '/\bsystem\s*\(/i',       // Matches 'system(' case-insensitively
                '/\bshell_exec\s*\(/i',   // Matches 'shell_exec(' case-insensitively
                '/\bpassthru\s*\(/i',     // Matches 'passthru(' case-insensitively
                '/\b`.*`\b/i',            // Matches backticks (`) that may indicate shell execution
                '/\b(eval|assert)\s*\(/i' // Matches 'eval(' or 'assert(' case-insensitively
            ];

            $isValidJS = preg_match($jsPattern, $code);
            $isValidRuby = preg_match($rubyPattern, $code);

            // Validate XML code separately
            $isValidXML = $this->validateXML($code);

            if (!$isValidJS && !$isValidRuby && !$isValidXML) {
                return false; // Code doesn't match language patterns or is invalid XML
            }

            foreach ($blacklistPatterns as $pattern) {
                if (preg_match($pattern, $code)) {
                    return false; // Code contains potentially malicious patterns
                }
            }

            return $code; // Code is valid
        }

        private function validateXML($code)
        {
            $dom = new DOMDocument();

            // Disable error reporting for warnings and errors during parsing
            libxml_use_internal_errors(true);
            $isValid = $dom->loadXML($code);
            libxml_use_internal_errors(false);

            return $isValid;
        }

    }