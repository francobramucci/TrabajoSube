<?php
namespace TrabajoSube;

function chequeoCarga($monto){
    
    $cargas = [150, 200, 250, 300, 350, 400, 450, 500, 
           600, 700, 800, 900, 1000, 1100, 1200, 1300, 
           1400, 1500, 2000, 2500, 3000, 3500, 4000];
           
    for ($i=0; $i <= sizeof($cargas) ; $i++) { 
        if($monto == $cargas[$i]) return true;
    }
    return false;
}

class Tarjeta{
    public $saldo;
    public $minsaldo = ~211.84;
    public $maxsaldo = 6600;
    public $id;
    public $excedente;
    public $usos;
    public $dia;
    public $mes;

    public function __construct($id = 0){
        $this->saldo = 0;
        $this->id = $id;
        $this->usos = 0;
        $this->dia = date("d", time());
        $this->mes = date("m", time());
    }

    public function cargarDinero($monto){
        $nuevosaldo = $this->saldo + $monto;
        if(chequeoCarga($monto)){
            if($nuevosaldo <= $this->maxsaldo){
                $this->saldo = $nuevosaldo;
                echo "Saldo actual: " . strval($this->saldo) . "\n";
                return $this->saldo;
            }
            else{
                $this->saldo = $this->maxsaldo;
                $this->excedente = $nuevosaldo - $this->maxsaldo;
                echo "Saldo actual: " . strval($this->saldo) . ". Excedente: ". strval($this->excedente) ."\n";
                return $this->saldo;
            }
        }
        else{
            echo "ERROR. Ingrese un monto válido";
        }
        
    }

    public function actualizarDias($mes){
        if($mes != $this->mes){
            $this->mes = $mes;
            $this->usos = 0;
            return true;
        }
        else{
            return false;
        }
    }

}