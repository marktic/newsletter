<?php

namespace Marktic\Newsletter\Base\Actions\Behaviours;

trait HasData
{
    protected $data;

    public static function for(mixed $contact)
    {
        return (new self())->withData($contact);
    }

    public function withData(mixed $data): self
    {
        $this->data = $data;
        return $this;
    }
}