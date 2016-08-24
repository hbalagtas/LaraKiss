# Kiss[anime][asian][cartoon] Downloader using Laravel 

requirements: Ubuntu server, google chrome stable browser, chrome webdriver, selenium standalone server

sudo apt install xvfb screen default-jre
sudo dpkg -i google-chrome-stable_current_amd64.deb
sudo apt-get install -f

WebDriver Cheatsheet
https://gist.github.com/aczietlow/7c4834f79a7afd920d8f

If webdriver hangs or timeouts
http://www.eschrade.com/page/chromedriver-hangs-with-selenium-server-and-xvfb/
export DBUS_SESSION_BUS_ADDRESS=/dev/null

GUI for adding series or individual links
Schedule tasks to run between 2AM - 7AM

Users 
- id
- email
- password

shows
- id
- name
- cover
- summary
- source
- folder
- watched
- timestamps

Episodes
- id
- show_id
- url
- video_link
- source
- filename
- watched (bool)
- downloaded (bool)
- processing (bool)
- timestamps