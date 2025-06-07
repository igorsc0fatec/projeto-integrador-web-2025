<?php
class Personalizado
{
    private int $idPersonalizado;
    private string $nomePersonalizado;
    private string $descPersonalizado;
    private bool $ativoPersonalizado;
    private bool $cobertura;
    private bool $decoracao;
    private bool $formato;
    private bool $massa;
    private bool $recheio;
    private int $idTipoProduto;
    private int $distanciaMaxima;

    public function getIdPersonalizado(): int
    {
        return $this->idPersonalizado;
    }

    public function setIdPersonalizado(int $idPersonalizado): void
    {
        $this->idPersonalizado = $idPersonalizado;
    }

    public function getNomePersonalizado(): string
    {
        return $this->nomePersonalizado;
    }

    public function setNomePersonalizado(string $nomePersonalizado): void
    {
        $this->nomePersonalizado = $nomePersonalizado;
    }

    public function getDescPersonalizado(): string
    {
        return $this->descPersonalizado;
    }

    public function setDescPersonalizado(string $descPersonalizado): void
    {
        $this->descPersonalizado = $descPersonalizado;
    }
    public function getDistanciaMaxima(): float {
        return $this->distanciaMaxima;
    }

    // Getters
    public function getIdTipoProduto(): int
    {
        return $this->idTipoProduto;
    }

    public function getAtivoPersonalizado(): bool
    {
        return $this->ativoPersonalizado;
    }

    public function getCobertura(): bool
    {
        return $this->cobertura;
    }

    public function getDecoracao(): bool
    {
        return $this->decoracao;
    }

    public function getFormato(): bool
    {
        return $this->formato;
    }

    public function getMassa(): bool
    {
        return $this->massa;
    }

    public function getRecheio(): bool
    {
        return $this->recheio;
    }

    // Setters
    public function setIdTipoProduto(int $idTipoProduto): void
    {
        $this->idTipoProduto = $idTipoProduto;
    }

    public function setAtivoPersonalizado(bool $ativoPersonalizado): void
    {
        $this->ativoPersonalizado = $ativoPersonalizado;
    }

    public function setCobertura(bool $cobertura): void
    {
        $this->cobertura = $cobertura;
    }

    public function setDecoracao(bool $decoracao): void
    {
        $this->decoracao = $decoracao;
    }

    public function setFormato(bool $formato): void
    {
        $this->formato = $formato;
    }

    public function setMassa(bool $massa): void
    {
        $this->massa = $massa;
    }

    public function setRecheio(bool $recheio): void
    {
        $this->recheio = $recheio;
    }

    public function setDistanciaMaxima(float $distanciaMaxima): void {
        $this->distanciaMaxima = $distanciaMaxima;
    }
}
?>