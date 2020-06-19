$(document).ready(function() {
  // Handler for .ready() called.

 	jQuery(function($){
		$('.footable').footable();
	});


});


function apertura_credito(id_cuenta){
	var cuenta = id_cuenta;
	var numero_cuenta = $("#numero_cuenta").val();
	var monto = $("#monto").val();

	$.ajax({
		url: 'creditos.php',
		type: 'POST',
		dataType: 'json',
		data:{numero_cuenta: cuenta},
	success: function(data) {
			if(data.verificar){
				alert("Se realizo con exito cuenta de credito: "+data.cuenta+" con la cantidad de:"+data.cantidad);
				location.reload();
			}else{
				alert("No fue posible abrir el crédito");
			}

		}
	});
}

function filtrar(){
	var tipo_cuenta = $("input:radio[name=tipo_cuenta]:checked").val();
	$.ajax({
		url: 'filtros.php',
		type: 'POST',
		dataType: 'json',
		data:{tipo_cuenta: tipo_cuenta},
	success: function(data) {
			location.reload();	
		}
	});
}

function filtrar_admin(){
	var tipo_cuenta = $("input:radio[name=tipo_cuenta]:checked").val();
	$.ajax({
		url: 'filtros_admin.php',
		type: 'POST',
		dataType: 'json',
		data:{tipo_cuenta: tipo_cuenta},
	success: function(data) {
			location.reload();	
		}
	});

}

function borrar_registro(id_historial){
	var historial = id_historial;
	$.ajax({
		url: 'borrar_registro.php',
		type: 'POST',
		dataType: 'json',
		data:{historial: historial},
	success: function(data) {
			location.reload();	
		}
	});
}