<?php

/**
 * Media archive timeline
 *
 * This is a PHP script which considered a collection of
 * directories containing /YYYY/MM/DD/ and creates a timeline
 * with the contained media. 
 */


/**
 * All the directories to look in for media. Trailing slash not needed
 */
$sources = Array(
	'/shared/big/Movies/HomeVideos/Moores/',
	'/shared/big/Movies/HomeVideos/Papa_and_Grandmas_VHS/',
	'/shared/big/Movies/HomeVideos/VHS/',
	'/shared/big/Movies/HomeVideos/Walton/',
	'/shared/big/Photos/',
	'/shared/big/RyanPhotos/',
	'/shared/big/MooreDocs/blog/',
);

$index = Array();
foreach($sources as $source){
	buildIndex($source,&$index);
}


function buildIndex($source,$index){
	if(!isset($index['unsorted'])){
		$index['unsorted'] = 0;
	}


	// Check each source directory for years
	foreach(glob($source . '/*') as $maybe_year){
		if(is_dir($maybe_year)){
			if(preg_match('|/([0-9]{4})$|',$maybe_year,$year_match)){
				// Found a year directory!
				if(!isset($index[$year_match[1]])){
					$index[$year_match[1]] = Array('count' => 0,'months' => Array());
				}

				// Check each year directory for month directories
				foreach(glob($maybe_year . '/*') as $maybe_month){
					if(is_dir($maybe_month)){
						if(preg_match('|/([0-9]{2})$|',$maybe_month,$month_match)){
							// Found a month directory!
							if(!isset($index[$year_match[1]]['months'][$month_match[1])){
								$index[$year_match[1]['months'][$month_match[1]] = Array('count' => 0, 'days' => 0);
							}

							// Check each month directory for day directories
							foreach(glob($maybe_month . '/*') as $maybe_day){
								if(is_dir($maybe_day)){
									if(preg_match('|/([0-9]{2}$|',$maybe_day,$day_match[1])){
										if(!isset($index[$year_match[1]]['months'][$month_match[1]['days'][$day_match[1]])){
											$index[$year_match[1]['months'][$month_match[1]]['days'][$day_match[1]] = Array('count' => 0);
										}

										foreach(glob($maybe_day . '/*') as $maybe_file){
											if(is_file($maybe_file)){
												// Found a file in the day directory
												$index[$year_match[1]['months'][$month_match[1]]['days'][$day_match[1]]['count']++;
											}
										}
									}

								}else{
									// Found a file in the month directory
									$index[$year_match[1]['months'][$month_match[1]]['count']++;
								}
							}
						}
					}else{
						// Found a file in the year directory
						$index[$year_match[1]['count']++;
					}
				}
			} // else directory, non-year. Ignore it

		}else{
			// These are files in the source directory that aren't in a year directory
			// we assume that these pertain to the source but haven't been organized yet
			$index['unsorted']++;
		}
	}
}
