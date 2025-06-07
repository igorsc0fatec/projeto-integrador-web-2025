function confirmarDesativarConta() {
    Swal.fire({
        title: 'Tem certeza que deseja desativar a conta?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Não',
        cancelButtonText: 'Sim'
    }).then((result) => {
        if (!result.isConfirmed) {
            window.location.href = "../view/desativar-conta.php";
        }
    })

}

function voltar() {
    window.history.back();
}