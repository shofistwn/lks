<?php
class Calendar
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->naviHref = htmlentities($_SERVER['PHP_SELF']);
    }

    /********************* PROPERTY ********************/
    private $dayLabels = array("Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun");

    private $currentYear = 0;

    private $currentMonth = 0;

    private $currentDay = 0;

    private $currentDate = null;

    private $daysInMonth = 0;

    private $naviHref = null;

    /********************* PUBLIC **********************/

    /**
     * print out the calendar
     */
    public function show()
    {
        $year  = null;

        $month = null;

        if (null == $year && isset($_GET['year'])) {

            $year = $_GET['year'];
        } else if (null == $year) {

            $year = date("Y", time());
        }

        if (null == $month && isset($_GET['month'])) {

            $month = $_GET['month'];
        } else if (null == $month) {

            $month = date("m", time());
        }

        $this->currentYear = $year;

        $this->currentMonth = $month;

        $this->daysInMonth = $this->_daysInMonth($month, $year);

        $content = '<div class="custom-calendar-wrap">' .
            '<div class="custom-inner">' .
            $this->_createNavi() .
            '<div id="calendar" class="fc-calendar-container">' .
            '<div class="fc-calendar fc-five-rows">' .
            '<div class="fc-head">' . $this->_createLabels() . '</div>';
        $content .= '<div class="fc-body">';

        $weeksInMonth = $this->_weeksInMonth($month, $year);
        // Create weeks in a month
        for ($i = 0; $i < $weeksInMonth; $i++) {

            //Create days in a week
            for ($j = 1; $j <= 7; $j++) {
                $content .= $this->_showDay($i * 7 + $j);
            }
        }

        $content .= '</div>';

        $content .= '</div>';
        $content .= '</div>';

        $content .= '</div>';
        return $content;
    }

    /********************* PRIVATE **********************/
    /**
     * create the li element for ul
     */
    private function _showDay($cellNumber)
    {
        if ($this->currentDay == 0) {

            $firstDayOfTheWeek = date('N', strtotime($this->currentYear . '-' . $this->currentMonth . '-01'));

            if (intval($cellNumber) == intval($firstDayOfTheWeek)) {

                $this->currentDay = 1;
            }
        }

        if (($this->currentDay != 0) && ($this->currentDay <= $this->daysInMonth)) {

            $this->currentDate = date('Y-m-d', strtotime($this->currentYear . '-' . $this->currentMonth . '-' . ($this->currentDay)));

            $cellContent = $this->currentDay;

            $this->currentDay++;
        } else {

            $this->currentDate = null;

            $cellContent = null;
        }

        // mereturn hari
        if ($cellNumber % 7 == 1) {
            return '<div class="fc-row"><div><span class="fc-date">' . $cellContent . '</span></div>';
        } else if ($cellNumber % 7 == 0) {
            return '<div><span class="fc-date">' . $cellContent . '</span></div></div>';
        } elseif ($this->currentDate == date('Y-m-d')) {
            // jika tanggal hari ini, maka beri highlight
            return '<div class="fc-today"><span class="fc-date">' . $cellContent . '</span></div>';
        }

        return '<div><span class="fc-date">' . $cellContent . '</span></div>';
    }

    /**
     * create navigation
     */
    private function _createNavi()
    {

        $nextMonth = $this->currentMonth == 12 ? 1 : intval($this->currentMonth) + 1;

        $nextYear = $this->currentMonth == 12 ? intval($this->currentYear) + 1 : $this->currentYear;

        $preMonth = $this->currentMonth == 1 ? 12 : intval($this->currentMonth) - 1;

        $preYear = $this->currentMonth == 1 ? intval($this->currentYear) - 1 : $this->currentYear;

        return
            '<div class="custom-header clearfix">
            <nav>
                <a href="' . $this->naviHref . '?month=' . sprintf('%02d', $preMonth) . '&year=' . $preYear . '" class="custom-btn custom-prev"></a>
                <a href="' . $this->naviHref . '?month=' . sprintf("%02d", $nextMonth) . '&year=' . $nextYear . '" class="custom-btn custom-next"></a>
            </nav>
            <h2 id="custom-month" class="custom-month">
            ' . date('M', strtotime($this->currentYear . '-' . $this->currentMonth . '-1')) . '
            </h2>
            <h3 id="custom-year" class="custom-year">
            ' . date('Y', strtotime($this->currentYear . '-' . $this->currentMonth . '-1')) . '
            </h3>
        </div>';
    }

    /**
     * create calendar week labels
     */
    private function _createLabels()
    {
        $content = '';
        foreach ($this->dayLabels as $index => $label) {
            // minggu
            $content .= '<div>' . $label . '</div>';
        }

        return $content;
    }



    /**
     * calculate number of weeks in a particular month
     */
    private function _weeksInMonth($month = null, $year = null)
    {
        if (null == ($year)) {
            $year =  date("Y", time());
        }

        if (null == ($month)) {
            $month = date("m", time());
        }

        // find number of days in this month
        $daysInMonths = $this->_daysInMonth($month, $year);

        $numOfweeks = ($daysInMonths % 7 == 0 ? 0 : 1) + intval($daysInMonths / 7);

        $monthEndingDay = date('N', strtotime($year . '-' . $month . '-' . $daysInMonths));

        $monthStartDay = date('N', strtotime($year . '-' . $month . '-01'));

        if ($monthEndingDay < $monthStartDay) {

            $numOfweeks++;
        }

        return $numOfweeks;
    }

    /**
     * calculate number of days in a particular month
     */
    private function _daysInMonth($month = null, $year = null)
    {
        if (null == ($year))
            $year =  date("Y", time());

        if (null == ($month))
            $month = date("m", time());

        return date('t', strtotime($year . '-' . $month . '-01'));
    }
}
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PHP Calendar</title>
    <link rel="stylesheet" href="calendar.css">
</head>

<body>

    <?php
    $calendar = new Calendar(date("Y m d"));
    echo $calendar->show();
    ?>
</body>

</html>