const inputQuant = document.getElementById("qtdProduto");
inputQuant.addEventListener("focusout", function () {
    // Obtém o valor atual do input
    let valor = parseFloat(inputQuant.value);
    // Verifica se o valor é menor que 1
    if (valor < 1 || isNaN(valor)) {
        // Se for, define o valor do input como 1
        inputQuant.value = 1;
    }
});

document.getElementById('valorProduto').addEventListener('input', function (e) {
    formatarMoeda(this);
});

document.getElementById('frete').addEventListener('input', function (e) {
    formatarMoeda(this);
});

function formatarMoeda(campo) {
    let value = campo.value.replace(/\D/g, '');

    if (value === '') {
        campo.value = '';
        return;
    }

    while (value.length < 3) {
        value = '0' + value;
    }

    const valueWithComma = value.slice(0, -2) + ',' + value.slice(-2);
    const formattedValue = valueWithComma.replace(/^0+([1-9])/, '$1');

    campo.value = 'R$ ' + formattedValue;
    campo.setSelectionRange(campo.value.length, campo.value.length);
}

document.querySelector('form').addEventListener('submit', function (e) {
    const campos = [
        { id: 'valorProduto', nome: 'valor do produto' },
        { id: 'frete', nome: 'valor do frete' }
    ];

    for (let campo of campos) {
        const input = document.getElementById(campo.id);
        const numericValue = parseFloat(input.value.replace('R$ ', '').replace(',', '.'));

        if (isNaN(numericValue) || numericValue <= 0) {
            e.preventDefault();
            alert(`Por favor, insira um ${campo.nome} válido maior que zero.`);
            input.focus();
            return;
        }
    }
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

function updateCharCount() {
    const textarea = document.getElementById('descProduto');
    const charCount = document.getElementById('charCount');
    const remaining = 150 - textarea.value.length;
    charCount.textContent = `${remaining} caracteres restantes`;
}

function validaProduto() {
    if (!previewImage()) {
        Swal.fire({
            title: 'Erro ao cadastrar o produto!',
            text: 'Imagem do produto inválida!',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return false
    } else {
        return true
    }
}