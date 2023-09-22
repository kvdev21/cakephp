START /B /BELOWNORMAL /WAIT C:/wamp/www/DVDCreator/bin/ffmpeg/ffmpeg -y  -i "C:/X Factor Videos/VideoCaptures/201102021540/TEST.mpg" -target ntsc-dvd -acodec ac3 -ab 192k -ar 48000 -aspect 16:9 -vf "scale=720:480" -r 29.97 "C:/X Factor Videos/VideoCaptures/201102021540/tmp.ntsc/dvd_compliant.mpg" > "C:/X Factor Videos/VideoCaptures/201102021540/tmp.ntsc/output.txt"

ECHO Error level:
ECHO %ERRORLEVEL%
pause