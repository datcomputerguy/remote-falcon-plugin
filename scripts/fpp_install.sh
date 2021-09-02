#!/bin/bash

/usr/bin/php /home/fpp/media/plugins/remote-falcon/writeDefaultConfig.php &

# Mark to reboot
sed -i -e "s/^restartFlag .*/restartFlag = 1/" ${FPPHOME}/media/settings

#fpp_install