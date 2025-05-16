# mRSS
Latest version: Pre-alpha non-working state  
mRSS is a customisable RSS reader for the web, created for the Hack Club 'Neighborhood' project.
## Features
- none lol read the name of the latest version
## Usage
To use mRSS, go to https://mrss.mateishome.page. You can add a new feed by clicking the button in the bottom left and entering
the URL of the feed you want. However, make sure you log in or else all your feeds and settings will disappear when you reset
your cookies.
## Self-hosting
OOOOOOOOOOOOOOOOOOOOOOOOOH you wanna self-host mRSS do ya? well let me tell you that it's quite the doosie! you need:
- PHP 7.3 (other versions might work idk you think i actually test for that)
- mariaDB (other mySQL databases might work, see previous point)
- uhhh idk i think thats it. linux maybe? i use raspbian  

once you have aquired these tools and seasoned them to perfection, make sure to edit params.json because the default values probably only work for me :P.
i'd recommend setting userDBname and mainDBname to the same thing, i only have them different because mateishomepage exists. and uhhh if it doesnt work then
sucks to suck i guess.  
then run `php firstsetup.php` (which doesn't exist yet check the name of the latest version) in the directory which you want to install mRSS. and uhhh if it doesnt work then
sucks to suck i guess.