///////////// Edit by Nicolas /////////////
# YesWiki Carto fork installation on a VPS
Requirements : `git`, `composer`, `MySQL`/`MariaDB` database, `Apache`, and have `data/` and `carto.js` files.
- Go to your VPS's main web directory
- `git clone https://github.com/Weltskaiser/yeswiki-carto.git`
- Go inside the created folder
- `git checkout dev/carto`
- `composer install`
- Paste the `data/` folder at the root of the website folder
- Add the `carto.js` file in the `javascripts/` folder
- Run it with Apache
Go to your website, fill the info asked. For YesWiki to work you must create a MySQL/MariaDB database and a user having all rights on it.
Check your main page works. Then go to `<your_website_url>/?carto` and check a map is displaying geometry data.
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
