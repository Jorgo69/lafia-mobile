<?php

declare(strict_types=1);

namespace App\Modules\Tips\Database\Seeders;

use App\Modules\Tips\Models\PracticalTip;
use Illuminate\Database\Seeder;

final class PracticalTipSeeder extends Seeder
{
    public function run(): void
    {
        $tips = [
            // === TELECOM ===
            [
                'slug' => 'numeros-10-chiffres',
                'category' => 'telecom',
                'title' => 'Numeros a 10 chiffres',
                'content' => 'Depuis le 30 novembre 2024, tous les numeros beninois passent de 8 a 10 chiffres. Ajoutez simplement 01 au debut de chaque numero, quel que soit l\'operateur (MTN, Moov, Celtiis).',
                'source' => 'ARCEP Benin',
                'is_pinned' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'parts-marche-operateurs',
                'category' => 'telecom',
                'title' => 'Parts de marche operateurs 2025',
                'content' => 'MTN : 40,9% | Celtiis : 35,6% | Moov : 23,5%. Les 3 operateurs sont actifs sur tout le territoire. Lafia supporte les 3.',
                'source' => 'ARCEP Benin 2025',
                'sort_order' => 2,
            ],
            [
                'slug' => 'portabilite-numero',
                'category' => 'telecom',
                'title' => 'Portabilite du numero',
                'content' => 'Vous pouvez garder votre numero en changeant d\'operateur. La portabilite est gratuite et prend 48h maximum. Rendez-vous dans une agence de votre nouvel operateur avec votre piece d\'identite.',
                'source' => 'ARCEP Benin',
                'sort_order' => 3,
            ],

            // === ELECTRICITE ===
            [
                'slug' => 'tarifs-sbee-2026',
                'category' => 'electricite',
                'title' => 'Tarifs SBEE 2026-2027',
                'content' => 'Tranche sociale (moins de 20 kWh) : 88 FCFA/kWh. Tranche 1 (0-250 kWh) : 125 FCFA/kWh. Tranche 2 (plus de 250 kWh) : 148 FCFA/kWh. TVA 18% en sus.',
                'source' => 'SBEE / ARE Benin',
                'is_pinned' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'sbee-delestage',
                'category' => 'electricite',
                'title' => 'En cas de coupure SBEE',
                'content' => 'Signalez les coupures au 61 61 61 61 (SBEE). Debranchez vos appareils pendant la coupure pour eviter les surtensions au retour du courant. Gardez une lampe torche chargee.',
                'source' => 'SBEE',
                'sort_order' => 2,
            ],
            [
                'slug' => 'sbee-compteur-prepaye',
                'category' => 'electricite',
                'title' => 'Recharger son compteur SBEE',
                'content' => 'MTN : Composez *400# > MoMoPay > SBEE > Recharge. Moov : Composez *855*4*2#. Celtiis : *889*7#. Vous recevrez un code STS a 20 chiffres a entrer sur le compteur.',
                'sort_order' => 3,
            ],

            // === EAU ===
            [
                'slug' => 'soneb-reference',
                'category' => 'eau',
                'title' => 'Payer sa facture SONEB',
                'content' => 'Votre reference SONEB comporte 14 caracteres (12 chiffres + 2 lettres). Elle est sur votre facture papier. Paiement en ligne : soneb.service-public.bj ou via MoMo/Moov Money.',
                'source' => 'SONEB',
                'sort_order' => 1,
            ],
            [
                'slug' => 'soneb-fuite',
                'category' => 'eau',
                'title' => 'Signaler une fuite d\'eau',
                'content' => 'Contactez la SONEB au 61 00 11 10. En attendant l\'intervention, fermez le robinet d\'arret si accessible. Une fuite non signalee peut augmenter votre facture de 200% a 500%.',
                'source' => 'SONEB',
                'sort_order' => 2,
            ],

            // === SANTE ===
            [
                'slug' => 'pharmacie-garde-info',
                'category' => 'sante',
                'title' => 'Pharmacies de garde',
                'content' => 'Les pharmacies de garde changent chaque lundi. Consultez l\'onglet Pharma de Lafia pour connaitre celles ouvertes cette semaine dans votre zone. L\'ONPB publie les programmes sur onpb.bj.',
                'source' => 'ONPB',
                'is_pinned' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'samu-136',
                'category' => 'sante',
                'title' => 'SAMU : 136',
                'content' => 'Le 136 est le numero du SAMU au Benin pour les urgences medicales. Gratuit depuis tous les operateurs. Decrivez clairement la situation et votre localisation.',
                'source' => 'Ministere de la Sante',
                'sort_order' => 2,
            ],

            // === SECURITE ===
            [
                'slug' => 'numeros-urgence',
                'category' => 'securite',
                'title' => 'Numeros d\'urgence gratuits',
                'content' => '118 : Sapeurs-Pompiers / Protection Civile. 117 : Police Secours. 136 : SAMU. 160 : Centre d\'appel Police Republicaine. 111 : Protection de l\'enfant. Tous gratuits.',
                'is_pinned' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'police-republicaine',
                'category' => 'securite',
                'title' => 'Police Republicaine : 160',
                'content' => 'Le 160 est le centre d\'appel de la Police Republicaine. Disponible 24h/24 pour signaler une agression, un vol, un accident ou tout trouble a l\'ordre public.',
                'source' => 'Police Republicaine du Benin',
                'sort_order' => 2,
            ],

            // === ADMIN ===
            [
                'slug' => 'sgds-dechets',
                'category' => 'admin',
                'title' => 'Gestion des dechets (SGDS)',
                'content' => 'La SGDS gere la collecte des dechets dans le Grand Nokoue. Paiement via Celtiis : *611*3#. Pour signaler un probleme de collecte, contactez votre mairie.',
                'source' => 'SGDS-GN',
                'sort_order' => 1,
            ],
        ];

        foreach ($tips as $tip) {
            PracticalTip::updateOrCreate(
                ['slug' => $tip['slug']],
                $tip,
            );
        }
    }
}
