<?php

/**
 * File: userpanel_voice_usage.php.
 * Author: Ulrich Block
 * Date: 30.03.14
 * Contact: <ulrich.block@easy-wi.com>
 *
 * This file is part of Easy-WI.
 *
 * Easy-WI is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Easy-WI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy-WI.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Diese Datei ist Teil von Easy-WI.
 *
 * Easy-WI ist Freie Software: Sie koennen es unter den Bedingungen
 * der GNU General Public License, wie von der Free Software Foundation,
 * Version 3 der Lizenz oder (nach Ihrer Wahl) jeder spaeteren
 * veroeffentlichten Version, weiterverbreiten und/oder modifizieren.
 *
 * Easy-WI wird in der Hoffnung, dass es nuetzlich sein wird, aber
 * OHNE JEDE GEWAEHELEISTUNG, bereitgestellt; sogar ohne die implizite
 * Gewaehrleistung der MARKTFAEHIGKEIT oder EIGNUNG FUER EINEN BESTIMMTEN ZWECK.
 * Siehe die GNU General Public License fuer weitere Details.
 *
 * Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
 * Programm erhalten haben. Wenn nicht, siehe <http://www.gnu.org/licenses/>.
 */

if (!isset($user_id) or $main != 1 or (isset($user_id) and !$pa['voiceserverStats'])) {
    header('Location: userpanel.php');
    die;
}

$sprache = getlanguagefile('traffic', $user_language, $reseller_id);
$voSprache = getlanguagefile('voice', $user_language, $reseller_id);

if ($ui->w('action', 4, 'post') and !token(true)) {

    $template_file = $spracheResponse->token;

} else {

    $data = array();
    $slotUsage = array();
    $trafficUsage = array();

    $menuStart = 6;

    $display = $sprache->total;

    $dateRange = ($ui->escaped('dateRange', 'post')) ? $ui->escaped('dateRange', 'post') : date('m/d/Y', strtotime("-6 days")) . ' - ' . date('m/d/Y');
    $accuracy = (in_array($ui->st('accuracy', 'post'), array('da', 'mo'))) ? $ui->st('accuracy', 'post') : 'da';

    @list($startDate, $endDate) = explode('-', str_replace(' ', '', $dateRange));
    @list($startMonth, $startDay, $startYear) = explode('/', $startDate);
    @list($endMonth, $endDay, $endYear) = explode('/', $endDate);

    $freeSlots = 0;
    $usedSlots = 0;
    $freeTraffic = 0;
    $fileTraffic = 0;

    if ($endYear > 2000 and $startYear > 2000) {

        $menuStart = round((strtotime("{$endYear}-{$endMonth}-{$endDay}") - strtotime("{$startYear}-{$startMonth}-{$startDay}")) / 86400);

        $extractOrNormal = ($accuracy == 'mo') ? "CONCAT(EXTRACT(YEAR FROM `date`),'-',EXTRACT(MONTH FROM `date`))" : '`date`';

        $startDateFormatted = date('Y-m-d', strtotime($startYear . '-' . $startMonth . '-' . $startDay));
        $endDateFormatted = date('Y-m-d', strtotime($endYear . '-' . $endMonth . '-' . $endDay));

        if ($ui->id('serverID', 10, 'post')) {
            $query = $sql->prepare("SELECT $extractOrNormal AS `groupedDate`,SUM(`used`)/COUNT(`sid`) AS `averageused`,SUM(`traffic`)/1024 as `fileTrafficMB` FROM `voice_server_stats` WHERE `sid`=? AND `uid`=? AND `resellerid`=? AND `date` BETWEEN ? AND ? GROUP BY `groupedDate` ORDER BY `groupedDate`");
            $query->execute(array($ui->id('serverID', 10, 'post'), $user_id, $reseller_id, $startDateFormatted, $endDateFormatted));
        } else {
            $query = $sql->prepare("SELECT $extractOrNormal AS `groupedDate`,SUM(`used`)/COUNT(`sid`) AS `averageused`,SUM(`traffic`)/1024 as `fileTrafficMB` FROM `voice_server_stats` WHERE `uid`=? AND `resellerid`=? AND `date` BETWEEN ? AND ? GROUP BY `groupedDate` ORDER BY `groupedDate`");
            $query->execute(array($user_id, $reseller_id, $startDateFormatted, $endDateFormatted));
        }

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $slotUsage[] = "{y: '{$row['groupedDate']}', item1: " . ceil($row['averageused']) . "}";
            $trafficUsage[] = "{y: '{$row['groupedDate']}', item1: " . ceil($row['fileTrafficMB']) . "}";
        }

        if ($ui->id('serverID', 10, 'post')) {
            $query = $sql->prepare("SELECT `slots` AS `s`,`usedslots` AS `u`,`maxtraffic` AS `m`,`filetraffic` AS `f` FROM `voice_server` WHERE `id`=? AND `userid`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($ui->id('serverID', 10, 'post'), $user_id, $reseller_id));
        } else {
            $query = $sql->prepare("SELECT SUM(`slots`) AS `s`, SUM(`usedslots`) AS `u`, SUM(`maxtraffic`) AS `m`, SUM(`filetraffic`) AS `f` FROM `voice_server` WHERE `userid`=? AND `resellerid`=?");
            $query->execute(array($user_id, $reseller_id));
        }

        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $freeSlots = (int) ($row['s'] - $row['u']);
            $usedSlots = (int) $row['u'];
            $freeTraffic = ceil($row['m'] - ($row['f'] / 1024));
            $fileTraffic = ceil($row['f'] / 1024);
        }
    }


    $kind = 'al';

    if ($ui->id('serverID', 10, 'post') and $ui->st('kind', 'post') == 'se') {

        $kind = 'se';

        $query = $sql->prepare("SELECT v.`id`,v.`ip`,v.`port`,v.`dns`,m.`usedns` FROM `voice_server` v INNER JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`id`=? AND v.`userid`=? AND v.`resellerid`=? LIMIT 1");
        $query->execute(array($ui->id('serverID', 30, 'post'), $user_id, $reseller_id));
        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
            $display = $row['ip'] . ':' . $row['port'];
        }

        $query = $sql->prepare("SELECT v.`id`,v.`ip`,v.`port`,v.`dns`,m.`usedns` FROM `voice_server` v INNER JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`userid`=? AND v.`resellerid`=? ORDER BY v.`ip`,v.`port`");
        $query->execute(array($user_id, $reseller_id));
        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
            $data[] = ($ui->id('serverID', 10, 'post') == $row['id']) ? '<option value='. $row['id'] .' selected="selected">' . $row['ip'] . ':' . $row['port'] . '</option>' : '<option value=' . $row['id'] . '>' . $row['ip'] . ':' . $row['port'] . '</option>';
        }
    }

    $htmlExtraInformation['css'][] = '<link href="css/adminlte/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css">';
    $htmlExtraInformation['js'][] = '<script src="js/adminlte/plugins/daterangepicker/moment.js" type="text/javascript"></script>';
    $htmlExtraInformation['js'][] = '<script src="js/adminlte/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>';

    $htmlExtraInformation['js'][] = "<script type=\"text/javascript\">

$(function() {
    //Date range as a button
    $('#dateRange').daterangepicker(
        {
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                'Last 7 Days': [moment().subtract('days', 6), moment()],
                'Last 30 Days': [moment().subtract('days', 29), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
            },
            startDate: moment().subtract('days', {$menuStart}),
            endDate: moment(),
            opens: 'right'
        },
        function(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
    );
});
</script>";

    $htmlExtraInformation['css'][] = '<link href="css/adminlte/morris/morris.css" rel="stylesheet" type="text/css">';
    $htmlExtraInformation['js'][] = '<script src="js/adminlte/plugins/morris/raphael-min.js" type="text/javascript"></script>';
    $htmlExtraInformation['js'][] = '<script src="js/adminlte/plugins/morris/morris.min.js" type="text/javascript"></script>';
    $htmlExtraInformation['js'][] = "<script type=\"text/javascript\">
$(function() {
    'use strict';

    var area = new Morris.Area({
        element: 'slot-usage',
        resize: true,
        data: [" . implode(',', $slotUsage) . "],
        xkey: 'y',
        ykeys: ['item1'],
        labels: ['{$voSprache->usage}'],
        lineColors: ['#3c8dbc'],
        hideHover: 'auto'
    });

    var line = new Morris.Area({
        element: 'traffic-usage',
        resize: true,
        data: [" . implode(',', $trafficUsage) . "],
        xkey: 'y',
        ykeys: ['item1'],
        labels: ['MegaByte'],
        lineColors: ['#3c8dbc'],
        hideHover: 'auto'
    });

    var donut = new Morris.Donut({
        element: 'traffic-chart',
        resize: true,
        colors: ['#00a65a', '#f56954'],
        data: [
            {label: '{$gsprache->free}', value: {$freeTraffic}},
            {label: '{$gsprache->used}', value: {$fileTraffic}}
        ],
        hideHover: 'auto'
    });

    var donut = new Morris.Donut({
        element: 'slots-chart',
        resize: true,
        colors: ['#00a65a', '#f56954'],
        data: [
            {label: '{$gsprache->free}', value: {$freeSlots}},
            {label: '{$gsprache->used}', value: {$usedSlots}}
        ],
        hideHover: 'auto'
    });
});
</script>";

/*<?php echo ;?>/<?php echo $maxTraffic;?>, "#00a65a"
                    <?php echo ;?>/<?php echo ;?>*/
    $template_file = 'userpanel_voice_stats.tpl';
}