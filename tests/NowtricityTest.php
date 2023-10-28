<?php

namespace ToolstudIo\Nowtricity\Tests;

use PHPUnit\Framework\TestCase;
use ToolstudIo\Nowtricity\Exceptions\NotAuthorizedException;
use ToolstudIo\Nowtricity\Nowtricity;

class NowtricityTest extends TestCase
{
    /** @test */
    public function test_can_get_countries()
    {
        $obj = new Nowtricity($this->getApiKey());
        $countries = $obj->countries();
        $this->assertNotEmpty($countries);
        $this->assertArrayHasKey('belgium', $countries);
    }

    public function test_can_get_current(): void
    {
        $obj = new Nowtricity($this->getApiKey());
        $current = $obj->current('belgium');
        $this->assertNotEmpty($current);
        $this->assertArrayHasKey('country', $current);
        $this->assertArrayHasKey('emissions', $current);
    }

    public function test_current_for_invalid_country()
    {
        $obj = new Nowtricity($this->getApiKey());
        $current = $obj->current('xxx');
        $this->assertEmpty($current);

    }

    public function test_current_for_empty_country()
    {
        $obj = new Nowtricity($this->getApiKey());
        $current = $obj->current('');
        $this->assertEmpty($current);

    }

    public function test_exception_for_invalid_api_key()
    {
        $obj = new Nowtricity("xxx");
        $this->expectException(NotAuthorizedException::class);
        $current = $obj->countries();
        $this->assertEmpty($current);
    }

    public function test_can_get_last24(): void
    {
        $obj = new Nowtricity($this->getApiKey());
        $last24 = $obj->last24('belgium');
        $this->assertNotEmpty($last24);
        $this->assertArrayHasKey('country', $last24);
        $this->assertArrayHasKey('emissions', $last24);
    }

    public function test_can_get_year(): void
    {
        $obj = new Nowtricity($this->getApiKey());
        $year = $obj->year('belgium', '2022');
        $this->assertNotEmpty($year);
        $this->assertArrayHasKey('country', $year);
        $this->assertArrayHasKey('year', $year);
        $this->assertArrayHasKey('months', $year);
    }

    private function getApiKey(): string
    {
        $envFile = __DIR__.'/../.env';
        if (file_exists($envFile)) {
            $env = parse_ini_file($envFile);

            return $env['NOWTRICITY_API_KEY'] ?? '';
        }

        return '';
    }
}
