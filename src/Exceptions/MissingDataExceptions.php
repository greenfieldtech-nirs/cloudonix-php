<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | ClientExceptions.php
 * Creator: Nir Simionovich <nirs@cloudonix.io> | 2019-06-29
 */

namespace Cloudonix\Exceptions;

use Exception;
use GuzzleHttp\Exception\BadResponseException;

class MissingDomainIdException extends Exception {}
class MissingDnidIdException extends Exception {}
class MissingApplicationIdException extends Exception {}
class MissingSubscriberIdException extends Exception {}
class MissingTrunkIdException extends Exception {}
class MissingAdditionalDataException extends Exception {}
class MissingTenantNameException extends Exception {}
class MissingTenantIdException extends Exception {}
class MissingApikeyIdException extends Exception {}


