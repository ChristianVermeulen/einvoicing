<?php
namespace Tests\Integration;

use DOMDocument;
use Einvoicing\Readers\UblReader;
use Einvoicing\Writers\UblWriter;
use PHPUnit\Framework\TestCase;
use const LIBXML_NOERROR;
use function file_get_contents;

final class IntegrationTest extends TestCase {
    /** @var UblReader */
    private $reader;

    /** @var UblWriter */
    private $writer;

    protected function setUp(): void {
        $this->reader = new UblReader();
        $this->writer = new UblWriter();
    }

    protected function normalize(string $xml): string {
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->loadXML($xml, LIBXML_NOERROR);
        return $doc->C14N();
    }

    protected function importAndExportInvoice(string $xmlPath): void {
        $inputXml = file_get_contents($xmlPath);
        $invoice = $this->reader->import($inputXml);
        $outputXml = $this->writer->export($invoice);

        $this->assertEquals(
            $this->normalize($inputXml),
            $this->normalize($outputXml)
        );
    }

    public function testCanRecreatePeppolBaseExample(): void {
        $this->importAndExportInvoice(__DIR__ . "/peppol-base.xml");
    }

    public function testCanRecreatePeppolVatExample(): void {
        $this->importAndExportInvoice(__DIR__ . "/peppol-vat-s.xml");
    }

    public function testCanRecreatePeppolAllowanceExample(): void {
        $this->importAndExportInvoice(__DIR__ . "/peppol-allowance.xml");
    }
}
