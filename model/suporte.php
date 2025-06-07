<?php

class Suporte
{
    private int $idSuporte;
    private string $titulo;
    private string $descSuporte;
    private int $idTipoSuporte;
    private int $idUsuario;
    private bool $resolvido;

    public function getIdSuporte()
    {
        return $this->idSuporte;
    }

    public function setIdSuporte($idSuporte)
    {
        $this->idSuporte = $idSuporte;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function getDescSuporte()
    {
        return $this->descSuporte;
    }

    public function setDescSuporte($descSuporte)
    {
        $this->descSuporte = $descSuporte;
    }

    public function getIdTipoSuporte()
    {
        return $this->idTipoSuporte;
    }

    public function setIdTipoSuporte($idTipoSuporte)
    {
        $this->idTipoSuporte = $idTipoSuporte;
    }

    public function getIdUsuario(): int
    {
        return $this->idUsuario;
    }

    public function setIdUsuario(int $idUsuario): void
    {
        $this->idUsuario = $idUsuario;
    }

    public function isResolvido(): bool {
        return $this->resolvido;
    }

    public function setResolvido(bool $resolvido): void {
        $this->resolvido = $resolvido;
    }
}
?>