# Eventus

Useful Wordpress plugin that allow you to manage handball teams results through FFHB website.

## How to use

### Installing

* Retrieve the project and drag it into the plugins folder of your WordPress site.

```file
wp-content/plugins/
```

* Install librairies with composer

```sh
composer install
```

* And that's it !

### Initialization

You need to create a Club first.
![newclub](https://user-images.githubusercontent.com/24525092/48315817-5fa44600-e5db-11e8-8275-1e912c395a84.png)

Then, you can create a team with your link to FFHB's website results.
![newteam](https://user-images.githubusercontent.com/24525092/48315818-5fa44600-e5db-11e8-9ef2-69d207388db8.png)

You can now synchronize matches data.
![showteams](https://user-images.githubusercontent.com/24525092/48315819-603cdc80-e5db-11e8-9682-01f315594fa1.png)

### More

Do not forget to put your GoogleMap Matrix Distance Api Key in the settings page.

This plugin has been created to work with Enfold Theme. So, if you do not have it, you will not be able to display information on your site.

Feel free to edit the project, so you can use it with your own theme.

Most of the files are documented. So, you can give it an eye if you want to understand more.

## Updates

### 2.7.3

* Evo string for teams are now regex (so possible to have multiple strings, ex: "team1|team2")

### 2.7.2

* Fix an issue with Google Distance Matrix API limitation

### 2.7.1

* Evo reset option are not display by default
* Fix hide button to create team of there are no club
* Fix an issue with a translation
* Fix an issue with message

### 2.7 - Seeker Update

* Add seeker class and seeker screens that allows you to find teams by a club and a championship !
* Add option to define default rdv before match time
* Evo seeker (for team) and finder (for matches) are now async (so faster process)
* Evo Jquery is removed, Js scripts for each admin page and front components has been reworked
* Evo button to open link results on team are now hidden if no url
* Fix bug on input date/time in firefox
* Fix rdv time for outdoor matches are now limited to 10 calculations (Google Api Matrix Distance limitation)
* Fix translations issues
* Fix deletion of a team

### 2.6 - About Update of the FFHB website

* Evo implement new API of the FFHB Website
* Evo opti on css
* Add licence MIT
* Fix momentary display of the second url

### 2.5 - Calendar Update

* Fix mail for cron seems to works again
* Add calendar ICS for each teams
* Add buttons to download or copy link for each ICS (Avia component)
* Evo results shown in two rows : main club is in a different color
* Evo remove some useless text for widget home
* Evo none buttons/links can be clicked when button loading
* Evo use twig for templating screen in back end
* Add choice of season

### 2.4.2

* Add possibility to reset log for each notification mail send
* Evo check error on framework's website

### 2.4.1

* Fix url site in cron with php7.2
* Evo transform somes buttons to links

### 2.4 - Translations Update

* Fix ButtonResults and ResultsCarousel with multiple links
* Fix ResultsCarousel
* Fix displaying only last match with results
* Fix cron is now usable (bind 'cron.php' to a cron job)
* Fix hour rdv and matches are now set to NULL by default (instead of '00:00')
* Fix second button url for teams
* Add you can now open in other tab button in admins pages
* Add you can now select an image for a team
* Add you can open restults in new tab thanks to a button
* Add namespace in every files
* Add date of last synchronization of matches is saved in DB
* Add translations files with every strings translated
* Evo you can add two different results links to a team
* Evo you can't no longer pick sexe(s) for clubs
* Evo some files and repositories have changed
* Evo selected image are now display in pickers
* Evo lot of optimizations in multiple files (Finder, PostHandler, Screens, etc...)
* Evo Widget Dashboard has been remade

### 2.3.1

* Fix escape some string with quotes, etc...
* Fix positions notices

### 2.3 - BackEnd Improvement Update

* Fix if there are no Enfold Theme
* Fix check if at least one sex is selected on club before submit
* Fix unselect sex on change club in creation team
* Fix reset value auto increment when purge table
* Fix url encoding for api map
* Fix cron
* Add link to club in team card
* Add link to github in credits
* Add htaccess to denied direct acces to php files
* Add tooltips on heavy load buttons
* Add documentations on most of the files
* Evo Club are now displayed in cards
* Evo some files have changed name
* Evo optimized displaying buttons choose sex and delete image
* Evo rename every columns in db with a corresponding prefix
* Evo big change on DAOs. Now opti with joint request
* Evo back end check on team & club submit
* Evo you can now submit create/update team/club when press enter

### 2.2 - (Massive) BackEnd Update

* Fix displaying of last match
* Fix displaying of match in calendar : show only the ones greater than yesterday
* Fix displaying of results carousel
* Fix calc of hours rdv
* Fix issue with bracket in button results
* Fix pick a sexe is required to create a team
* Fix few visual issues
* Fix memory issue in Finder
* Add button to delete matches for all team or for just one team
* Add modal to confirm delete
* Add match day in matches list
* Add page to show logs
* Add infos debug about a team in footer card
* Add possibility to select an image for a team and delete it
* Add Avia Builder Element for team picture
* Add img default if null for a team on the Avia Builder Element
* Add loading spinner on some buttons
* Add message asking to create a club first if none exist
* Add notice to show successes or warnings
* Add an admin screen
* Add ApiKey GoogleMap setting in admin screen
* Add credit in admin screen
* Add possibility to switch from team to matches and reciprocally
* Evo team are now display in card
* Evo url have changed
* Evo move log file
* Evo DAOs and Screens are declared in singleton
* Evo url is no more required to create a team
* Evo create/delete tables are now store in class Database
* Evo data processing of each view have moved in postHandler
* Evo each required fields are shown with an asterisk
* Evo class Master doesn't exist anymore
* Evo you can't sync match if the team has no url

### 2.1.1*

* Improve visual elements

### 2.1* - Avia Constructor Update

* Add Avia Builder Element
* Delete widget results

### 2.0* - Birth of Eventus

* Plugin recast and new name "Eventus"

### 1.0* - Resultat Match

* First and deprecated version named "Resultat Match"

*not available on this github
