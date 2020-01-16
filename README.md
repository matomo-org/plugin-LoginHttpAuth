# Matomo LoginHttpAuth Plugin

[![Build Status](https://travis-ci.org/matomo-org/plugin-LoginHttpAuth.svg?branch=master)](https://travis-ci.org/matomo-org/plugin-LoginHttpAuth)

## Support discontinued

We are only supporting this plugin up to Matomo 3 and we will no longer support it as part of Matomo 4 due to little usage. If you are interested in maintaining this plugin and making it compatible with Matomo 4 please [get in touch](https://matomo.org/contact/).

## Description

This plugin extends the standard Matomo authentication to use Basic HTTP Authentication.
It lets you login to Matomo using the HTTP Auth mechanism.

How do I setup HTTP Auth using Matomo?

* Login your Matomo as Super User. Click Settings, then click Marketplace.
* Install the LoginHttpAuth plugin, then click Activate.
* Click Settings, then click Users.
    * Check that there is a user in Matomo for each person that should have access to Matomo.
* Enable HTTP Auth on the Matomo on your web server.

    For example, if you are using Apache webserver:

    * generate a .htpasswd file with your encrypted logins and passwords
    * [copy this example .htaccess file](https://raw.githubusercontent.com/matomo-org/plugin-LoginHttpAuth/master/TemplateHtaccess/.htaccess) in the root directory of Matomo, and set the path to your .htpasswd file
* When you go to Matomo, you will see the Authentication window.
  Congratulations! You are now using HTTP Auth to protect Matomo.

## License

GPL v3 or later

