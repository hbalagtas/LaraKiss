#! /bin/bash
Xvfb :99 &
export DISPLAY=:99
#java -jar selenium-server-standalone-2.53.0.jar
java -jar selenium-server-standalone-2.53.0.jar -Dwebdriver.chrome.driver=chromedriver_mac
#java -jar selenium-server-standalone-3.0.0-beta2.jar -Dwebdriver.chrome.driver=chromedriver
