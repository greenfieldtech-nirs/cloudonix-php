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
     * @filename:  PasswordHelper.php
     * @author  :  nirs
     * @created :  2023-05-29
     */

    namespace Cloudonix\Helpers;

    use Hackzilla\PasswordGenerator\Generator\HybridPasswordGenerator;

    class PasswordHelper
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
    }