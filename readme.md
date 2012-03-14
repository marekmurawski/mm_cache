mmCache plugin for Wolf CMS
==============

mmCache is a general purpose cache plugin for Wolf CMS. It is useful in caching computationally intensive operations like **rendering multi-level menus**, which can generate many database requests.

Another case of usage would be fetching some **external content from remote server** with slow connection.


Examples
--------

These are examples of mmFragment an mmCache usage. You can copy/paste following
code into your Pages, Snippets or Layouts.

### 1. Output fragment caching

The random number is cached for 20 seconds in this example.

``` php
<?php
// check if there is a cached fragment named 'samplefragment'
// if the fragment exists in cache it will be echoed
// if it doesn't exist the inner clause of 'if' statement
// will be executed
if (! mmFragment::load('samplefragment')) {

// make some output with random number
        echo "Random number to be cached: <strong>" . mt_rand(0, 10000) . "</strong><br/>";

        // save fragment for 20 seconds
        mmFragment::save(20);

        // this text will only show when fragment doesn't exist
        // because we've already stored fragment
        echo "<em>Cache didn't exist. Saved fragment for 20 seconds</em>";
} else { // fragment was found

// the "else" gets executed if fragment is served from cache
// you can omit this part or you can do this: 
// echo "<!-- CACHED -->"
// for debugging hidden from end-user
echo "<em>Above text is served from cache it expires in: " . (mmFragment::getTimeout('samplefragment') - time()) . " seconds</em>";
}
?>
```

### 2. Nested fragments

Fragments can be nested. In this example the inner number
is cached for 18 seconds and the outer number is cached for
7 seconds. For detalis see the source of snippet.

``` php
<?php
if (! mmFragment::load('nestedfragmentouter')) {
        echo "Outer number: <strong>" . mt_rand(0, 10000) . "</strong><br/>";
                if (! mmFragment::load('nestedfragmentinner')) {
                echo "Inner number:  <strong>" . mt_rand(0, 10000) . "</strong><br/>";
                mmFragment::save(18); //save inner fragment
                }
        mmFragment::save(7); //save outer fragment
}
?>
```

### 3. Caching whole page content based on uri

You can cache whole page content as a fragment with key defined as the current URI. For security reasons it's highly recommended to generate md5 (or other) hash of uri. This way you avoid filename collisions / security vulnerabilities.

In your layout put this code at the very beginning:

``` php
<?php
$fragmentKey = $_SERVER["REQUEST_URI"];
// or 
// $fragmentKey = $this->url(); // but this way you don't save query strings like ?id=123&p=12 etc.
if (! mmFragment::load($fragmentKey)) {
?>
// here you put your layout contents
```

at the end of your layout put this code:

``` php
mmFragment::save(3600); //save cache of current page for 3600 seconds
}
?>
```

### 4. Data / variables caching
In this example we store a variable (array) in cache for 13 seconds.

``` php
<?php
// Get the instance of mmCache
$cache = mmCache::getInstance();

// if there is no cached data for 'sampledata' make something
// inside the 'if' clause
if (!$data = $cache->get('sampledata')) {

        // prepare empty array
        $data = array();

        // random array size
        $size = mt_rand(5, 15);

        // fill array with random numbers
        for ($i = 0; $i < $size; $i++) {
                $data[] = 'Random number: ' . mt_rand(1, 1000);
                }

        // store $data for 13 seconds
        $cache->set('sampledata', $data, 13);
}
?>
<pre>
<?php 
print_r($data); 
echo "<em>This data expires in: ". ($cache->getTimeout('sampledata') - time()) . " seconds</em>";
?>
</pre>
```