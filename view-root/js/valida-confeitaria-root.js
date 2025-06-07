$(document).ready(function () {
    $('#cnpjConfeitaria').mask('00.000.000/0000-00');
});

function validarCNPJ() {
    var cnpj = document.getElementById("cnpjConfeitaria").value;
    var erroCnpj = document.getElementById("erroCnpj");
    cnpj = cnpj.replace(/[^\d]/g, '');// Remove qualquer caractere que não seja número
    if (cnpj.length !== 14) {// Verifica se o CNPJ possui 14 dígitos
        erroCnpj.textContent = "CNPJ inválido!";
        return false;
    }
    var firstDigit = cnpj.charAt(0); // Verifica se todos os dígitos do CNPJ são iguais (caso contrário, não é um CNPJ válido)
    if (cnpj.split('').every(digit => digit === firstDigit)) {
        erroCnpj.textContent = "CNPJ inválido!";
        return false;
    }
    var peso = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];// Calcula o primeiro dígito verificador
    var soma = 0;
    for (var i = 0; i < 12; i++) {
        soma += parseInt(cnpj.charAt(i)) * peso[i];
    }
    var firstCheckDigit = soma % 11 < 2 ? 0 : 11 - (soma % 11);// Verifica se o primeiro dígito verificador está correto
    if (parseInt(cnpj.charAt(12)) !== firstCheckDigit) {
        erroCnpj.textContent = "CNPJ inválido!";
        return false;
    }
    peso.unshift(6);// Calcula o segundo dígito verificador
    soma = 0;
    for (var i = 0; i < 13; i++) {
        soma += parseInt(cnpj.charAt(i)) * peso[i];
    }
    var secondCheckDigit = soma % 11 < 2 ? 0 : 11 - (soma % 11);
    if (parseInt(cnpj.charAt(13)) !== secondCheckDigit) {// Verifica se o segundo dígito verificador está correto
        erroCnpj.textContent = "CNPJ inválido!";
        return false;
    }
    erroCnpj.textContent = "";
    return true;
}

var cnpj = document.getElementById("cnpjConfeitaria");
cnpj.addEventListener("focusout", validarCNPJ);

$(document).ready(function () {
    $('#cep').mask('00000-000');
});

function limpa_formulário_cep() {
    //Limpa valores do formulário de cep.
    document.getElementById('logradouro').value = ("");
    document.getElementById('bairro').value = ("");
    document.getElementById('cidade').value = ("");
    document.getElementById('uf').value = ("");
}

document.getElementById('cep').addEventListener('focusout', function () {
    var cep = document.getElementById('cep').value;
    var erroCep1 = document.getElementById("erroCep1");

    // Requisição para a API dos Correios
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
            if (!data.erro) {
                // Preenche os campos de endereço com os dados da API
                document.getElementById('logradouro').value = data.logradouro;
                document.getElementById('bairro').value = data.bairro;
                document.getElementById('cidade').value = data.localidade;
                document.getElementById('uf').value = data.uf;
                erroCep1.textContent = ""; // Remove a mensagem de erro e a classe de estilo do campo de entrada
            } else {
                erroCep1.textContent = "Cep inválido!"; // Exibe a mensagem de erro e aplica a classe de estilo ao campo de entrada
                limpa_formulário_cep()
            }
        })
        .catch(error => {
            console.error('Erro ao preencher o endereço:', error);
            limpa_formulário_cep()
        });
});

function previewImage() {
    const fileInput = document.getElementById('img');
    const preview = document.getElementById('preview');
    const errorSpan = document.getElementById('erroImagem');
    errorSpan.textContent = '';
    const file = fileInput.files[0];

    if (file) {
        if (file.size > 16 * 1024 * 1024) {
            errorSpan.textContent = 'A imagem deve conter no máximo 16MB';
            fileInput.value = '';
            preview.src = 'assets/img/pessoa.webp';
            return false;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
        return true;

    } else {
        errorSpan.textContent = 'O campo da imagem está vazio!';
        preview.src = 'assets/img/pessoa.webp';
        return false;
    }
}

function validaConfeitaria() {
    var log = document.getElementById("logradouro").value;
    if (!validarCNPJ()) {
        Swal.fire({
            title: 'Erro ao editar usuario!',
            text: 'O CNPJ é invalido!',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return false;
    }else if (log === "") {
        Swal.fire({
            title: 'Erro ao editar dados!',
            text: 'O CEP digitado é invalido!',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return false;
    } else if (!previewImage()) {
        Swal.fire({
            title: 'Erro ao editar dados!',
            text: 'A Imagem é invalida!',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return false;
    } else {
        return true;
    }
}