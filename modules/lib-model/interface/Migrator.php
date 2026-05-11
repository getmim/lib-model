<?php
/**
 * Model migrator interface
 * @package lib-model
 * @version 1.0.0
 */

namespace LibModel\Iface;

interface Migrator
{
    public function __construct(string $model, array $data);

    public function db(array $configs): bool;

    public function getShards(): ?array;

    public function lastError(): ?string;

    public function schema(string $table): bool;

    public function start(string $table): bool;

    public function test(string $table): ?array;
}
