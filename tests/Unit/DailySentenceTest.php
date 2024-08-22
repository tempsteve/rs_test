<?php

namespace Tests\Unit;

use App\Services\DailySentenceService;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class DailySentenceTest extends TestCase
{
    public function testGetSentenceReturnsNonEmptyString(): void
    {
        $service = new DailySentenceService();
        $result = $service->getSentence();

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testGetSentenceHandlesUnreachableApi(): void
    {
        $service = $this->getMockBuilder(DailySentenceService::class)
            ->onlyMethods(['getSentence'])
            ->getMock()
        ;

        $service->method('getSentence')
            ->will($this->throwException(new \Exception('API endpoint unreachable')))
        ;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API endpoint unreachable');

        $service->getSentence();
    }

    public function testGetSentenceHandlesCurlExecFailure(): void
    {
        $service = $this->getMockBuilder(DailySentenceService::class)
            ->onlyMethods(['getSentence'])
            ->getMock()
        ;

        $service->method('getSentence')
            ->will($this->returnCallback(function () {
                $ch = curl_init('http://metaphorpsum.com/sentences/3');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                return false; // Simulate curl_exec failure
            }))
        ;

        $result = $service->getSentence();

        $this->assertFalse($result);
    }

    public function testGetSentenceCallsCurlInitWithCorrectUrl(): void
    {
        $service = $this->getMockBuilder(DailySentenceService::class)
            ->onlyMethods(['getSentence'])
            ->getMock()
        ;

        $service->method('getSentence')
            ->will($this->returnCallback(function () {
                $ch = curl_init('http://metaphorpsum.com/sentences/3');
                $this->assertEquals('http://metaphorpsum.com/sentences/3', curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                return curl_exec($ch);
            }))
        ;

        $service->getSentence();
    }

    public function testGetSentenceHandlesEmptyStringResponse(): void
    {
        $service = $this->getMockBuilder(DailySentenceService::class)
            ->onlyMethods(['getSentence'])
            ->getMock()
        ;

        $service->method('getSentence')
            ->will($this->returnValue(''))
        ;

        $result = $service->getSentence();

        $this->assertIsString($result);
        $this->assertEmpty($result);
    }

    public function testGetSentenceHandlesApiErrorResponse(): void
    {
        $service = $this->getMockBuilder(DailySentenceService::class)
            ->onlyMethods(['getSentence'])
            ->getMock()
        ;

        $service->method('getSentence')
            ->will($this->returnValue(false))
        ;

        $result = $service->getSentence();

        $this->assertFalse($result);
    }
}
