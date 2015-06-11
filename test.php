<?php
function error_handler($error_level, $error_message, $error_file, $error_line, $error_context) {
    echo $error_level . "\n";
    echo "测试错误信息呢！";
    echo "<span color='red'>" . $error_message . $error_line . "</span>";
}




