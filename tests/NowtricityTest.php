<?php

namespace ToolstudIo\Nowtricity\Tests;

use PHPUnit\Framework\TestCase;
use ToolstudIo\Nowtricity\Nowtricity;

class NowtricityTest extends TestCase
{
    /** @test */
    public function can_get_countries()
    {
        $obj = new Nowtricity($this->getApiKey());
        $countries = $obj->countries();
        $this->assertNotEmpty($countries);
        $this->arrayHasKey("belgium", $countries);
    }

    private function getApiKey(): string
    {
        $envFile = __DIR__ . '/../.env';
        if(file_exists($envFile)){
            $env = parse_ini_file($envFile);
            return $env['NOWTRICITY_API_KEY'] ?? '';
        }
        return '';
    }
}
