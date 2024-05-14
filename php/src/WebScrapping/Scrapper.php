<?php

namespace Chuva\Php\WebScrapping;

/**
 * Scrapper class to extract data from proceedings HTML pages.
 */
class Scrapper {
  /**
   * Extracts data from the DOMDocument loaded with the HTML of the proceedings page.
   *
   * @param \DOMDocument $dom The DOMDocument instance loaded with the HTML.
   * @return array Extracted data as an array of associative arrays.
   */
  public function scrap(\DOMDocument $dom): array {
    $xpath = new \DOMXPath($dom);
    $data = [];
    $paperCards = $xpath->query("//a[contains(@class, 'paper-card')]");
    foreach ($paperCards as $card) {
      $data[] = [
        'id' => trim($xpath->evaluate("string(.//div[contains(@class, 'volume-info')])", $card)),
        'title' => trim($xpath->evaluate("string(.//h4[contains(@class, 'paper-title')])", $card)),
        'type' => trim($xpath->evaluate("string(.//div[contains(@class, 'tags')][1])", $card)),
        'authors' => $this->extractAuthors($xpath, $card),
      ];
    }
    return $data;
  }

  /**
   * Helper function to extract authors and their institutions.
   *
   * @param \DOMXPath $xpath The XPath instance for the document.
   * @param \DOMNode $card The node containing the paper information.
   * @return string Authors and institutions concatenated as a single string.
   */
  private function extractAuthors(\DOMXPath $xpath, \DOMNode $card): string {
    $authors = $xpath->query(".//div[contains(@class, 'authors')]/span", $card);
    $authorTexts = [];
    foreach ($authors as $author) {
      $authorTexts[] = $author->nodeValue . ', ' . $author->getAttribute('title');
    }
    return implode(';', $authorTexts);
  }
}
