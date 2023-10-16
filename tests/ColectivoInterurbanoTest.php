<?php

namespace TrabajoSube;
    
use PHPUnit\Framework\TestCase;

class ColectivoInterurbanoTest extends TestCase
{
    public function testCostoBoletoInterurbano() {
        $lineasInterurbanas = [
            "Expreso",
            "35/9",
            "M"
        ];

        foreach ($lineasInterurbanas as $linea) {
            $colectivo = new ColectivoInterurbano($linea);
            $this->assertEquals(184, $colectivo->costo);
        }
    }
}

