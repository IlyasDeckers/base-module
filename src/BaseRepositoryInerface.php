<?php
namespace Clockwork\Base;

use Illuminate\Http\Request;

interface BaseRepositoryInterface
{
    public function find(Request $request) : object;

    public function getAll(Request $request) : object;

    public function store(Request $request) : object;

    public function update(array $data) : object;

    public function destroy(int $id) : void;
}