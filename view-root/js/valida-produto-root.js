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

const inputValor = document.getElementById("valorProduto");
inputValor.addEventListener("focusout", function () {
    let valor = parseFloat(inputValor.value);
    if (valor < 0.01 || isNaN(valor)) {
        inputValor.value = 0.01;
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