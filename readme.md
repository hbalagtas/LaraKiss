# Kiss[anime][asian][cartoon] Downloader using Laravel 

requirements: Ubuntu server, google chrome stable browser, chrome webdriver, selenium standalone server

It seems webdriver works best on a windows machine rather than a virtual one, with the virtual one running linux the webdriver kept crashing and reCaptcha keeps getting triggered. On the windows server I was able to completely download a full season without getting the reCaptcha. I am also using Firefox instead of the chrome webdriver.

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

reCaptcha issue - so far I haven't been able to get around this issue using webdriver, however using xquartz on osx I was able to just click through the test and resume downloading. Not sure how often the test occurs but I'm testing for now.

http://askubuntu.com/questions/163155/how-do-i-access-my-remote-ubuntu-server-via-x-windows-from-my-mac

On your Mac, you'll need to install XQuartz, if you haven't done so already (see http://xquartz.macosforge.org). Once that's installed, reboot and open an XQuartz terminal window and SSH to your Ubuntu Server system:

ssh -X yourubuntuserver
If lightdm is running (see http://askubuntu.com/a/153423/66799 to read how turn it on and off as needed), run this command to get Ubuntu Unity tunnelled to your Mac over X11:

gnome-session --session=ubuntu-2d
This works for me with Ubuntu Server 12.04 LTS and Mac OS X Mountain Lion 10.8.2, but I do get occasional Composiz errors. That said, I always got Composiz errors when running Ubuntu Desktop locally, too. (-:

You can log out of Unity to close the Ubuntu Desktop windows.