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

     public function test_if_24hrs_is_false(): void
     {
 
         $zk = new ZkTecoService();
         $response = $zk->getConfig('24hrs_format');
 
         $this->assertEquals(false,$response);
     }

     public function test_if_live_update_is_false(){
        $zk = new ZkTecoService();
        $response = $zk->getConfig('live_update');

        $this->assertEquals(false,$response);
     }


     public function test_if_ip_address_is_exist(){
        $zk = new ZkTecoService();
        $response = $zk->getConfig('zkteco');

        $this->assertEquals('111.111.1.111',$response);

    
      
     }

     public function test_if_primary_color_is_correct (){
        $zk = new ZkTecoService();
        $response = $zk->getConfig('primary');

        $this->assertEquals('#49b265',$response);

     }
     public function test_if_secondary_color_is_correct (){
        $zk = new ZkTecoService();
        $response = $zk->getConfig('secondary');

        $this->assertEquals('#26A69A',$response);

     }
}
