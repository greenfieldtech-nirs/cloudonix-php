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
     * @filename: ConfigHelper.php
     * @author  :   nirs
     * @created :  2023-05-11
     */

    const CONFIG_ALL = 100;
    const CACHE_DRIVER = "Files";
    const HTTP_TIMEOUT = 2.0;
    const HTTP_ENDPOINT_HOST = "api.cloudonix.io";
    const HTTP_ENDPOINT_TRANSPORT = "http";
    const HTTP_ENDPOINT = HTTP_ENDPOINT_TRANSPORT . "://" . HTTP_ENDPOINT_HOST;
    const HTTP_AGENT = "cloudonix-php library 0.3";

    const DISABLE = -200;
    const DEBUG = 200;
    const INFO = 100;
    const NOTICE = 99;
    const WARNING = 90;
    const ERROR = 80;
    const CRITICAL = 70;
    const ALERT = 60;
    const EMERGENCY = 0;

    const URLPATH_TENANTS = "/tenants";
    const URLPATH_DOMAINS = "/domains";
    const URLPATH_SUBSCRIBERS = "/subscribers";
    const URLPATH_APPLICATIONS = "/applications";
    const URLPATH_CONTAINER_APPLICATIONS = "/hosted-applications";
    const URLPATH_TRUNKS = "/trunks";
    const URLPATH_APIKEYS = "/keys";
    const URLPATH_ALIASES = "/aliases";
    const URLPATH_DNIDS = "/dnids";
    const URLPATH_SESSIONS = "/sessions";
    const URLPATH_CONFERENCES = "/conferences";



