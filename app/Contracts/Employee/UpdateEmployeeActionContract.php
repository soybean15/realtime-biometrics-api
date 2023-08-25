<?php

namespace App\Contracts;

interface UpdateEmployeeActionContract
{
    public function execute(array $data, $employeeId);
}
