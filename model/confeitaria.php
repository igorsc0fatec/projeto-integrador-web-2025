<?php
include_once 'usuario.php';
class Confeitaria
{
    private int $idConfeitaria;
    private string $nomeConfeitaria;
    private string $cnpjConfeitaria;
    private string $cepConfeitaria;
    private string $logConfeitaria;
    private string $numLocal;
    private string $bairroConfeitaria;
    private string $cidadeConfeitaria;
    private string $ufConfeitaria;
    private float $longitude;
    private float $latitude;

    public function getIdConfeitaria()
    {
        return $this->idConfeitaria;
    }

    public function setIdConfeitaria(int $idConfeitaria)
    {
        $this->idConfeitaria = $idConfeitaria;
    }

    public function getNomeConfeitaria(): string
    {
        return $this->nomeConfeitaria;
    }

    public function setNomeConfeitaria(string $nomeConfeitaria): void
    {
        $this->nomeConfeitaria = $nomeConfeitaria;
    }

    public function getCnpjConfeitaria(): string
    {
        return $this->cnpjConfeitaria;
    }

    public function setCnpjConfeitaria(string $cnpjConfeitaria): void
    {
        $this->cnpjConfeitaria = $cnpjConfeitaria;
    }
    public function setCepConfeitaria(string $cep): void
    {
        $this->cepConfeitaria = $cep;
    }

    public function setLogConfeitaria(string $logradouro): void
    {
        $this->logConfeitaria = $logradouro;
    }

    public function setNumLocal(string $numero): void
    {
        $this->numLocal = $numero;
    }

    public function setBairroConfeitaria(string $bairro): void
    {
        $this->bairroConfeitaria = $bairro;
    }

    public function setCidadeConfeitaria(string $cidade): void
    {
        $this->cidadeConfeitaria = $cidade;
    }

    public function setUfConfeitaria(string $uf): void
    {
        $this->ufConfeitaria = $uf;
    }

    public function getCepConfeitaria(): string
    {
        return $this->cepConfeitaria;
    }

    public function getLogConfeitaria(): string
    {
        return $this->logConfeitaria;
    }

    public function getNumLocal(): string
    {
        return $this->numLocal;
    }

    public function getBairroConfeitaria(): string
    {
        return $this->bairroConfeitaria;
    }

    public function getCidadeConfeitaria(): string
    {
        return $this->cidadeConfeitaria;
    }

    public function getUfConfeitaria(): string
    {
        return $this->ufConfeitaria;
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