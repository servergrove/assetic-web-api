Assetic Web API
===============

Assetic Web API provides a web service to run assetic filters remotely when the supporting files are not available.
This is a common issue in shared hosting accounts where java and/or ruby is not available in chroot environments.

This can also be useful for those that do not want to install and configure java, ruby, compass, etc.

Client Usage
------------

Download the client script assetic-client.php and configure assetic java path:

    filters:
        cssrewrite: ~
        yui_css:
            java: "~/assetic-client.php"
            jar: "yuicompressor-2.4.6.jar"
        yui_js:
            java: "~/assetic-client.php"
            jar: "yuicompressor-2.4.6.jar"
        coffee:
            bin: ~/assetic-client.php
            node:  /usr/local/bin/php


The jar setting is not important as long as it contains yuicompressor*.jar

Web API Server
--------------

ServerGrove provides a free web service located at http://assetic.servergrove.com

If you want to host your own server, you can do so by installing the Silex based app. Simply clone the repository and
configure your web server document root to point to the web directory.

Contributing
------------

Please feel free to send comments, issues and/or pull requests.

TODO
----

Need to implement remaining filters. yuicompresor and coffeescript are the only ones working at the moment.

Credits
-------

* Assetic Web API: Pablo Godel <pablo@servergrove.com>
* Assetic: Kris Wallsmith
* Silex PHP Micro-framework: Fabien Potencier and Igor Wielder