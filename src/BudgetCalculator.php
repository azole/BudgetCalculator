<?php

/*
 *
    # 預算查詢

    201806 300 --> 不會有小數點，不會有負數
    201808 360

    yyyymmdd


    20180615 - 20180715
      16        15

    300 x 16/30 + 0 * 15 = 160

    每個月的天數都不同，2月也要判斷閏年

    不需要判斷輸入格式問題

    時間因素，先不考慮小數點
 *
 * */

use Carbon\Carbon;

require __DIR__ . '/BudgetModel.php';
require __DIR__ . '/Period.php';
require __DIR__ . '/Budget.php';

class BudgetCalculator
{
    /** @var BudgetModel $model */
    private $model;


    public function __construct($model = null)
    {
        $this->model = $model ?: new BudgetModel();
    }

    public function calculate($startDateString, $endDateString)
    {
        $period = new Period($startDateString, $endDateString);

        $monthBudgets = $this->model->query();
        $budgets = array_map(function($value, $yearMonth) {
            return new Budget($yearMonth, $value);
        }, $monthBudgets, array_keys($monthBudgets));

        return array_reduce($budgets, function($carry, $budget) use($period) {
            return $carry + $budget->effectiveAmount($period);
        }, 0);
    }

    public function findBudget($monthBudgets, Carbon $currentMonth)
    {
        foreach($monthBudgets as $budget) {
            if($budget->getYearMonth() == $currentMonth->format('Ym')) {
                return $budget;
            }
        }
        return null;
    }


}