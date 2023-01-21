<?php
defined('BASEPATH') or exit('No direct script access allowed');

class HomeModel extends CI_Model
{

    function getVisitors()
    {
        $thisYear = date('Y');
        $prevYear = intval(($thisYear) - 1);

        $thisMonth = date('m');
        $prevMonth = intval(($thisMonth) - 1);

        $thisDay = date('d');
        $yesterday = intval(date('d')) - 1;
        // $thisWeek = intval($today) - 7;

        $annual = $this->db->query("SELECT COUNT(*) AS total FROM `visitors` WHERE YEAR(attempt) = '$thisYear'")->result();
        $yearBefore = $this->db->query("SELECT COUNT(*) AS total FROM `visitors` WHERE YEAR(attempt) = '$prevYear'")->result();

        $monthly = $this->db->query("SELECT COUNT(*) AS total FROM `visitors` WHERE YEAR(attempt) = '$thisYear' AND MONTH(attempt) = '$thisMonth'")->result();
        $monthBefore = $this->db->query("SELECT COUNT(*) AS total FROM `visitors` WHERE YEAR(attempt) = '$thisYear' AND MONTH(attempt) = '$prevMonth'")->result();

        // $weekly = $this->db->query("SELECT COUNT(*) AS total FROM `visitors` WHERE attempt BETWEEN '$thisYear-$thisMonth-$thisWeek' AND '$thisYear-$thisMonth-$today'")->result();

        $today = $this->db->query("SELECT COUNT(*) AS total FROM `visitors` WHERE YEAR(attempt) = '$thisYear' AND MONTH(attempt) = '$thisMonth' AND DAY(attempt) = '$thisDay'")->result();
        $yesterDay = $this->db->query("SELECT COUNT(*) AS total FROM `visitors` WHERE YEAR(attempt) = '$thisYear' AND MONTH(attempt) = '$thisMonth' AND DAY(attempt) = '$yesterday'")->result();

        if (intval($today[0]->total) == 0) {
            $dailyProgress = 0;
        } else {
            $dailyProgress = intval($yesterDay[0]->total) * 100 / intval($today[0]->total);
        }

        if (intval($monthly[0]->total) == 0) {
            $monthlyProgress = 0;
        } else {
            $monthlyProgress = intval($monthBefore[0]->total) * 100 / intval($monthly[0]->total);
        }

        // $monthlyProgress = intval($monthBefore[0]->total) * 100 / intval($monthly[0]->total);

        if (intval($annual[0]->total) == 0) {
            $annualProgress = 0;
        } else {
            $annualProgress = intval($yearBefore[0]->total) * 100 / intval($annual[0]->total);
        }
        
        // $annualProgress = intval($yearBefore[0]->total) * 100 / intval($annual[0]->total);


        if ($yesterDay > $today) {
            $dailyProgress *= -1;
        }

        if ($monthBefore > $monthly) {
            $monthlyProgress *= -1;
        }

        if ($yearBefore > $annual) {
            $annualProgress *= -1;
        }

        $dataVisit = [
            'annual' => $annual[0]->total,
            'monthly' => $monthly[0]->total,
            'daily' => $today[0]->total,
            'yesterday' => $yesterDay[0]->total,
            'progress' => [
                'annual_progress' => intval($annualProgress),
                'monthly_progress' => intval($monthlyProgress),
                'daily_progress' => intval($dailyProgress)
            ]
        ];

        return $dataVisit;
    }

    function insertVisitors($date)
    {
        return $this->db->insert('visitors', ['attempt' => $date]);
    }

    function getRangeVisitors()
    {
        $today = date('Y-m-d');
        $lastWeek = date(('Y-m-d'), strtotime("-1 week"));
        $lastMonth = date(('Y-m-d'), strtotime("-1 month"));
        $lastYear = date(('Y-m-d'), strtotime("-1 year"));

        $weekly = $this->db->query("SELECT attempt AS day, COUNT(*) AS amount FROM `visitors` WHERE attempt BETWEEN '$lastWeek' AND '$today' GROUP BY DAY(attempt);")->result();

        $monthly = $this->db->query("SELECT attempt AS day, COUNT(*) AS amount FROM `visitors` WHERE attempt BETWEEN '$lastMonth' AND '$today' GROUP BY DAY(attempt);")->result();
        
        $annually = $this->db->query("SELECT attempt AS day, COUNT(*) AS amount FROM `visitors` WHERE attempt BETWEEN '$lastYear' AND '$today' GROUP BY DAY(attempt);")->result();

        $data = [
            'weekly' => $weekly,
            'monthly' => $monthly,
            'annually' => $annually
        ];

        return $data;
    }
}
