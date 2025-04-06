<?php

namespace App\Services;

use Hidehalo\Nanoid\Client;

class NanoidGenerator
{
    /**
     * Generate a unique Nanoid for a given model and field.
     *
     * @param string $modelClass The model class to check for collisions.
     * @param string $field The field to check for uniqueness.
     * @param int $size The length of the Nanoid.
     * @return string
     */
    public static function generateUniqueSlug(string $modelClass, string $field, int $size = 6): string
    {
        // Except "l and I" - Lowercase L and uppercase I - to avoid confusion.
        $alphabet = 'ABCDEFGHJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz0123456789-';
        $client = new Client();

        do {
            // Generate a Nanoid with the custom alphabet
            $slug = $client->formattedId($alphabet, $size);
        } while ($modelClass::where($field, $slug)->exists());

        return $slug;
    }
}