<?php 
$plantRanges = [
    'nepenthes' => [
        'zone' => 1,
        'tempMin' => 22, 'tempMax' => 30,
        'humMin' => 70, 'humMax' => 90,
        'name' => 'Nepenthes',
        'origin' => 'Asia tropicale',
        'light' => 'Luce indiretta molto intensa',
        'substrate' => 'Torba + perlite + sfagno vivo',
        'notes' => 'Ama caldo umido. Evitare sbalzi termici.',
        'image' => 'images/nepenthes.jpg'
    ],
    'nepenthes_lowland' => [
        'zone' => 1,
        'tempMin' => 24, 'tempMax' => 32,
        'humMin' => 70, 'humMax' => 90,
        'name' => 'Nepenthes Lowland',
        'origin' => 'Bassa quota tropicale',
        'light' => 'Alta luce indiretta',
        'substrate' => 'Torba + perlite + sfagno',
        'notes' => 'Temperature costantemente alte tutto l\'anno.',
        'image' => 'images/nepenthes_lowland.jpg'
    ],
    'nepenthes_intermediate' => [
        'zone' => 2,
        'tempMin' => 18, 'tempMax' => 25,
        'humMin' => 60, 'humMax' => 80,
        'name' => 'Nepenthes Intermediate',
        'origin' => 'Quota media',
        'light' => 'Luce filtrata intensa',
        'substrate' => 'Torba + perlite + bark',
        'notes' => 'Buona resistenza agli sbalzi stagionali.',
        'image' => 'images/nepenthes_intermediate.jpg'
    ],
    'nepenthes_highland' => [
        'zone' => 2,
        'tempMin' => 12, 'tempMax' => 20,
        'humMin' => 60, 'humMax' => 90,
        'name' => 'Nepenthes Highland',
        'origin' => 'Alta montagna tropicale',
        'light' => 'Luce media con molta umidità',
        'substrate' => 'Sfagno vivo + perlite',
        'notes' => 'Necessita escursione termica giorno/notte.',
        'image' => 'images/nepenthes_highland.jpg'
    ],
    'drosera_capensis_alba' => [
        'zone' => 1,
        'tempMin' => 18, 'tempMax' => 28,
        'humMin' => 60, 'humMax' => 90,
        'name' => 'Drosera Capensis Alba',
        'origin' => 'Sud Africa',
        'light' => 'Luce piena ma filtrata',
        'substrate' => 'Torba acida + sabbia',
        'notes' => 'Molto adattabile. Facile per principianti.',
        'image' => 'images/drosera_capensis_alba.jpg'
    ],
    'drosera_capillaris' => [
        'zone' => 1,
        'tempMin' => 18, 'tempMax' => 26,
        'humMin' => 70, 'humMax' => 90,
        'name' => 'Drosera Capillaris',
        'origin' => 'Americhe tropicali',
        'light' => 'Luce intensa diretta o filtrata',
        'substrate' => 'Torba + sabbia silicea',
        'notes' => 'Ama umidità e acqua distillata.',
        'image' => 'images/drosera_capillaris.jpg'
    ],
    'drosera_tokaiensis' => [
        'zone' => 1,
        'tempMin' => 18, 'tempMax' => 26,
        'humMin' => 70, 'humMax' => 90,
        'name' => 'Drosera Tokaiensis',
        'origin' => 'Ibrido tra D. spatulata e D. rotundifolia',
        'light' => 'Luce solare diretta filtrata',
        'substrate' => 'Torba + sabbia',
        'notes' => 'Robusta, tollera basse temperature.',
        'image' => 'images/drosera_tokaiensis.jpg'
    ],
    'sarracenia_leucophylla_alata' => [
        'zone' => 1,
        'tempMin' => 5, 'tempMax' => 30,
        'humMin' => 50, 'humMax' => 70,
        'name' => 'Sarracenia Leucophylla x Alata',
        'origin' => 'USA sud-orientale',
        'light' => 'Sole pieno',
        'substrate' => 'Torba + perlite',
        'notes' => 'Richiede riposo invernale.',
        'image' => 'images/sarracenia.jpg'
    ],
    'sphagnum' => [
        'zone' => 1,
        'tempMin' => 10, 'tempMax' => 25,
        'humMin' => 80, 'humMax' => 100,
        'name' => 'Sfagno vivo',
        'origin' => 'Zone paludose temperate',
        'light' => 'Luce indiretta umida',
        'substrate' => '---',
        'notes' => 'Usato come substrato per molte carnivore.',
        'image' => 'images/sphagnum.jpg'
    ],
    'muschi_selvatici' => [
        'zone' => 1,
        'tempMin' => 10, 'tempMax' => 24,
        'humMin' => 80, 'humMax' => 100,
        'name' => 'Muschi Selvatici',
        'origin' => 'Boschi e ambienti umidi',
        'light' => 'Ombra',
        'substrate' => 'Terreno acido e costantemente umido',
        'notes' => 'Molto decorativi e umidificanti.',
        'image' => 'images/muschio.jpg'
    ],
    'asplenium_trichomanes' => [
        'zone' => 1,
        'tempMin' => 10, 'tempMax' => 22,
        'humMin' => 70, 'humMax' => 90,
        'name' => 'Asplenium Trichomanes',
        'origin' => 'Zone temperate',
        'light' => 'Ombra luminosa',
        'substrate' => 'Terreno ben drenato e umido',
        'notes' => 'Felce ornamentale, tollerante.',
        'image' => 'images/asplenium_trichomanes.jpg'
    ],
    'myosotis_scorpioides' => [
        'zone' => 1,
        'tempMin' => 12, 'tempMax' => 25,
        'humMin' => 80, 'humMax' => 100,
        'name' => 'Myosotis Scorpioides',
        'origin' => 'Europea, ambienti umidi',
        'light' => 'Luce solare diretta o semiombra',
        'substrate' => 'Paludoso o umido',
        'notes' => 'Fioritura delicata, ama acqua abbondante.',
        'image' => 'images/myosotis.jpg'
    ],
    'marchantia_polymorpha' => [
        'zone' => 1,
        'tempMin' => 10, 'tempMax' => 22,
        'humMin' => 85, 'humMax' => 100,
        'name' => 'Marchantia Polymorpha',
        'origin' => 'Zone umide temperate',
        'light' => 'Ombra o penombra costante',
        'substrate' => 'Terreno paludoso o sfagno umido',
        'notes' => 'Richiede costante umidità e acqua pura.',
        'image' => 'images/marchantia_polymorpha.jpg'
    ],
    'asplenium_scolopendrium' => [
        'zone' => 1,
        'tempMin' => 10, 'tempMax' => 24,
        'humMin' => 70, 'humMax' => 90,
        'name' => 'Asplenium Scolopendrium',
        'origin' => 'Europa',
        'light' => 'Ombra profonda',
        'substrate' => 'Terreno neutro-acido umido',
        'notes' => 'Sensibile all\'aria secca.',
        'image' => 'images/asplenium_scolopendrium.jpg'
    ],
    'darlingtonia_californica' => [
        'zone' => 2,
        'tempMin' => 15, 'tempMax' => 25,
        'humMin' => 70, 'humMax' => 90,
        'name' => 'Darlingtonia Californica',
        'origin' => 'California e Oregon',
        'light' => 'Luce intensa',
        'substrate' => 'Torba + sabbia + ghiaino',
        'notes' => 'Richiede radici fredde, acqua corrente.',
        'image' => 'images/darlingtonia.jpg'
    ],
    'pinguicula_aphrodite' => [
        'zone' => 2,
        'tempMin' => 18, 'tempMax' => 25,
        'humMin' => 50, 'humMax' => 70,
        'name' => 'Pinguicula Aphrodite',
        'origin' => 'Messico (montagna)',
        'light' => 'Luce indiretta brillante',
        'substrate' => 'Perlite + torba + sabbia silicea',
        'notes' => 'Tollera periodi secchi. Fioritura prolungata.',
        'image' => 'images/pinguicula_aphrodite.jpg'
    ],
    'philodendron_prince_of_orange' => [
        'zone' => 2,
        'tempMin' => 18, 'tempMax' => 28,
        'humMin' => 60, 'humMax' => 80,
        'name' => 'Philodendron Prince of Orange',
        'origin' => 'Ibrido ornamentale',
        'light' => 'Luce indiretta intensa',
        'substrate' => 'Terriccio arioso + perlite + bark',
        'notes' => 'Decorativo. Evitare ristagni.',
        'image' => 'images/philodendron.jpg'
    ],
    'peperomia_caperata' => [
        'zone' => 2,
        'tempMin' => 18, 'tempMax' => 24,
        'humMin' => 50, 'humMax' => 70,
        'name' => 'Peperomia Caperata',
        'origin' => 'Brasile',
        'light' => 'Luce soffusa',
        'substrate' => 'Terriccio per piante tropicali',
        'notes' => 'Compatta e decorativa.',
        'image' => 'images/peperomia.jpg'
    ],
    'polypodium_vulgare' => [
        'zone' => 2,
        'tempMin' => 10, 'tempMax' => 22,
        'humMin' => 70, 'humMax' => 90,
        'name' => 'Polypodium Vulgare',
        'origin' => 'Europa',
        'light' => 'Ombra',
        'substrate' => 'Terreno fresco e drenato',
        'notes' => 'Felce rustica, perfetta per zone ombrose.',
        'image' => 'images/polypodium.jpg'
    ],
    'anthurium_forgetii' => [
        'zone' => 2,
        'tempMin' => 20, 'tempMax' => 28,
        'humMin' => 80, 'humMax' => 90,
        'name' => 'Anthurium Forgetii',
        'origin' => 'Colombia',
        'light' => 'Luce indiretta',
        'substrate' => 'Bark + perlite + sfagno',
        'notes' => 'Ama alta umidità e temperature stabili.',
        'image' => 'images/anthurium.jpg'
    ],
    'calathea_rufibarba' => [
        'zone' => 2,
        'tempMin' => 18, 'tempMax' => 25,
        'humMin' => 70, 'humMax' => 80,
        'name' => 'Calathea Rufibarba',
        'origin' => 'Brasile',
        'light' => 'Luce soffusa',
        'substrate' => 'Terriccio drenante ricco di humus',
        'notes' => 'Non sopporta acqua calcarea.',
        'image' => 'images/calathea.jpg'
    ],
    'fragaria_vesca' => [
        'zone' => 2,
        'tempMin' => 15, 'tempMax' => 25,
        'humMin' => 60, 'humMax' => 75,
        'name' => 'Fragaria Vesca',
        'origin' => 'Europa',
        'light' => 'Sole pieno o mezz\'ombra',
        'substrate' => 'Terriccio ricco e ben drenato',
        'notes' => 'Produce frutti se ben illuminata.',
        'image' => 'images/fragaria.jpg'
    ],
    'tillandsia_ionantha' => [
        'zone' => 2,
        'tempMin' => 15, 'tempMax' => 30,
        'humMin' => 50, 'humMax' => 70,
        'name' => 'Tillandsia Ionantha',
        'origin' => 'America Centrale',
        'light' => 'Luce brillante indiretta',
        'substrate' => 'Epifita (nessun substrato)',
        'notes' => 'Nebulizzare regolarmente.',
        'image' => 'images/tillandsia.jpg'
    ]
];
?>
