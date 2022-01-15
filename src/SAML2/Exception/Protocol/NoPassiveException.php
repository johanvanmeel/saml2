<?php

declare(strict_types=1);

namespace SAML2\Exception\Protocol;

use SAML2\Exception\ProtocolViolationException;

/**
 * A SAML error indicating that the responding provider cannot authenticate the principal
 *   passively, as has been requested.
 *
 * @package simplesamlphp/saml2
 */
class NoPassiveException extends ProtocolViolationException
{
}
