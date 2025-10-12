<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RealTeam;

class RealTeamsSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            // LIGA PROFESIONAL AR
            ['name' => 'River Plate', 'short_name' => 'River', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/C.A._River_Plate_logo.svg/1200px-C.A._River_Plate_logo.svg.png'],
            ['name' => 'Gimnasia y Esgrima La Plata', 'short_name' => 'Gimnasia LP', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/Gimnasia_y_Esgrima_LP_logo.svg/1200px-Gimnasia_y_Esgrima_LP_logo.svg.png'],
            ['name' => 'Racing Club', 'short_name' => 'Racing', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5a/Racing_Club_de_Avellaneda_logo.svg/1200px-Racing_Club_de_Avellaneda_logo.svg.png'],
            ['name' => 'Independiente', 'short_name' => 'Independiente', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/CA_Independiente_logo.svg/1200px-CA_Independiente_logo.svg.png'],
            ['name' => 'Boca Juniors', 'short_name' => 'Boca', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Boca_Juniors_logo.svg/1200px-Boca_Juniors_logo.svg.png'],
            ['name' => 'San Lorenzo', 'short_name' => 'San Lorenzo', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/e/e1/San_Lorenzo_de_Almagro_logo.svg/1200px-San_Lorenzo_de_Almagro_logo.svg.png'],
            ['name' => 'Huracán', 'short_name' => 'Huracán', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/w  ikipedia/commons/thumb/7/7a/Club_Atl%C3%A9tico_Hurac%C3%A1n_logo.svg/1200px-Club_Atl%C3%A9tico_Hurac%C3%A1n_logo.svg.png'],
            ['name' => 'Vélez Sarsfield', 'short_name' => 'Vélez', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7a/C.A._V%C3%A9lez_Sarsfield_logo.svg/1200px-C.A._V%C3%A9lez_Sarsfield_logo.svg.png'],
            ['name' => 'Estudiantes de La Plata', 'short_name' => 'Estudiantes', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f5/Estudiantes_de_La_Plata_logo.svg/1200px-Estudiantes_de_La_Plata_logo.svg.png'],
            ['name' => 'Tigre', 'short_name' => 'Tigre', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d1/Club_Atl%C3%A9tico_Tigre_logo.svg/1200px-Club_Atl%C3%A9tico_Tigre_logo.svg.png'],
            ['name' => 'Platense', 'short_name' => 'Platense', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/36/Club_Atl%C3%A9tico_Platense_logo.svg/1200px-Club_Atl%C3%A9tico_Platense_logo.svg.png'],
            ['name' => 'Barracas Central', 'short_name' => 'Barracas', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e4/Barracas_Central_Logo.svg/1200px-Barracas_Central_Logo.svg.png'],
            ['name' => 'Argentinos Juniors', 'short_name' => 'Argentinos', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/Argentinos_Juniors_logo.svg/1200px-Argentinos_Juniors_logo.svg.png'],
            ['name' => 'Unión de Santa Fe', 'short_name' => 'Unión', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/63/Un%C3%B3n_de_Santa_Fe_logo.svg/1200px-Un%C3%B3n_de_Santa_Fe_logo.svg.png'],
            ['name' => 'Colón de Santa Fe', 'short_name' => 'Colón', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f5/Club_Atl%C3%A9tico_Col%C3%B3n_de_Santa_Fe_logo.svg/1200px-Club_Atl%C3%A9tico_Col%C3%B3n_de_Santa_Fe_logo.svg.png'],
            ['name' => 'Defensa y Justicia', 'short_name' => 'Defensa', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4e/Defensa_y_Justicia_logo.svg/1200px-Defensa_y_Justicia_logo.svg.png'],
            ['name' => 'Godoy Cruz', 'short_name' => 'Godoy Cruz', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5a/Godoy_Cruz_Antonio_Tomba_logo.svg/1200px-Godoy_Cruz_Antonio_Tomba_logo.svg.png'],
            ['name' => 'Lanús', 'short_name' => 'Lanús', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Club_Atl%C3%A9tico_Lan%C3%BAs_logo.svg/1200px-Club_Atl%C3%A9tico_Lan%C3%BAs_logo.svg.png'],
            ['name' => 'Newell\'s Old Boys', 'short_name' => 'Newell\'s', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Newell%27s_Old_Boys_logo.svg/1200px-Newell%27s_Old_Boys_logo.svg.png'],
            ['name' => 'Rosario Central', 'short_name' => 'Central', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1b/Rosario_Central_logo.svg/1200px-Rosario_Central_logo.svg.png'],
            ['name' => 'Talleres de Córdoba', 'short_name' => 'Talleres', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/84/C.A._Talleres_de_C%C3%B3rdoba_logo.svg/1200px-C.A._Talleres_de_C%C3%B3rdoba_logo.svg.png'],
            ['name' => 'Belgrano de Córdoba', 'short_name' => 'Belgrano', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/Club_Atl%C3%A9tico_Belgrano_logo.svg/1200px-Club_Atl%C3%A9tico_Belgrano_logo.svg.png'],
            ['name' => 'Instituto de Córdoba', 'short_name' => 'Instituto', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/44/Instituto_Atl%C3%A9tico_Central_C%C3%B3rdoba_logo.svg/1200px-Instituto_Atl%C3%A9tico_Central_C%C3%B3rdoba_logo.svg.png'],
            ['name' => 'Sarmiento de Junín', 'short_name' => 'Sarmiento', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e6/Sarmiento_de_Jun%C3%ADn_logo.svg/1200px-Sarmiento_de_Jun%C3%ADn_logo.svg.png'],
            ['name' => 'Central Córdoba SdE', 'short_name' => 'Central Cba', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3a/Central_C%C3%B3rdoba_de_Santiago_del_Estero_logo.svg/1200px-Central_C%C3%B3rdoba_de_Santiago_del_Estero_logo.svg.png'],
            ['name' => 'Atlético Tucumán', 'short_name' => 'At. Tucumán', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6a/Club_Atl%C3%A9tico_Tucum%C3%A1n_logo.svg/1200px-Club_Atl%C3%A9tico_Tucum%C3%A1n_logo.svg.png'],
            ['name' => 'Arsenal de Sarandí', 'short_name' => 'Arsenal', 'country' => 'AR', 'founded_year' => '1902', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/11/Arsenal_de_Sarand%C3%AD_logo.svg/1200px-Arsenal_de_Sarand%C3%AD_logo.svg.png'],    
     

            // PREMIER LEAGUE (Ejemplos)
            ['name' => 'Manchester United', 'short_name' => 'Man Utd', 'country' => 'GB', 'founded_year' => '1932', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/7/7a/Manchester_United_FC_crest.svg/1200px-Manchester_United_FC_crest.svg.png'],
            ['name' => 'Manchester City', 'short_name' => 'Man City', 'country' => 'GB', 'founded_year' => '1932', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/e/e0/Manchester_City_FC_logo.svg/1200px-Manchester_City_FC_logo.svg.png'],
            ['name' => 'Liverpool FC', 'short_name' => 'Liverpool', 'country' => 'GB', 'founded_year' => '1932', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/0/0c/Liverpool_FC_logo.svg/1200px-Liverpool_FC_logo.svg.png'],
            ['name' => 'Chelsea FC', 'short_name' => 'Chelsea', 'country' => 'GB', 'founded_year' => '1932', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/5/53/Chelsea_FC_logo.svg/1200px-Chelsea_FC_logo.svg.png'],
            ['name' => 'Arsenal FC', 'short_name' => 'Arsenal', 'country' => 'GB', 'founded_year' => '1932', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/5/5c/Arsenal_FC.svg/1200px-Arsenal_FC.svg.png'],
            ['name' => 'Tottenham Hotspur', 'short_name' => 'Spurs', 'country' => 'GB', 'founded_year' => '1932', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/b/b4/Tottenham_Hotspur.svg/1200px-Tottenham_Hotspur.svg.png'],    
            ['name' => 'Everton FC', 'short_name' => 'Everton', 'country' => 'GB', 'founded_year' => '1932', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/7/7c/Everton_FC_logo.svg/1200px-Everton_FC_logo.svg.png'],
            ['name' => 'Leicester City', 'short_name' => 'Leicester', 'country' => 'GB', 'founded_year' => '1932', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/6/63/Leicester02.png/1200px-Leicester02.png'],
            ['name' => 'West Ham United', 'short_name' => 'West Ham', 'country' => 'GB', 'founded_year' => '1932', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/c/c2/West_Ham_United_FC_logo.svg/1200px-West_Ham_United_FC_logo.svg.png'],
            ['name' => 'Aston Villa', 'short_name' => 'Aston Villa', 'country' => 'GB', 'founded_year' => '1932', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f9/Aston_Villa_FC_crest.svg/1200px-Aston_Villa_FC_crest.svg.png'],
            ['name' => 'Newcastle United', 'short_name' => 'Newcastle', 'country' => 'GB', 'founded_year' => '1932', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/5/56/Newcastle_United_Logo.svg/1200px-Newcastle_United_Logo.svg.png'],
            ['name' => 'Wolverhampton Wanderers', 'short_name' => 'Wolves', 'country' => 'GB', 'founded_year' => '1932', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/f/fc/Wolverhampton_Wanderers.svg/1200px-Wolverhampton_Wanderers.svg.png'],
            ['name' => "Leeds United", 'short_name' => "Leeds", 'country' => 'GB', 'founded_year' => '1932', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/0/0c/Leeds_United_Logo.svg/1200px-Leeds_United_Logo.svg.png'],
            ['name' => 'Crystal Palace', 'short_name' => 'Crystal Palace', 'country' => 'GB', 'founded_year' => '1932', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/0/0c/Crystal_Palace_FC_logo.svg/1200px-Crystal_Palace_FC_logo.svg.png'],
            ['name' => 'Southampton FC', 'short_name' => 'Southampton', 'country' => 'GB', 'founded_year' => '1932', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/c/c9/Southampton_FC_logo.svg/1200px-Southampton_FC_logo.svg.png'],
        

            // LA LIGA (Ejemplos)
            ['name' => 'Real Madrid', 'short_name' => 'Real Madrid', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/5/56/Real_Madrid_CF.svg/1200px-Real_Madrid_CF.svg.png'],
            ['name' => 'FC Barcelona', 'short_name' => 'Barcelona', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/4/47/FC_Barcelona_%28crest%29.svg/1200px-FC_Barcelona_%28crest%29.svg.png'],
            ['name' => 'Atlético Madrid', 'short_name' => 'Atlético', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f4/Atletico_Madrid_2017_logo.svg/1200px-Atletico_Madrid_2017_logo.svg.png'],
            ['name' => 'Sevilla FC', 'short_name' => 'Sevilla', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f2/Sevilla_CF_logo.svg/1200px-Sevilla_CF_logo.svg.png'],
            ['name' => 'Valencia CF', 'short_name' => 'Valencia', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/7/7e/Valencia_CF_logo.svg/1200px-Valencia_CF_logo.svg.png'],
            ['name' => 'Villarreal CF', 'short_name' => 'Villarreal', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f5/Villarreal_CF_logo.svg/1200px-Villarreal_CF_logo.svg.png'],
            ['name' => 'Real Sociedad', 'short_name' => 'Real Sociedad', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/d/d1/Real_Sociedad_logo.svg/1200px-Real_Sociedad_logo.svg.png'],
            ['name' => 'FC Barcelona', 'short_name' => 'Barcelona', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/4/47/FC_Barcelona_%28crest%29.svg/1200px-FC_Barcelona_%28crest%29.svg.png'],
            ['name' => 'Atlético Madrid', 'short_name' => 'Atlético', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f4/Atletico_Madrid_2017_logo.svg/1200px-Atletico_Madrid_2017_logo.svg.png'],
            ['name' => 'Sevilla FC', 'short_name' => 'Sevilla', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f2/Sevilla_CF_logo.svg/1200px-Sevilla_CF_logo.svg.png'],
            ['name' => 'Valencia CF', 'short_name' => 'Valencia', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/7/7e/Valencia_CF_logo.svg/1200px-Valencia_CF_logo.svg.png'],
            ['name' => 'Villarreal CF', 'short_name' => 'Villarreal', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f5/Villarreal_CF_logo.svg/1200px-Villarreal_CF_logo.svg.png'],
            ['name' => 'Real Sociedad', 'short_name' => 'Real Sociedad', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/d/d1/Real_Sociedad_logo.svg/1200px-Real_Sociedad_logo.svg.png'],
            ['name' => 'Athletic Club', 'short_name' => 'Athletic', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/1/1a/Athletic_Bilbao_logo.svg/1200px-Athletic_Bilbao_logo.svg.png'],
            ['name' => 'Celta de Vigo', 'short_name' => 'Celta', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/6/67/Celta_de_Vigo_logo.svg/1200px-Celta_de_Vigo_logo.svg.png'],
            ['name' => 'RCD Espanyol', 'short_name' => 'Espanyol', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/2/2e/RCD_Espanyol_de_Barcelona_logo.svg/1200px-RCD_Espanyol_de_Barcelona_logo.svg.png'],
            ['name' => 'Getafe CF', 'short_name' => 'Getafe', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/9/9b/Getafe_CF_logo.svg/1200px-Getafe_CF_logo.svg.png'],
            ['name' => 'Real Betis', 'short_name' => 'Betis', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/3/3f/Real_Betis_logo.svg/1200px-Real_Betis_logo.svg.png'],
            ['name' => 'Rayo Vallecano', 'short_name' => 'Rayo', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/1/12/Rayo_Vallecano_logo.svg/1200px-Rayo_Vallecano_logo.svg.png'],
            ['name' => 'Mallorca', 'short_name' => 'Mallorca', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/f/fd/RCD_Mallorca_logo.svg/1200px-RCD_Mallorca_logo.svg.png'],
            ['name' => 'Osasuna', 'short_name' => 'Osasuna', 'country' => 'SP', 'founded_year' => '1933', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/d/d2/CA_Osasuna_logo.svg/1200px-CA_Osasuna_logo.svg.png'],

            // LIGUE 1 (Ejemplos)
            ['name' => 'Paris Saint-Germain', 'short_name' => 'PSG', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/a/a7/Paris_Saint-Germain_F.C..svg/1200px-Paris_Saint-Germain_F.C..svg.png'],
            ['name' => 'Olympique de Lyon', 'short_name' => 'Lyon', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/c/c6/Olympique_Lyonnais.svg/1200px-Olympique_Lyonnais.svg.png'],
            ['name' => 'AS Monaco', 'short_name' => 'Monaco', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/e/ea/AS_Monaco_FC.svg/1200px-AS_Monaco_FC.svg.png'],
            ['name' => 'Olympique de Marseille', 'short_name' => 'Marseille', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/2/2c/Olympique_de_Marseille_logo.svg/1200px-Olympique_de_Marseille_logo.svg.png'],
            ['name' => 'Lille OSC', 'short_name' => 'Lille', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/7/7a/Lille_OSC_logo.svg/1200px-Lille_OSC_logo.svg.png'],
            ['name' => 'FC Nantes', 'short_name' => 'Nantes', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/8/8c/FC_Nantes_logo.svg/1200px-FC_Nantes_logo.svg.png'],
            ['name' => 'OGC Nice', 'short_name' => 'Nice', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/d/d5/OGC_Nice_logo.svg/1200px-OGC_Nice_logo.svg.png  '],
            ['name' => 'Montpellier HSC', 'short_name' => 'Montpellier', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/1/12/Montpellier_HSC_logo.svg/1200px-Montpellier_HSC_logo.svg.png'],
            ['name' => 'RC Strasbourg Alsace', 'short_name' => 'Strasbourg', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/6/6a/RC_Strasbourg_Alsace_logo.svg/1200px-RC_Strasbourg_Alsace_logo.svg.png'],
            ['name' => 'Stade Rennais FC', 'short_name' => 'Rennes', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/5/5e/Stade_Rennais_FC_logo.svg/1200px-Stade_Rennais_FC_logo.svg.png'],
            ['name' => 'Olympique de Marseille', 'short_name' => 'Marseille', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/2/2c/Olympique_de_Marseille_logo.svg/1200px-Olympique_de_Marseille_logo.svg.png'],
            ['name' => 'Olympique Lyonnais', 'short_name' => 'Lyon', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/c/c6/Olympique_Lyonnais.svg/1200px-Olympique_Lyonnais.svg.png'],
            ['name' => 'AS Monaco', 'short_name' => 'Monaco', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/e/ea/AS_Monaco_FC.svg/1200px-AS_Monaco_FC.svg.png'],
            ['name' => 'Lille OSC', 'short_name' => 'Lille', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/7/7a/Lille_OSC_logo.svg/1200px-Lille_OSC_logo.svg.png'],
            ['name' => 'FC Nantes', 'short_name' => 'Nantes', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/8/8c/FC_Nantes_logo.svg/1200px-FC_Nantes_logo.svg.png'],
            ['name' => 'OGC Nice', 'short_name' => 'Nice', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/d/d5/OGC_Nice_logo.svg/1200px-OGC_Nice_logo.svg.png  '],
            ['name' => 'Montpellier HSC', 'short_name' => 'Montpellier', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/1/12/Montpellier_HSC_logo.svg/1200px-Montpellier_HSC_logo.svg.png'],
            ['name' => 'RC Strasbourg Alsace', 'short_name' => 'Strasbourg', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/6/6a/RC_Strasbourg_Alsace_logo.svg/1200px-RC_Strasbourg_Alsace_logo.svg.png'],
            ['name' => 'Stade Rennais FC', 'short_name' => 'Rennes', 'country' => 'FR', 'founded_year' => '1970', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/5/5e/Stade_Rennais_FC_logo.svg/1200px-Stade_Rennais_FC_logo.svg.png'],
        ];

        foreach ($teams as $team) {
            RealTeam::firstOrCreate(
                ['name' => $team['name']], // Buscar por nombre
                $team // Crear con todos los datos
            );
        }

        $this->command->info('Real teams creados: ' . count($teams));
    }
}