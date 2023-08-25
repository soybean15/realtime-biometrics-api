<?php

namespace App\Contracts;

interface DeleteEmployeeActionContract
{
    public function execute($employeeId);
}
