<?php

namespace Tests\Feature;

use App\Http\Managers\ReportManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_if_employee_is_late(): void
    {
        $manager = new ReportManager();
        $result = $manager ->isLate('2023-10-23 08:02:00');
        $this->assertEquals(true,$result);
    }
    public function test_if_employee_is__not_late(): void
    {
        $manager = new ReportManager();
        $result = $manager ->isLate('2023-10-23 08:00:00');
        $this->assertEquals(false,$result);
    }
    public function test_if_employee_is_late_ignore_date(): void
    {
        $manager = new ReportManager();
        $result = $manager ->isLate('08:01:00');
        $this->assertEquals(true,$result);
    }
    public function test_if_employee_is_not_late_ignore_date(): void
    {
        $manager = new ReportManager();
        $result = $manager ->isLate('08:00:00');
        $this->assertEquals(false,$result);
    }
}
