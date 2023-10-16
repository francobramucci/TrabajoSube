<?php

namespace TrabajoSube;

class FranquiciaCompleta extends Tarjeta{
    
    public $cantboletos;

    public function __construct(){
        Tarjeta::__construct();
        $this->cantboletos = 2;
    }
    
    public function renovarBoletos($tiempo){
        $dia = date("d", $tiempo);
        if($this->dia != $dia){
            $this->cantboletos = 2;
            $this->dia = $dia;
        }

    }

    public function hayBoletos() {
        if ($this->cantboletos > 0){
            return true;
        } 
        else{
            echo "No tienes más boletos disponibles. Cobrando boleto normal...\n";
            return false;
        }
    }

}
