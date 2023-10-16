<?php
namespace TrabajoSube;

class Colectivo{
    
    public $linea;
    public $costo = 120;
    public $tiempo;

    public function __construct($linea, $tiempo = null){
        $this->linea = $linea;
        $this->tiempo = ($tiempo !== null) ? $tiempo : new Tiempo();
    }

    public function getLinea(){
        return $this->linea;
    }

    public function chequeoMedio($tarjeta){
        $tarjeta->renovarBoletos($this->tiempo->time());
        
        if($tarjeta->hayBoletos()){
            if($tarjeta->pasoDelTiempo($this->tiempo->time())){ 
                return true;
            }
            else{
                return false;
            } 
        }
        else{
            return false;
        } 
    }

    public function chequeoCompleto($tarjeta){
        $tarjeta->renovarBoletos($this->tiempo->time());
        return ($tarjeta->hayBoletos());
    }

    public function establecerCostoFrec($tarjeta){
        $usos = $tarjeta->usos;
        if($usos <= 29) return $tarjeta->saldo - $this->costo;
        if($usos <= 79) return $tarjeta->saldo - $this->costo * 0.8;
        else return $tarjeta->saldo - $this->costo * 0.75;
    }
    /*
        El argumento $contemplo_beneficio es equivalente a cuando el conductor del colectivo presiona el botón
        para cobrar el boleto teniendo en cuenta el beneficio o la franquicia de la tarjeta. De esta forma si una persona
        que ya uso su medio boleto quiere pagar de vuelta un medio boleto ($contemplo_beneficio = true) se arrojará un error.
        Sin embargo si la persona quiere pagar un boleto normal ($conemtplo_beneficio = false) podrá hacerlo descontándosele 
        el valor completo del boleto.
    */

    public function pagarCon($tarjeta, $contemplo_beneficio = false){
        if($tarjeta instanceof FranquiciaParcial){
            if($contemplo_beneficio){
                if($this->chequeoMedio($tarjeta)){
                    $nuevosaldo = $tarjeta->saldo - $this->costo/2;
                    if($nuevosaldo >= $tarjeta->minsaldo){
                        $tarjeta->ultimopago = $this->tiempo->time();
                        $tarjeta->cantboletos--;
                    }
                }
                else{
                    return false;
                } 
            }
            else{
                $nuevosaldo = $tarjeta->saldo - $this->costo;
            }
        }
        /*--------------------------------------------------------------------------------*/
        else if($tarjeta instanceof FranquiciaCompleta){
            if($this->chequeoCompleto($tarjeta)){
                $nuevosaldo = $tarjeta->saldo;
                $tarjeta->cantboletos--;
            }
            else{
                $nuevosaldo = $tarjeta->saldo - $this->costo;
            }
        }
        /*--------------------------------------------------------------------------------*/

        else{
            $mes = date("m", $this->tiempo->time());
            
            if($tarjeta->actualizarDias($mes)){
                $nuevosaldo = $tarjeta->saldo - $this->costo;
            }
            else{
                $nuevosaldo = $this->establecerCostoFrec($tarjeta);
                $hoy = date("d",$this->tiempo->time());
                if(1 <= $hoy && $hoy <= 30){
                    if ($nuevosaldo >= $tarjeta->minsaldo){
                        $tarjeta->usos++;
                    }
                }
            }
        }
        /*--------------------------------------------------------------------------------*/
        
        if ($nuevosaldo >= $tarjeta->minsaldo){
            $diff = $tarjeta->saldo - $nuevosaldo; 
            if($tarjeta->excedente > 0){
                $nuevosaldo = min($tarjeta->maxsaldo, $tarjeta->excedente + $nuevosaldo);
                $tarjeta->excedente = max($tarjeta->excedente - $diff, 0);
                
            }
            $tarjeta->saldo = $nuevosaldo;
            return new Boleto($this->costo, $tarjeta->saldo, $this->linea, $tarjeta->id, time(), get_class($tarjeta));
        }
        else{
            echo "Saldo insuficiente\n"; 
            return false;
        }
    }

}
