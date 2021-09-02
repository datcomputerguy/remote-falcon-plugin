<?php
include_once "/opt/fpp/www/common.php";

$pluginName = basename(dirname(__FILE__));

WriteSettingToFile("interruptSchedule",urlencode("false"),$pluginName);
WriteSettingToFile("autoRestartPlugin",urlencode("false"),$pluginName);
WriteSettingToFile("pluginEnabled",urlencode("true"),$pluginName);
WriteSettingToFile("pluginRestarting",urlencode("false"),$pluginName);
?>
