<?php

namespace App\ValueObjects;

use RuntimeException;

class Money
{
    private const SCALE = 4;

    public function __construct(private string $value)
    {
        $this->value = self::format($value);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function zero(): self
    {
        return new self('0');
    }

    public function add(self $other): self
    {
        return new self(self::bc('bcadd', $this->value, $other->value));
    }

    public function subtract(self $other): self
    {
        return new self(self::bc('bcsub', $this->value, $other->value));
    }

    public function multiply(string $factor): self
    {
        return new self(self::bc('bcmul', $this->value, $factor));
    }

    public function divide(string $divisor): self
    {
        return new self(self::bc('bcdiv', $this->value, $divisor));
    }

    public function isLessThan(self $other): bool
    {
        return self::bc('bccomp', $this->value, $other->value) === -1;
    }

    public function equals(self $other): bool
    {
        return self::bc('bccomp', $this->value, $other->value) === 0;
    }

    public function greaterThan(self $other): bool
    {
        return self::bc('bccomp', $this->value, $other->value) === 1;
    }

    public function toString(): string
    {
        return $this->value;
    }

    private static function format(string $value): string
    {
        if (! function_exists('bcadd')) {
            throw new RuntimeException('BCMath extension is required for precise money calculations.');
        }

        $normalized = bcadd($value, '0', self::SCALE);

        return rtrim(rtrim($normalized, '0'), '.') ?: '0';
    }

    private static function bc(string $function, string $left, string $right): mixed
    {
        if (! function_exists($function)) {
            throw new RuntimeException('BCMath extension is required for precise money calculations.');
        }

        $result = $function($left, $right, self::SCALE);

        if ($function === 'bccomp') {
            return $result;
        }

        return self::format((string) $result);
    }
}
