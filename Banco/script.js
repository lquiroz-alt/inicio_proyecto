$(document).ready(function() {



	
	$("#boton_depositar").click(function(){
		$(".ocultos1").show(900);
	});

	$("#boton_retiro").click(function(){
		$(".ocultos").show(900);
	});



});

function validar_ejemplo(){
	//HTTP://192.168.23.2/Banco/conexio_tienda.php?cuentau=2222&cuentat=3333&monto=2333&sitio=dfff&desc=Compra,
	$.ajax({
		url: '/Banco/transacciones.php?nombre='+$("#nombre").val()+"&monto="+$("#monto").val(),
		type: 'GET',
		dataType: 'json',
	success: function(valor) {
		console.log(valor);
	}
	
	});

}


function deposito_debito(){
	var numero_cuenta = $("#numero_cuenta").val();
	var cantidad = $("#monto").val();
	var tipo_cuenta = $("input:radio[name=tipo_cuenta]:checked").val();
	$.ajax({
		url: 'deposito.php',
		type: 'POST',
		dataType: 'json',
		data:{numero_cuenta: numero_cuenta, cantidad: cantidad, tipo_cuenta: tipo_cuenta},
	success: function(data) {
			if(data){

				alert("Se realizo con exito el deposito de $"+cantidad+" a la cuenta: "+numero_cuenta);
				
			}else{
				alert("No se pudo realizar el deposito");
			}
			location.reload();
		}
	});
}


function borrar_cuenta(valor){
	var id_cliente = valor;
	$.ajax({
		url: 'borrar_cuenta.php',
		type: 'POST',
		dataType: 'json',
		data:{id_cliente: id_cliente},
	success: function(data) {
			location.reload();
		}
	});
}

function transferencia_credito(){
	var numero_cuenta = $("#numero_cuenta").val();
	var cantidad = $("#monto").val();
	var tipo_cuenta = $("input:radio[name=tipo_cuenta]:checked").val();
	$.ajax({
		url: 'transferencias_credito.php',
		type: 'POST',
		dataType: 'json',
		data:{numero_cuenta: numero_cuenta, cantidad: cantidad, tipo_cuenta: tipo_cuenta},
	success: function(data) {
			if(data){

				alert("Se realizo con exito el deposito de $"+cantidad+" a la cuenta: "+numero_cuenta);
				
			}else{
				alert("No se pudo realizar el deposito");
			}
			location.reload();
		}
	});
}


