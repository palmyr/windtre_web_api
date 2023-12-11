<?php

namespace Palmyr\WindtreWebApi\Model;

class DataUsageModel implements \Stringable
{
    private string $description;
    private string $nextResetDate;
    private float $residual;
    private string $residualUnit;

    public function __construct(
        string $description,
        string $nextResetDate,
        float $residual,
        string $residualUnit
    ) {
        $this->description = $description;
        $this->nextResetDate = $nextResetDate;
        $this->residual = $residual;
        $this->residualUnit = $residualUnit;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getNextResetDate(): string
    {
        return $this->nextResetDate;
    }

    public function getResidual(): float
    {
        return $this->residual;
    }

    public function getResidualUnit(): string
    {
        return $this->residualUnit;
    }

    public function __toString(): string
    {
        return $this->description() . " "
            . $this->getNextResetDate() . " "
            . $this->getResidual()
            . $this->getResidualUnit();
    }
}
