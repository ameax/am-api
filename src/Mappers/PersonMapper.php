<?php

declare(strict_types=1);

namespace Ameax\AmApi\Mappers;

class PersonMapper
{
    private array $data = [];

    private array $fieldMapping = [
        'honorifics' => 'titel',
        'firstname' => 'vorname',
        'lastname' => 'nachname',
        'phone' => 'tel',
        'mobile' => 'mobil',
        'department' => 'abteilung',
    ];

    private array $genderMapping = [
        'male' => 'Herr',
        'female' => 'Frau',
        'other' => 'Herr',
    ];

    public static function make(): self
    {
        return new self();
    }

    public function setData(array $data): self
    {
        foreach ($data as $key => $value) {
            // Handle gender transformation
            if ($key === 'gender') {
                $this->data['anrede'] = $this->genderMapping[strtolower($value)] ?? 'Herr';
                continue;
            }

            // Apply field mapping if exists
            if (isset($this->fieldMapping[$key])) {
                $this->data[$this->fieldMapping[$key]] = $value;
            } else {
                // Pass through unmapped fields
                $this->data[$key] = $value;
            }
        }

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public static function fromApiResponse(array $apiData): array
    {
        $result = [];
        $reverseMapping = array_flip((new self())->fieldMapping);
        $reverseGenderMapping = array_flip((new self())->genderMapping);

        foreach ($apiData as $key => $value) {
            // Handle gender field
            if ($key === 'anrede') {
                $result['gender'] = $reverseGenderMapping[$value] ?? 'other';
                continue;
            }

            if (isset($reverseMapping[$key])) {
                $result[$reverseMapping[$key]] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}