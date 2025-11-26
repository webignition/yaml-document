<?php

declare(strict_types=1);

namespace webignition\YamlDocument\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use webignition\YamlDocument\Document;

class DocumentTest extends TestCase
{
    public function testCreate(): void
    {
        $document = new Document();
        self::assertSame('', $document->getContent());
        self::assertTrue($document->isEmpty());

        $content = 'content';
        $document = new Document($content);
        self::assertSame($content, $document->getContent());
        self::assertFalse($document->isEmpty());
    }

    public function testAppend(): void
    {
        $document = new Document();
        self::assertSame('', $document->getContent());
        self::assertTrue($document->isEmpty());

        $content = 'content';
        $appendedDocument = $document->append($content);
        self::assertNotSame($appendedDocument, $document);
        self::assertSame($content, $appendedDocument->getContent());
        self::assertFalse($appendedDocument->isEmpty());
    }

    #[DataProvider('parseDataProvider')]
    public function testParse(Document $document, mixed $expectedParsedDocument): void
    {
        self::assertSame($expectedParsedDocument, $document->parse());
    }

    /**
     * @return array<mixed>
     */
    public static function parseDataProvider(): array
    {
        return [
            'empty' => [
                'document' => new Document(),
                'expectedParsedDocument' => null,
            ],
            'string' => [
                'document' => new Document('string content'),
                'expectedParsedDocument' => 'string content',
            ],
            'int' => [
                'document' => new Document('1'),
                'expectedParsedDocument' => 1,
            ],
            'simple array' => [
                'document' => new Document(
                    '- one' . "\n"
                    . '- two' . "\n"
                    . '- three'
                ),
                'expectedParsedDocument' => [
                    'one',
                    'two',
                    'three',
                ],
            ],
        ];
    }

    #[DataProvider('isDocumentStartDataProvider')]
    public function testIsDocumentStart(string $line, bool $expectedIsDocumentStart): void
    {
        self::assertSame($expectedIsDocumentStart, Document::isDocumentStart($line));
    }

    /**
     * @return array<mixed>
     */
    public static function isDocumentStartDataProvider(): array
    {
        return [
            'empty' => [
                'line' => '',
                'expectedIsDocumentStart' => false,
            ],
            'string content' => [
                'line' => 'content',
                'expectedIsDocumentStart' => false,
            ],
            'commented-out start' => [
                'line' => '#---',
                'expectedIsDocumentStart' => false,
            ],
            'start' => [
                'line' => '---',
                'expectedIsDocumentStart' => true,
            ],
            'start with trailing whitespace' => [
                'line' => '--- ',
                'expectedIsDocumentStart' => true,
            ],
        ];
    }

    #[DataProvider('isDocumentEndDataProvider')]
    public function testIsDocumentEnd(string $line, bool $expectedIsDocumentEnd): void
    {
        self::assertSame($expectedIsDocumentEnd, Document::isDocumentEnd($line));
    }

    /**
     * @return array<mixed>
     */
    public static function isDocumentEndDataProvider(): array
    {
        return [
            'empty' => [
                'line' => '',
                'expectedIsDocumentEnd' => false,
            ],
            'string content' => [
                'line' => 'content',
                'expectedIsDocumentEnd' => false,
            ],
            'commented-out end' => [
                'line' => '#...',
                'expectedIsDocumentEnd' => false,
            ],
            'ebd' => [
                'line' => '...',
                'expectedIsDocumentEnd' => true,
            ],
            'end with trailing whitespace' => [
                'line' => '... ',
                'expectedIsDocumentEnd' => true,
            ],
        ];
    }
}
