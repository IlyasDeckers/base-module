<?php
namespace IlyasDeckers\BaseModule\Interfaces;

use Illuminate\Http\Request;

interface BaseControllerInterface
{
    public function index(Request $request) : object;

    public function show(Request $request) : object;

    public function store(Request $request) : object;

    public function update(Request $request) : object;

    public function destroy(int $id) : void;

    public function getRules() : array;
}
