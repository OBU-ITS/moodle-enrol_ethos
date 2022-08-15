<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_person_info;
use enrol_ethos\ethosclient\entities\ethos_section_instructors_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_section_instructors_provider extends ethos_provider
{
    const VERSION = 'v10';
    const PATH = 'section-instructors';

    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider(self::PATH, self::VERSION);
    }

    private static ?ethos_section_instructors_provider $instance = null;
    public static function getInstance() : ethos_section_instructors_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($id) : ?ethos_section_instructors_info {
        $item = $this->getFromEthosById($id);

        return $this->convert($item);
    }

    /**
     * @param $limit
     * @param $offset
     * @return ethos_section_instructors_info[]
     */
    public function getBatch($limit, $offset) : array {
        $items = $this->getFromEthos(null, true, $limit, $offset);

        return array_map(array($this, 'convert'), $items);
    }

    /**
     * @return ethos_section_instructors_info[]
     */
    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    /**
     * @param $instructorId
     * @return ethos_person_info[]
     */
    public function getByInstructorPersonGuid($instructorId) : array {
        $url = $this->buildUrlWithCriteria('{"instructor":"' . $instructorId . '"}');
        $items = $this->getFromEthos($url);
        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_section_instructors_info {
        return new ethos_section_instructors_info($item);
    }
}
