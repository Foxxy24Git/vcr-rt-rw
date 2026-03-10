<?php

namespace Tests\Unit;

use App\Services\Mikrotik\Clients\RealMikrotikClient;
use Tests\TestCase;

class RealMikrotikClientTest extends TestCase
{
    public function test_it_generates_hotspot_comment_format_correctly(): void
    {
        $client = new RealMikrotikClient;

        $comment = $client->formatHotspotComment(
            resellerPhone: '082288231533',
            resellerName: 'mande',
            packageCode: '1-hari'
        );

        $this->assertSame('vc-082288231533-[mande]-[1-hari]', $comment);
    }
}
