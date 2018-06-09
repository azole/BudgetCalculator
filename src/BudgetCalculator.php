<?php

use Carbon\Carbon;

require __DIR__ . '/BudgetModel.php';

class BudgetCalculator
{
    /** @var BudgetModel $model */
    private $model;

    /**
     * BudgetCalculator constructor.
     */
    private $budgetDateFormat = 'Ym';

    public function __construct($model = null)
    {
        $this->model = $model ?: new BudgetModel();
    }

    public function calculate($startDateString, $endDateString)
    {
        $startDate = $this->convertToDateObject($startDateString);
        $endDate = $this->convertToDateObject($endDateString);

        if ( ! $this->isValidDatePeriod($startDate, $endDate)) {
            throw new Exception('Invalid date');
        }

        $monthBudgets = $this->model->query();

        $keys = $this->calculateMonthKeys($startDate, $endDate);

        $sum = 0;
        foreach ($keys as $i => $key) {
            // get the budge of current month
            $monthBudget = isset($monthBudgets[$key]) ? $monthBudgets[$key] : 0;

            if ($i == 0) {
                // first month
                // check if startDate and endDate is same month
                $monthEndDate = $key == $endDate->format($this->budgetDateFormat) ? $endDate : $startDate->copy()->lastOfMonth();

                $sum += $monthBudget * $this->calculateRatio($startDate, $monthEndDate);
            } elseif ($i == count($keys) - 1) {
                // last month
                $sum += $monthBudget * $this->calculateRatio($endDate->copy()->startOfMonth(), $endDate);
            } else {
                $sum += $monthBudget;
            }
        }

        return $sum;
    }

    /**
     * @param $end
     * @param $start
     * @return float|int
     *
     */
    private function calculateRatio($start, $end)
    {
        return ($end->diffInDays($start) + 1) / $start->daysInMonth;
    }

    private function isValidDatePeriod(Carbon $startDate, Carbon $endDate)
    {
        return $endDate >= $startDate;
    }

    private function notDone(Carbon $iterator, Carbon $end)
    {
        return $iterator->format($this->budgetDateFormat) <= $end->format($this->budgetDateFormat);
    }

    /**
     * @param $dateString
     * @return Carbon
     */
    private function convertToDateObject($dateString)
    {
        return new Carbon($dateString);
    }

    /**
     * @param $iterator
     * @param $endDate
     * @param $keys
     * @return array
     */
    public function calculateMonthKeys($startDate, $endDate)
    {
        $keys = [];
        $iterator = $startDate->copy();
        while ($this->notDone($iterator, $endDate)) {
            $keys[] = $iterator->format($this->budgetDateFormat);

            $iterator = $iterator->addMonth(1);
        }
        return $keys;
    }
}