<?php

$faker = \Faker\Factory::create('pt_BR');

$factory->define(App\Course::class, function () use ($faker) {

    return [
        'courseName'=> $faker->unique()->randomElement([
            "Administração",
            "Alimentos",
            "Artes Cênicas",
            "Artes Visuais",
            "Ciências Biológicas",
            "Ciências Contábeis",
            "Ciências Econômicas",
            "Ciências Sociais",
            "Ciência da Computação",
            "Comunicação Social - Jornalismo",
            "Comunicação Social - Produção Editorial",
            "Comunicação Social - Publicidade e Propaganda",
            "Comunicação Social - Relações Públicas",
            "Dança",
            "Direito",
            "Educação Especial",
            "Educação Física",
            "Eletrônica Industrial",
            "Engenharia Acústica",
            "Engenharia Aeroespacial",
            "Engenharia Civil",
            "Engenharia de Telecomunicações",
            "Engenharia Elétrica",
            "Engenharia da Computação",
            "Estatística",
            "Filosofia",
            "Física",
            "Geografia",
            "Geoprocessamento",
            "Gestão de Cooperativas",
            "História",
            "Letras",
            "Matemática",
            "Música e Tecnologia",
            "Música",
            "Pedagogia",
            "Processos Químicos",
            "Redes de Computadores",
            "Serviço Social",
            "Sistemas de Informação",
            "Sistemas para Internet"
        ]),
        'duration' => $faker->randomElement([6,8,10,12]),
        'type' => $faker->randomElement(['Graduação', 'Pós-Graduação', 'Mestrado', 'Doutorado', 'Ensino Médio', 'Ensino Técnico'])
    ];
});
