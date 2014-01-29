<?php

	if (count($argv) < 1) {
		print "Usage: php DataFactory.test.php\n";
		exit(-1);
	}

	if (!function_exists('notify')) {
		function notify ($testName, $expectation, $actual) {
			$passed = ($expectation == $actual);

			printf("[%s] Running test '%s' %s\n",
				$passed ? 'Passed' : 'Failed',
				$testName,
				($passed) ? '' : sprintf("(Expected '%s' received '%s')",
						$expectation, $actual)
			);

		}
	}

	include_once '../../../src/conf/config.inc.php';

	try {
		$editions = $LOOKUP_FACTORY->getEditions();
		$edition = $editions[9];
		$dataset = $DATA_FACTORY->getDatasetForRegionAndEdition(144.75, 13.5, 
				$edition->id, null);
		notify('Check Guam is not valid for nehrp-2003', is_null($dataset),
				true);
		$dataset = $DATA_FACTORY->getDatasetForRegionAndEdition(-103.0, 40.5,
				$edition->id, null);

		$edition = $editions[1];
		$dataset = $DATA_FACTORY->getDatasetForRegionAndEdition(144.75, 13.5,
				$edition->id, $edition->design_code_variant_ids[2]);
		notify('Get 2 data points for Guam / asce_41-2013 / BSE-2E dataset', 2,
				count($dataset->data_recs));

		$edition = $editions[5];
		$dataset = $DATA_FACTORY->getDatasetForRegionAndEdition(-147.65, 64.75,
				$edition->id, null);
		notify('Get 4 data points for Alaska / aashto-2009 dataset',
				4, count($dataset->data_recs));

		$edition = $editions[7];
		$dataset = $DATA_FACTORY->getDatasetForRegionAndEdition(-111.0, 42.0,
				$edition->id, null);
		notify('Check SLC subregion returned for asce-41-2006 dataset',
				8, $dataset->region_id);
		notify('Get 1 data point for SLC / asce-41-2006 dataset',
				1, count($dataset->data_recs));

/*		Note: These will not run after creation script.  An SDE-enabled tool
 *		is needed to create the TSUBL layer.
 */
		$tsubl_value = $DATA_FACTORY->getTsubLForPoint(-103.0, -40.5);
		notify('Get undefined tsubl value', -1, $tsubl_value);
		$tsubl_value = $DATA_FACTORY->getTsubLForPoint(-103.0, 40.5);
		notify('Get defined tsubl value', 4, $tsubl_value);

//		print json_encode($dataset) . "\n";
//		print json_encode($tsubl_value) . "\n";
	} catch (Exception $e) {
		print $e->getMessage() . "\n";
	}
?>

