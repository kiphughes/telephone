<?php
  require_once 'telephone.php';

	function run_unit_tests()
	{
		$telephone           = new Telephone();
		$configuration_names = array(
			'new_zealand'
		);

		// 1473906, generic
		$results = $telephone->parse('1473906', $configuration_names);
		assert($results === array());


		// 2114739, Vodafone without trunk prefix, 5 digits
		$results = $telephone->parse('2114739', $configuration_names);
		assert($results === array());

		// 02114739, Vodafone with trunk prefix, 5 digits
		$results = $telephone->parse('02114739', $configuration_names);
		assert($results === array());

		// 642114739, Vodafone with country code, 5 digits
		$results = $telephone->parse('642114739', $configuration_names);
		assert($results === array());

		// 00642114739, Vodafone with international call prefix, 5 digits
		$results = $telephone->parse('00642114739', $configuration_names);
		assert($results === array());


		// 21147390, Vodafone without trunk prefix, 6 digits
		$results = $telephone->parse('21147390', $configuration_names);
		assert(count($results['new_zealand']) === 1);
		assert($results['new_zealand'][0]['success'] === TRUE);
		assert($results['new_zealand'][0]['is_landline'] === FALSE);
		assert($results['new_zealand'][0]['is_mobile'] === TRUE);
		assert($results['new_zealand'][0]['parts']['international_call_prefix'] === '');
		assert($results['new_zealand'][0]['parts']['country_code'] === '');
		assert($results['new_zealand'][0]['parts']['trunk_prefix'] === '');
		assert($results['new_zealand'][0]['parts']['area_code'] === '21');
		assert($results['new_zealand'][0]['parts']['local_number'] === '147390');

		// 021147390, Vodafone with trunk prefix, 6 digits
		$results = $telephone->parse('021147390', $configuration_names);
		assert(count($results['new_zealand']) === 1);
		assert($results['new_zealand'][0]['success'] === TRUE);
		assert($results['new_zealand'][0]['is_landline'] === FALSE);
		assert($results['new_zealand'][0]['is_mobile'] === TRUE);
		assert($results['new_zealand'][0]['parts']['international_call_prefix'] === '');
		assert($results['new_zealand'][0]['parts']['country_code'] === '');
		assert($results['new_zealand'][0]['parts']['trunk_prefix'] === '0');
		assert($results['new_zealand'][0]['parts']['area_code'] === '21');
		assert($results['new_zealand'][0]['parts']['local_number'] === '147390');

		// 6421147390, Vodafone with country code, 6 digits
		$results = $telephone->parse('6421147390', $configuration_names);
		assert(count($results['new_zealand']) === 1);
		assert($results['new_zealand'][0]['success'] === TRUE);
		assert($results['new_zealand'][0]['is_landline'] === FALSE);
		assert($results['new_zealand'][0]['is_mobile'] === TRUE);
		assert($results['new_zealand'][0]['parts']['international_call_prefix'] === '');
		assert($results['new_zealand'][0]['parts']['country_code'] === '64');
		assert($results['new_zealand'][0]['parts']['trunk_prefix'] === '');
		assert($results['new_zealand'][0]['parts']['area_code'] === '21');
		assert($results['new_zealand'][0]['parts']['local_number'] === '147390');

		// 006421147390, Vodafone with international call prefix, 6 digits
		$results = $telephone->parse('006421147390', $configuration_names);
		assert(count($results['new_zealand']) === 1);
		assert($results['new_zealand'][0]['success'] === TRUE);
		assert($results['new_zealand'][0]['is_landline'] === FALSE);
		assert($results['new_zealand'][0]['is_mobile'] === TRUE);
		assert($results['new_zealand'][0]['parts']['international_call_prefix'] === '00');
		assert($results['new_zealand'][0]['parts']['country_code'] === '64');
		assert($results['new_zealand'][0]['parts']['trunk_prefix'] === '');
		assert($results['new_zealand'][0]['parts']['area_code'] === '21');
		assert($results['new_zealand'][0]['parts']['local_number'] === '147390');

		// 211473906, Vodafone without trunk prefix, 7 digits
		$results = $telephone->parse('211473906', $configuration_names);
		assert(count($results['new_zealand']) === 1);
		assert($results['new_zealand'][0]['success'] === TRUE);
		assert($results['new_zealand'][0]['is_landline'] === FALSE);
		assert($results['new_zealand'][0]['is_mobile'] === TRUE);
		assert($results['new_zealand'][0]['parts']['international_call_prefix'] === '');
		assert($results['new_zealand'][0]['parts']['country_code'] === '');
		assert($results['new_zealand'][0]['parts']['trunk_prefix'] === '');
		assert($results['new_zealand'][0]['parts']['area_code'] === '21');
		assert($results['new_zealand'][0]['parts']['local_number'] === '1473906');

		// 0211473906, Vodafone with trunk prefix, 7 digits
		$results = $telephone->parse('0211473906', $configuration_names);
		assert(count($results['new_zealand']) === 1);
		assert($results['new_zealand'][0]['success'] === TRUE);
		assert($results['new_zealand'][0]['is_landline'] === FALSE);
		assert($results['new_zealand'][0]['is_mobile'] === TRUE);
		assert($results['new_zealand'][0]['parts']['international_call_prefix'] === '');
		assert($results['new_zealand'][0]['parts']['country_code'] === '');
		assert($results['new_zealand'][0]['parts']['trunk_prefix'] === '0');
		assert($results['new_zealand'][0]['parts']['area_code'] === '21');
		assert($results['new_zealand'][0]['parts']['local_number'] === '1473906');

		// 64211473906, Vodafone with country code, 7 digits
		$results = $telephone->parse('64211473906', $configuration_names);
		assert(count($results['new_zealand']) === 1);
		assert($results['new_zealand'][0]['success'] === TRUE);
		assert($results['new_zealand'][0]['is_landline'] === FALSE);
		assert($results['new_zealand'][0]['is_mobile'] === TRUE);
		assert($results['new_zealand'][0]['parts']['international_call_prefix'] === '');
		assert($results['new_zealand'][0]['parts']['country_code'] === '64');
		assert($results['new_zealand'][0]['parts']['trunk_prefix'] === '');
		assert($results['new_zealand'][0]['parts']['area_code'] === '21');
		assert($results['new_zealand'][0]['parts']['local_number'] === '1473906');

		// 0064211473906, Vodafone with international call prefix, 7 digits
		$results = $telephone->parse('0064211473906', $configuration_names);
		assert($results['new_zealand'][0]['success'] === TRUE);
		assert($results['new_zealand'][0]['is_landline'] === FALSE);
		assert($results['new_zealand'][0]['is_mobile'] === TRUE);
		assert($results['new_zealand'][0]['parts']['international_call_prefix'] === '00');
		assert($results['new_zealand'][0]['parts']['country_code'] === '64');
		assert($results['new_zealand'][0]['parts']['trunk_prefix'] === '');
		assert($results['new_zealand'][0]['parts']['area_code'] === '21');
		assert($results['new_zealand'][0]['parts']['local_number'] === '1473906');


		// 2114739061, Vodafone without trunk prefix, 8 digits
		$results = $telephone->parse('2114739061', $configuration_names);
		assert($results['new_zealand'][0]['success'] === TRUE);
		assert($results['new_zealand'][0]['is_landline'] === FALSE);
		assert($results['new_zealand'][0]['is_mobile'] === TRUE);
		assert($results['new_zealand'][0]['parts']['international_call_prefix'] === '');
		assert($results['new_zealand'][0]['parts']['country_code'] === '');
		assert($results['new_zealand'][0]['parts']['trunk_prefix'] === '');
		assert($results['new_zealand'][0]['parts']['area_code'] === '21');
		assert($results['new_zealand'][0]['parts']['local_number'] === '14739061');

		// 02114739061, Vodafone with trunk prefix, 8 digits
		$results = $telephone->parse('02114739061', $configuration_names);
		assert($results['new_zealand'][0]['success'] === TRUE);
		assert($results['new_zealand'][0]['is_landline'] === FALSE);
		assert($results['new_zealand'][0]['is_mobile'] === TRUE);
		assert($results['new_zealand'][0]['parts']['international_call_prefix'] === '');
		assert($results['new_zealand'][0]['parts']['country_code'] === '');
		assert($results['new_zealand'][0]['parts']['trunk_prefix'] === '0');
		assert($results['new_zealand'][0]['parts']['area_code'] === '21');
		assert($results['new_zealand'][0]['parts']['local_number'] === '14739061');

		// 642114739061, Vodafone with country code, 8 digits
		$results = $telephone->parse('642114739061', $configuration_names);
		assert($results['new_zealand'][0]['success'] === TRUE);
		assert($results['new_zealand'][0]['is_landline'] === FALSE);
		assert($results['new_zealand'][0]['is_mobile'] === TRUE);
		assert($results['new_zealand'][0]['parts']['international_call_prefix'] === '');
		assert($results['new_zealand'][0]['parts']['country_code'] === '64');
		assert($results['new_zealand'][0]['parts']['trunk_prefix'] === '');
		assert($results['new_zealand'][0]['parts']['area_code'] === '21');
		assert($results['new_zealand'][0]['parts']['local_number'] === '14739061');

		// 00642114739061, Vodafone with international call prefix, 8 digits
		$results = $telephone->parse('00642114739061', $configuration_names);
		assert($results['new_zealand'][0]['success'] === TRUE);
		assert($results['new_zealand'][0]['is_landline'] === FALSE);
		assert($results['new_zealand'][0]['is_mobile'] === TRUE);
		assert($results['new_zealand'][0]['parts']['international_call_prefix'] === '00');
		assert($results['new_zealand'][0]['parts']['country_code'] === '64');
		assert($results['new_zealand'][0]['parts']['trunk_prefix'] === '');
		assert($results['new_zealand'][0]['parts']['area_code'] === '21');
		assert($results['new_zealand'][0]['parts']['local_number'] === '14739061');


		// 21147390612, Vodafone without trunk prefix, 9 digits
		$results = $telephone->parse('21147390612', $configuration_names);
		assert($results === array());

		// 021147390612, Vodafone with trunk prefix, 9 digits
		$results = $telephone->parse('021147390612', $configuration_names);
		assert($results === array());

		// 6421147390612, Vodafone with country code, 9 digits
		$results = $telephone->parse('6421147390612', $configuration_names);
		assert($results === array());

		// 006421147390612, Vodafone with international call prefix, 9 digits
		$results = $telephone->parse('006421147390612', $configuration_names);
		assert($results === array());
	}

	assert_options(ASSERT_BAIL, TRUE);
	run_unit_tests();
	echo 'Unit tests done.';
