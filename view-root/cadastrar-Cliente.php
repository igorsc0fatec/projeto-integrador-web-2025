<?php
require_once '../controller/controller-usuario.php';
require_once '../controller/controller-adm.php';

$usuarioController = new ControllerUsuario();
$admController = new ControllerAdm();
?>



<div class="content-header">
    <h2><i class="fas fa-user-plus"></i> Cadastrar Novo Cliente</h2>
    <p>Preencha os dados abaixo para cadastrar um novo cliente no sistema.</p>
</div>


<div class="form-container">
    <form id="form-novo-adm" method="POST">
        <div class="form-section">
            <h3><i class="fas fa-user"></i> Dados Pessoais</h3>
            <div class="form-group">
                <label for="nome">Nome Completo:</label>
                <input id="nomeCliente" type="text" name="nomeCliente" placeholder="Digite o Nome do Cliente" required>
            </div>
            
            <div class="form-group">
                <label for="cpf">CPF:</label>
                <input id="cpfCliente" type="text" name="cpfCliente" placeholder="Digite o CPF" required>
                
            </div>
            
            <div class="form-group">
            <label for="date">Data de Nascimento</label>
                        <input id="nascCliente" type="date" name="nascCliente"
                            placeholder="Digite sua data de nascimento " required>
                        <span id="erroData" class="error"></span>
            </div>

            <div class="form-group">
                <label for="cpf">CEP:</label>
                <input id="cep" type="text" name="cep" placeholder="Digite o CEP:" required>
                
            </div>

            <div class="form-group">
                <label for="logradouro">Logradouro:</label>
                <input id="logradouro" type="text" name="logradouro" placeholder="Digite o Logradouro:" required>
                
            </div>

            <div class="form-group">
                <label for="numLocal">Nº do Local:</label>
                <input id="numLocal" type="text" name="numLocal" placeholder="Nº do Local:" required>
                
            </div>

            <div class="form-group">
                <label for="bairro">bairro:</label>
                <input id="bairro" type="text" name="bairro" placeholder="Digite o bairro:" required>
                
            </div>

            <div class="form-group">
                <label for="uf">UF:</label>
                <input id="uf" type="text" name="uf" placeholder="Digite o UF:" required>
                
            </div>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-lock"></i> Dados de Acesso</h3>
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input id="emailUsuario" type="email" name="emailUsuario" placeholder="Digite o E-mail" required>
            </div>
            
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input id="senhaUsuario" type="password" name="senhaUsuario" placeholder="Digite a senha" minlength="8"
                    maxlength="15" required>
                <small class="form-text">Mínimo de 8 caracteres</small>
            </div>
            
            <div class="form-group">
                <label for="confirmar-senha">Confirmar Senha:</label>
                <input id="confirmaSenha" type="password" name="confirmaSenha" placeholder="Digite sua senha novamente"
                    minlength="8" maxlength="15" required>
                <small id="senha-error" class="text-danger" style="display:none;">As senhas não coincidem</small>
            </div>
        </div>

        <span id="erroSenha1" class="error"></span>
                <input id="msg" type="hidden" name="msg" value=1>

        
        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="loadContent('visao-geral')">Cancelar</button>
            <button type="submit" class="btn-submit" id="cadastrarCliente" name="cadastrarCliente">Cadastrar</button>
        </div>
    </form>
</div>

<script>
// Validação do formulário
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-adm');
    const btnCadastrar = document.getElementById('btn-cadastrar');
    const messageDiv = document.getElementById('form-message');

    btnCadastrar.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Validar senhas iguais
        const senha = form.querySelector('[name="senhaUsuario"]').value;
        const confirmaSenha = form.querySelector('[name="confirmaSenha"]').value;
        
        if (senha !== confirmaSenha) {
            showMessage('As senhas não coincidem!', 'error');
            return;
        }

        // Mostrar loading
        btnCadastrar.disabled = true;
        btnCadastrar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cadastrando...';

        // Coletar dados do formulário
        const formData = new FormData(form);

        // Enviar via AJAX
        fetch('../controller/controller-adm.php?action=cadastrar', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na rede');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                form.reset();
            } else {
                showMessage(data.message, 'error');
                // Mostrar erros específicos se existirem
                if (data.errors) {
                    for (const [field, error] of Object.entries(data.errors)) {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            markError(input, error);
                        }
                    }
                }
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showMessage('Ocorreu um erro ao processar sua solicitação.', 'error');
        })
        .finally(() => {
            btnCadastrar.disabled = false;
            btnCadastrar.innerHTML = 'Cadastrar';
        });
    });

    // Funções auxiliares
    function showMessage(text, type) {
        messageDiv.innerHTML = `<div class="message-${type}">${text}</div>`;
        messageDiv.style.display = 'block';
    }

    function markError(input, error) {
        input.style.borderColor = '#e74a3b';
        const errorElement = document.createElement('div');
        errorElement.className = 'field-error';
        errorElement.textContent = error;
        input.parentNode.appendChild(errorElement);
        
        // Remover marcação de erro quando o usuário começar a digitar
        input.addEventListener('input', function() {
            this.style.borderColor = '';
            if (errorElement.parentNode) {
                errorElement.parentNode.removeChild(errorElement);
            }
        }, { once: true });
    }
});

// Máscara para CPF (mantenha esta parte)
document.getElementById('cpfCliente').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.length > 3) {
        value = value.substring(0, 3) + '.' + value.substring(3);
    }
    if (value.length > 7) {
        value = value.substring(0, 7) + '.' + value.substring(7);
    }
    if (value.length > 11) {
        value = value.substring(0, 11) + '-' + value.substring(11);
    }
    
    e.target.value = value.substring(0, 14);
});


</script>

<style>
    .content-header {
        margin-bottom: 2rem;
    }

    .content-header h2 {
        color: #4e73df;
        margin-bottom: 0.5rem;
    }

    .content-header p {
        color: #858796;
    }

    .form-container {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }

    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e3e6f0;
    }

    .form-section h3 {
        color: #4e73df;
        margin-bottom: 1.5rem;
        font-size: 1.2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #5a5c69;
        font-weight: 600;
    }

    .form-group input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d3e2;
        border-radius: 4px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .form-group input:focus {
        border-color: #4e73df;
        outline: none;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn-submit, .btn-cancel {
        padding: 0.75rem 1.5rem;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-submit {
        background-color: #4e73df;
        color: white;
        border: none;
    }

    .btn-submit:hover {
        background-color: #2e59d9;
    }

    .btn-cancel {
        background-color: #f8f9fc;
        color: #5a5c69;
        border: 1px solid #d1d3e2;
    }

    .btn-cancel:hover {
        background-color: #eaecf4;
    }

    .alert {
        padding: 1rem;
        margin-bottom: 2rem;
        border-radius: 4px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .text-danger {
        color: #e74a3b;
        font-size: 0.875rem;
    }


</style>
