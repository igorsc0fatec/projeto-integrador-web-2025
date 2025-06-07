$(document).ready(function () {
    $('#cep').mask('00000-000');
});

function limpa_formulário_cep() {
    document.getElementById('logradouro').value = "";
    document.getElementById('bairro').value = "";
    document.getElementById('cidade').value = "";
    document.getElementById('uf').value = "";
    document.getElementById('latitude').value = "";
    document.getElementById('longitude').value = "";
}

document.getElementById('cep').addEventListener('focusout', function () {
    const cep = document.getElementById('cep').value.replace('-', '');
    const erroCep1 = document.getElementById("erroCep1");

    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(async data => {
            if (!data.erro) {
                // Preenche os campos com dados do ViaCEP
                document.getElementById('logradouro').value = data.logradouro;
                document.getElementById('bairro').value = data.bairro;
                document.getElementById('cidade').value = data.localidade;
                document.getElementById('uf').value = data.uf;

                // Monta endereço completo para buscar coordenadas
                const enderecoCompleto = `${data.logradouro}, ${data.localidade}, ${data.uf}`;
                const geocode = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(enderecoCompleto)}`);
                const resultado = await geocode.json();

                if (resultado.length > 0) {
                    document.getElementById('latitude').value = resultado[0].lat;
                    document.getElementById('longitude').value = resultado[0].lon;
                } else {
                    document.getElementById('latitude').value = "";
                    document.getElementById('longitude').value = "";
                    erroCep1.textContent = "Coordenadas não encontradas.";
                }

                erroCep1.textContent = ""; // remove erros visuais
            } else {
                erroCep1.textContent = "CEP inválido!";
                limpa_formulário_cep();
            }
        })
        .catch(error => {
            console.error('Erro ao preencher o endereço:', error);
            limpa_formulário_cep();
        });
});

function validaEndereco() {
    var log = document.getElementById("logradouro").value;
    if (log === "") {
        Swal.fire({
            title: 'Erro ao cadastrar endereço!',
            text: 'O CEP é invalido!',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return false;
    } else {
        return true;
    }
}