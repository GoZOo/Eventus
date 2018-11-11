# Eventus

Useful Wordpress plugin that allow you to manage handball teams results through FFHB website.

## Installing

Simply retrieve the project and drag it into the "wp-content/plugins/" folder of your wordpress site.

## Updates

### 2.3

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


### 2.2

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

### 2.1.1 (not available on this github)
* Improve visual elements

### 2.1 (not available on this github)
* Add Avia Builder Element
* Delete widget results

### 2.0 (not available on this github)
* Plugin recast and new name "Eventus"
 
### 1.0 (not available on this github)
* First and deprecated version named "Resultat Match"
