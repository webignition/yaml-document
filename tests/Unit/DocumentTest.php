<?php

declare(strict_types=1);

namespace webignition\YamlDocument\Tests\Unit;

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

    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(Document $document, mixed $expectedParsedDocument): void
    {
        self::assertSame($expectedParsedDocument, $document->parse());
    }

    /**
     * @return array<mixed>
     */
    public function parseDataProvider(): array
    {
        return [
            'empty' => [
                'document' => new Document(),
                'expectedParsedContent' => null,
            ],
            'string' => [
                'document' => new Document('string content'),
                'expectedParsedContent' => 'string content',
            ],
            'int' => [
                'document' => new Document('1'),
                'expectedParsedContent' => 1,
            ],
            'simple array' => [
                'document' => new Document(
                    '- one' . "\n" .
                    '- two' . "\n" .
                    '- three'
                ),
                'expectedParsedContent' => [
                    'one',
                    'two',
                    'three'
                ],
            ],
        ];
    }

    /**
     * @dataProvider isDocumentStartDataProvider
     */
    public function testIsDocumentStart(string $line, bool $expectedIsDocumentStart): void
    {
        self::assertSame($expectedIsDocumentStart, Document::isDocumentStart($line));
    }

    /**
     * @return array<mixed>
     */
    public function isDocumentStartDataProvider(): array
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

    /**
     * @dataProvider isDocumentEndDataProvider
     */
    public function testIsDocumentEnd(string $line, bool $expectedIsDocumentEnd): void
    {
        self::assertSame($expectedIsDocumentEnd, Document::isDocumentEnd($line));
    }

    /**
     * @return array<mixed>
     */
    public function isDocumentEndDataProvider(): array
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
