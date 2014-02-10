# Piwik LoginHttpAuth Plugin

## Description

This plugin extends the standard Piwik authentication to use Basic HTTP Authentication.
It lets you login to Piwik using the HTTP Auth mechanism.

How do I setup HTTP Auth using Piwik?

* Login your Piwik as Super User. Click Settings, then click Marketplace.
* Install the LoginHttpAuth plugin, then click Activate.
* Click Settings, then click Users.
    * Check that there is a user in Piwik for each person that should have access to Piwik.
* Enable HTTP Auth on the Piwik on your web server.

    For example, if you are using Apache webserver:

    * generate a .htpasswd file with your encrypted logins and passwords
    * [copy this example .htaccess file](https://github.com/piwik/plugin-LoginHttpAuth/blob/master/TemplateHtaccess/.htaccess) in the root directory of Piwik, and set the path to your .htpasswd file
* When you go to Piwik, you will see the Authentication window.
  Congratulations! You are now using HTTP Auth to protect Piwik.

## Changelog

 * 1.0 - First public release [compatible with Piwik 2](http://piwik.org/blog/2013/12/piwik-2-0-release-announced-biggest-best-release-yet/)

## License

GPL v3 or later

## Support

* Report bugs for LoginHttpAuth in [our Github issues tracker](https://github.com/piwik/plugin-LoginHttpAuth/issues)
* Please direct any feedback to [hello@piwik.org](mailto:hello@piwik.org)


