<?php
/**
 * Created by PhpStorm.
 * User: azole
 * Date: 2018/6/10
 * Time: 11:31
 */
use Carbon\Carbon;

class Period
{
    private $startDate;
    private $endDate;

    /**
     * @return Carbon
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return Carbon
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    

    /**
     * Period constructor.
     */
    public function __construct($startDate, $endDate)
    {
        if ($endDate < $startDate) {
            throw new Exception('Invalid date');
        }

        $this->startDate = new Carbon($startDate);
        $this->endDate = new Carbon($endDate);
    }

    /**
     * @param $this
     * @param $budget
     * @return int
     * @throws Exception
     */
    public function overlayDays(Period $otherPeriod)
    {
        if($this->isNoOverlap($otherPeriod)) {
            return 0;
        }

        $overlapStartDate = $this->getStartDate() > $otherPeriod->getStartDate()
            ? $this->getStartDate()
            : $otherPeriod->getStartDate();

        $overlapEndDate = $this->getEndDate() < $otherPeriod->getEndDate()
            ? $this->getEndDate()
            : $otherPeriod->getEndDate();

        return $overlapEndDate->diffInDays($overlapStartDate) + 1;
    }

    /**
     * @param Period $otherPeriod
     * @return bool
     */
    private function isNoOverlap(Period $otherPeriod)
    {
        return $otherPeriod->getEndDate() < $this->getStartDate() || $otherPeriod->getStartDate() > $this->getEndDate();
    }
}