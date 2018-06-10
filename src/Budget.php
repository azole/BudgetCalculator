<?php
/**
 * Created by PhpStorm.
 * User: azole
 * Date: 2018/6/10
 * Time: 11:45
 */

use Carbon\Carbon;

class Budget
{
    private $amount;
    private $yearMonth;

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getYearMonth()
    {
        return $this->yearMonth;
    }

    /**
     * @return mixed
     */
    public function getFirstDay()
    {
        return Carbon::parse($this->getYearMonth() . '01');
    }

    /**
     * @return mixed
     */
    public function getLastDay()
    {
        return Carbon::parse($this->getYearMonth() . $this->daysInMonth());
    }

    /**
     * Budget constructor.
     */
    public function __construct($yearMonth, $amount)
    {
        $this->yearMonth = $yearMonth;
        $this->amount = $amount;
    }

    private function daysInMonth()
    {
        return Carbon::parse($this->getYearMonth() . '01')->daysInMonth;
    }

    private function dailyAmount()
    {
        return $this->getAmount() / $this->daysInMonth();
    }

    public function effectiveAmount(Period $period)
    {
        return $this->dailyAmount() * $period->overlayDays($this->period());
    }

    private function period()
    {
        return new Period($this->getFirstDay(), $this->getLastDay());
    }
}