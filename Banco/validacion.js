function validacion_final(){
	var nombre_usuario = document.getElementById("nombre_usuario").value;
	var contra = document.getElementById("contra").value;
	var email = document.getElementById("email_usuario").value;
	var cantidad_deposito = document.getElementById("cantidad_deposito").value;
	var tipo_cuenta_1 = document.getElementById("sm").value;
	var tipo_cuenta_2 = document.getElementById("sh").value;

	if(nombre_usuario == '' || contra == '' || email == '' || cantidad_deposito == '' || (tipo_cuenta_2 == '' && tipo_cuenta_1 == '')){
		alert("Asegurate de llenar todos los campos");
		return false;
	}else{
		return true;
	}
}

//Funciones para validar que se escribio un mensaje
function validar_nombre_usuario () {
	//Validar si la variable esta vacía
	var nombre_usuario = document.getElementById("nombre_usuario").value;
	var mensaje;
	if(nombre_usuario == "") {
		mensaje = "Escriba un nombre de usuario";
		document.getElementById("mensaje_nombre").style.color = 'rgba(30,119,0,1)';
		return document.getElementById("mensaje_nombre").innerHTML = mensaje;
	}else {
		mensaje="";
		document.getElementById("mensaje_nombre").style.color = 'rgba(30,119,0,1)';
		return document.getElementById("mensaje_nombre").innerHTML = mensaje;
	}
	
}

//Validar lo que se escribe en el campo nombre de usuario
function validar_nombre_usuario_escrito(e) {
	//Validar si la variable tiene caracteres permitidos
	key = e.KeyCode || e.which;
	tecla = String.fromCharCode(key).toLowerCase();
	letras_numeros = "abcdefghijklmnñopqrstuvwxyz1234567890_-/";
	especiales = [8, 37, 39, 46];
	tecla_especial = false;
	var i;
	for(i in especiales){
		if(key == especiales[i]){
			tecla_especial = true;
			break;
		}
	}
	if(letras_numeros.indexOf(tecla) == -1 && !tecla_especial){
		return false;
	}
}

///Limpiar campo en caso quede contenga algun caracter invalido
function limpiar_campo() {
	var texto_nombre_usuario = getElementById("nombre_usuario").value;
	var tamanio_texto = texto_nombre_usuario.length;
	var i;
	for(i=0; i<tamanio_texto; i++){
		if(!isNaN(texto_nombre_usuario[i])){
			return document.getElementById("nombre_usuario").value = '';
		}
	}
}

///Validacion de contraseñas
function contra_validacion() {
	var contra = document.getElementById("contra").value;
	var mensaje;
	if(contra != "" && contra.length >= 5){
		mensaje = "Contraseña valida";
		document.getElementById("mensaje_contra").style.color = 'rgba(30,119,0,1)';
		return document.getElementById("mensaje_contra").innerHTML = mensaje;
		
	}else{
		mensaje = "Escribe una contraseña que sea mayor o igual 6 caracteres";
		document.getElementById("mensaje_contra").style.color = 'rgba(183,0,4,1)';
		return document.getElementById("mensaje_contra").innerHTML = mensaje;
	}
}

function contra2_validacion(){
	var contra2 = document.getElementById("contra2").value;
	var mensaje;
	if(contra2 != "" && contra2.length >= 5){
		mensaje = "Contraseña valida";
		document.getElementById("mensaje_contra2").style.color = 'rgba(30,119,0,1)';
		return document.getElementById("mensaje_contra2").innerHTML = mensaje;
	}else{
		mensaje = "Repita contraseña";
		document.getElementById("mensaje_contra2").style.color = 'rgba(183,0,4,1)';
		return document.getElementById("mensaje_contra2").innerHTML = mensaje;
	}	
}

function contra_iguales(){
	var contra = document.getElementById("contra").value;
	var contra2 = document.getElementById("contra2").value;
	var mensaje;
	if(contra == contra2){
				mensaje = "Contraseña coinciden";
				document.getElementById("mensaje_contra").style.color = 'rgba(30,119,0,1)';
				document.getElementById("mensaje_contra").innerHTML = mensaje;
				document.getElementById("mensaje_contra2").style.color = 'rgba(30,119,0,1)';
				return document.getElementById("mensaje_contra2").innerHTML = mensaje;
		}else{
			mensaje = "Contraseñas no coinciden";
			document.getElementById("mensaje_contra").style.color = 'rgba(183,0,4,1)';
			document.getElementById("mensaje_contra").innerHTML = mensaje;
			document.getElementById("mensaje_contra2").style.color = 'rgba(183,0,4,1)';
			return document.getElementById("mensaje_contra2").innerHTML = mensaje;
		}
}
//Validación correo electrónico
function isValidEmail(mail) { 
  return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(mail); 
}

function validacion_email(){
	var email = document.getElementById("email_usuario").value;
	var mensaje;
	if(email != ""){
		if(isValidEmail(email)){
			mensaje = "Email valido";
			document.getElementById("mensaje_email").style.color = 'rgba(30,119,0,1)';
			return document.getElementById("mensaje_email").innerHTML = mensaje;
		}else{
			mensaje = "Email invalido";
			document.getElementById("mensaje_email").style.color = 'rgba(183,0,4,1)';
			return document.getElementById("mensaje_email").innerHTML = mensaje;
		}
	}else {
		mensaje = "Escribe tu email";
		document.getElementById("mensaje_email").style.color = 'rgba(183,0,4,1)';
		return document.getElementById("mensaje_email").innerHTML = mensaje;
	}
}

// Validación de sexo
marcado_sexo=false;
function validacion_sexo(validar_sexo){
	/*var radio_sexo = document.getElementsByName("sexo");
	var sexo;
	var i;
	var mensaje;*/
	marcado_sexo = validar_sexo;
	if(!marcado_sexo){
		alert("Por favor seleccione un tipo de cliente");
		return false;
	}else{
		return true;
	}
	/*
	for(i=0; i<radio_sexo.length; i++){
		if(radio_sexo[i].checked == true){
			sexo = new String(radio_sexo[i].value);
		}
	}
	if(sexo != ""){

	}else{
		mensaje = "Seleccione una opción";
		return document.getElementById("mensaje_sexo").innerHTML = mensaje;
	}*/
}

///Validación fecha
marcado_fecha = false;
function validacion_fecha (validar_fecha) {
	/*var fecha = document.getElementById("fecha_usuario").value;
	var mensaje;*/
	marcado_fecha = validar_fecha;
	if(!marcado_fecha){
		alert("Por favor seleccione un genero");
		return false;
	}else{
		return true;
	}
	/*if(fecha != ""){

	}else{
		mensaje = "Seleccione su fecha de naciemiento";
		document.getElementById("mensaje_email").style.color = 'rgba(183,0,4,1)';
		return document.getElementById("mensaje_fecha").innerHTML = mensaje;
	}*/
}


//JQUERY Validación de numeros
$('.solo_numeros').keydown(function(e){
	//Permite backspace, delete, tab, escape, enter, and
	if($.inArray(e.keyCode, [46,8,9,27,13,110,190]) == -1 || (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || (e.keyCode >= 35 && e.KeyCode <= 40)){
		return;
	}
	if((e.shiftkey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)){
		e.preventDefault();
	}
});