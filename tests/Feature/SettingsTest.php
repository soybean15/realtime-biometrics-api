<?php

namespace Tests\Feature;

use App\Http\Services\ZkTecoService;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    /**
     * A basic feature test example.
     */

     public function test_if_24hrs_format_returns_false(): void
     {
 
         $zk = new ZkTecoService();
         $response = $zk->getSetting('24hrs_format');
 
         $this->assertEquals(false,$response);
     }

     public function test_if_live_update_returns_false(){
        $zk = new ZkTecoService();
        $response = $zk->getSetting('live_update');

        $this->assertEquals(false,$response);
     }


     public function test_if_ip_address_exist(){
        $zk = new ZkTecoService();
        $response = $zk->getSetting('zkteco');

        $this->assertEquals('111.111.1.111',$response);

    
      
     }

     public function test_if_primary_color_is_correct (){
        $zk = new ZkTecoService();
        $response = $zk->getSetting('primary');

        $this->assertEquals('#49b265',$response);

     }
     public function test_if_secondary_color_is_correct (){
        $zk = new ZkTecoService();
        $response = $zk->getSetting('secondary');

        $this->assertEquals('#26A69A',$response);

     }

  
}
