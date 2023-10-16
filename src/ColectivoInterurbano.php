<?php

namespace TrabajoSube;

class ColectivoInterurbano extends Colectivo{
    
    public function __construct($linea){
        Colectivo::__construct($linea);
        if (strpos($linea, "Expreso") === 0 || strpos($linea, "35/9") === 0 || strpos($linea, "M") === 0) {
            $this->costo = 184;
        }
    }

}