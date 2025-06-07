<?php

class Endereco
{
    private int $id;
    private string $cep;
    private string $logradouro;
    private string $numLocal;
    private string $bairro;
    private string $cidade;
    private string $uf;
    private float $longitude;
    private float $latitude;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getCep(): string
    {
        return $this->cep;
    }

    public function setCep(string $cep): void
    {
        $this->cep = $cep;
    }

    public function getLogradouro(): string
    {
        return $this->logradouro;
    }

    public function setLogradouro(string $logradouro): void
    {
        $this->logradouro = $logradouro;
    }

    public function getNumLocal(): string
    {
        return $this->numLocal;
    }

    public function setNumLocal(string $numLocal): void
    {
        $this->numLocal = $numLocal;
    }

    public function getBairro(): string
    {
        return $this->bairro;
    }

    public function setBairro(string $bairro): void
    {
        $this->bairro = $bairro;
    }

    public function setCidade(string $cidade): void
    {
        $this->cidade = $cidade;
    }

    public function getCidade(): string
    {
        return $this->cidade;
    }

    public function setUf(string $uf): void
    {
        $this->uf = $uf;
    }

    public function getUf(): string
    {
        return $this->uf;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    // Setter para longitude
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    // Getter para latitude
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    // Setter para latitude
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }
}

?>