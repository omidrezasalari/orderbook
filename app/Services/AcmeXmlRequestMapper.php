<?php

namespace App\Services;

use App\Contracts\RequestMapperInterface;
use App\DTOs\InsuranceRequestData;
use Carbon\Carbon;
use SimpleXMLElement;

class AcmeXmlRequestMapper implements RequestMapperInterface
{
    public function map(InsuranceRequestData $data): string
    {
        $xml = new SimpleXMLElement('<TarificacionThirdPartyRequest/>');
        $datosGenerales = $xml->addChild('DatosGenerales');

        $datosGenerales->addChild('CondPpalEsTomador', $data->holder === 'CONDUCTOR_PRINCIPAL' ? 'YES' : 'NO');
        $datosGenerales->addChild('ConductorUnico', $data->occasionalDriver === 'YES' ? 'NO' : 'YES');
        $datosGenerales->addChild('FecCot', Carbon::now()->toDateString());
        $datosGenerales->addChild('AnosSegAnte', $data->prevInsuranceYears);
        $datosGenerales->addChild('NroCondOca', $data->occasionalDriver === 'YES' ? 1 : 0);
        $datosGenerales->addChild('SeguroEnVigor', $data->prevInsuranceExists);


        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;

        return $dom->saveXML();
    }
}
