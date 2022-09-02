<?php
/*
    SELECT email, priceName, COUNT(email) as paymentTimes
    FROM `paymentActions`
    WHERE priceName LIKE '%1 month%'
    GROUP BY email, priceName
    HAVING paymentTimes >= 2;

    SELECT email, priceName, COUNT(email) as paymentTimes
    FROM `paymentActions`
    WHERE priceName LIKE '%1 month%'
    GROUP BY email, priceName;

    SELECT email, COUNT(email) as paymentTimes
    FROM `paymentActions`
    WHERE priceName LIKE '%1 month%'
    GROUP BY email
    HAVING paymentTimes >= 2;

    SELECT email
    FROM `paymentActions`
    WHERE priceName LIKE '%1 month%'
    GROUP BY email
    HAVING COUNT(email) >= 2;

    SELECT email, priceName, pricePeriod
    FROM `paymentActions`
    WHERE email IN (
        SELECT email
        FROM `paymentActions`
        WHERE priceName LIKE '%1 month%'
        GROUP BY email
        HAVING COUNT(email) >= 2
    );

    SELECT email
    FROM `paymentActions`
    WHERE priceName LIKE '%1 month%'
    AND email IN (
        SELECT email
        FROM `paymentActions`
        WHERE priceName LIKE '%trial%'
        GROUP BY email
    )
    GROUP BY email
    HAVING COUNT(email) = 2;


// trial-to-1-month
    SELECT DISTINCT email
    FROM `paymentActions`
    WHERE priceName LIKE '%1 month%'
    AND email IN (
        SELECT email
        FROM `paymentActions`
        WHERE priceName LIKE '%trial%'
        GROUP BY email
    );

    // trial-to-1-month
    SELECT DISTINCT email
    FROM `paymentActions`
    WHERE priceName LIKE '%1 month%'
    AND email IN (
        SELECT email
        FROM `paymentActions`
        WHERE priceName LIKE '%trial%'
        GROUP BY email
    );

// 3 month to 4 month
    SELECT email
    FROM `paymentActions`
    WHERE priceName LIKE '%1 month%'
    AND email IN (
        SELECT email
        FROM `paymentActions`
        WHERE priceName LIKE '%1 month%'
        GROUP BY email
        HAVING COUNT(email) >= 3
    )
    OR email IN (
        SELECT DISTINCT email
        FROM `paymentActions`
        WHERE priceName LIKE '%3 month%'
    )
    GROUP BY email
    HAVING COUNT(email) >= 4;

*/

use backend\models\PaymentActions;

?>


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>


<body style="font-family: Arial,serif;">


    <?php
        $trialCount = PaymentActions::find()->select('email')->where(['like', 'priceName', 'trial'])->distinct()->count();
        $trial_to_1_month = Yii::$app->db->createCommand("SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%trial%' GROUP BY email)")->queryAll();
        $trial_to_2_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%trial%' GROUP BY email) GROUP BY email HAVING COUNT(email) = 2")->queryAll();
        $trial_to_3_month = Yii::$app->db->createCommand("SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%1 month%' AND `email` IN (SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%trial%' GROUP BY `email`) GROUP BY `email` HAVING COUNT(`email`) = 3")->queryAll();
        $trial_to_3_month_3p = Yii::$app->db->createCommand("SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%3 month%' AND `email` IN (SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%trial%' GROUP BY `email`)")->queryAll();
        $trial_to_4_month = Yii::$app->db->createCommand("SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%1 month%' AND `email` IN (SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%trial%' GROUP BY `email`) GROUP BY `email` HAVING COUNT(`email`) = 4")->queryAll();
        $trial_to_5_month = Yii::$app->db->createCommand("SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%1 month%' AND `email` IN (SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%trial%' GROUP BY `email`) GROUP BY `email` HAVING COUNT(`email`) = 5")->queryAll();
        $trial_to_6_month = Yii::$app->db->createCommand("SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%1 month%' AND `email` IN (SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%trial%' GROUP BY `email`) GROUP BY `email` HAVING COUNT(`email`) = 6")->queryAll();
        $trial_to_6_month_6p = Yii::$app->db->createCommand("SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%1 month%' AND `email` IN (SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%trial%' GROUP BY `email`) GROUP BY `email` HAVING COUNT(`email`) = 6")->queryAll();

        $distinct_1_month_count = PaymentActions::find()->select('email')->where(['like', 'priceName', '1 month'])->distinct()->count();
        $distinct_1_month_to_2_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%') GROUP BY email HAVING COUNT(email) >= 2")->queryAll();
        $distinct_1_month_to_3_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%') GROUP BY email HAVING COUNT(email) >= 3")->queryAll();
        $distinct_1_month_to_3_month_3p = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%3 month%' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%')")->queryAll();
        $distinct_1_month_to_4_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%') GROUP BY email HAVING COUNT(email) >= 4")->queryAll();
        $distinct_1_month_to_5_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%') GROUP BY email HAVING COUNT(email) >= 5")->queryAll();
        $distinct_1_month_to_6_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%') GROUP BY email HAVING COUNT(email) >= 6")->queryAll();
        $distinct_1_month_to_6_month_6p = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%6 month%' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%')")->queryAll();

        $distinct_2_month_count = sizeof(Yii::$app->db->createCommand("SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 2")->queryAll());
        $distinct_2_month_to_3_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN ( SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 2 ) GROUP BY email HAVING COUNT(email) >= 3")->queryAll();
        $distinct_2_month_to_3_month_3p = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%3 month%' AND email IN ( SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 2)")->queryAll();
        $distinct_2_month_to_4_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN ( SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 2 ) GROUP BY email HAVING COUNT(email) >= 4")->queryAll();
        $distinct_2_month_to_5_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN ( SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 2 ) GROUP BY email HAVING COUNT(email) >= 5")->queryAll();
        $distinct_2_month_to_6_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN ( SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 2 ) GROUP BY email HAVING COUNT(email) >= 6")->queryAll();
        $distinct_2_month_to_6_month_6p = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%6 month%' AND email IN ( SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 2 )")->queryAll();

        $distinct_3_month_count = sizeof(Yii::$app->db->createCommand("SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 3")->queryAll()) + sizeof(Yii::$app->db->createCommand("SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%3 month%'")->queryAll());
        $distinct_3_month_to_4_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 3) OR email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%3 month%') GROUP BY email HAVING COUNT(email) >= 4")->queryAll();
        $distinct_3_month_to_5_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 3) OR email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%3 month%') GROUP BY email HAVING COUNT(email) >= 5")->queryAll();
        $distinct_3_month_to_6_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 3) OR email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%3 month%') GROUP BY email HAVING COUNT(email) >= 6")->queryAll();
        $distinct_3_month_to_6_month_6p = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%6 month%' AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 3) OR email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%3 month%') GROUP BY email HAVING COUNT(email) >= 6")->queryAll();

        $distinct_4_month_count = sizeof(Yii::$app->db->createCommand("SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 4")->queryAll());
        $distinct_4_month_to_5_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 4) GROUP BY email HAVING COUNT(email) >= 5")->queryAll();
        $distinct_4_month_to_6_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 4) GROUP BY email HAVING COUNT(email) >= 6")->queryAll();
        $distinct_4_month_to_6_month_6p = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%6 month%' AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 4)")->queryAll();

        $distinct_5_month_count = sizeof(Yii::$app->db->createCommand("SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 5")->queryAll());
        $distinct_5_month_to_6_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 5) GROUP BY email HAVING COUNT(email) >= 6")->queryAll();
        $distinct_5_month_to_6_month_6p = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%6 month%' AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' GROUP BY email HAVING COUNT(email) >= 5)")->queryAll();

        //debug(sizeof($distinct_2_month_to_3_month));
        //debug(sizeof($distinct_2_month_to_3_month_3p));
        ?>
    <br>
    <div class="container-fluid w-100">
        <div class="row">
            <div class="col-12" align="center">
                <script src="https://code.highcharts.com/highcharts.js"></script>
                <script src="https://code.highcharts.com/modules/heatmap.js"></script>
                <script src="https://code.highcharts.com/modules/exporting.js"></script>

                <h2>Retention Cohort</h2>
                <div id="container" style="width: 1400px;height: 600px;"></div>

                <style>
                    .highcharts-container {
                        width: 100% !important;
                    }
                    .highcharts-label text {
                        font-size: 13px !important;
                    }
                    .highcharts-axis-labels text {
                        font-size: 14px !important;
                    }
                </style>
                <script>
                    function fetchNumber(txt = "") {
                        return txt.substring(
                            txt.indexOf("[") + 1,
                            txt.lastIndexOf("]")
                        );
                    }

                    Highcharts.chart('container', {
                        chart: {
                            type: 'heatmap',
                            marginTop: 40,
                            marginBottom: 80,
                            plotBorderWidth: 1
                        },

                        title: {
                            text: ''
                        },

                        credits: {
                            enabled: false
                        },
                        navigation: {
                            buttonOptions: {
                                enabled: false
                            }
                        },

                        xAxis: {
                            categories: ['1 month', '2 month', '3 month', '4 month', '5 month', '6 month'],
                            opposite: true,
                            title: {text: ''},
                            tickWidth: 1,
                        },

                        yAxis: {
                            categories: [
                                '5 month: [<?= $distinct_5_month_count ?>]',
                                '4 month: [<?= $distinct_4_month_count ?>]',
                                '3 month: [<?= $distinct_3_month_count ?>]',
                                '2 month: [<?= $distinct_2_month_count ?>]',
                                '1 month: [<?= $distinct_1_month_count ?>]',
                                'trial: [<?= $trialCount ?>]'
                            ],
                            title: {text: ''},
                            gridLineWidth: 1
                        },

                        colorAxis: {
                            min: 0,
                            minColor: '#FFFFFF',
                            maxColor: '#00a5f5',
                        },

                        legend: {
                            enabled: false
                        },

                        tooltip: {
                            formatter: function () {
                                console.log(this.point.x + ' - ' + this.point.y)
                                console.log(this.point.x + ' - ' + this.point.y)

                                return  '<br><b>' + Math.round((fetchNumber(this.series.yAxis.categories[this.point.y]) * this.point.value) / 100) + ' users</b><br>';
                                /*return '<b>' + this.series.xAxis.categories[this.point.x] + '</b>  <br><b>' +
                                    this.point.value + '%</b>  <br><b>' + this.series.yAxis.categories[this.point.y] + '</b>';*/
                            }
                        },

                        series: [{
                            name: 'Sales per employee',
                            borderWidth: 0,
                            data: [
                                [0, 0, 0],
                                [5, 0, 0],
                                [0, 1, 0],
                                [0, 2, 0],
                                [0, 3, 0],
                                [0, 4, 0],
                                [0, 5, <?= round(sizeof($trial_to_1_month) / $trialCount * 100) ?>],
                                [1, 5, <?= round(sizeof($trial_to_2_month) / $trialCount * 100) ?>],
                                [2, 5, <?= round((sizeof($trial_to_3_month) + sizeof($trial_to_3_month_3p)) / $trialCount * 100) ?>],
                                [3, 5, <?= round(sizeof($trial_to_4_month) / $trialCount * 100) ?>],
                                [4, 5, <?= round(sizeof($trial_to_5_month) / $trialCount * 100) ?>],
                                [5, 5, <?= round((sizeof($trial_to_6_month) + sizeof($trial_to_6_month_6p)) / $trialCount * 100) ?>],
                                [1, 0, 0],
                                [1, 1, 0],
                                [1, 2, 0],
                                [1, 3, 0],
                                [1, 4, <?= round(sizeof($distinct_1_month_to_2_month) / $distinct_1_month_count * 100) ?>],
                                [2, 0, 0],
                                [2, 1, 0],
                                [2, 2, 0],
                                [2, 3, <?= round( (sizeof($distinct_2_month_to_3_month) + sizeof($distinct_2_month_to_3_month_3p)) / $distinct_2_month_count * 100) ?>],
                                [2, 4, <?= round((sizeof($distinct_1_month_to_3_month) + sizeof($distinct_1_month_to_3_month_3p)) / $distinct_1_month_count * 100) ?>],
                                [3, 0, 0],
                                [3, 1, 0],
                                [3, 2, <?= round( sizeof($distinct_3_month_to_4_month) / $distinct_3_month_count * 100) ?>],
                                [3, 3, <?= round( sizeof($distinct_2_month_to_4_month) / $distinct_2_month_count * 100) ?>],
                                [3, 4, <?= round(sizeof($distinct_1_month_to_4_month) / $distinct_1_month_count * 100) ?>],
                                [4, 0, 0],
                                [4, 1, <?= round( sizeof($distinct_4_month_to_5_month) / $distinct_4_month_count * 100) ?>],
                                [4, 2, <?= round( sizeof($distinct_3_month_to_5_month) / $distinct_3_month_count * 100) ?>],
                                [4, 3, <?= round( sizeof($distinct_2_month_to_5_month) / $distinct_2_month_count * 100) ?>],
                                [5, 0, <?= round((sizeof($distinct_5_month_to_6_month) + sizeof($distinct_5_month_to_6_month_6p)) / $distinct_5_month_count * 100) ?>],
                                [5, 1, <?= round((sizeof($distinct_4_month_to_6_month) + sizeof($distinct_4_month_to_6_month_6p)) / $distinct_4_month_count * 100) ?>],
                                [5, 2, <?= round( (sizeof($distinct_3_month_to_6_month) + sizeof($distinct_3_month_to_6_month_6p)) / $distinct_3_month_count * 100) ?>],
                                [5, 3, <?= round( (sizeof($distinct_2_month_to_6_month) + sizeof($distinct_2_month_to_6_month_6p)) / $distinct_2_month_count * 100) ?>],
                                [4, 4, <?= round(sizeof($distinct_1_month_to_5_month) / $distinct_1_month_count * 100) ?>],
                                [5, 4, <?= round((sizeof($distinct_1_month_to_6_month) + sizeof($distinct_1_month_to_6_month_6p)) / $distinct_1_month_count * 100) ?>]
                            ],

                            dataLabels: {
                                enabled: true,
                                color: '#000000',
                                formatter: function() {
                                    console.log(this.point.value);
                                    if(this.point.value === 0) {
                                        return '';
                                    }
                                    return this.point.value;
                                }
                            }
                        }]
                    })
                </script>
            </div>
        </div>
    </div>


    <?php
        $xValues = '[';
        $trialToOneMonth = '[';
        $trialToTwoMonth = '[';
        $trialToThreeMonth = '[';
        $oneMonthToTwoMonth = '[';
        $oneMonthToThreeMonth = '[';
        $oneMonthToFourthMonth = '[';

        $trialToOneMonthGeneral = '[';
        $trialToTwoMonthGeneral = '[';
        $trialToThreeMonthGeneral = '[';
        $oneMonthToTwoMonthGeneral = '[';
        $oneMonthToThreeMonthGeneral = '[';
        $oneMonthToFourthMonthGeneral = '[';

        $trialToOneMonthIntensive = '[';
        $trialToTwoMonthIntensive = '[';
        $trialToThreeMonthIntensive = '[';
        $oneMonthToTwoMonthIntensive = '[';
        $oneMonthToThreeMonthIntensive = '[';
        $oneMonthToFourthMonthIntensive = '[';

        $trialToOneMonthConversation = '[';
        $trialToTwoMonthConversation = '[';
        $trialToThreeMonthConversation = '[';
        $oneMonthToTwoMonthConversation = '[';
        $oneMonthToThreeMonthConversation = '[';
        $oneMonthToFourthMonthConversation = '[';

        $initialDate = new DateTime("2021-07-04 00:00");
        $now = new DateTime();
        $interval = $initialDate->diff($now);

        for ($day = 0; $day <= $interval->format('%a'); $day++) {
            $xValues .= "'".$initialDate->format('Y-m-d')."'".', ';

            // Total Bias
            $trialCount = PaymentActions::find()->select('email')->where(['like', 'priceName', 'trial'])->andWhere(['<=', 'dateTime', $initialDate->format('Y-m-d')])->distinct()->count();
            $trial_to_1_month = Yii::$app->db->createCommand("SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%trial%' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' GROUP BY email)")->queryAll();
            $trial_to_2_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%trial%' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' GROUP BY email) GROUP BY email HAVING COUNT(email) = 2")->queryAll();
            $trial_to_3_month = Yii::$app->db->createCommand("SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%1 month%' AND `email` IN (SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%trial%' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' GROUP BY `email`) GROUP BY `email` HAVING COUNT(`email`) = 3")->queryAll();
            $trial_to_3_month_3p = Yii::$app->db->createCommand("SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%3 month%' AND `email` IN (SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%trial%' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' GROUP BY `email`)")->queryAll();

            $trialToOneMonth .= "'".round((sizeof($trial_to_1_month) / (($trialCount == 0) ? 1 : $trialCount)) * 100)."', ";
            $trialToTwoMonth .= "'".round((sizeof($trial_to_2_month) / (($trialCount == 0) ? 1 : $trialCount)) * 100)."', ";
            $trialToThreeMonth .= "'".round(((sizeof($trial_to_3_month) + sizeof($trial_to_3_month_3p)) / (($trialCount == 0) ? 1 : $trialCount)) * 100)."', ";

            $distinct_1_month_count = PaymentActions::find()->select('email')->where(['like', 'priceName', '1 month'])->andWhere(['<=', 'dateTime', $initialDate->format('Y-m-d')])->distinct()->count();
            $distinct_1_month_to_2_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' ) GROUP BY email HAVING COUNT(email) >= 2")->queryAll();
            $distinct_1_month_to_3_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' ) GROUP BY email HAVING COUNT(email) >= 3")->queryAll();
            $distinct_1_month_to_3_month_3p = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%3 month%' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' )")->queryAll();
            $distinct_1_month_to_4_month = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' ) GROUP BY email HAVING COUNT(email) >= 4")->queryAll();

            $oneMonthToTwoMonth .= "'".round((sizeof($distinct_1_month_to_2_month) / (($distinct_1_month_count == 0) ? 1 : $distinct_1_month_count)) * 100)."', ";
            $oneMonthToThreeMonth .= "'".round(((sizeof($distinct_1_month_to_3_month) + sizeof($distinct_1_month_to_3_month_3p)) / (($trialCount == 0) ? 1 : $trialCount)) * 100)."', ";
            $oneMonthToFourthMonth .= "'".round((sizeof($distinct_1_month_to_4_month) / (($distinct_1_month_count == 0) ? 1 : $distinct_1_month_count)) * 100)."', ";


            // General English Bias
            $trialCountGeneral = PaymentActions::find()->select('email')->where(['like', 'priceName', 'trial'])->andWhere(['like', 'packetName', 'General English'])->andWhere(['<=', 'dateTime', $initialDate->format('Y-m-d')])->distinct()->count();
            $trial_to_1_monthGeneral = Yii::$app->db->createCommand("SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `packetName` = 'General English' AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%trial%' AND `packetName` = 'General English' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' GROUP BY email)")->queryAll();
            $trial_to_2_monthGeneral = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `packetName` = 'General English' AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%trial%' AND `packetName` = 'General English' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' GROUP BY email) GROUP BY email HAVING COUNT(email) = 2")->queryAll();
            $trial_to_3_monthGeneral = Yii::$app->db->createCommand("SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%1 month%' AND `packetName` = 'General English' AND `email` IN (SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%trial%' AND `packetName` = 'General English' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' GROUP BY `email`) GROUP BY `email` HAVING COUNT(`email`) = 3")->queryAll();
            $trial_to_3_month_3pGeneral = Yii::$app->db->createCommand("SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%3 month%' AND `packetName` = 'General English' AND `email` IN (SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%trial%' AND `packetName` = 'General English' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' GROUP BY `email`)")->queryAll();

            $trialToOneMonthGeneral .= "'".round((sizeof($trial_to_1_monthGeneral) / (($trialCountGeneral == 0) ? 1 : $trialCountGeneral)) * 100)."', ";
            $trialToTwoMonthGeneral .= "'".round((sizeof($trial_to_2_monthGeneral) / (($trialCountGeneral == 0) ? 1 : $trialCountGeneral)) * 100)."', ";
            $trialToThreeMonthGeneral .= "'".round(((sizeof($trial_to_3_monthGeneral) + sizeof($trial_to_3_month_3pGeneral)) / (($trialCountGeneral == 0) ? 1 : $trialCountGeneral)) * 100)."', ";

            $distinct_1_month_countGeneral = PaymentActions::find()->select('email')->where(['like', 'priceName', '1 month'])->andWhere(['like', 'packetName', 'General English'])->andWhere(['<=', 'dateTime', $initialDate->format('Y-m-d')])->distinct()->count();
            $distinct_1_month_to_2_monthGeneral = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `packetName` = 'General English' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `packetName` = 'General English' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' ) GROUP BY email HAVING COUNT(email) >= 2")->queryAll();
            $distinct_1_month_to_3_monthGeneral = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `packetName` = 'General English' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `packetName` = 'General English' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' ) GROUP BY email HAVING COUNT(email) >= 3")->queryAll();
            $distinct_1_month_to_3_month_3pGeneral = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%3 month%' AND `packetName` = 'General English' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `packetName` = 'General English' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' )")->queryAll();
            $distinct_1_month_to_4_monthGeneral = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `packetName` = 'General English' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `packetName` = 'General English' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' ) GROUP BY email HAVING COUNT(email) >= 4")->queryAll();

            $oneMonthToTwoMonthGeneral .= "'".round((sizeof($distinct_1_month_to_2_monthGeneral) / (($distinct_1_month_countGeneral == 0) ? 1 : $distinct_1_month_countGeneral)) * 100)."', ";
            $oneMonthToThreeMonthGeneral .= "'".round(((sizeof($distinct_1_month_to_3_monthGeneral) + sizeof($distinct_1_month_to_3_month_3pGeneral)) / (($trialCountGeneral == 0) ? 1 : $trialCountGeneral)) * 100)."', ";
            $oneMonthToFourthMonthGeneral .= "'".round((sizeof($distinct_1_month_to_4_monthGeneral) / (($distinct_1_month_countGeneral == 0) ? 1 : $distinct_1_month_countGeneral)) * 100)."', ";


            // Intensive English Bias
            $trialCountIntensive = PaymentActions::find()->select('email')->where(['like', 'priceName', 'trial'])->andWhere(['like', 'packetName', 'Intensive'])->andWhere(['<=', 'dateTime', $initialDate->format('Y-m-d')])->distinct()->count();
            $trial_to_1_monthIntensive = Yii::$app->db->createCommand("SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `packetName` LIKE '%Intensive%' AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%trial%' AND `packetName` LIKE '%Intensive%' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' GROUP BY email)")->queryAll();
            $trial_to_2_monthIntensive = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `packetName` LIKE '%Intensive%' AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%trial%' AND `packetName` LIKE '%Intensive%' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' GROUP BY email) GROUP BY email HAVING COUNT(email) = 2")->queryAll();
            $trial_to_3_monthIntensive = Yii::$app->db->createCommand("SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%1 month%' AND `packetName` LIKE '%Intensive%' AND `email` IN (SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%trial%' AND `packetName` LIKE '%Intensive%' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' GROUP BY `email`) GROUP BY `email` HAVING COUNT(`email`) = 3")->queryAll();
            $trial_to_3_month_3pIntensive = Yii::$app->db->createCommand("SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%3 month%' AND `packetName` LIKE '%Intensive%' AND `email` IN (SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%trial%' AND `packetName` LIKE '%Intensive%' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' GROUP BY `email`)")->queryAll();

            $trialToOneMonthIntensive .= "'".round((sizeof($trial_to_1_monthIntensive) / (($trialCountIntensive == 0) ? 1 : $trialCountIntensive)) * 100)."', ";
            $trialToTwoMonthIntensive .= "'".round((sizeof($trial_to_2_monthIntensive) / (($trialCountIntensive == 0) ? 1 : $trialCountIntensive)) * 100)."', ";
            $trialToThreeMonthIntensive .= "'".round(((sizeof($trial_to_3_monthIntensive) + sizeof($trial_to_3_month_3pIntensive)) / (($trialCountIntensive == 0) ? 1 : $trialCountIntensive)) * 100)."', ";

            $distinct_1_month_countIntensive = PaymentActions::find()->select('email')->where(['like', 'priceName', '1 month'])->andWhere(['like', 'packetName', 'Intensive'])->andWhere(['<=', 'dateTime', $initialDate->format('Y-m-d')])->distinct()->count();
            $distinct_1_month_to_2_monthIntensive = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `packetName` LIKE '%Intensive%' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `packetName` LIKE '%Intensive%' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' ) GROUP BY email HAVING COUNT(email) >= 2")->queryAll();
            $distinct_1_month_to_3_monthIntensive = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `packetName` LIKE '%Intensive%' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `packetName` LIKE '%Intensive%' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' ) GROUP BY email HAVING COUNT(email) >= 3")->queryAll();
            $distinct_1_month_to_3_month_3pIntensive = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%3 month%' AND `packetName` LIKE '%Intensive%' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `packetName` LIKE '%Intensive%' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' )")->queryAll();
            $distinct_1_month_to_4_monthIntensive = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `packetName` LIKE '%Intensive%' AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND `packetName` LIKE '%Intensive%' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' ) GROUP BY email HAVING COUNT(email) >= 4")->queryAll();

            $oneMonthToTwoMonthIntensive .= "'".round((sizeof($distinct_1_month_to_2_monthIntensive) / (($distinct_1_month_countIntensive == 0) ? 1 : $distinct_1_month_countIntensive)) * 100)."', ";
            $oneMonthToThreeMonthIntensive .= "'".round(((sizeof($distinct_1_month_to_3_monthIntensive) + sizeof($distinct_1_month_to_3_month_3pIntensive)) / (($trialCountIntensive == 0) ? 1 : $trialCountIntensive)) * 100)."', ";
            $oneMonthToFourthMonthIntensive .= "'".round((sizeof($distinct_1_month_to_4_monthIntensive) / (($distinct_1_month_countIntensive == 0) ? 1 : $distinct_1_month_countIntensive)) * 100)."', ";


            // Conversation English Bias
            $trialCountConversation = sizeof(Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE `packetName` = 'Only Speaking' OR `packetName` = 'Conversation Club' AND `dateTime` <= '".$initialDate->format('Y-m-d')."' ")->queryAll());

            $trial_to_1_monthConversation = Yii::$app->db->createCommand("SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND (`packetName` = 'Only Speaking' OR `packetName` = 'Conversation Club') AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%trial%' AND (`packetName` = 'Only Speaking' OR `packetName` = 'Conversation Club') AND `dateTime` <= '".$initialDate->format('Y-m-d')."' GROUP BY email)")->queryAll();
            $trial_to_2_monthConversation = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND (`packetName` = 'Only Speaking' OR `packetName` = 'Conversation Club') AND email IN (SELECT email FROM `paymentActions` WHERE priceName LIKE '%trial%' AND (`packetName` = 'Only Speaking' OR `packetName` = 'Conversation Club') AND `dateTime` <= '".$initialDate->format('Y-m-d')."' GROUP BY email) GROUP BY email HAVING COUNT(email) = 2")->queryAll();
            $trial_to_3_monthConversation = Yii::$app->db->createCommand("SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%1 month%' AND (`packetName` = 'Only Speaking' OR `packetName` = 'Conversation Club') AND `email` IN (SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%trial%' AND (`packetName` = 'Only Speaking' OR `packetName` = 'Conversation Club') AND `dateTime` <= '".$initialDate->format('Y-m-d')."' GROUP BY `email`) GROUP BY `email` HAVING COUNT(`email`) = 3")->queryAll();
            $trial_to_3_month_3pConversation = Yii::$app->db->createCommand("SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%3 month%' AND (`packetName` = 'Only Speaking' OR `packetName` = 'Conversation Club') AND `email` IN (SELECT `email` FROM `paymentActions` WHERE `priceName` LIKE '%trial%' AND (`packetName` = 'Only Speaking' OR `packetName` = 'Conversation Club') AND `dateTime` <= '".$initialDate->format('Y-m-d')."' GROUP BY `email`)")->queryAll();

            $trialToOneMonthConversation .= "'".round((sizeof($trial_to_1_monthConversation) / (($trialCountConversation == 0) ? 1 : $trialCountConversation)) * 100)."', ";
            $trialToTwoMonthConversation .= "'".round((sizeof($trial_to_2_monthConversation) / (($trialCountConversation == 0) ? 1 : $trialCountConversation)) * 100)."', ";
            $trialToThreeMonthConversation .= "'".round(((sizeof($trial_to_3_monthConversation) + sizeof($trial_to_3_month_3pConversation)) / (($trialCountConversation == 0) ? 1 : $trialCountConversation)) * 100)."', ";

            $distinct_1_month_countConversation = sizeof(Yii::$app->db->createCommand("SELECT DISTINCT email FROM `paymentActions` WHERE `priceName` LIKE '%1 month%' AND (`packetName` = 'Only Speaking' OR `packetName` = 'Conversation Club') AND `dateTime` <= '".$initialDate->format('Y-m-d')."' ")->queryAll());

            $distinct_1_month_to_2_monthConversation = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND (`packetName` = 'Only Speaking' OR `packetName` = 'Conversation Club') AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND (`packetName` = 'Only Speaking' OR `packetName` = 'Conversation Club') AND `dateTime` <= '".$initialDate->format('Y-m-d')."' ) GROUP BY email HAVING COUNT(email) >= 2")->queryAll();
            $distinct_1_month_to_3_monthConversation = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND (`packetName` = 'Only Speaking' OR `packetName` = 'Conversation Club') AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND (`packetName` = 'Only Speaking' OR `packetName` = 'Conversation Club') AND `dateTime` <= '".$initialDate->format('Y-m-d')."' ) GROUP BY email HAVING COUNT(email) >= 3")->queryAll();
            $distinct_1_month_to_3_month_3pConversation = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%3 month%' AND (`packetName` = 'Only Speaking' OR `packetName` = 'Conversation Club') AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND (`packetName` = 'Only Speaking' OR `packetName` = 'Conversation Club') AND `dateTime` <= '".$initialDate->format('Y-m-d')."' )")->queryAll();
            $distinct_1_month_to_4_monthConversation = Yii::$app->db->createCommand("SELECT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND (`packetName` = 'Only Speaking' OR `packetName` = 'Conversation Club') AND email IN (SELECT DISTINCT email FROM `paymentActions` WHERE priceName LIKE '%1 month%' AND (`packetName` = 'Only Speaking' OR `packetName` = 'Conversation Club') AND `dateTime` <= '".$initialDate->format('Y-m-d')."' ) GROUP BY email HAVING COUNT(email) >= 4")->queryAll();

            $oneMonthToTwoMonthConversation .= "'".round((sizeof($distinct_1_month_to_2_monthConversation) / (($distinct_1_month_countConversation == 0) ? 1 : $distinct_1_month_countConversation)) * 100)."', ";
            $oneMonthToThreeMonthConversation .= "'".round(((sizeof($distinct_1_month_to_3_monthConversation) + sizeof($distinct_1_month_to_3_month_3pConversation)) / (($trialCountConversation == 0) ? 1 : $trialCountConversation)) * 100)."', ";
            $oneMonthToFourthMonthConversation .= "'".round((sizeof($distinct_1_month_to_4_monthConversation) / (($distinct_1_month_countConversation == 0) ? 1 : $distinct_1_month_countConversation)) * 100)."', ";

            $initialDate->modify('+1 day');
        }

        $xValues .= ']';
        $trialToOneMonth .= ']';
        $trialToTwoMonth .= ']';
        $trialToThreeMonth .= ']';
        $oneMonthToTwoMonth .= ']';
        $oneMonthToThreeMonth .= ']';
        $oneMonthToFourthMonth .= ']';

        $trialToOneMonthGeneral .= ']';
        $trialToTwoMonthGeneral .= ']';
        $trialToThreeMonthGeneral .= ']';
        $oneMonthToTwoMonthGeneral .= ']';
        $oneMonthToThreeMonthGeneral .= ']';
        $oneMonthToFourthMonthGeneral .= ']';

        $trialToOneMonthIntensive .= ']';
        $trialToTwoMonthIntensive .= ']';
        $trialToThreeMonthIntensive .= ']';
        $oneMonthToTwoMonthIntensive .= ']';
        $oneMonthToThreeMonthIntensive .= ']';
        $oneMonthToFourthMonthIntensive .= ']';

        $trialToOneMonthConversation .= ']';
        $trialToTwoMonthConversation .= ']';
        $trialToThreeMonthConversation .= ']';
        $oneMonthToTwoMonthConversation .= ']';
        $oneMonthToThreeMonthConversation .= ']';
        $oneMonthToFourthMonthConversation .= ']';

    ?>
    <br>
    <div class="container-fluid w-100">
        <div class="row">
            <div class="col-12" align="center">
                <h2>Total Bias</h2>
                <div style="max-width: 100%;min-width: 1300px;">
                    <canvas id="totalChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <br><br><br>
    <div class="container-fluid w-100">
        <div class="row">
            <div class="col-12" align="center">
                <h2>General English Bias</h2>
                <div style="max-width: 100%;min-width: 1300px;">
                    <canvas id="general-packet"></canvas>
                </div>
            </div>
        </div>
    </div>

    <br><br><br>
    <div class="container-fluid w-100">
        <div class="row">
            <div class="col-12" align="center">
                <h2>Intensive English Bias</h2>
                <div style="max-width: 100%;min-width: 1300px;">
                    <canvas id="intensive-packet"></canvas>
                </div>
            </div>
        </div>
    </div>

    <br><br><br>
    <div class="container-fluid w-100">
        <div class="row">
            <div class="col-12" align="center">
                <h2>Conversation English Bias</h2>
                <div style="max-width: 100%;min-width: 1300px;">
                    <canvas id="conversation-packet"></canvas>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let xValues = <?= $xValues ?>;

        let totalChart = new Chart("totalChart" , {
            type: "line",
            data: {
                labels: xValues,
                datasets: [
                    {
                        label: 'trial-to-1m',
                        data: <?= $trialToOneMonth ?>,
                        borderColor: "blue",
                        fill: false,
                        tension: 1
                    },
                    {
                        label: 'trial-to-2m',
                        data: <?= $trialToTwoMonth ?>,
                        borderColor: "red",
                        fill: false,
                        tension: 1
                    },
                    {
                        label: 'trial-to-3m',
                        data: <?= $trialToThreeMonth ?>,
                        borderColor: "green",
                        fill: false,
                        tension: 1
                    },
                    {
                        label: '1m-to-2m',
                        data: <?= $oneMonthToTwoMonth ?>,
                        borderColor: "black",
                        fill: false,
                        tension: 1
                    },
                    {
                        label: '1m-to-3m',
                        data: <?= $oneMonthToThreeMonth ?>,
                        borderColor: "orange",
                        fill: false,
                        hidden: true,
                        tension: 1
                    },
                    {
                        label: '1m-to-4m',
                        data: <?= $oneMonthToFourthMonth ?>,
                        borderColor: "purple",
                        fill: false,
                        hidden: true,
                        tension: 1
                    },
                ]
            },
            options: {
                legend: {display: false},
                scales: {
                    xAxes: [{
                        type: 'date',
                        time: {
                            displayFormats: {

                            }
                        }
                    }],
                }
            }
        });

        let generalChart = new Chart("general-packet" , {
            type: "line",
            data: {
                labels: xValues,
                datasets: [
                    {
                        label: 'trial-to-1m',
                        data: <?= $trialToOneMonthGeneral ?>,
                        borderColor: "blue",
                        fill: false,
                        tension: 1
                    },
                    {
                        label: 'trial-to-2m',
                        data: <?= $trialToTwoMonthGeneral ?>,
                        borderColor: "red",
                        fill: false,
                        tension: 1
                    },
                    {
                        label: 'trial-to-3m',
                        data: <?= $trialToThreeMonthGeneral ?>,
                        borderColor: "green",
                        fill: false,
                        tension: 1
                    },
                    {
                        label: '1m-to-2m',
                        data: <?= $oneMonthToTwoMonthGeneral ?>,
                        borderColor: "black",
                        fill: false,
                        tension: 1
                    },
                    {
                        label: '1m-to-3m',
                        data: <?= $oneMonthToThreeMonthGeneral ?>,
                        borderColor: "orange",
                        fill: false,
                        hidden: true,
                        tension: 1
                    },
                    {
                        label: '1m-to-4m',
                        data: <?= $oneMonthToFourthMonthGeneral ?>,
                        borderColor: "purple",
                        fill: false,
                        hidden: true,
                        tension: 1
                    },
                ]
            },
            options: {
                legend: {display: false},
                scales: {
                    xAxes: [{
                        type: 'date',
                        time: {
                            displayFormats: {

                            }
                        }
                    }],
                }
            }
        });

        let intensiveChart = new Chart("intensive-packet" , {
            type: "line",
            data: {
                labels: xValues,
                datasets: [
                    {
                        label: 'trial-to-1m',
                        data: <?= $trialToOneMonthIntensive ?>,
                        borderColor: "blue",
                        fill: false,
                        tension: 1
                    },
                    {
                        label: 'trial-to-2m',
                        data: <?= $trialToTwoMonthIntensive ?>,
                        borderColor: "red",
                        fill: false,
                        tension: 1
                    },
                    {
                        label: 'trial-to-3m',
                        data: <?= $trialToThreeMonthIntensive ?>,
                        borderColor: "green",
                        fill: false,
                        tension: 1
                    },
                    {
                        label: '1m-to-2m',
                        data: <?= $oneMonthToTwoMonthIntensive ?>,
                        borderColor: "black",
                        fill: false,
                        tension: 1
                    },
                    {
                        label: '1m-to-3m',
                        data: <?= $oneMonthToThreeMonthIntensive ?>,
                        borderColor: "orange",
                        fill: false,
                        hidden: true,
                        tension: 1
                    },
                    {
                        label: '1m-to-4m',
                        data: <?= $oneMonthToFourthMonthIntensive ?>,
                        borderColor: "purple",
                        fill: false,
                        hidden: true,
                        tension: 1
                    },
                ]
            },
            options: {
                legend: {display: false},
                scales: {
                    xAxes: [{
                        type: 'date',
                        time: {
                            displayFormats: {

                            }
                        }
                    }],
                }
            }
        });

        let conversationChart = new Chart("conversation-packet" , {
            type: "line",
            data: {
                labels: xValues,
                datasets: [
                    {
                        label: 'trial-to-1m',
                        data: <?= $trialToOneMonthConversation ?>,
                        borderColor: "blue",
                        fill: false,
                        tension: 1
                    },
                    {
                        label: 'trial-to-2m',
                        data: <?= $trialToTwoMonthConversation ?>,
                        borderColor: "red",
                        fill: false,
                        tension: 1
                    },
                    {
                        label: 'trial-to-3m',
                        data: <?= $trialToThreeMonthConversation ?>,
                        borderColor: "green",
                        fill: false,
                        tension: 1
                    },
                    {
                        label: '1m-to-2m',
                        data: <?= $oneMonthToTwoMonthConversation ?>,
                        borderColor: "black",
                        fill: false,
                        tension: 1
                    },
                    {
                        label: '1m-to-3m',
                        data: <?= $oneMonthToThreeMonthConversation ?>,
                        borderColor: "orange",
                        fill: false,
                        hidden: true,
                        tension: 1
                    },
                    {
                        label: '1m-to-4m',
                        data: <?= $oneMonthToFourthMonthConversation ?>,
                        borderColor: "purple",
                        fill: false,
                        hidden: true,
                        tension: 1
                    },
                ]
            },
            options: {
                legend: {display: false},
                scales: {
                    xAxes: [{
                        type: 'date',
                        time: {
                            displayFormats: {

                            }
                        }
                    }],
                }
            }
        });
    </script>


</body>