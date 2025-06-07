$(document).ready(function () {
    // Aplica máscaras aos campos
    $('#cpfCliente').mask('000.000.000-00', { reverse: true });

    // Validação em tempo real do CPF
    $('#cpfCliente').on('input', validarCPF);

    // Validação em tempo real da senha
    $('#senhaUsuario, #confirmaSenha').on('input', validarSenha);
});

function validarCPF() {
    const cpfInput = $('#cpfCliente');
    const erroCpf = $('#erroCpf');
    const cpf = cpfInput.val().replace(/[^\d]/g, '');

    // Remove estilos de erro anteriores
    cpfInput.css('border-color', '');
    erroCpf.text('');

    if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
        mostrarErro(cpfInput, erroCpf, 'CPF inválido!');
        return false;
    }

    // Validação dos dígitos verificadores
    const pesosPrimeiroDigito = [10, 9, 8, 7, 6, 5, 4, 3, 2];
    const pesosSegundoDigito = [11, 10, 9, 8, 7, 6, 5, 4, 3, 2];

    const primeiroDigito = calcularDigitoVerificador(cpf.slice(0, 9), pesosPrimeiroDigito);
    const segundoDigito = calcularDigitoVerificador(cpf.slice(0, 10), pesosSegundoDigito);

    if (cpf.charAt(9) != primeiroDigito || cpf.charAt(10) != segundoDigito) {
        mostrarErro(cpfInput, erroCpf, 'CPF inválido!');
        return false;
    }

    return true;
}

function calcularDigitoVerificador(cpfParcial, pesos) {
    const soma = cpfParcial.split('').reduce((acc, digit, index) => acc + (digit * pesos[index]), 0);
    const resto = soma % 11;
    return resto < 2 ? 0 : 11 - resto;
}

function mostrarErro(elemento, mensagemElemento, mensagem) {
    elemento.css('border-color', 'red');
    mensagemElemento.text(mensagem).css('color', 'red');
}

function validaAdm() {

    if (!validarCPF()) {
        exibirErroSweetAlert('Erro ao cadastrar usuário!', 'O CPF é inválido!');
        return false;
    }

    if (!validarSenha()) {
        exibirErroSweetAlert('Erro ao cadastrar usuário!', 'As senhas não correspondem!');
        return false;
    }

    return true;
}

function exibirErroSweetAlert(titulo, mensagem) {
    Swal.fire({
        title: titulo,
        text: mensagem,
        icon: 'error',
        confirmButtonText: 'OK'
    });
}