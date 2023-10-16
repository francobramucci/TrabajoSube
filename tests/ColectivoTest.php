<?php 

namespace TrabajoSube;

use PHPUnit\Framework\TestCase;

class ColectivoTest extends TestCase{

    public function testConstruct(){
        $cole = new Colectivo(103, new Tiempo());
        $this->assertEquals($cole->linea, 103);
    }
    public function testGetlinea(){
        $cole = new Colectivo(103, new Tiempo());
        $this->assertEquals($cole->getLinea(), 103);
    }

    public function testPagarCon(){
        $tarjeta = new Tarjeta();
        $cole = new Colectivo(102, new Tiempo());
        $this->assertInstanceOf(Boleto::class, $cole->pagarCon($tarjeta));   
    }

    public function testPagarConMedio(){
        $tarjeta = new FranquiciaParcial();
        $tiempo = new TiempoFalso();
        $cole = new Colectivo(102, $tiempo);
        
        $saldoini = 1000;
        $tarjeta->cargarDinero($saldoini);
        
        $cole->tiempo->avanzar(7*60*60);

        //Primer pago (aceptado)
        $cole->pagarCon($tarjeta, true);
        $this->assertEquals($tarjeta->saldo, $saldoini - $cole->costo/2);
        
        //Segundo pago (rechazado) - No pasaron los 5 minutos
        $cole->tiempo->avanzar(50);
        $cole->pagarCon($tarjeta, true);
        $this->assertEquals($tarjeta->saldo, $saldoini - $cole->costo/2);
        
        //Tercer pago (aceptado)
        $cole->tiempo->avanzar(300);
        $cole->pagarCon($tarjeta, true);
        $this->assertEquals($tarjeta->saldo, $saldoini - $cole->costo/2*2);
        
        //Cuarto pago (aceptado)
        $cole->tiempo->avanzar(300);
        $cole->pagarCon($tarjeta, true);
        $this->assertEquals($tarjeta->saldo, $saldoini - $cole->costo/2*3);
        
        //Quinto pago (aceptado)
        $cole->tiempo->avanzar(300);
        $cole->pagarCon($tarjeta, true);
        $this->assertEquals($tarjeta->saldo, $saldoini - $cole->costo/2*4);
        
        //Sexto pago (rechazado) - No hay más boletos
        $cole->tiempo->avanzar(300);
        $cole->pagarCon($tarjeta, true);
        $this->assertEquals($tarjeta->saldo, $saldoini - $cole->costo/2*4);
    }

    public function testPagarConCompleto(){
        $tarjeta = new FranquiciaCompleta();
        $cole = new Colectivo(102, new TiempoFalso());

        $saldoini = 1000;
        $tarjeta->cargarDinero($saldoini);

        $cole->tiempo->avanzar(7*60*60);

        $cole->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->saldo, $saldoini);
    
        $cole->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->saldo, $saldoini);

        $cole->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->saldo, $saldoini - $cole->costo);
        
        //Pasan 24 horas y se renuevan la cantidad de boletos
        $cole->tiempo->avanzar(60*60*24);

        $cole->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->saldo, $saldoini - $cole->costo);
    }

    public function testPagarExcedente(){
        $tarjeta = new Tarjeta();
        $cole = new Colectivo(102);

        $tarjeta->cargarDinero(4000);
        $tarjeta->cargarDinero(3000);

        //Testeo para un caso donde el excedente supere el costo de un boleto
        $cole->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->saldo, 6600);
        $this->assertEquals($tarjeta->excedente, 280);
        echo $tarjeta->excedente;

        //Testeo para un caso donde el excedente no supere el costo de un boleto
        $cole->pagarCon($tarjeta);
        $cole->pagarCon($tarjeta);
        $cole->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->saldo, 6520);
        $this->assertEquals($tarjeta->excedente, 0);
    }


    public function testUsoFrecuente(){
        $tarjeta = new Tarjeta();
        $cole = new Colectivo(102, new TiempoFalso());

        $tarjeta->cargarDinero(1000);
        
        //Pagando sin ningún uso
        $cole->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->saldo, 880);
        
        //Pagando con 60 usos
        $tarjeta->usos = 60;
        $cole->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->saldo, 880-($cole->costo)*0.8);
        
        $tarjeta->usos = 90;
        $cole->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->saldo, 784-($cole->costo)*0.75);

    }

    public function testPagarConHorario(){
        $tarjeta = new FranquiciaParcial();
        $cole = new Colectivo(102, new TiempoFalso());
        
        $tarjeta->cargarDinero(1000);

        //Pago con franquicia sin estar en la franja horaria permitida
        $cole->pagarCon($tarjeta, true);
        $this->assertEquals($tarjeta->saldo, 1000 - $cole->costo);
        
        //Pago con franquicia estando en el horario permitido
        $cole->tiempo->avanzar(60*60*7);
        $cole->pagarCon($tarjeta, true);
        $this->assertEquals($tarjeta->saldo, 880 - 60);
    }

}