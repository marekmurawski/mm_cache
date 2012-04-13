<div id="mmCacheSamples" style="border: 1px dotted red; padding: 24px;">
	<h2> mmCache SAMPLE SNIPPET</h2>
	<p>See the cache in action here. Try <strong>hitting F5</strong> to refresh this page and observe the results.</p>
	<h3>1. Basic output Fragment caching</h3>
	<p>The random number is cached for 20 seconds in this example.</p>
		<?php
		if (!mmFragment::load('samplefragment')) {
			echo "Random number to be cached: <strong>" . mt_rand(0, 10000) . "</strong><br/>";
			mmFragment::save(20);
		}
		?>


	<h3>2. Nested fragments</h3>
	<p>Fragments can be nested. In this example the inner number is cached for 18 seconds and the outer number is cached for 7 seconds. For detalis see the source of snippet.</p>
		<?php
		if (!mmFragment::load('nestedfragmentouter')) {
			echo "Outer number (7s timeout): <strong>" . mt_rand(0, 10000) . "</strong><br/>";
			if (!mmFragment::load('nestedfragmentinner')) {
				echo "Inner number (18s timeout):  <strong>" . mt_rand(0, 10000) . "</strong><br/>";
				mmFragment::save(18); //save inner fragment
			}
			mmFragment::save(7); //save outer fragment
		}
		?>


	<h3>3. Data / variables caching</h3>
	<p>
		In this example we store a variable (an <code>array</code> with random number of items) in cache for 13 seconds. 
		If data is present in Cache it will be assigned to <code>$data</code> during the if-condition examining, 
		if it's not cached we'll execute the code inside if-clause, 
		assign $data values and save it to Cache for 13 seconds.
	</p>
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

		<pre><?php
			print_r($data);
			echo "<br/><em>This data expires in: " .
			($cache->getTimeout('sampledata') - time()) .
			" seconds</em>";
			?>
		</pre>

</div> <!-- end of mmCache samples -->