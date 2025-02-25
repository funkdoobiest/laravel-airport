<?php
/**
 * Created by PhpStorm.
 * User: dyangalih
 * Date: 2019-02-14
 * Time: 16:39
 */

namespace WebAppId\Airport\Seeds;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use WebAppId\Airport\Repositories\AirportRepository;
use WebAppId\Airport\Services\Params\AirportParam;

/**
 * @author: Dyan Galih<dyan.galih@gmail.com>
 * Date: 07/01/20
 * Time: 18.07
 * Class AirportSeeder
 * @package WebAppId\Airport\Seeds
 */
class AirportSeeder extends Seeder
{
    public function run(AirportRepository $airportRepository, AirportParam $airportParam)
    {
        if (strpos($_SERVER['SCRIPT_NAME'], 'phpunit')) {
            $file = __DIR__ . '/../Resources/Csv/dummy/Airports.csv';
        } else {
            $file = __DIR__ . '/../Resources/Csv/Airports.csv';
        }

        $header = array('id', 'ident', 'type', 'name', 'latitude_deg', 'longitude_deg', 'elevation_ft', 'continent', 'iso_country', 'iso_region', 'municipality', 'scheduled_service', 'gps_code', 'iata_code', 'local_code', 'home_link', 'wikipedia_link', 'keywords');

        $delimiter = ',';
        if (file_exists($file) || is_readable($file)) {

            if (($handle = fopen($file, 'r')) !== false) {
                DB::beginTransaction();
                while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                    $data = array_combine($header, $row);
                    if ($data['id'] != 'id') {

                        if ($data['elevation_ft'] == '') {
                            $data['elevation_ft'] = 0;
                        }

                        $result = $this->container->call([$airportRepository, 'getByIdent'], ['ident' => $data['ident']]);

                        $airportParam->setId($data['id']);
                        $airportParam->setIdent($data['ident']);
                        $airportParam->setType($data['type']);
                        $airportParam->setName($data['name']);
                        $airportParam->setLatitudeDeg($data['latitude_deg']);
                        $airportParam->setLongitudeDeg($data['longitude_deg']);
                        $airportParam->setElevationFt($data['elevation_ft']);
                        $airportParam->setContinent($data['continent']);
                        $airportParam->setIsoCountry($data['iso_country']);
                        $airportParam->setIsoRegion($data['iso_region']);
                        $airportParam->setMunicipality($data['municipality']);
                        $airportParam->setScheduledService($data['scheduled_service']);
                        $airportParam->setGpsCode($data['gps_code']);
                        $airportParam->setIataCode($data['iata_code']);
                        $airportParam->setLocalCode($data['local_code']);
                        $airportParam->setHomeLink($data['home_link']);
                        $airportParam->setWikipediaLink($data['wikipedia_link']);
                        $airportParam->setKeywords($data['keywords']);

                        if ($result == null) {

                            $this->container->call([$airportRepository, 'store'], ['airportParam' => $airportParam]);
                        } else {
                            $this->container->call([$airportRepository, 'updateByIdent'], ['ident' => $data['ident'], 'airportParam' => $airportParam]);
                        }
                    }
                }
                DB::commit();
                fclose($handle);
            }
        }
    }
}