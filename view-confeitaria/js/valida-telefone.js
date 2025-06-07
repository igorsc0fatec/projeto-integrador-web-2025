$(document).ready(function() {
    // Aplica a máscara no campo de telefone
    $('#telefone').mask('(00)00000-0000');
});

function validaTelefone() {
    var telefone = document.getElementById("telefone").value.trim();
    var numDigits = telefone.replace(/\D/g, '').length;

    if (numDigits < 10 || numDigits > 11) {
        Swal.fire({
            title: 'Erro ao cadastrar o telefone!',
            text: 'O numero do telefone tem que ter entre 10 e 11 numeros!',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return false;
    } else {
        return true;
    }
}