$(document).ready(function () {
    // Aplica máscaras aos campos
    $('#cnpjConfeitaria').mask('00.000.000/0000-00');
    $('#cep').mask('00000-000');

    // Validação em tempo real do CNPJ
    $('#cnpjConfeitaria').on('input', validarCNPJ);

    // Validação em tempo real do CEP
    $('#cep').on('input', validarCEP);

    // Validação em tempo real da senha
    $('#senhaUsuario, #confirmaSenha').on('input', validarSenha);

    // Validação em tempo real da imagem
    $('#img').on('change', previewImage);
});

function validarCNPJ() {
    const cnpjInput = $('#cnpjConfeitaria');
    const erroCnpj = $('#erroCnpj');
    const cnpj = cnpjInput.val().replace(/\D/g, ''); // Remove caracteres não numéricos

    // Remove estilos de erro anteriores
    cnpjInput.css('border-color', '');
    erroCnpj.text('');

    if (cnpj.length !== 14 || /^(\d)\1{13}$/.test(cnpj)) {
        mostrarErro(cnpjInput, erroCnpj, 'CNPJ inválido!');
        return false;
    }

    // Validação dos dígitos verificadores
    const pesosPrimeiroDigito = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    const pesosSegundoDigito = [6, ...pesosPrimeiroDigito];

    const primeiroDigito = calcularDigitoVerificador(cnpj.slice(0, 12), pesosPrimeiroDigito);
    const segundoDigito = calcularDigitoVerificador(cnpj.slice(0, 13), pesosSegundoDigito);

    if (cnpj.charAt(12) != primeiroDigito || cnpj.charAt(13) != segundoDigito) {
        mostrarErro(cnpjInput, erroCnpj, 'CNPJ inválido!');
        return false;
    }

    return true;
}

function calcularDigitoVerificador(cnpjParcial, pesos) {
    const soma = cnpjParcial.split('').reduce((acc, digit, index) => acc + (digit * pesos[index]), 0);
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

function previewImage() {
    const fileInput = $('#img');
    const preview = $('#preview');
    const errorSpan = $('#erroImagem');
    const file = fileInput[0].files[0];

    // Remove estilos de erro anteriores
    fileInput.css('border-color', '');
    errorSpan.text('');

    if (!file) {
        mostrarErro(fileInput, errorSpan, 'O campo da imagem está vazio!');
        preview.attr('src', 'assets/img/pessoa.webp');
        return false;
    }

    if (file.size > 16 * 1024 * 1024) {
        mostrarErro(fileInput, errorSpan, 'A imagem deve conter no máximo 16MB');
        fileInput.val('');
        preview.attr('src', 'assets/img/pessoa.webp');
        return false;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        preview.attr('src', e.target.result);
    };
    reader.readAsDataURL(file);

    return true;
}

function mostrarErro(elemento, mensagemElemento, mensagem) {
    elemento.css('border-color', 'red');
    mensagemElemento.text(mensagem).css('color', 'red');
}

function validaConfeitaria() {
    const log = $('#logradouro').val();
    const cepInput = $('#cep');

    if (!validarCNPJ()) {
        exibirErroSweetAlert('Erro ao cadastrar usuário!', 'O CNPJ é inválido!');
        return false;
    }

    if (!validarSenha()) {
        exibirErroSweetAlert('Erro ao cadastrar usuário!', 'As senhas não correspondem!');
        return false;
    }

    if (log === "") {
        mostrarErro(cepInput, $('#erroCep1'), 'O CEP é inválido!');
        exibirErroSweetAlert('Erro ao cadastrar usuário!', 'O CEP é inválido!');
        return false;
    }

    if (!previewImage()) {
        exibirErroSweetAlert('Erro ao cadastrar usuário!', 'A imagem é inválida!');
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