<?php

namespace Marktic\Newsletter\ConsentStatements\Actions;

class BuildStatementHash
{
    public static function fromText($text): string
    {
        return md5($text);
    }
}