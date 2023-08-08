<?php

namespace Castor\Monorepo;

class Composer
{
    /**
     * @param string $path
     * @return Composer
     */
    public static function parseFile(string $path): Composer
    {
        $json = @file_get_contents($path);
        if (!is_string($json)) {
            throw new \InvalidArgumentException("Filename '$path' does not exist or is not readable");
        }

        return self::parse($json);
    }

    /**
     * @param string $json
     * @return Composer
     */
    public static function parse(string $json): Composer
    {
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \InvalidArgumentException('Not valid json', previous: $e);
        }
        return new self($data);
    }

    public function __construct(
        private readonly array $data
    ) {
    }

    public function getName(): string
    {
        return $this->data['name'] ?? '';
    }

    public function getPHPVersion(): string
    {
        return $this->data['require']['php'] ?? '';
    }

    public function getAutoload(): array
    {
        return $this->data['autoload'] ?? [];
    }

    public function getAutoloadDev(): array
    {
        return $this->data['autoload-dev'] ?? [];
    }
}