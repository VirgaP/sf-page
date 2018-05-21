<?php
/**
 * Created by PhpStorm.
 * User: virga
 * Date: 2018-05-17
 * Time: 22:46
 */

namespace App\DataFixtures\ORM;


use Nelmio\Alice\Loader\NativeLoader;
use Faker\Generator as FakerGenerator;

class CustomNativeLoader extends NativeLoader
{
    protected function createFakerGenerator(): FakerGenerator
    {
        $generator = parent::createFakerGenerator();
        $generator->addProvider(new CustomFixtureProvider($generator));
        return $generator;
    }
}