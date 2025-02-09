<?php

namespace Tests\Unit;

use App\DTOs\InsuranceRequestData;
use App\Contracts\RequestMapperInterface;
use Tests\TestCase;
use App\Contracts\XmlRequestMapperInterface;
use Mockery;

class AcmeXmlRequestMapperTest extends TestCase
{
    public function test_map_generates_valid_xml()
    {
        $mockMapper = Mockery::mock(RequestMapperInterface::class);
        $mockMapper
            ->shouldReceive('map')
            ->once()
            ->andReturn('<TarificacionThirdPartyRequest><DatosGenerales/></TarificacionThirdPartyRequest>');

        $input = new InsuranceRequestData('CONDUCTOR_PRINCIPAL', 'NO', 5);
        $xmlString = $mockMapper->map($input);

        $this->assertStringContainsString('<TarificacionThirdPartyRequest>', $xmlString);
    }
}
