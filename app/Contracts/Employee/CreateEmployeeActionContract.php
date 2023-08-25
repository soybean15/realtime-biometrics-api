<?php

namespace App\Contracts;

interface CreateEmployeeActionContract
{
    public function execute(array $data);
}
