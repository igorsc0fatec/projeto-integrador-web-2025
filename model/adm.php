<?php

class Adm
{
    private int $idAdm;
    private string $nomeAdm;
    private string $funcaoAdm;
    private string $cpfAdm;

    public function getIdAdm(): int
    {
        return $this->idAdm;
    }

    public function getNomeAdm(): string
    {
        return $this->nomeAdm;
    }

    public function getFuncaoAdm(): string
    {
        return $this->funcaoAdm;
    }

    public function getCpfAdm(): string
    {
        return $this->cpfAdm;
    }

    // Setters
    public function setIdAdm(int $idAdm): void
    {
        $this->idAdm = $idAdm;
    }

    public function setNomeAdm(string $nomeAdm): void
    {
        $this->nomeAdm = $nomeAdm;
    }

    public function setFuncaoAdm(string $funcaoAdm): void
    {
        $this->funcaoAdm = $funcaoAdm;
    }

    public function setCpfAdm(string $cpfAdm): void
    {
        $this->cpfAdm = $cpfAdm;
    }
}
