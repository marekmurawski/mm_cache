CACHE PLUGIN
------------

This plugin improve Frog CMS to add a basic file cache functionality.
Like this you improve performance and reduce database query.


HOW IT WORK
------------

The Cache plugin, write the full html page in a cache file and store it for an undeterminate time (default one day).
Next, each request for the same page, the plugin will serve the cached version of the page.

This plugin add a column `is_cacheable` at the table `page`.


HOW TO INSTALL IT
------------------

 1. Put the cache folder in the plugins directory.
 2. Create a folder named 'caches' in the root of your project with write access, like
    {{{
      mkdir caches
      chmod 777 caches
    }}}
 3. Go to the Administration tab and active the cache plugin.



AUTHOR
-------
Gilles Doge, Antistatique.net - www.antistatique.net
gde@antistatique.net
