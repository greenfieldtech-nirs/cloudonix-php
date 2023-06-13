<?php
    /**     * @package cloudonixPhp
     * @file    Helpers/ConfigHelper.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    /**
     * Class Library Constants and Enumerators
     */
    const HTTP_TIMEOUT = 2.0;
    const HTTP_ENDPOINT_HOST = "api.cloudonix.io";
    const HTTP_ENDPOINT_TRANSPORT = "http";

    const HTTP_ENDPOINT = HTTP_ENDPOINT_TRANSPORT . "://" . HTTP_ENDPOINT_HOST;
    const HTTP_AGENT = "cloudonix-php library 0.3";

    const LOGGER_DISABLE = -200;
    const LOGGER_DEBUG = 200;
    const LOGGER_INFO = 100;
    const LOGGER_NOTICE = 99;
    const LOGGER_WARNING = 90;
    const LOGGER_ERROR = 80;
    const LOGGER_CRITICAL = 70;
    const LOGGER_ALERT = 60;
    const LOGGER_EMERGENCY = 0;
    const URLPATH_TENANTS = "/tenants";
    const URLPATH_DOMAINS = "/domains";
    const URLPATH_SUBSCRIBERS = "/subscribers";
    const URLPATH_APPLICATIONS = "/applications";
    const URLPATH_CONTAINER_APPLICATIONS = "/hosted-applications";
    const URLPATH_TRUNKS = "/trunks";
    const URLPATH_APIKEYS = "/keys";
    const URLPATH_ALIASES = "/aliases";
    const URLPATH_DNIDS = "/dnids";
    const URLPATH_CALLS = "/calls";
    const URLPATH_SESSIONS = "/sessions";
    const FILTER_INCOMING = "?by_direction=incoming";
    const FILTER_OUTGOING = "?by_direction=outgoing";
    const FILTER_APPLICATION = "?by_direction=application";
    const URLPATH_CONFERENCES = "/conferences";



