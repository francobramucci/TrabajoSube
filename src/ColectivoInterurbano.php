<?php

namespace TrabajoSube;

class ColectivoInterurbano extends Colectivo{
    
    public function __construct($linea){
        Colectivo::__construct($linea);
        $this->costo = 184;
    }

}