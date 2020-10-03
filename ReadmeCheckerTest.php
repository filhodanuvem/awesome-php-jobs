<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use League\CommonMark\CommonMarkConverter;
use Symfony\Component\DomCrawler\Crawler;

final class ReadmeCheckerTest extends TestCase
{
    public function testDuplicatedLinks()
    {
        $converter = new CommonMarkConverter();
        $readmeMarkdownFile = file_get_contents(__DIR__ .'/README.md');
        
        $readmeHtmlFile = $converter->convertToHtml($readmeMarkdownFile);

        $crawler = new Crawler($readmeHtmlFile);

        $links = [];
        foreach ($crawler->filter('li a') as $node) {
            $href = $node->getAttribute('href');
            if (array_search($href, $links)){
                $this->fail("Duplicated link " . $href);
            }
            $links[] = $href;
         }
                        
        $this->expectNotToPerformAssertions();
    }

    public function testAlphabeticalOrder() 
    {
        $converter = new CommonMarkConverter();
        $readmeMarkdownFile = file_get_contents(__DIR__ .'/README.md');
        
        $readmeHtmlFile = $converter->convertToHtml($readmeMarkdownFile);

        $crawler = new Crawler($readmeHtmlFile);
        $previousCompany = "";
        $company = "";
        foreach ($crawler->filter('li') as $node) {
            $previousCompany = strtolower((string)$company);
            if (empty($previousCompany)) {
                $company = strtolower((string)$node->textContent);
                continue;
            }
            $company = strtolower((string)$node->textContent);
            $previousIsGreaterThanActualCompany = strcmp($previousCompany, $company) > 0;
            if ($previousIsGreaterThanActualCompany) {
                $this->fail(
                    sprintf("Company `%s` should not be before than `%s`", $previousCompany, $company)
                );
            }
        }

        $this->expectNotToPerformAssertions();
    }
}