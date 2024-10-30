<?php

namespace Mk4U\Cache;

trait HelperTrait{
    /**
     * Validar $key
     */
    private function validateKey(string $key): void
    {
        if (empty($key) || !preg_match('/^[A-Za-z0-9_.]+$/', $key)) {
            throw new \InvalidArgumentException("$key is not a legal value.");
        }
    }
}