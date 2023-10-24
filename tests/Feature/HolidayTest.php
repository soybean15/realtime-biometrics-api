<?php

namespace Tests\Feature;

use App\Http\Managers\ReportManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HolidayTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_if_date_is_active(): void
    {
        $manager = new ReportManager();


        $result =  $manager->isDateActive('2023-10-23');
        $this->assertEquals(true,$result);

        $result2 =  $manager->isDateActive('2023-10-20');
        $this->assertEquals(true,$result2);


       
    }
    public function  test_if_date_is_not_active(): void
    {
        $manager = new ReportManager();


        $result =  $manager->isDateActive('2023-10-25');
        $this->assertEquals(false,$result);


   
       
    }

    public function test_if_date_is_moved(){

        $manager = new ReportManager();


        $result =  $manager->isDateActive('2023-11-01');
        $this->assertEquals(true,$result);

    }

    public function test_if_date_is_moved_inactive(){

        $manager = new ReportManager();

        $result =  $manager->isDateActive('2023-11-04');//weekend
        $this->assertEquals(false,$result);

        $result2 =  $manager->isDateActive('2023-11-06');//moved holiday
        $this->assertEquals(false,$result2);

        $result3 =  $manager->isDateActive('2023-11-27');//holiday
        $this->assertEquals(false,$result3);


    }


}
