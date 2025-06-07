function updateCharCount() {
    const textarea = document.getElementById('descricao');
    const charCount = document.getElementById('charCount');
    const remaining = 150 - textarea.value.length;
    charCount.textContent = `${remaining} caracteres restantes`;
}

document.getElementById('ValorGrama').addEventListener('input', function(e) {
    // Remove tudo que não é dígito
    let value = this.value.replace(/\D/g, '');
    
    // Se o campo estiver vazio, define como 0
    if (value === '') {
        this.value = '';
        return;
    }
    
    // Adiciona os zeros necessários para os centavos
    while (value.length < 3) {
        value = '0' + value;
    }
    
    // Insere a vírgula dos centavos
    const valueWithComma = value.slice(0, -2) + ',' + value.slice(-2);
    
    // Remove zeros à esquerda desnecessários
    const formattedValue = valueWithComma.replace(/^0+([1-9])/, '$1');
    
    // Atualiza o valor no campo
    this.value = 'R$ ' + formattedValue;
    
    // Move o cursor para a posição correta
    this.setSelectionRange(this.value.length, this.value.length);
});

// Validação ao submeter o formulário
document.querySelector('form').addEventListener('submit', function(e) {
    const valorGrama = document.getElementById('ValorGrama');
    const numericValue = parseFloat(valorGrama.value.replace('R$ ', '').replace(',', '.'));
    
    if (isNaN(numericValue) || numericValue <= 0) {
        e.preventDefault();
        alert('Por favor, insira um valor válido maior que zero.');
        valorGrama.focus();
    }
});