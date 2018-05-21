<?php
/**
 * Created by PhpStorm.
 * User: virga
 * Date: 2018-05-17
 * Time: 22:46
 */

namespace App\DataFixtures\ORM;

use Faker\Provider\Base as BaseProvider;
use Faker\Generator;

class CustomFixtureProvider extends BaseProvider
{

    public function __construct(Generator $generator)
    {
        parent::__construct($generator);
    }

    public function animalName()
    {
        $key = array_rand($this->animalNames);
        return $this->animalNames[$key];
    }

//    public function animalType()
//    {
//        $key = array_rand($this->animalSpecies);
//        return $this->animalSpecies[$key];
//    }

    private $animalNames = [
        'Arčis',
        'Arikas',
        'Asikas',
        'Afina',
        'Afira',
        'Basteris',
        'Bakse',
        'Čakas',
        'Čara',
        'Gugas',
        'Gogas',
        'Pūkis',
        'Maška',
        'Pupinukas',
        'Pupis',
        'Rotas',
        'Rubensas',
        'Snikersas',
        'Torres',
        'Vanilla',
        'Zoris',
        'Zoze',
        'Turkis',
        'Tuškis',
        'Tuta'

    ];

//    private $animalSpecies = [
//        'katė',
//        'šuo',
//        'reptilija',
//        'paukštis'
//    ];
}