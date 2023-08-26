<?php

namespace App\Actions\Employee;
use App\Contracts\CreateNewEmployeeContract;


class CreateNewEmployee implements CreateNewEmployeeContract

{


    public function execute($data){

       return $data;
    }

}