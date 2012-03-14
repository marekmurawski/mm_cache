mmCache plugin for Wolf CMS
==============
This is an example snippet to demonstrate the usages of mmCache plugin. 
It is useful in caching computationally intensive operations like **rendering 
multi-level menus**, which can generate many database requests.
Another case would be fetching some **content from remote server** in case
of slow connection.

### Output fragment caching

The random number is cached for 20 seconds in this example.

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

### Nested fragments

Fragments can be nested. In this example the inner number
is cached for 18 seconds and the outer number is cached for
7 seconds. For detalis see the source of snippet.

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


### Data / variables caching
In this example we store a variable (array) in cache for 13 seconds.

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
    // print the stored or just-prepared data
    print_r($data); 
    ?>
    </pre>
    <?php
    echo "<em>This data expires in: ". ($cache->getTimeout('sampledata') - time()) . " seconds</em>";
    ?>
