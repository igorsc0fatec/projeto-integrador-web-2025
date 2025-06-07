$(document).ready(function () {
    // Validação em tempo real da data de nascimento
    $('#nascCliente').on('input', validarData);
});

function validarData() {
    const dataInput = $('#nascCliente').val();
    const erroData = $('#erroData');
    const data = new Date(dataInput);
    const hoje = new Date();

    let idade = hoje.getFullYear() - data.getFullYear();
    const mes = hoje.getMonth() - data.getMonth();

    if (mes < 0 || (mes === 0 && hoje.getDate() < data.getDate())) {
        idade--;
    }

    if (idade < 18) {
        mostrarErro($('#nascCliente'), erroData, 'Idade menor que 18 anos!');
        return false;
    } else if (idade > 100) {
        mostrarErro($('#nascCliente'), erroData, 'Idade maior que 100 anos!');
        return false;
    } else {
        erroData.text('');
        return true;
    }
}

function validaData() {
    if (!validarData()) {
        exibirErroSweetAlert('Erro ao cadastrar usuário!', 'Data de nascimento inválida!');
        return false;
    }

    return true;
}

function mostrarErro(elemento, mensagemElemento, mensagem) {
    elemento.css('border-color', 'red');
    mensagemElemento.text(mensagem).css('color', 'red');
}

function exibirErroSweetAlert(titulo, mensagem) {
    Swal.fire({
        title: titulo,
        text: mensagem,
        icon: 'error',
        confirmButtonText: 'OK'
    });
}