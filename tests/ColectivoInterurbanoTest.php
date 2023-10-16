<?php

namespace TrabajoSube;
    
use PHPUnit\Framework\TestCase;

class ColectivoInterurbanoTest extends TestCase
{
    public function testCosto()
    {
        $colectivo = new ColectivoInterurbano('Linea 1');
        $this->assertEquals(184, $colectivo->getCosto());
    }

    public function testLinea()
    {
        $colectivo = new ColectivoInterurbano('Linea 1');
        $this->assertEquals('Linea 1', $colectivo->getLinea());
    }
}
