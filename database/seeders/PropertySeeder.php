<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Property::create([
            'title' => 'Casa contemporânea com piscina em Alphaville',
            'purpose' => 'venda',
            'type' => 'Casa',
            'price' => 3450000,
            'condo_fee' => 1850,
            'iptu' => 920,
            'area' => 420,
            'lot_area' => 680,
            'bedrooms' => 4,
            'bathrooms' => 5,
            'parking' => 4,
            'suite' => 3,
            'address' => 'Rua das Palmeiras, 245',
            'neighborhood' => 'Alphaville',
            'city' => 'Barueri',
            'state' => 'SP',
            'zip_code' => '06454-000',
            'latitude' => '-23.5019',
            'longitude' => '-46.8424',
            'description' => 'Arquitetura contemporânea e ambientes integrados em uma das ruas mais desejadas de Alphaville. Piscina aquecida, home theater, ginásio e jardim com paisagismo tropical. A poucos minutos do centros comerciais e das principais vias de acesso à capital.',
            'features' => [
                'Piscina aquecida',
                'Home theater',
                'Ginásio',
                'Jardim paisagístico',
                'Ar-condicionado',
                'Portaria 24h',
                'Churrasqueira',
                'Escritório',
            ],
            'photos' => [
                self::unsplash('photo-1613490493576-7fde63acd811'),
                self::unsplash('photo-1600607687939-ce8a6c25118c'),
                self::unsplash('photo-1600566753190-17f0baa2a6c3'),
                self::unsplash('photo-1600607687920-4e2a09cf159d'),
            ],
            'featured' => true,
            'status' => 'disponivel',
            'created_at' => '2026-09-12 10:00:00',
            'updated_at' => '2026-09-12 10:00:00',
        ]);

        Property::create([
            'title' => 'Apartamento premium com vista panorâmica',
            'purpose' => 'venda',
            'type' => 'Apartamento',
            'price' => 2180000,
            'condo_fee' => 2400,
            'iptu' => 640,
            'area' => 186,
            'lot_area' => null,
            'bedrooms' => 3,
            'bathrooms' => 4,
            'parking' => 3,
            'suite' => 2,
            'address' => 'Av. Brigadeiro Faria Lima, 3200',
            'neighborhood' => 'Itaim Bibi',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '04538-132',
            'latitude' => '-23.5850',
            'longitude' => '-46.6820',
            'description' => 'Localizado no coração do Itaim Bibi, este apartamento reúne acabamentos de alto padrão, living amplo com varanda gourmet e vista livre. Condomínio com lazer completo: piscina, spa, salão de festas e academia equipada.',
            'features' => [
                'Varanda gourmet',
                'Vista panorâmica',
                'Academia',
                'Spa',
                'Piscina',
                'Portaria 24h',
                'Armários planejados',
            ],
            'photos' => [
                self::unsplash('photo-1600210492486-724fe5c67fb0'),
                self::unsplash('photo-1600607688969-a5bfcd646154'),
                self::unsplash('photo-1600566753086-00f18fb6b3ea'),
            ],
            'featured' => true,
            'status' => 'disponivel',
            'created_at' => '2026-09-20 14:30:00',
            'updated_at' => '2026-09-20 14:30:00',
        ]);

        Property::create([
            'title' => 'Casa de praia com jardim e vista para o mar',
            'purpose' => 'aluguel',
            'type' => 'Casa',
            'price' => 18000,
            'condo_fee' => 0,
            'iptu' => 380,
            'area' => 260,
            'lot_area' => 450,
            'bedrooms' => 4,
            'bathrooms' => 4,
            'parking' => 2,
            'suite' => 2,
            'address' => 'Rua dos Jatobás, 88',
            'neighborhood' => 'Praia do Flamengo',
            'city' => 'Santos',
            'state' => 'SP',
            'zip_code' => '11020-000',
            'latitude' => '-23.9608',
            'longitude' => '-46.3336',
            'description' => 'A poucos passos da praia, casa espaçosa e arejada com jardim privativo e área de lazer. Ideal para famílias que buscam conforto, segurança e a tranquilidade do litoral paulista.',
            'features' => [
                'A 100m da praia',
                'Jardim',
                'Churrasqueira',
                'Ar-condicionado',
                'Mobiliada',
                'Segurança',
            ],
            'photos' => [
                self::unsplash('photo-1600596542815-ffad4c1539a9'),
                self::unsplash('photo-1600573472550-8090b5e0745e'),
                self::unsplash('photo-1600566752355-35792bedcfea'),
            ],
            'featured' => false,
            'status' => 'disponivel',
            'created_at' => '2026-10-02 09:15:00',
            'updated_at' => '2026-10-02 09:15:00',
        ]);

        Property::create([
            'title' => 'Cobertura duplex com terraço e spa privativo',
            'purpose' => 'venda',
            'type' => 'Cobertura',
            'price' => 4890000,
            'condo_fee' => 3100,
            'iptu' => 1180,
            'area' => 310,
            'lot_area' => null,
            'bedrooms' => 4,
            'bathrooms' => 5,
            'parking' => 4,
            'suite' => 3,
            'address' => 'Rua Oscar Freire, 2100',
            'neighborhood' => 'Jardins',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01426-001',
            'latitude' => '-23.5610',
            'longitude' => '-46.6710',
            'description' => 'Cobertura duplex com projeto de interiores assinado, terraço com piscina privativa e vista para os Jardins. Ambientes generosos, iluminação natural e acabamentos em mármore e madeira nobre.',
            'features' => [
                'Piscina privativa',
                'Terraço gourmet',
                'Spa',
                'Academia',
                'Vista livre',
                'Portaria 24h',
                'Elevador privativo',
            ],
            'photos' => [
                self::unsplash('photo-1512917774080-9991f1c4c750'),
                self::unsplash('photo-1600607687644-c7171b42498f'),
                self::unsplash('photo-1600047509807-ba8f99d2cdde'),
            ],
            'featured' => true,
            'status' => 'disponivel',
            'created_at' => '2026-10-18 16:45:00',
            'updated_at' => '2026-10-18 16:45:00',
        ]);

        Property::create([
            'title' => 'Sala comercial em building classe A',
            'purpose' => 'aluguel',
            'type' => 'Comercial',
            'price' => 12500,
            'condo_fee' => 3200,
            'iptu' => 890,
            'area' => 120,
            'lot_area' => null,
            'bedrooms' => 0,
            'bathrooms' => 2,
            'parking' => 2,
            'suite' => 0,
            'address' => 'Av. Paulista, 1578',
            'neighborhood' => 'Bela Vista',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01310-200',
            'latitude' => '-23.5700',
            'longitude' => '-46.6520',
            'description' => 'Sala comercial em edifício classe A com recepção, salas de reunião e infraestrutura completa. Próxima ao metrô Trianon-Masp e aos principais centros de negócios da Paulista.',
            'features' => [
                'Classe A',
                'Salas de reunião',
                'Recepção',
                'Próximo ao metrô',
                'Ar-condicionado central',
            ],
            'photos' => [
                self::unsplash('photo-1497366811353-6870744d04b2'),
                self::unsplash('photo-1497366754035-f200968a6e72'),
            ],
            'featured' => false,
            'status' => 'disponivel',
            'created_at' => '2026-11-01 11:20:00',
            'updated_at' => '2026-11-01 11:20:00',
        ]);

        Property::create([
            'title' => 'Studio mobiliado e pronto para morar no centro',
            'purpose' => 'aluguel',
            'type' => 'Apartamento',
            'price' => 4200,
            'condo_fee' => 980,
            'iptu' => 120,
            'area' => 48,
            'lot_area' => null,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'parking' => 1,
            'suite' => 0,
            'address' => 'Rua Augusta, 1420',
            'neighborhood' => 'Consolação',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01304-002',
            'latitude' => '-23.5580',
            'longitude' => '-46.6620',
            'description' => 'Studio moderno, totalmente mobiliado e pronto para morar. Localização privilegiada, com bares, restaurantes, metrô e universidades a poucos minutos.',
            'features' => [
                'Mobiliado',
                'Pronto para morar',
                'Portaria 24h',
                'Próximo ao metrô',
                'Coworking',
            ],
            'photos' => [
                self::unsplash('photo-1502672260266-1c1ef2d93688'),
                self::unsplash('photo-1600585154526-990dced4db0d'),
            ],
            'featured' => false,
            'status' => 'disponivel',
            'created_at' => '2026-11-10 08:00:00',
            'updated_at' => '2026-11-10 08:00:00',
        ]);
    }

    private static function unsplash(string $photoId, int $width = 1400): string
    {
        return "https://images.unsplash.com/{$photoId}?auto=format&fit=crop&w={$width}&q=85";
    }
}
