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

namespace Castor\Security\Claim;

use Castor\Security\Identity as IIdentity;

final class Identity implements IIdentity
{
    /**
     * @var string[]
     */
    private array $claims;

    public function __construct(
        private readonly string $id,
        string ...$claims
    ) {
        $this->claims = $claims;
    }

    public function toString(): string
    {
        return $this->id;
    }

    public function getClaims(): array
    {
        return $this->claims;
    }

    public function hasClaim(string $claim): bool
    {
        return \in_array($claim, $this->claims, true);
    }
}
