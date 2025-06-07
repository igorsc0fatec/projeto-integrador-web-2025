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
    const textarea = document.getElementById('descPersonalizado');
    const charCount = document.getElementById('charCount');
    const remaining = 150 - textarea.value.length;
    charCount.textContent = `${remaining} caracteres restantes`;
}

function validaPersonalizado() {
    if (!previewImage()) {
        alert("IMAGEM inválida!")
        return false
    } else {
        return true
    }
}