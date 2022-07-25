<?php

namespace Marktic\Newsletter\Base\Actions\Behaviours;

trait HasData
{
    protected $data;

    public static function for(mixed $data = null)
    {
        return (new self())->withData($data);
    }

    public function withData(mixed $data): self
    {
        $this->data = $data;
        return $this;
    }
}