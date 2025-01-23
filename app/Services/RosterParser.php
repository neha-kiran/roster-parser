<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Carbon;

class RosterParser
{
    /**
     * Supported file types and their corresponding parsing methods.
     *
     * @var array
     */
    protected $parsers = [
        'html' => 'parseHtml',
        'pdf' => 'parsePdf',
        'csv' => 'parseCsv',
        'txt' => 'parseTxt',
    ];

    protected $defaultDate = '';
    /**
     * Parse the uploaded roster file based on its type.
     *
     * @param string $filePath
     * @param string $extension
     * @return array
     * @throws Exception
     */
    public function parse($filePath, $extension): array
    {   
        if (!isset($this->parsers[$extension])) {
            throw new Exception("Unsupported file type: {$extension}");
        }

        $method = $this->parsers[$extension];

        if (!method_exists($this, $method)) {
            throw new Exception("Parsing method for {$extension} is not implemented.");
        }

         // Check if the file exists
         if (!file_exists($filePath)) {
            die("File not found at: " . $filePath);
        }

        // Check if the file is readable
        if (!is_readable($filePath)) {
            die("File is not readable: " . $filePath);
        }

        $fileContent = file_get_contents($filePath);

        // Check if the file content was retrieved successfully
        if ($fileContent === false) {
            die("Failed to read the file: " . $filePath);
        }

        // Debug: Print the file content to see if it's what you expect
        // echo $fileContent;
        // $fileContent = file_get_contents($filePath);

        return $this->$method($fileContent);
    }

    /**
     * Parse HTML roster file.
     *
     * @param string $content
     * @return array
     */
    protected function parseHtml(string $content): array
    { 
        // Your HTML parsing logic here
        $events = [];

        $search = array("<nav", "</nav>", "<nobr", "</nobr>");
        $replace = array("<div", "</div>","<div", "</div>");
        $fileContent = str_replace($search, $replace, $content);

        $dom = new \DOMDocument();
        @$dom->loadHTML($content);
        $xpath = new \DOMXPath($dom);

        // Get the header row
        $headers = $xpath->query('//table[@id="ctl00_Main_activityGrid"]//tr[1]/td');
        if ($headers->length <= 0) {
            echo "No headers found in the first row.";
        }

        $headerMap = [];
        // Create a map of header names to column indices
        foreach ($headers as $index => $header) {
            $headerMap[strtolower(trim($header->textContent))] = $index;
        }
        // Get the data rows
        $rows = $xpath->query('//table[@id="ctl00_Main_activityGrid"]//tr[position() > 1]');
        foreach ($rows as $row) {
            $columns = $xpath->query('./td', $row);

            if ($columns->length > 0) {
                $activity = $columns->item($headerMap['activity'])?->textContent ?? '';
                $eventType = $this->determineEventType($activity);
                
                $date = $columns->item($headerMap['date'])?->textContent ?? '';
                if(!empty($date)){ 
                $this->defaultDate = $date =  Carbon::create(2022, 1, filter_var($date, FILTER_SANITIZE_NUMBER_INT), 0);
                }

                $events[] = [
                    'date' => $this->defaultDate->toDateString(),
                    'type' => $eventType,
                    'check_in' => $this->parseTime($columns->item($headerMap['c/i(z)'])?->textContent ?? null),
                    'check_out' => $this->parseTime($columns->item($headerMap['c/o(z)'])?->textContent ?? ' '),
                    'flight_number' => $eventType === 'FLT' ? $this->extractFlightNumber($activity) : null,
                    'start_time' => $this->parseTime($columns->item($headerMap['std(z)'])?->textContent ?? null),
                    'end_time' => $this->parseTime($columns->item($headerMap['sta(z)'])?->textContent ?? null),
                    'start_location' => strtolower($columns->item($headerMap['from'])?->textContent) ?? null,
                    'end_location' => strtolower($columns->item($headerMap['to'])?->textContent) ?? null,
                ];
            }
        }

        return $events;
        // return ['parsed' => 'html'];
    }

    /**
     * Parse PDF roster file.
     *
     * @param string $content
     * @return array
     */
    protected function parsePdf(string $content): array
    {
        // Example: Add PDF parsing logic using a library like Spatie/PdfToText
        return ['parsed' => 'pdf'];
    }

    /**
     * Parse CSV roster file.
     *
     * @param string $content
     * @return array
     */
    protected function parseCsv(string $content): array
    {
        // Example: Parse CSV data
        return ['parsed' => 'csv'];
    }

    /**
     * Parse TXT roster file.
     *
     * @param string $content
     * @return array
     */
    protected function parseTxt(string $content): array
    {
        // Example: Parse plain text
        return ['parsed' => 'txt'];
    }

    /**
     * Determine the event type based on the activity code.
     *
     * @param string $activity
     * @return string
     */
    private function determineEventType(string $activity): string
    {
        if (preg_match('/^[A-Z]{2}\d+$/', $activity)) {
            return 'FLT'; // Flight
        }

        return match ($activity) {
            'OFF' => 'DO',
            'SBY' => 'SBY',
            'CI' => 'CI',
            'CO' => 'CO',
            default => 'UNK', // Unknown
        };
    }

    /**
     * Extract the flight number from the activity code.
     *
     * @param string $activity
     * @return string|null
     */
    private function extractFlightNumber(string $activity): ?string
    {
        return preg_match('/^[A-Z]{2}\d+$/', $activity) ? $activity : null;
    }

    // /**
    //  * Parse time string.
    //  *
    //  * @param string $time
    //  * @return string|null
    //  */
    private function parseTime(string $time): ?string
    {
        if (!is_numeric($time)) {
            return '00:00:00';
        }

        return carbon::parse($time)->toTimeString();
    }
}
