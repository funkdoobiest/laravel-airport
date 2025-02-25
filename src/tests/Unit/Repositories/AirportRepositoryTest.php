<?php
/**
 * Created by PhpStorm.
 * User: dyangalih
 * Date: 2019-02-14
 * Time: 02:28
 */

namespace WebAppId\Airport\Tests\Unit\Repositories;

use Illuminate\Contracts\Container\BindingResolutionException;
use WebAppId\Airport\Models\Airport;
use WebAppId\Airport\Repositories\AirportRepository;
use WebAppId\Airport\Services\Params\AirportParam;
use WebAppId\Airport\Tests\TestCase;

/**
 * @author: Dyan Galih<dyan.galih@gmail.com>
 * Date: 07/01/20
 * Time: 18.09
 * Class AirportRepositoryTest
 * @package WebAppId\Airport\Tests\Unit\Repositories
 */
class AirportRepositoryTest extends TestCase
{
    /**
     * @var AirportRepository
     */
    private $airportRepository;

    /**
     * AirportRepositoryTest constructor.
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        try {
            $this->airportRepository = $this->getContainer()->make(AirportRepository::class);
        } catch (BindingResolutionException $e) {
            report($e);
        }
    }
    
    public function dummyData(int $id)
    {
        $airportParam = new AirportParam();
        $airportParam->setId($id+1000000);
        $airportParam->setIdent($this->getFaker()->text(10));
        $airportParam->setType($this->getFaker()->text(20));
        $airportParam->setName($this->getFaker()->text(75));
        $airportParam->setLatitudeDeg($this->getFaker()->latitude);
        $airportParam->setLongitudeDeg($this->getFaker()->longitude);
        $airportParam->setElevationFt($this->getFaker()->numberBetween(1, 20));
        $airportParam->setContinent($this->getFaker()->text(5));
        $airportParam->setIsoCountry($this->getFaker()->countryISOAlpha3);
        $airportParam->setIsoRegion($this->getFaker()->countryISOAlpha3);
        $airportParam->setMunicipality($this->getFaker()->text(15));
        $airportParam->setScheduledService($this->getFaker()->text(5));
        $airportParam->setGpsCode($this->getFaker()->text(5));
        $airportParam->setIataCode($this->getFaker()->text(5));
        $airportParam->setLocalCode($this->getFaker()->text(20));
        $airportParam->setHomeLink($this->getFaker()->url);
        $airportParam->setWikipediaLink($this->getFaker()->url);
        $airportParam->setKeywords($this->getFaker()->text(200));
        
        return $airportParam;
    }
    
    private function createData($dummy)
    {
        return $this->getContainer()->call([$this->airportRepository, 'store'], ['airportParam' => $dummy]);
    }
    
    public function testAddRepository(): ?Airport
    {
        $dummy = $this->dummyData($this->getFaker()->randomNumber());
        $result = $this->createData($dummy);
        
        self::assertNotEquals(null, $result);
        self::assertEquals($dummy->getId(), $result->id);
        self::assertEquals($dummy->getIdent(), $result->ident);
        self::assertEquals($dummy->getType(), $result->type);
        self::assertEquals($dummy->getName(), $result->name);
        self::assertEquals($dummy->getLatitudeDeg(), $result->latitude_deg);
        self::assertEquals($dummy->getLongitudeDeg(), $result->longitude_deg);
        self::assertEquals($dummy->getElevationFt(), $result->elevation_ft);
        self::assertEquals($dummy->getContinent(), $result->continent);
        self::assertEquals($dummy->getIsoCountry(), $result->iso_country);
        self::assertEquals($dummy->getIsoRegion(), $result->iso_region);
        self::assertEquals($dummy->getMunicipality(), $result->municipality);
        self::assertEquals($dummy->getScheduledService(), $result->scheduled_service);
        self::assertEquals($dummy->getGpsCode(), $result->gps_code);
        self::assertEquals($dummy->getIataCode(), $result->iata_code);
        self::assertEquals($dummy->getLocalCode(), $result->local_code);
        self::assertEquals($dummy->getHomeLink(), $result->home_link);
        self::assertEquals($dummy->getWikipediaLink(), $result->wikipedia_link);
        self::assertEquals($dummy->getKeywords(), $result->keywords);
        return $result;
    }
    
    public function testFindAirportByIdent()
    {
        $data = $this->testAddRepository();
        $result = $this->getContainer()->call([$this->airportRepository, 'getByIdent'], ['ident' => $data->ident]);
        self::assertNotEquals(null, $result);
    }
    
    public function testUpdateAirportByIdent()
    {
        $data = $this->testAddRepository();
        $newData = $this->dummyData($this->getFaker()->randomNumber());
        $newData->setIdent($data->ident);
        $result = $this->getContainer()->call([$this->airportRepository, 'updateByIdent'], ['ident' => $data->ident, 'airportParam' => $newData]);
        
        self::assertNotEquals(null, $result);
        self::assertEquals($newData->getId(), $result->id);
        self::assertEquals($newData->getType(), $result->type);
        self::assertEquals($newData->getName(), $result->name);
        self::assertEquals($newData->getLatitudeDeg(), $result->latitude_deg);
        self::assertEquals($newData->getLongitudeDeg(), $result->longitude_deg);
        self::assertEquals($newData->getElevationFt(), $result->elevation_ft);
        self::assertEquals($newData->getContinent(), $result->continent);
        self::assertEquals($newData->getIsoCountry(), $result->iso_country);
        self::assertEquals($newData->getIsoRegion(), $result->iso_region);
        self::assertEquals($newData->getMunicipality(), $result->municipality);
        self::assertEquals($newData->getScheduledService(), $result->scheduled_service);
        self::assertEquals($newData->getGpsCode(), $result->gps_code);
        self::assertEquals($newData->getIataCode(), $result->iata_code);
        self::assertEquals($newData->getLocalCode(), $result->local_code);
        self::assertEquals($newData->getHomeLink(), $result->home_link);
        self::assertEquals($newData->getWikipediaLink(), $result->wikipedia_link);
        self::assertEquals($newData->getKeywords(), $result->keywords);
    }
    
    /**
     * @return array
     */
    public function bulkData(): array
    {
        $random = $this->getFaker()->numberBetween(40, 100);
        $result = [];
        for ($i = 0; $i < $random; $i++) {
            $dummy = $this->dummyData($i);
            $result[] = $this->createData($dummy);
        }
        
        return $result;
    }
    
    public function testGetAirportLike()
    {
        $this->bulkData();
        
        $key = ['a', 'i', 'u', 'e', 'o'];
        
        $randomIndexKey = $this->getFaker()->numberBetween(0, count($key) - 1);
        
        $count = $this->getContainer()->call([$this->airportRepository,'getByNameLike'], ['q' => $key[$randomIndexKey]]);
    
        self::assertGreaterThanOrEqual(1, $count);
    }
    
    public function testGetAllAirportByCountry()
    {
        
        $key = ['AD','AE','AF','AG','AI','AL','AM','AO','AQ','AR','AS','AT','AU','AW','AZ','BA','BB','BD','BE','BF','BG','BH','BI','BJ','BL','BM','BN','BO','BQ','BR','BS','BT','BW','BY','BZ','CA','CC','CD','CF','CG','CH','CI','CK','CL','CM','CN','CO','CR','CU','CV','CW','CX','CY','CZ','DE','DJ','DK','DM','DO','DZ','EC','EE','EG','EH','ER','ES','ET','FI','FJ','FK','FM','FO','FR','GA','GB','GD','GE','GF','GG','GH','GI','GL','GM','GN','GP','GQ','GR','GT','GU','GW','GY','HK','HN','HR','HT','HU','ID','IE','IL','IM','IN','IO','IQ','IR','IS','IT','JE','JM','JO','JP','KE','KG','KH','KI','KM','KN','KP','KR','KW','KY','KZ','LA','LB','LC','LI','LK','LR','LS','LT','LU','LV','LY','MA','MC','MD','ME','MF','MG','MH','MK','ML','MM','MN','MO','MP','MQ','MR','MS','MT','MU','MV','MW','MX','MY','MZ','NA','NC','NE','NF','NG','NI','NL','NO','NP','NR','NU','NZ','OM','PA','PE','PF','PG','PH','PK','PL','PM','PR','PS','PT','PW','PY','QA','RE','RO','RS','RU','RW','SA','SB','SC','SD','SE','SG','SH','SI','SK','SL','SM','SN','SO','SR','SS','ST','SV','SX','SY','SZ','TC','TD','TF','TG','TH','TJ','TL','TM','TN','TO','TR','TT','TV','TW','TZ','UA','UG','UM','US','UY','UZ','VA','VC','VE','VG','VI','VN','VU','WF','WS','XK','YE','YT','ZA','ZM','ZW','ZZ'];
        
        $randomIndexKey = $this->getFaker()->numberBetween(0, count($key) - 1);
        
        $result = $this->getContainer()->call([$this->airportRepository, 'getByNameLike'], ['q' => '', 'countryCode' => $key[$randomIndexKey]]);
        
        $count = $this->getContainer()->call([$this->airportRepository, 'getAllByNameLikeCount'], ['countryCode' => $key[$randomIndexKey]]);
        
        self::assertGreaterThanOrEqual(1, count($result));
        
        self::assertGreaterThanOrEqual(1, $count);
    }
    
    public function testGetAirportByIataCode(){
        $key = ['HIR','MUA','INU','MJR','CCT','BUA','CMU','DAU','GKA','GUR','PNP','HKN','KMA','KVG','MAG','HGU','MDU','MAS','LAE','POM','RAB','VAI','WBM','WWK','LLK','TGV','ROU','JAM','UAK','GOH'];
    
        $randomIndexKey = $this->getFaker()->numberBetween(0, count($key) - 1);
        
        $result =  $this->getContainer()->call([$this->airportRepository, 'getByIataCode'],['iataCode' => $key[$randomIndexKey]]);
        
        self::assertNotEquals(null, $result);
    }
}