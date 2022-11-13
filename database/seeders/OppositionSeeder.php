<?php

namespace Database\Seeders;

use App\Core\Services\UuidGeneratorService;
use App\Models\Opposition;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OppositionSeeder extends Seeder
{
    /**
     * @throws \Exception
     */
    public function run(): void
    {

        $factoryInstance = Factory::create();

        $acronym = [
            "I",
            "II",
            "III",
            "IV",
            "V",
            "VI",
            "VII",
            "VIII",
            "IX",
        ];

        $oppositions = [
            [
                'name' => 'Corporaciones locales - Normativa común',
            ],
            [
                'name' => 'Auxiliares Administrativos del Ayuntamiento de Madrid',
            ],
            [
                'name' => 'Conocimientos básicos de informática y aplicaciones virtuales.',
            ],
            [
                'name' => 'Auxiliar Administrativo Ayuntamiento Sevilla',
            ],
            [
                'name' => 'Ley 7/1985, Reguladora de las Bases del Régimen Local',
            ],
            [
                'name' => 'Agentes Medioambientales Organismos Autónomos - Turno Libre',
            ],
            [
                'name' => 'TCAE del SAS (Servicio Andaluz de Salud) Turno Libre',
            ],
            [
                'name' => 'TCAE del SAS (Servicio Andaluz de Salud) Promoción Interna',
            ],
            [
                'name' => 'TCAE del Servicio Aragonés de Salud',
            ],
            [
                'name' => 'TCAE del Servicio Canario de Salud',
            ],
            [
                'name' => 'TCAE del Servicio Cántabro de Salud',
            ],
            [
                'name' => 'TCAE del SACYL (Servicio de Salud de Castilla y León)',
            ],
            [
                'name' => 'TCAE del SESCAM (Servicio de Salud de Castilla-La Mancha)',
            ],
            [
                'name' => 'TCAE del SERGAS (Servicio Gallego de Salud)',
            ],
            [
                'name' => 'Escala de Auxiliares de clínica (C2) de la Xunta de Galicia Turno libre',
            ],
            [
                'name' => 'Escala de Auxiliares de clínica (C2) de la Xunta de Galicia Promoción interna',
            ],
            [
                'name' => 'TCAE del SERMAS (Servicio Madrileño de Salud)',
            ],
            [
                'name' => 'TCAE del SMS (Servicio Murciano de Salud)',
            ],
            [
                'name' => 'TCAE del OSASUNBIDEA (Servicio Navarro de Salud)',
            ],
            [
                'name' => 'TCAE de Instituciones Sanitarias de la C. de Sanidad Universal y Salud Pública de la C. Valenciana',
            ],
            [
                'name' => 'Constitución Española',
            ],
            [
                'name' => 'Normativa de Igualdad y Violencia de Género',
            ],
            [
                'name' => 'Ley Orgánica 3/2018, de Protección de Datos Personales y garantía de los derechos digitales',
            ],
            [
                'name' => 'Ley Orgánica 6/1985, de 1 de julio, del Poder Judicial',
            ],
            [
                'name' => 'Ley 39/2015 del Procedimiento Administrativo Común de las Administraciones Públicas',
            ],
            [
                'name' => 'Ley 40/2015, de 1 de octubre, de Régimen Jurídico del Sector Público',
            ],
            [
                'name' => 'Texto refundido de la Ley del Estatuto Básico del Empleado Público',
            ],
            [
                'name' => 'Ley 9/2017, de 8 de noviembre, de Contratos del Sector Público',
            ],
            [
                'name' => 'Ley 19/2013, de transparencia, acceso a la información pública y buen gobierno',
            ],
            [
                'name' => 'Ley 31/1995, de Prevención de Riesgos Laborales',
            ],
            [
                'name' => 'Normativa Medio Ambiente, Costas y Aguas',
            ],
            [
                'name' => 'Código Penal',
            ],
        ];

        foreach ($oppositions as $opposition) {

            $random_number = random_int(2,3);

            Opposition::query()->create([
                'id' => UuidGeneratorService::getUUIDUnique(Opposition::class),
                'name' => $opposition['name'],
                'period' => "202{$random_number}-EXAM-{$acronym[random_int(0,count($acronym) - 1)]}-{$factoryInstance->numerify('####')}"
            ]);
        }
    }
}
