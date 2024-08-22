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

    public function testGetSentenceWithSourceHandlesEmptyStringSource(): void
    {
        $service = new DailySentenceService();
        $result = $service->getSentenceWithSource('');

        $this->assertFalse($result);
    }

    public function testGetSentenceWithSourceCallsCurlInitWithCorrectUrlForItsthisforthat(): void
    {
        $service = $this->getMockBuilder(DailySentenceService::class)
            ->onlyMethods(['getSentenceWithSource'])
            ->getMock()
        ;

        $service->method('getSentenceWithSource')
            ->will($this->returnCallback(function ($source) {
                if ($source === 'itsthisforthat') {
                    $ch = curl_init('https://itsthisforthat.com/api.php?text');
                    $this->assertEquals('https://itsthisforthat.com/api.php?text', curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    return curl_exec($ch);
                }
                return false;
            }))
        ;

        $service->getSentenceWithSource('itsthisforthat');
    }

    public function testGetSentenceWithSourceHandlesNullSource(): void
    {
        $service = new DailySentenceService();
        $result = $service->getSentenceWithSource(null);

        $this->assertFalse($result);
    }

    public function testGetSentenceWithSourceHandlesNumericSource(): void
    {
        $service = new DailySentenceService();
        $result = $service->getSentenceWithSource(123);

        $this->assertFalse($result);
    }

    public function testGetSentenceWithSourceHandlesSpecialCharacterSource(): void
    {
        $service = new DailySentenceService();
        $result = $service->getSentenceWithSource('@#$%');

        $this->assertFalse($result);

    }

    public function testGetSentenceWithSourceHandlesObjectSource(): void
    {
        $service = new DailySentenceService();
        $result = $service->getSentenceWithSource((object) ['source' => 'metaphorpsum']);

        $this->assertFalse($result);
    }

    public function testGetSentenceWithSourceHandlesArraySource(): void
    {
        $service = new DailySentenceService();
        $result = $service->getSentenceWithSource(['source' => 'metaphorpsum']);

        $this->assertFalse($result);
    }

    public function testGetSentenceWithSourceHandlesEmptyStringResponseForItsthisforthat(): void
    {
        $service = $this->getMockBuilder(DailySentenceService::class)
            ->onlyMethods(['getSentenceWithSource'])
            ->getMock()
        ;

        $service->method('getSentenceWithSource')
            ->will($this->returnCallback(function ($source) {
                if ($source === 'itsthisforthat') {
                    $ch = curl_init('https://itsthisforthat.com/api.php?text');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    return ''; // Simulate curl_exec returning an empty string
                }
                return false;
            }))
        ;

        $result = $service->getSentenceWithSource('itsthisforthat');

        $this->assertIsString($result);
        $this->assertEmpty($result);
    }

    public function testGetSentenceWithSourceHandlesCurlExecFailureForItsthisforthat(): void
    {
        $service = $this->getMockBuilder(DailySentenceService::class)
            ->onlyMethods(['getSentenceWithSource'])
            ->getMock()
        ;

        $service->method('getSentenceWithSource')
            ->will($this->returnCallback(function ($source) {
                if ($source === 'itsthisforthat') {
                    $ch = curl_init('https://itsthisforthat.com/api.php?text');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    return false; // Simulate curl_exec failure
                }
                return false;
            }))
        ;

        $result = $service->getSentenceWithSource('itsthisforthat');

        $this->assertFalse($result);
    }

    public function testGetSentenceWithSourceHandlesNonEmptyStringResponseForItsthisforthat(): void
    {
        $service = $this->getMockBuilder(DailySentenceService::class)
            ->onlyMethods(['getSentenceWithSource'])
            ->getMock()
        ;

        $service->method('getSentenceWithSource')
            ->will($this->returnCallback(function ($source) {
                if ($source === 'itsthisforthat') {
                    $ch = curl_init('https://itsthisforthat.com/api.php?text');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    return 'This is a test sentence'; // Simulate curl_exec returning a non-empty string
                }
                return false;
            }))
        ;

        $result = $service->getSentenceWithSource('itsthisforthat');

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertEquals('This is a test sentence', $result);
    }
}
