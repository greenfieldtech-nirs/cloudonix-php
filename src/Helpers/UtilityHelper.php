<?php
    /**
     * @package cloudonixPhp
     * @file    Helpers/UtilityHelper.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Helpers;

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
    }