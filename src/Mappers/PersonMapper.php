<?php

declare(strict_types=1);

namespace Ameax\AmApi\Mappers;

class PersonMapper
{
    private array $fieldMapping = [
        'honorifics' => 'titel',
        'firstname' => 'vorname',
        'lastname' => 'nachname',
        'phone' => 'tel',
    ];

    private array $genderMapping = [
        'male' => 'Herr',
        'female' => 'Frau',
        'other' => 'Herr',
    ];

    public function mapToApi(array $data): array
    {
        $result = [];

        // Handle gender field
        if (isset($data['gender'])) {
            $result['anrede'] = $this->mapGender($data['gender']);
        }

        // Map fields using the mapping
        foreach ($this->fieldMapping as $from => $to) {
            if (isset($data[$from])) {
                $result[$to] = $data[$from];
            }
        }

        // Copy fields that keep the same name
        foreach (['customer_id', 'email', 'fax', 'position', 'persontype_id', 'mobil', 'abteilung'] as $directField) {
            if (isset($data[$directField])) {
                $result[$directField] = $data[$directField];
            }
        }

        return $result;
    }

    public function mapFromApi(array $data): array
    {
        $result = [];

        // Reverse mapping
        $reverseMapping = array_flip($this->fieldMapping);

        foreach ($data as $key => $value) {
            if (isset($reverseMapping[$key])) {
                $result[$reverseMapping[$key]] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        // Handle gender field
        if (isset($data['anrede'])) {
            $result['gender'] = $this->mapGenderFromApi($data['anrede']);
            unset($result['anrede']);
        }

        return $result;
    }

    private function mapGender(mixed $gender): string
    {
        // Handle enum objects
        if (is_object($gender)) {
            $reflection = new \ReflectionClass($gender);
            
            if ($reflection->hasMethod('value') || $reflection->hasProperty('value')) {
                $enumValue = method_exists($gender, '__toString')
                    ? (string) $gender->__toString()
                    : 'male';

                // Extract gender from enum string representation
                if (stripos($enumValue, 'male') !== false && stripos($enumValue, 'female') === false) {
                    $gender = 'male';
                } elseif (stripos($enumValue, 'female') !== false) {
                    $gender = 'female';
                } else {
                    $gender = 'other';
                }
            }
        }

        // Handle string values
        if (is_string($gender)) {
            $gender = strtolower($gender);
        }

        return $this->genderMapping[$gender] ?? 'Herr';
    }

    private function mapGenderFromApi(string $anrede): string
    {
        $reverseGenderMapping = array_flip($this->genderMapping);
        return $reverseGenderMapping[$anrede] ?? 'other';
    }
}