function updateCharCount() {
    const textarea = document.getElementById('descSuporte');
    const charCount = document.getElementById('charCount');
    const remaining = 150 - textarea.value.length;
    charCount.textContent = `${remaining} caracteres restantes`;
}