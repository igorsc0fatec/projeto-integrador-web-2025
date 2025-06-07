<?php
class Usuario
{
    private int $idUsuario;
    private string $emailUsuario;
    private bool $emailVerificado;
    private bool $contaAtiva;
    private string $senhaUsuario;
    private string $online;
    private string $dataCriacao;
    private int $idTipoUsuario;

    public function getIdUsuario(): int
    {
        return $this->idUsuario;
    }

    public function setIdUsuario(int $idUsuario): void
    {
        $this->idUsuario = $idUsuario;
    }

    public function getIdTipoUsuario(): int
    {
        return $this->idTipoUsuario;
    }

    public function setIdTipoUsuario(int $idTipoUsuario): void
    {
        $this->idTipoUsuario = $idTipoUsuario;
    }
    public function getEmailUsuario(): string
    {
        return $this->emailUsuario;
    }

    public function setEmailUsuario(string $emailUsuario): void
    {
        $this->emailUsuario = $emailUsuario;
    }

    public function isEmailVerificado(): bool
    {
        return $this->emailVerificado;
    }

    public function setEmailVerificado(bool $emailVerificado): void
    {
        $this->emailVerificado = $emailVerificado;
    }

    public function isContaAtiva(): bool
    {
        return $this->contaAtiva;
    }

    public function setContaAtiva(bool $contaAtiva): void
    {
        $this->contaAtiva = $contaAtiva;
    }

    public function getSenhaUsuario(): string
    {
        return $this->senhaUsuario;
    }

    public function setSenhaUsuario(string $senhaUsuario): void
    {
        $this->senhaUsuario = $senhaUsuario;
    }

    public function getDataCriacao(): string
    {
        return $this->dataCriacao;
    }

    public function setDataCriacao(string $dataCriacao): void
    {
        $this->dataCriacao = $dataCriacao;
    }

    public function getOnline(): string
    {
        return $this->online;
    }

    public function setOnline(string $online): void
    {
        $this->online = $online;
    }
}

?>