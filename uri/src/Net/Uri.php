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

namespace Castor\Net;

use Castor\Net\Uri\ParseError;
use Castor\Net\Uri\Query;
use Castor\Net\Uri\UserInfo;

/**
 * Uri represents an RFC 3986 compliant Uniform Resource Identifier in the form of a value object.
 *
 * It uses PHP's internal parsing algorithm, but adds a few conveniences on top of it.
 *
 * This has been designed with extensibility in mind so other kinds of objects based on URIs can benefit of its
 * API, for instance URLs or URNs.
 *
 * This object is completely immutable and is capable of performing the most basic operations defined in RFC 3986.
 *
 * @psalm-external-mutation-free
 */
class Uri implements \Stringable, \JsonSerializable
{
    private string $scheme;
    private UserInfo $userinfo;
    private string $host;
    private string $path;
    private string $rawPath;
    private string $rawQuery;
    private string $fragment;
    private string $rawFragment;

    /**
     * @psalm-external-mutation-free
     */
    final private function __construct()
    {
        $this->scheme = '';
        $this->userinfo = new UserInfo();
        $this->host = '';
        $this->path = '';
        $this->rawPath = '';
        $this->rawQuery = '';
        $this->fragment = '';
        $this->rawFragment = '';
    }

    /**
     * @psalm-mutation-free
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    public function __clone()
    {
        $this->userinfo = clone $this->userinfo;
    }

    public static function isValid(string $uri): bool
    {
        try {
            self::parse($uri);

            return true;
        } catch (ParseError) {
            return false;
        }
    }

    /**
     * Creates a new URI from its parts.
     *
     * $host takes a "hostname:port" or "hostname" form.
     * $userinfo takes a "user:pass" or "user" form.
     *
     * @psalm-allow-private-mutation
     */
    public static function fromParts(
        string $scheme = '',
        string $userinfo = '',
        string $host = '',
        string $path = '',
        string $rawPath = '',
        string $rawQuery = '',
        string $fragment = '',
        string $rawFragment = '',
    ): Uri {
        $uri = new static();
        $uri->scheme = $scheme;
        $uri->userinfo = UserInfo::parse($userinfo);
        $uri->host = $host;
        $uri->path = $path;
        $uri->rawPath = $rawPath;
        $uri->rawQuery = $rawQuery;
        $uri->fragment = $fragment;
        $uri->rawFragment = $rawFragment;

        return $uri;
    }

    /**
     * Parses a URI from its string representation.
     *
     * @throws ParseError
     *
     * @psalm-allow-private-mutation
     */
    public static function parse(string $string): static
    {
        $result = \parse_url($string);
        if (false === $result) {
            throw new ParseError('Error while parsing uri');
        }

        $uri = new static();
        $uri->scheme = $result['scheme'] ?? '';

        $uri->rawPath = $result['path'] ?? '';
        $uri->path = \rawurldecode($uri->rawPath);
        if ($uri->path === $uri->rawPath) {
            $uri->rawPath = '';
        }

        $uri->rawQuery = $result['query'] ?? '';

        $uri->rawFragment = $result['fragment'] ?? '';
        $uri->fragment = \rawurldecode($uri->rawFragment);
        if ($uri->fragment === $uri->rawFragment) {
            $uri->rawFragment = '';
        }

        $uri->userinfo = UserInfo::create($result['user'] ?? '', $result['pass'] ?? null);

        $uri->host = $result['host'] ?? '';
        $port = $result['port'] ?? 0;
        if ('' !== $uri->host && 0 !== $port) {
            $uri->host .= ':'.$port;
        }

        return $uri;
    }

    /**
     * @psalm-mutation-free
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Returns the userInfo as a string.
     *
     * It could be in "user:pass" or "user" form.
     *
     * @psalm-mutation-free
     */
    public function getUserinfo(): UserInfo
    {
        return $this->userinfo;
    }

    /**
     * Returns the host as a string.
     *
     * It could be in "hostname:port" or "hostname" form.
     *
     * @psalm-mutation-free
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Returns the url decoded path as a string.
     *
     * For the raw, non-decoded path use:
     *
     * @see Uri::getRawPath()
     *
     * @psalm-mutation-free
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns the raw path or the decoded path otherwise.
     *
     * @psalm-mutation-free
     */
    public function getRawPath(): string
    {
        // If there is a raw path, no need to recompute it
        if ('' !== $this->rawPath) {
            return $this->rawPath;
        }

        // We assume the user knows what they are doing
        return $this->path;
    }

    /**
     * Returns the raw query string.
     *
     * The query string HAS NOT been url decoded.
     *
     * To manipulate it, use:
     *
     * @see Query::decode()
     * @see Query::encode()
     *
     * @psalm-mutation-free
     */
    public function getRawQuery(): string
    {
        return $this->rawQuery;
    }

    /**
     * Returns the url decoded fragment as a string.
     *
     * For the raw, non-decoded fragment use:
     *
     * @see Uri::getRawFragment()
     *
     * @psalm-mutation-free
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * Returns the raw fragment or the decoded fragment otherwise.
     *
     * @psalm-mutation-free
     */
    public function getRawFragment(): string
    {
        // If there is a raw fragment, no need to recompute it
        if ('' !== $this->rawFragment) {
            return $this->rawFragment;
        }

        // We assume the user knows what they are doing
        return $this->fragment;
    }

    /**
     * Returns a new URI with the passed scheme.
     *
     * @return $this
     */
    public function withScheme(string $scheme): static
    {
        $copy = clone $this;
        $copy->scheme = $scheme;

        return $copy;
    }

    /**
     * Returns a new URI with the passed userinfo.
     *
     * @return $this
     */
    public function withUserinfo(UserInfo $userinfo): static
    {
        $copy = clone $this;
        $copy->userinfo = $userinfo;

        return $copy;
    }

    /**
     * Returns a new URI with the passed host.
     *
     * @return $this
     */
    public function withHost(string $host): static
    {
        $copy = clone $this;
        $copy->host = $host;

        return $copy;
    }

    /**
     * Returns a new URI with the passed path.
     *
     * The caller MUST provide an url-encoded path if needed.
     *
     * @return $this
     */
    public function withPath(string $rawPath): static
    {
        $copy = clone $this;
        $copy->path = \rawurldecode($rawPath);
        $copy->rawPath = $rawPath;

        if ($copy->path === $copy->rawPath) {
            $copy->rawPath = '';
        }

        return $copy;
    }

    /**
     * Returns a new URI with the encoded query string of the passed Query object.
     *
     * @return $this
     */
    public function withQuery(Query $query): static
    {
        return $this->withRawQuery($query->encode());
    }

    /**
     * Returns a new URI with the passed rawQuery.
     *
     * @return $this
     */
    public function withRawQuery(string $rawQuery): static
    {
        $copy = clone $this;
        $copy->rawQuery = $rawQuery;

        return $copy;
    }

    /**
     * Returns a new URI with the passed fragment.
     *
     * The caller MUST provide an url-encoded fragment if needed.
     *
     * @return $this
     */
    public function withFragment(string $rawFragment): static
    {
        $copy = clone $this;
        $copy->fragment = \rawurldecode($rawFragment);
        $copy->rawFragment = $rawFragment;

        if ($copy->rawFragment === $copy->fragment) {
            $copy->rawFragment = '';
        }

        return $copy;
    }

    /**
     * Returns true if the URI is opaque, or false otherwise.
     *
     * An opaque URI is a URI without authority.
     *
     * @psalm-mutation-free
     */
    public function isOpaque(): bool
    {
        return '' === $this->getAuthority();
    }

    /**
     * Returns true if the URI is absolute, or false otherwise.
     *
     * An absolute URI is a URI that has a scheme.
     *
     * @psalm-mutation-free
     */
    public function isAbsolute(): bool
    {
        return '' !== $this->scheme;
    }

    /**
     * Returns the URI authority as a string.
     *
     * The authority DOES NOT contain the forward slash pair "//".
     *
     * @psalm-mutation-free
     */
    public function getAuthority(): string
    {
        $auth = '';

        $userInfo = $this->userinfo->encode();
        if ('' !== $userInfo) {
            $auth .= $userInfo.'@';
        }

        return $auth.$this->host;
    }

    /**
     * Returns the hostname of the URI as a string.
     */
    public function getHostname(): string
    {
        if ('' === $this->host) {
            return $this->host;
        }

        $sep = \strpos($this->host, ':');
        if (false === $sep) {
            return $this->host;
        }

        return \substr($this->host, 0, $sep);
    }

    /**
     * Returns the port of the URI as a string.
     *
     * If no port was specified, an empty string is returned.
     *
     * @psalm-mutation-free
     */
    public function getPort(): string
    {
        if ('' === $this->host) {
            return $this->host;
        }

        $sep = \strpos($this->host, ':');
        if (false === $sep) {
            return '';
        }

        return \substr($this->host, $sep + 1);
    }

    /**
     * Returns the port of the URI as an integer.
     *
     * If the port is not specified, -1 is returned, which is an invalid port.
     *
     * The zero port, although unusable, has a special meaning in system calls.
     *
     * @psalm-mutation-free
     */
    public function getPortNumber(): int
    {
        $port = $this->getPort();
        if ('' === $port) {
            return -1;
        }

        return (int) $port;
    }

    /**
     * Parses the query string of a URI.
     *
     * It returns a new Query object on every call.
     */
    public function getQuery(): Query
    {
        return Query::decode($this->rawQuery);
    }

    /**
     * Returns a string representation of the URI.
     *
     * This string is safely encoded from transmission.
     */
    public function toString(): string
    {
        $uri = '';
        if ('' !== $this->scheme) {
            $uri .= $this->scheme.':';
        }

        $auth = $this->getAuthority();
        if ('' !== $auth) {
            $uri .= '//'.$auth;
        }

        $path = $this->getRawPath();
        if ('' !== $path) {
            $uri .= $path;
        }

        if ('' !== $this->rawQuery) {
            $uri .= '?'.$this->rawQuery;
        }

        $fragment = $this->getRawFragment();
        if ('' !== $fragment) {
            $uri .= '#'.$fragment;
        }

        return $uri;
    }

    /**
     * @psalm-mutation-free
     */
    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * Returns true if two URIs are equal, or false otherwise.
     *
     * @psalm-mutation-free
     */
    public function equals(Uri $uri): bool
    {
        return $this->scheme === $uri->scheme
            && $this->userinfo->toString() === $uri->userinfo->toString()
            && $this->host === $uri->host
            && $this->path === $uri->path
            && $this->rawPath === $uri->rawPath
            && $this->rawQuery === $uri->rawQuery
            && $this->fragment === $uri->fragment
            && $this->rawFragment === $uri->rawFragment;
    }

    /**
     * Returns an exact copy of this uri.
     *
     * @psalm-mutation-free
     */
    public function copy(): static
    {
        return clone $this;
    }

    /**
     * Resolves a Uri according to RFC 3986 Section 5.
     *
     * @param Uri $ref The uri reference
     *
     * @return Uri
     */
    public function resolve(Uri $ref): static
    {
        $copy = clone $ref;

        if ('' === $ref->scheme) {
            $copy->scheme = $this->scheme;
        }

        if ('' !== $ref->scheme || '' !== $ref->host || !$ref->userinfo->isEmpty()) {
            $copy->path = Uri\resolvePath($ref->getPath(), '');

            return $copy;
        }

        if ($ref->isOpaque() && '' !== $ref->getPath()) {
            $copy->userinfo = UserInfo::create('');
            $copy->host = '';
            $copy->path = '';

            return $copy;
        }

        if ('' === $ref->path && '' === $ref->rawQuery) {
            $copy->rawQuery = $this->rawQuery;
            if ('' === $ref->fragment) {
                $copy->fragment = $this->fragment;
                $copy->rawFragment = $this->rawFragment;
            }
        }

        $copy->host = $this->host;
        $copy->userinfo = clone $this->userinfo;
        $copy->path = Uri\resolvePath($this->getPath(), $ref->getPath());

        return $copy;
    }
}
