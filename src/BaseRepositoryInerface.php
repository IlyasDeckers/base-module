<?php
namespace Clockwork\Base;

interface BaseRepositoryInterface
{
    public function find(object $request);

    public function getAll(object $request);
}