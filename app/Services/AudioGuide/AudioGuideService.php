<?php

declare(strict_types=1);

namespace App\Services\AudioGuide;

final class AudioGuideService
{
    /** @var array<string, array<string, string>> */
    private const GUIDES = [
        'fr' => [
            'dashboard.title' => 'Bienvenue sur Lafia. Appuyez sur le gros bouton rouge pour appeler les secours. Choisissez votre operateur en bas.',
            'dashboard.sos' => 'Ce bouton appelle directement les pompiers au 118. Appuyez dessus en cas d\'urgence.',
            'dashboard.services' => 'Voici les numeros d\'urgence. Pompiers, SAMU, Police. Appuyez sur un service pour appeler.',
            'dashboard.operator' => 'Choisissez votre reseau telephonique. MTN, Moov ou Celtiis. Les numeros seront adaptes.',
            'dashboard.centers' => 'Ce sont les centres de secours pres de chez vous. Appuyez pour appeler directement.',
            'profil.title' => 'Ici vous gardez vos informations de sante en securite. Personne ne peut les lire sauf vous.',
            'profil.vault' => 'Votre coffre-fort protege vos donnees. Appuyez sur Ouvrir pour voir vos informations.',
            'profil.form' => 'Remplissez votre groupe sanguin, vos allergies et vos medicaments. C\'est important pour les secours.',
            'cercle.title' => 'Le cercle de confiance protege vos donnees. Si vous perdez votre telephone, vos proches peuvent vous aider a recuperer vos informations.',
            'cercle.guardians' => 'Ajoutez des personnes de confiance. Maman, votre frere, un ami. Ils garderont un morceau de votre cle.',
            'cercle.recovery' => 'Si vous perdez votre telephone, demandez a vos proches d\'approuver la recuperation. Il en faut au moins deux.',
        ],
        'fon' => [
            'dashboard.title' => 'Lafia ko. De bo ton we lon nado lesse do do.',
            'dashboard.sos' => 'Bo ton we lon nado yi gble do.',
            'profil.title' => 'Fi ye lanme to nado hwe do.',
            'cercle.title' => 'Meton lele na kpodo we yi gble do.',
        ],
    ];

    /** @return array<string, string> */
    public function getGuides(string $locale = 'fr'): array
    {
        return self::GUIDES[$locale] ?? self::GUIDES['fr'];
    }

    public function getGuideText(string $key, string $locale = 'fr'): ?string
    {
        return self::GUIDES[$locale][$key] ?? self::GUIDES['fr'][$key] ?? null;
    }

    public function getAudioPath(string $key, string $locale = 'fr'): ?string
    {
        $filename = str_replace('.', '-', $key) . '.mp3';
        $path = public_path("audio/{$locale}/{$filename}");

        return file_exists($path) ? "/audio/{$locale}/{$filename}" : null;
    }

    /** @return array<string> */
    public function getAvailableLocales(): array
    {
        return ['fr', 'fon'];
    }
}
