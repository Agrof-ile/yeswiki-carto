///////////// Edit by Nicolas /////////////
# YesWiki Carto fork installation
## On the VPS
Requirements : `git`, `composer`, `MySQL`/`MariaDB` database, `Apache`, and have generated the `carto.js` file.
- Go to your VPS's main web directory
- `git clone https://github.com/Weltskaiser/yeswiki-carto.git`
- Go inside the created folder
- `git checkout dev/carto`
- `composer install`
- `chmod 777 .`
- `chmod 777 cache`
- `chmod 777 files`
- `chmod +x tools/carto/controllers/ruzip.sh`
- Add the `carto.js` file (the bundle containing a Leaflet map code) in the `javascripts/` folder
- Run it with Apache
Now, time to test.
- Go to your website, fill the info asked to create your website. Note that for YesWiki to work you must create a MySQL/MariaDB database and a user having all rights on it.
- Check your main page works. Then go to `<your_website_url>/?carto` and check a Leaflet map appears.

## On the website's interface
Then try to log in as WikiAdmin. Create a random form containing at least:
- a file upload field (asking for a SHP as ZIP);
- a geolocation field;
- a surface field;
- a type of land field, with options from a YesWiki list.
Warning: every field with multiple answers needs a data origin which must be a `List`. Don't put commas `,` in the keys' names. You can give them the unique identifier you want, as long as you make sure the unique identifiers are the same than those in the `tools/carto/props_ids.json` file. The file contains more data about the `type_of_land` property: make sure the list name, the property id and the options keys ids follow what you've created from the website's interface when creating the form. WARNING : YesWiki removes the capitalization of the letters after the first one in the list identifiers: if you create a list `ListeType`, its identifier will be `Listetype` and this value should be in the `tools/carto/props_ids.json` file.

Now:
- Check your form id in the main forms page (it should be something like 5). Open `tools/carto/map_form_id.json` and replace the value in the field `map_form_id` (it should be set by default at 3) by your actual form id.
- Create a random account and fill the form with different data. Once the form completed, YesWiki shows you your card ("fiche"), that is one answer to the form that you own and can modify. By default, no one on the web can see it without beeing logged in and having rights on it (by default the owner and the administrator accounts). Look at your "fiche" and check your file has been uploaded.
- Log in as admin, go back to `<your_website_url>/?carto` and look if your SHPs are displaying!
///////////// End of the edit /////////////


# YesWiki installation
Not much to it (as long as it works, ahem). Unpack/upload the distribution files
into a directory that can be accessed via the web. Then go to the corresponding URL.  
A web-based installer will walk you through the rest.

**Important**: If copied from Github repository, YesWiki needs to work some files 
installed via `composer`. So after downloading/synchroning files on your server, 
start the comment `composer install`.
_You can find information about installation of `composer` at the following link_:
https://getcomposer.org/

#### Example:
If your website, say, http://www.mysite.com, is mapped to the directory /home/jdoe/www/,
and you place the YesWiki distribution files into /home/jdoe/www/wiki/, you should go to
http://www.mysite.com/wiki/.  

IMPORTANT: for installing or upgrading YesWiki, do NOT access any of the files contained
in the setup/ subdirectory. They're used by the web-based installer/updater, but you
should really just access the YesWiki directory itself, and it will (or at least should)
work perfectly.

Detailed instructions are available at https://yeswiki.net/?DocumentationInstallation (in French).

## Installation through Docker

First you need to install docker and docker-compose: https://docs.docker.com/install

Then just run `docker-compose up` to install and launch the containers

Then go to http://localhost:81. In the setup, you will need to provide following configuration for MySQL server:

- **Host: db**
- Login: root
- Password: root

You can see/modify the created tables by going to: http://localhost:8080
