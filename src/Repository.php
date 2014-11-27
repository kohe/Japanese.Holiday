<?php


namespace Japanese\Holiday;

use Japanese\Holiday\Calculator\CalculatorAggregate;
use Symfony\Component\Yaml\Yaml;

class Repository
{
    /**
     * @var array
     */
    private $holidayCollection;

    /**
     * @var AnnualCalculator
     */
    private $calculator;

    /**
     * @var string
     *
     * path to directory where config files are located
     */
    private $configBasePath;

    /**
     * @param string|null $configBasePath
     * @param CalculatorAggregate|null $calculator
     */
    public function __construct($configBasePath = null, CalculatorAggregate $calculator = null)
    {
        $this->holidayCollection = [];
        $this->configBasePath = $configBasePath ? $configBasePath : __DIR__.'/Resources/config';
        $this->calculator = $calculator ? $calculator : $this->createCalculator();
    }

    public function getHolidaysForYear($year = null)
    {
        if(!isset($this->holidayCollection[$year]))
        {
            $this->loadHolidaysForYear($year);
        }

        return $this->holidayCollection[$year];
    }

    public function isHoliday($date)
    {
        $year = date('Y', strtotime($date));
        if(!isset($this->holidayCollection[$year]))
        {
            $this->loadHolidaysForYear($year);
        }

        return isset($this->holidayCollection[$year][$date]);
    }

    public function getHolidayName($date)
    {
        if ($this->isHoliday($date)) {
            $ts = strtotime($date);
            if ($ts) {
                $year = date('Y', $ts);
                return $this->holidayCollection[$year][$date];
            }
        }
    }

    /**
     * @return AnnualCalculator
     */
    private function createCalculator()
    {
        $calculator = new AnnualCalculator(Yaml::parse(file_get_contents($this->configBasePath.'/holidays.yml')));

        return $calculator;
    }

    /**
     * @param int $year
     */
    private function loadHolidaysForYear($year)
    {
        $this->holidayCollection[$year] = $this->calculator->computeDates($year);
    }
} 
