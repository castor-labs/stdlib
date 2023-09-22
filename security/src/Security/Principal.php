<?php

declare(strict_types=1);

/**
 * @project The Castor Standard Library
 * @link https://github.com/castor-labs/stdlib
 * @package castor/stdlib
 * @author Matias Navarro-Carter mnavarrocarter@gmail.com
 * @license MIT
 * @copyright 2022 CastorLabs Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Castor\Security;

/**
 * A Principal represents the context in which the authentication takes place.
 *
 * Its main purpose is to store the identity that is authenticated.
 *
 * You can use ClaimsPrincipal to inject metadata about the kind of authentication that took place
 * in the form of simple string claims (for example, if the attestation of identity was strong or weak).
 * You can create your own claims and verify them later in your application.
 */
interface Principal
{
    /**
     * Returns the identity of the Principal.
     */
    public function getIdentity(): Identity;
}
