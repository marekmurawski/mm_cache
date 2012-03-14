<div class="documentation">
    <h1>mmCache plugin documentation</h1>    
<p>
mmCache is a general purpose cache plugin for Wolf CMS. It is useful in caching computationally intensive operations like rendering multi-level menus, which can generate many database requests.</p>

<p>Another case of usage would be fetching some external content from remote server with slow connection.</p>

<p>For obvious reasons, fragment caching should not be applied to any content that contains forms.</p>

<h2>Examples</h2>

<p>
These are examples of mmFragment an mmCache usage. You can copy/paste following code into your Pages, Snippets or Layouts.
</p>
<h3>1. Basic output Fragment caching</h3>
<p>
The random number is cached for 20 seconds in this example.
</p>
<pre>
    &lt;?php
    if (! mmFragment::load('samplefragment') ) {
            echo "Random number to be cached: &lt;strong&gt;" . mt_rand(0, 10000) . "&lt;/strong&gt;&lt;br/&gt;";
            mmFragment::save(20);
    }
    ?&gt;
</pre>
<p>
That's it! The echo'ed text with random number will be cached for 20 seconds on subsequent page requests.
</p>

<h3>2. Basic output Fragment caching (with comments)</h3>
<p>
This is the same example with commented code
</p>

<pre>
    &lt;?php

    /*
     * check if there is a cached fragment named 'samplefragment'
     * if the fragment exists in cache it will be echoed
     * if it doesn't exist the inner clause of 'if' statement
     * will be executed
     * !!! notice the exclamation mark in if statement
     */
    if (! mmFragment::load('samplefragment') ) {

            // make some output with random number
            echo "Random number to be cached: &lt;strong&gt;" . mt_rand(0, 10000) . "&lt;/strong&gt;&lt;br/&gt;";

            // save fragment for 20 seconds
            mmFragment::save(20);

            /* 
             * this text will only show when fragment doesn't exist
             * because we've already stored fragment
             */
            echo "&lt;em&gt;Cache didn't exist. Saved fragment for 20 seconds&lt;/em&gt;";

    } else {// fragment was found!

            /* 
             * the "else" gets executed if fragment is served from cache
             * you can omit this part or you can do this: 
             * echo "&lt;!-- CACHED --&gt;"
             * for debugging hidden from end-user
             */
            echo "&lt;em&gt;Above text is served from cache it expires in: " . 
                (mmFragment::getTimeout('samplefragment') - time()) . 
                " seconds&lt;/em&gt;";
    }
    ?&gt;
</pre>






<h3>3. Nested fragments</h3>

<p>
Fragments can be nested. In this example the inner number is cached for 18 seconds and the outer number is cached for 7 seconds. For detalis see the source of snippet.
</p>

<pre>
    &lt;?php
    if (! mmFragment::load('nestedfragmentouter') ) {
            echo "Outer number (7s timeout): &lt;strong&gt;" . mt_rand(0, 10000) . "&lt;/strong&gt;&lt;br/&gt;";
                    if (! mmFragment::load('nestedfragmentinner')) {
                    echo "Inner number (18s timeout):  &lt;strong&gt;" . mt_rand(0, 10000) . "&lt;/strong&gt;&lt;br/&gt;";
                    mmFragment::save(18); //save inner fragment
                    }
            mmFragment::save(7); //save outer fragment
    }
    ?&gt;
</pre>

<h3>4. Caching whole page content based on uri</h3>

<p>
You can cache whole page content as a fragment with key defined as the current URI. For security reasons it's highly recommended to generate md5 (or other) hash of uri. This way you avoid filename collisions / security vulnerabilities.
</p>
<p>
In your layout put this code at the very beginning:
</p>

<pre>
    &lt;?php

        // create safe fragment key with prefix 'wholePage/'
        $fragmentKey = 'wholePage/' . md5($_SERVER["REQUEST_URI"]);

            /**
            * or $fragmentKey = md5( $this-&gt;url() ); 
            * but this way you don't save query strings like ?id=123&p=12 etc.
            */

        if (! mmFragment::load($fragmentKey)) {
    ?&gt;

    // HERE YOU PUT YOUR LAYOUT HTML/PHP CODE
</pre>

<p>
... and at the end of your layout put this code:
</p>

<pre>
    &lt;?php
        mmFragment::save(3600); //save cache of current page for 3600 seconds
        }
    ?&gt;
</pre>

<h2>
5. Data / variables caching
</h2>
<p>
In this example we store a variable (array) in cache for 13 seconds. If data is present in Cache it will be assigned to $data during the if-condition examining, if it's not cached we'll execute the code inside if-clause, assign $data values and save it to Cache for 13 seconds. 
</p>
<p>
    Then $data available regardless of being cached or just-generated.
</p>

<pre>
    &lt;?php
        // Get the instance of mmCache
        $cache = mmCache::getInstance();

        // if there is no cached data for 'sampledata' make something
        // inside the 'if' clause
        if (! $data = $cache-&gt;get('sampledata') ) {

                // prepare empty array
                $data = array();

                // random array size
                $size = mt_rand(5, 15);

                // fill array with random numbers
                for ($i = 0; $i &lt; $size; $i++) {
                        $data[] = 'Random number: ' . mt_rand(1, 1000);
                        }

                // store $data for 13 seconds
                $cache-&gt;set('sampledata', $data, 13);
        }
    ?&gt;

    &lt;pre&gt;
    &lt;?php 
        print_r($data); 
        echo "&lt;br/&gt;&lt;em&gt;This data expires in: ". 
            ($cache-&gt;getTimeout('sampledata') - time()) . 
            " seconds&lt;/em&gt;";
    ?&gt;
    &lt;/pre&gt;
</pre>
</div>