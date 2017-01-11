
function validacion() {
    nombre = document.getElementById("nombre").value;
    if(!/^[ña-zA-Z]+$/.test(nombre)) {
        document.getElementById("infoNombre").innerHTML = "<div class='arrow_box'><label style='display: inline;' class='error' for='nombre'> Solo puede contener letras </label></div>";
        return false;
    }

   /*if( !validarNombre(nombre) ) {
        document.getElementById("infoNombre").innerHTML = "<div class='arrow_box'><label style='display: inline;' class='error' for='nombre'> Lo sentimos ese nombre ya esta cogido </label></div>";
        return false;
    }*/

    apellido = document.getElementById("apellido1").value;
    if(!/^[ña-zA-Z]+$/.test(apellido) )  {
        document.getElementById("infoApellido").innerHTML = "<div class='arrow_box'><label style='display: inline;' class='error' for='nombre'> Solo puede contener letras </label></div>";
        return false;
    }

    mail = document.getElementById("email").value;
    if((/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)/.test(mail))) {
        document.getElementById("infoEmail").innerHTML = "<div class='arrow_box'><label style='display: inline;' class='error' for='nombre'> El mail debe ser de la forma: ejemplo1@ejemplo2.dominio </label></div>";
        return false;
    }

    cuenta = document.getElementById("numTarjeta").value;
    if(!/^[0-9]{16}$/.test(cuenta)) {
        document.getElementById("infoCuenta").innerHTML = "<div class='arrow_box'><label style='display: inline;' class='error' for='cuenta'> tarjeta de crédito incorrecta  </label></div>";
        return false;
    }

    pass= document.getElementById("password").value;
    if(pass.length < 8) {
        document.getElementById("infoContrasena").innerHTML = "<div class='arrow_box'><label style='display: inline;' class='error' for='password'> El password debe tener al menos 8 caracteres </label></div>";
        return false;
    }

    cpass= document.getElementById("confirm_password").value;
    if(cpass.localeCompare(pass)) {
        document.getElementById("infoRepetirContrasena").innerHTML = "<div class='arrow_box'><label style='display: inline;' class='error' for='confirm_password'> Las contrasenas no coinciden </label></div>";
        return false;
    }
    return true;
}

function validarNombre(str) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200) {
            if(this.responseText.length!=0) {
                document.getElementById("infoNombre").innerHTML = this.responseText;
                return false;
            }
            return true;
        }
    };
    var dir = "./respuestaNombreResgistro.php?q="+str;
    xhttp.open("GET", dir, true);
    xhttp.send();
}
