<?php

 $preparedText = mb_strtolower($text['content'], 'UTF-8'); 
 $tokenizedText = preg_split('/\s+/', $preparedText); 
 $totalWords = count($tokenizedText);
  echo 'Words '.$totalWords.'<br>';
// Associative array to store occurrences of each unique keyword phrase
$keywordOccurrences = array();

// Function to count occurrences of a keyword phrase 
function keywordDensity($text, $keyword, $totalWords) {
    $count = preg_match_all('/\b' . preg_quote($keyword, '/') . '\b/iu', $text, $matches);
    $density = $count / $totalWords * 100;
    return ['count' => $count, 'density' => $density];
}

  // Process and save results for each keyword phrase   
  $keywordLower = mb_strtolower($keyword, 'UTF-8');

    if (isset($keywordOccurrences[$keywordLower])) {
        continue;
    }

    $result = keywordDensity($preparedText, $keywordLower, $totalWords);
    $keywordOccurrences[$keywordLower] = $result;
}

function calculateTextEntropy($text) {
    $words = str_word_count(mb_strtolower($text), 1);
    $wordFrequencies = array_count_values($words);
    $entropy = 0.0;
    $totalWords = array_sum($wordFrequencies);

    foreach ($wordFrequencies as $frequency) {
        $p = $frequency / $totalWords;
        $entropy -= $p * log($p);
    }
    return $entropy / log($totalWords);
}

function calculateKeywordDensity($text, $keywords) {
    $words = str_word_count(mb_strtolower($text), 1);
    $totalWords = count($words);
    $keywordMatches = 0;

    foreach ($keywords as $keyword) {
        $keywordMatches += substr_count(mb_strtolower($text), mb_strtolower($keyword));
    }

    return ($keywordMatches / $totalWords) * 100;
}

  $entropy = calculateTextEntropy($text['content']);
  $waterContent = (1 - $entropy) * 100; 
  $spamLevel = calculateKeywordDensity($text['content'], $keywords);
   echo "Spam Level: " . round($spamLevel, 2) . "%<br>";
   echo "Water: " . round($waterContent, 2) . "%<br>";


function calculateKeywordDensity2($text, $keywords) {  
    $textLower = mb_strtolower($text);
    $textCleaned = strip_tags($textLower);
    $preparedText = mb_strtolower($text, 'UTF-8'); 
    $tokenizedText = preg_split('/\s+/', $preparedText);  
    $totalWords = count($tokenizedText); 
    $keywordCounts = array();
   // Iterate over the keywords and count their occurrences
    foreach ($keywords as $keyword) {
        $keyLower = mb_strtolower(trim($keyword));
        $count = substr_count(trim($textLower), trim($keyLower));
        $keywordCounts[$keyLower] = isset($keywordCounts[$keyLower]) ? $keywordCounts[$keyLower] + $count : $count;
    }

    $totalKeywordDensity = 0;

    foreach ($keywordCounts as $keyword => $count) {
        $keywordDensity = ($count / $totalWords) * 100;
        $totalKeywordDensity += $keywordDensity;
        echo "Density for '{$keyword}': " . number_format($keywordDensity, 2) . "% ({$count}/{$totalWords}) <br>";
    }
    echo "Overall keyword density: " . number_format($totalKeywordDensity, 2) . "% <br>";
    return $keywordCounts;
}

 calculateKeywordDensity2($text['content'], $keywords);

function getTextFromTags($html, $tagName) {
    $dom = new DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    $tags = $dom->getElementsByTagName($tagName);
    $texts = [];
    foreach ($tags as $tag) {
        $texts[] = strtolower($tag->nodeValue);
    }
    return $texts;
}

// Check keywords and phrases in titles 
function checkKeywordsInTitles($html, $keywords) {
    $score = 0;
    foreach (['h1', 'h2', 'h3', 'h4', 'h5', 'h6'] as $tag) {
        $texts = getTextFromTags($html, $tag);
        foreach ($texts as $text) {
            foreach ($keywords as $keyword) {
                if (strpos($text, strtolower($keyword)) !== false) {
                    $score += 2; 
                }
            }
        }
    }
    return $score;
}

// Check keywords and phrases at the beginning of the text (first 500 characters)
function checkKeywordsInBeginning($text, $keywords) {
    $score = 0;
    $beginning = substr(strtolower($text), 0, 500);
    foreach ($keywords as $keyword) {
        if (strpos($beginning, strtolower($keyword)) !== false) {
            $score += 3; // Назначаем 3 балла за ключевое слово в начале текста
        }
    }
    return $score;
}

 $textContent = strtolower(strip_tags($text['content']));
 $titlesScore = checkKeywordsInTitles($text['content'], $keywords);
 $beginningScore = checkKeywordsInBeginning($textContent, $keywords);
 $totalScore = $titlesScore + $beginningScore;

echo "<br>Keyword score in titles: $titlesScore\n";
echo "<br>Keyword score at the beginning of the text: $beginningScore\n";
echo "<br>Total score: $totalScore\n";

if ($totalScore > 10) {
    echo "<br>Content has excellent keyword distribution.";
} elseif ($totalScore > 5) {
    echo "<br>Content has good keyword distribution.";
} else {
    echo "<br>Content needs improvement in keyword distribution.";
}






?>