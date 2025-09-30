<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\Api\DiagnosisController;
use ReflectionClass;

class LanguageDetectionTest extends TestCase
{
    private $controller;
    private $method;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create controller instance with required dependencies
        $this->controller = $this->app->make(DiagnosisController::class);
        
        // Use reflection to access private method
        $reflection = new ReflectionClass($this->controller);
        $this->method = $reflection->getMethod('detectLanguageFromText');
        $this->method->setAccessible(true);
    }

    /** @test */
    public function it_detects_albanian_language()
    {
        $text = 'Motori bën zhurma të çuditshme dhe nuk fillon mirë';
        $symptoms = ['Zhurma të çuditshme', 'Ndalesa e motori'];
        
        $result = $this->method->invoke($this->controller, $text, $symptoms);
        
        $this->assertEquals('sq', $result);
    }

    /** @test */
    public function it_detects_portuguese_language()
    {
        $text = 'O motor está fazendo barulhos estranhos e não funciona bem';
        $symptoms = ['Barulhos estranhos no motor'];
        
        $result = $this->method->invoke($this->controller, $text, $symptoms);
        
        $this->assertEquals('pt', $result);
    }

    /** @test */
    public function it_detects_spanish_language()
    {
        $text = 'El motor hace ruidos extraños y no arranca bien';
        $symptoms = ['Ruidos raros', 'Motor no arranca'];
        
        $result = $this->method->invoke($this->controller, $text, $symptoms);
        
        $this->assertEquals('es', $result);
    }

    /** @test */
    public function it_detects_french_language()
    {
        $text = 'Le moteur fait des bruits bizarres et ne démarre pas bien';
        $symptoms = ['Bruits étranges', 'Problème de démarrage'];
        
        $result = $this->method->invoke($this->controller, $text, $symptoms);
        
        $this->assertEquals('fr', $result);
    }

    /** @test */
    public function it_detects_german_language()
    {
        $text = 'Der Motor macht seltsame Geräusche und startet nicht gut';
        $symptoms = ['Seltsame Geräusche', 'Startprobleme'];
        
        $result = $this->method->invoke($this->controller, $text, $symptoms);
        
        $this->assertEquals('de', $result);
    }

    /** @test */
    public function it_returns_a_language_for_unknown_text()
    {
        $text = 'Some very neutral gibberish zxcvbnm qwerty uiop';
        $symptoms = ['Random gibberish zxcvbnm'];
        
        $result = $this->method->invoke($this->controller, $text, $symptoms);
        
        // Should return one of the supported languages (not null or empty)
        $this->assertContains($result, ['sq', 'en', 'de', 'fr', 'pt', 'es']);
    }

    /** @test */
    public function it_handles_empty_input()
    {
        $text = '';
        $symptoms = [];
        
        $result = $this->method->invoke($this->controller, $text, $symptoms);
        
        $this->assertEquals('en', $result);
    }

    /** @test */
    public function it_differentiates_spanish_from_portuguese()
    {
        // Test with Spanish-specific words
        $spanishText = 'El coche hace ruidos y no arranca';
        $spanishResult = $this->method->invoke($this->controller, $spanishText, []);
        
        // Test with Portuguese-specific words  
        $portugueseText = 'O carro está fazendo barulhos estranhos';
        $portugueseResult = $this->method->invoke($this->controller, $portugueseText, []);
        
        $this->assertEquals('es', $spanishResult);
        $this->assertEquals('pt', $portugueseResult);
    }

    /** @test */
    public function it_considers_symptoms_in_detection()
    {
        $text = 'The car has problems';
        $albanianSymptoms = ['Motori nuk punon', 'Zhurma të forta'];
        
        $result = $this->method->invoke($this->controller, $text, $albanianSymptoms);
        
        $this->assertEquals('sq', $result);
    }
}
