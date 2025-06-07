function validarSenha() {
    var senhaInput = document.getElementById("senhaUsuario").value;
    var confirmarSenhaInput = document.getElementById("confirmaSenha").value;
    var erroSenha1 = document.getElementById("erroSenha1");

    if (senhaInput !== confirmarSenhaInput) {
        erroSenha1.textContent = "As senhas não correspondem.";
        return false;
    } else {
        erroSenha1.textContent = "";
        return true;
    }
}

var confirmarSenhaInput = document.getElementById("confirmaSenha");
confirmarSenhaInput.addEventListener("focusout", validarSenha);

function validaEditUsuario() {
    if (!validarSenha()) {
        Swal.fire({
            title: 'Erro ao editar dados!',
            text: 'As senhas são diferentes!',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return false;
    } else {
        return true;
    }
}