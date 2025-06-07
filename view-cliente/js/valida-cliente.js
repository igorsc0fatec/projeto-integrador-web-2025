$(document).ready(function () {
    // Aplica máscaras aos campos
    $('#cpfCliente').mask('000.000.000-00', { reverse: true });
    $('#cep').mask('00000-000');

    // Validação em tempo real do CPF
    $('#cpfCliente').on('input', validarCPF);

    // Validação em tempo real da data de nascimento
    $('#nascCliente').on('input', validarData);

    // Validação em tempo real do CEP
    $('#cep').on('input', validarCEP);

    // Validação em tempo real da senha
    $('#senhaUsuario, #confirmaSenha').on('input', validarSenha);
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

function validarCEP() {
    const cepInput = $('#cep');
    const erroCep1 = $('#erroCep1');
    const cep = cepInput.val().replace(/\D/g, '');

    cepInput.css('border-color', '');
    erroCep1.text('');

    if (cep.length !== 8) {
        mostrarErro(cepInput, erroCep1, 'CEP inválido!');
        limparFormularioCEP();
        return false;
    }

    // Requisição para ViaCEP
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(async data => {
            if (data.erro) {
                mostrarErro(cepInput, erroCep1, 'CEP inválido!');
                limparFormularioCEP();
            } else {
                preencherEndereco(data);

                // Buscar coordenadas pelo Nominatim
                const enderecoCompleto = `${data.logradouro}, ${data.localidade}, ${data.uf}`;
                const geocode = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(enderecoCompleto)}`);
                const resultado = await geocode.json();

                if (resultado.length > 0) {
                    $('#latitude').val(resultado[0].lat);
                    $('#longitude').val(resultado[0].lon);
                } else {
                    $('#latitude, #longitude').val('');
                    erroCep1.text('Coordenadas não encontradas.');
                }
            }
        })
        .catch(() => {
            mostrarErro(cepInput, erroCep1, 'Erro ao buscar CEP.');
            limparFormularioCEP();
        });

    return true;
}

function preencherEndereco(data) {
    $('#logradouro').val(data.logradouro);
    $('#bairro').val(data.bairro);
    $('#cidade').val(data.localidade);
    $('#uf').val(data.uf);
}

function limparFormularioCEP() {
    $('#logradouro, #bairro, #cidade, #uf, #latitude, #longitude').val('');
}

function validarSenha() {
    const senhaInput = $('#senhaUsuario');
    const confirmarSenhaInput = $('#confirmaSenha');
    const erroSenha1 = $('#erroSenha1');

    // Remove estilos de erro anteriores
    senhaInput.css('border-color', '');
    confirmarSenhaInput.css('border-color', '');
    erroSenha1.text('');

    if (senhaInput.val() !== confirmarSenhaInput.val()) {
        mostrarErro(senhaInput, erroSenha1, 'As senhas não correspondem.');
        mostrarErro(confirmarSenhaInput, erroSenha1, '');
        return false;
    }

    return true;
}

function mostrarErro(elemento, mensagemElemento, mensagem) {
    elemento.css('border-color', 'red');
    mensagemElemento.text(mensagem).css('color', 'red');
}

function validaCliente() {
    const log = $('#logradouro').val();

    if (!validarData()) {
        exibirErroSweetAlert('Erro ao cadastrar usuário!', 'Data de nascimento inválida!');
        return false;
    }

    if (!validarCPF()) {
        exibirErroSweetAlert('Erro ao cadastrar usuário!', 'O CPF é inválido!');
        return false;
    }

    if (!validarSenha()) {
        exibirErroSweetAlert('Erro ao cadastrar usuário!', 'As senhas não correspondem!');
        return false;
    }

    if (log === "") {
        exibirErroSweetAlert('Erro ao cadastrar usuário!', 'O CEP é inválido!');
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