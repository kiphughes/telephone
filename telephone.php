<?php
  /**
	 * Parses and validates a telephone number. This requires a configuration (set inside the constructor currently) in order to work.
	 * An example configuration is provided. In order to validate according to one's specific needs (e.g. for a particular country, etc.),
	 * replace or append the configuration. Requires some basic knowledge of regular expressions.
	 *
	 * Version 0.1 is an initial commit and should not be used in a production environment without extensive testing.
	 *
	 * @author	Kip Hughes < kiphughes@gmail.com >
	 * @version	0.1
	 */
	class Telephone
	{
		/**
		 * Array of configruations used for parsing/validating. See constructor for example.
		 */
		private $_configurations = array();

		/**
		 * Array of international call prefix regexes. See constructor for setting.
		 */
		private $_international_call_prefixes = array();

		/**
		 * Convert the regex results into the output array. The regex results is in a format that is not organized in the way the output
		 * should be. Furthermore, this also sets flags (e.g. is_landline, is_mobile) that requires some additional logic to determine.
		 *
		 * @param	array	$matches
		 * @param	string	$configuration_name
		 * @return	array							Result array if the telephone number is valid. Empty array, otherwise.
		 */
		private function _get_formatted_telephone_number_matches(array $matches, $configuration_name)
		{
			assert($matches !== array());
			assert(is_string($configuration_name) === TRUE);
			assert($configuration_name !== '');

			// Due to a limitation with how the regex was written, it is possible for an area code to be interpreted as a country code.
			// When this happens, the country code is set but the area code is not. That is not a valid phone number.
			if ($matches['country_code'] !== '' && $matches['area_code'] === '')
			{
				// Set to empty array, which means telephone number is invalid
				return array();
			}

			// Due to a limitation with how the regex was written, it is possible an a trunk prefix and local number to be set, but not the area code.
			// That is not a valid phone number.
			if ($matches['trunk_prefix'] !== '' && $matches['area_code'] === '' && $matches['local_number'] !== '')
			{
				// Set to empty array, which means telephone number is invalid
				return array();
			}

			$formatted_telephone_matches = array();

			// Regex successfully matched the telephone number being parsed
			$formatted_telephone_matches['success'] = TRUE;

			// $matches will contain other elements in the array that we don't want in the final output, so take only
			// the elements we need and put it in $formatted_telephone_matches
			$formatted_telephone_matches['parts']['international_call_prefix'] = $matches['international_call_prefix'];
			$formatted_telephone_matches['parts']['country_code']              = $matches['country_code'];
			$formatted_telephone_matches['parts']['trunk_prefix']              = $matches['trunk_prefix'];
			$formatted_telephone_matches['parts']['area_code']                 = $matches['area_code'];
			$formatted_telephone_matches['parts']['local_number']              = $matches['local_number'];

			// Set the flags for landline and mobile
			if (in_array($matches['area_code'], array_keys($this->_configurations[$configuration_name]['area_codes_and_local_numbers_landline'])) === TRUE)
			{
				// The area code is a landline area code
				$formatted_telephone_matches['is_landline'] = TRUE;
				$formatted_telephone_matches['is_mobile']   = FALSE;
			}
			elseif (in_array($matches['area_code'], array_keys($this->_configurations[$configuration_name]['area_codes_and_local_numbers_mobile'])) === TRUE)
			{
				// The area code is a mobile area code
				$formatted_telephone_matches['is_landline'] = FALSE;
				$formatted_telephone_matches['is_mobile']   = TRUE;
			}
			else
			{
				// Unknown if a landline or mobile
				$formatted_telephone_matches['is_landline'] = FALSE;
				$formatted_telephone_matches['is_mobile']   = FALSE;
			}

			return $formatted_telephone_matches;
		}

		public function __construct()
		{
			//	new_zealand                               - Array key used as the configuration name.
			//		country_code_regex                    - Regex for the country code. Use regex OR | for multiple country codes.
			//		trunk_prefix_regex                    - Regex for the trunk prefix. Use regex OR | for multiple trunk prefixes.
			//		area_codes_and_local_numbers_general  - Nested associative array of area codes and local numbers. If this is set,
			//		                                        then area_codes_and_local_numbers_landline and area_codes_and_local_numbers_mobile
			//		                                        should be empty arrays array(). Using area_codes_and_local_numbers_general
			//		                                        means that this library will not know whether or not the telephone number
			//		                                        is landline or mobile number.
			//		area_codes_and_local_numbers_landline - Nested associative array of area codes and local numbers for landlines. If
			//		                                        this is set, then area_codes_and_local_numbers_general should be an empty
			//		                                        array array(). Using area_codes_and_local_numbers_landline means this library
			//		                                        will know if the telephone number is a landline or not.
			//		area_codes_and_local_numbers_mobile   - Nested associative array of area codes and local numbers for mobiles. If
			//		                                        this is set, then area_codes_and_local_numbers_general should be an empty
			//		                                        array array(). Using area_codes_and_local_numbers_mobile means this library
			//		                                        will know if the telephone number is a mobile or not.
			//
			// NOTE: this member is set in the constuctor instead as a member default because it formats nicer when put
			//       through http://beta.phpformatter.com/. If it was set as a member default, the formatting would be
			//       one long line, which is not very readable.
			$this->_configurations = array(
				'new_zealand' => array(
					'country_code_regex' => '64',
					'trunk_prefix_regex' => '0',
					'area_codes_and_local_numbers_general' => array(),
					'area_codes_and_local_numbers_landline' => array(
						'2' => array(
							'409\d{4}'
						),
						'3' => array(
							'\d{7}'
						),
						'4' => array(
							'\d{7}'
						),
						'6' => array(
							'\d{7}'
						),
						'7' => array(
							'\d{7}'
						),
						'9' => array(
							'\d{7}'
						)
					),
					'area_codes_and_local_numbers_mobile' => array(
						'20' => array(
							'\d{6,8}'
						), // Orcon; TODO: verify local number regex
						'21' => array(
							'\d{6,8}'
						), // Vodafone
						'22' => array(
							'\d{7}'
						), // 2degrees
						'23' => array(
							'\d{6,8}'
						), // Unused, owned by TelstraClear; TODO: verify local number regex
						'24' => array(
							'\d{6,8}'
						), // Unused, protected by Management Committee 30.01.09; TODO: verify local number regex
						'25' => array(
							'\d{6,7}'
						), // Unused, was used by Telecom New Zealand
						'26' => array(
							'\d{7}'
						), // Telecom New Zealand, Team Talk
						'27' => array(
							'\d{7}'
						), // Telecom New Zealand
						'28' => array(
							'\d{6,8}'
						), // CallPlus or BLACK + WHITE; TODO: verify local number regex
						'29' => array(
							'\d{6,8}'
						), // TelstraClear; TODO: verify local number regex
						'204' => array(
							'\d{6,8}'
						), // Skinny; TODO: verify local number regex
						'280' => array(
							'\d{6,8}'
						), // Compass Communications; TODO: verify local number regex
						'283' => array(
							'\d{6,8}'
						), // Teletraders MVNO; TODO: verify local number regex
						'2885' => array(
							'\d{6,8}'
						), // M2 MVNO; TODO: verify local number regex
						'2896' => array(
							'\d{6,8}'
						) // Airnet NZ Ltd; TODO: verify local number regex
					)
				)
			);

			// Array of international call prefix regexes, which is later imploded with a | (the regex OR). The keys are
			// the actual prefixes, while the values are an array of countries that uses that prefix. The array of countries
			// aren't currently being used.
			//
			// Source: http://en.wikipedia.org/wiki/List_of_international_call_prefixes
			//
			// NOTE: this member is set in the constuctor instead as a member default because it formats nicer when put
			//       through http://beta.phpformatter.com/. If it was set as a member default, the formatting would be
			//       one long line, which is not very readable.
			$this->_international_call_prefixes = array(
				'0' => array(
					'Samoa'
				),
				'00' => array(
					'All European Union countries (possibly other numbers also)',
					'Argentina',
					'China',
					'Egypt',
					'Georgia',
					'Iceland',
					'India',
					'Iran',
					'Mexico',
					'Norway',
					'Pakistan',
					'Philippines',
					'Serbia',
					'Switzerland',
					'Ukraine',
					'Most countries in Africa',
					'Most countries in the Middle East',
					'Some countries in the former Soviet Republics',
					'Some countries in Asia',
					'Most countries in South America',
					'Hungary'
				),
				'000' => array(
					'Kenya',
					'Tanzania',
					'Uganda'
				),
				'001' => array(
					'Cambodia',
					'Guyana',
					'Hong Kong',
					'Indonesia',
					'Mongolia',
					'Singapore',
					'South Korea',
					'Thailand'
				),
				'001\d' => array(
					'Australia (alternative phone carrier)'
				),
				'002' => array(
					'South Korea',
					'Taiwan'
				),
				'003\d{2}' => array(
					'South Korea'
				),
				'004' => array(
					'Thailand'
				),
				'005' => array(
					'Colombia',
					'Kenya',
					'South Korea',
					'Taiwan',
					'Thailand'
				),
				'006' => array(
					'Kenya',
					'South Korea',
					'Taiwan',
					'Thailand'
				),
				'0060' => array(
					'Hong Kong'
				),
				'00600' => array(
					'Thailand'
				),
				'007' => array(
					'Cambodia',
					'Colombia',
					'Hong Kong',
					'Indonesia',
					'Kenya',
					'Taiwan',
					'Thailand'
				),
				'007\d{2}' => array(
					'South Korea'
				),
				'008' => array(
					'Hong Kong',
					'Indonesia',
					'Singapore',
					'South Korea',
					'Thailand'
				),
				'009' => array(
					'Australia',
					'Colombia',
					'Hong Kong',
					'Indonesia',
					'Taiwan',
					'Thailand'
				),
				'01[2-9]' => array(
					'Israel'
				),
				'010' => array(
					'Japan'
				),
				'011' => array(
					'American Samoa',
					'Anguilla',
					'Antigua and Barbuda',
					'Bahamas',
					'Barbados',
					'Bermuda',
					'British Virgin Islands',
					'Canada',
					'Cayman Islands',
					'Dominica',
					'Dominican Republic',
					'Grenada',
					'Guam',
					'Roatan',
					'Honduras',
					'Haïti',
					'Jamaica',
					'Marshall Islands',
					'Micronesia',
					'Montserrat',
					'Northern Mariana Islands',
					'Palau',
					'Puerto Rico',
					'Saint Kitts and Nevis',
					'Saint Lucia',
					'Saint Vincent and the Grenadines',
					'Sint Maarten',
					'Trinidad and Tobago',
					'Turks and Caicos Islands',
					'United States of America',
					'United States Virgin Islands'
				),
				'1\d{2}0' => array(
					'Chile'
				),
				'1010' => array(
					'Georgia (carrier: MagtiCom)'
				),
				'1011' => array(
					'Georgia (carrier: SystemNet)'
				),
				'1012' => array(
					'Georgia (carrier: SystemNet)'
				),
				'1013' => array(
					'Georgia (carrier: MacroCom)'
				),
				'1015' => array(
					'Georgia (carrier: SakTelComPlus)'
				),
				'1016' => array(
					'Georgia (carrier: Silknet)'
				),
				'1017' => array(
					'Georgia (carrier: SystemNet)'
				),
				'1018' => array(
					'Georgia (carrier: Global Erty)'
				),
				'1019' => array(
					'Georgia (carrier: Akhtel)'
				),
				'1027' => array(
					'Georgia (carrier: CallCenter)'
				),
				'1040' => array(
					'Georgia (carrier: IntelPhone)'
				),
				'1041' => array(
					'Georgia (carrier: V-TEL Georgia)'
				),
				'1050' => array(
					'Georgia (carrier: MagtiCom)'
				),
				'1051' => array(
					'Georgia (carrier: Caucasus Online)'
				),
				'1052' => array(
					'Georgia (carrier: Alex Development Georgia)'
				),
				'1053' => array(
					'Georgia (carrier: Sky Service)'
				),
				'1054' => array(
					'Georgia (carrier: Lagi)'
				),
				'1057' => array(
					'Georgia (carrier: NewCom)'
				),
				'1060' => array(
					'Georgia (carrier: GeoNet)'
				),
				'1061' => array(
					'Georgia (carrier: Silnet)'
				),
				'1062' => array(
					'Georgia (carrier: Geocell)'
				),
				'1063' => array(
					'Georgia (carrier: Black Sea Telecom)'
				),
				'1064' => array(
					'Georgia (carrier: Central Communications Company of Georgia)'
				),
				'1065' => array(
					'Georgia (carrier: Akhali Kselebi)'
				),
				'1067' => array(
					'Georgia (carrier: Geotel)'
				),
				'1069' => array(
					'Georgia (carrier: Service)'
				),
				'1072' => array(
					'TelMax'
				),
				'1075' => array(
					'Georgia (carrier: WiMAX Georgia)'
				),
				'1076' => array(
					'Georgia (carrier: Silknet)'
				),
				'1094' => array(
					'Georgia (carrier: Warid Telecom Georgia)'
				),
				'119' => array(
					'Cuba'
				),
				'15\d{2}' => array(
					'Hong Kong'
				),
				'16\d{2}' => array(
					'Hong Kong'
				),
				'1666' => array(
					'Hong Kong'
				),
				'810' => array(
					'Belarus',
					'Kazakhstan',
					'Russia (carrier: Rostelecom)',
					'Tajikistan',
					'Turkmenistan',
					'Uzbekistan'
				),
				'826' => array(
					'Russia (carrier: Arctel)'
				),
				'827' => array(
					'Russia (carrier: Synterra)'
				),
				'828' => array(
					'Russia (carrier: Comstar)'
				),
				'856' => array(
					'Russia (carrier: GoldenTelecom)'
				),
				'857' => array(
					'Russia (carrier: Transtelecom)'
				),
				'858' => array(
					'Russia (carrier: MTT)'
				),
				'859' => array(
					'Russia (carrier: Orange Business Services)'
				),
				'990' => array(
					'Finland (carrier: TeliaSonera Finland Oyj)'
				),
				'991' => array(
					'Finland (carrier: Elisa Oyj)'
				),
				'992' => array(
					'Finland (carrier: Globetel Oy)'
				),
				'994' => array(
					'Finland (carrier:  TDC Oy FINLAND)'
				),
				'99500' => array(
					'Finland (carrier: Cubio Communications Ltd)'
				),
				'99511' => array(
					'Finland (carrier: Helistamine OU)'
				),
				'99532' => array(
					'Finland (carrier: Elisa Oyj)'
				),
				'99533' => array(
					'Finland (carrier: TDC Oy FINLAND)'
				),
				'996' => array(
					'Finland (carrier: DNA Oy)'
				),
				'9977' => array(
					'Finland (carrier: Elisa Oyj)'
				),
				'998' => array(
					'Finland (carrier: Orange Business Finland Oy)'
				),
				'999' => array(
					'Finland (carrier:  Elisa Oyj)'
				)
			);
		}

		/**
		 * Parse and validate a telephone number.
		 *
		 * @param	string	$telephone_number		The telephone number to be parsed and validated.
		 * @param	array	$configuration_names	Optional. An array of configuration names from $this->_configurations to use for parsing/validating the
		 * 											telephone number. The configuration names are the first level key of $this->_configurations. If this is
		 * 											not set, then all configuration names will apply.
		 * @return	array							An array of successfully parsed/validated telephone numbers. Results are grouped by $configuration_names.
		 * 											Empty array means no successful match was made. Example output for 6421147390:
		 *
		 * 											["new_zealand"]=>
		 * 												[0] =>
		 * 													["success"] => true
		 * 													["parts"] =>
		 * 														["international_call_prefix"] => ""
		 * 														["country_code"] => "64"
		 * 														["trunk_prefix"] => ""
		 * 														["area_code"] => "21"
		 * 														["local_number"] => "147390"
		 * 													["is_landline"] => false
		 * 													["is_mobile"]=> true
		 */
		public function parse($telephone_number, array $configuration_names = array())
		{
			// Sanitize $telephone_number; remove all non-digit characters
			$telephone_number = preg_replace('/[^\d]/', '', $telephone_number);

			if ($configuration_names === array())
			{
				// $configuration_names was not set; set it to all the keys of $this->_configurations
				$configuration_names = array_keys($this->_configurations);
			}

			// This will contain an array of telephone number matches; a telephone number can be valid multiple times depending
			// on the configuration (e.g. a telephone number might be valid in New Zealand and Australia)
			$telephone_matches = array();

			foreach ($configuration_names as $configuration_name)
			{
				assert(is_array($this->_configurations[$configuration_name]['area_codes_and_local_numbers_general']) === TRUE);
				assert(is_array($this->_configurations[$configuration_name]['area_codes_and_local_numbers_landline']) === TRUE);
				assert(is_array($this->_configurations[$configuration_name]['area_codes_and_local_numbers_mobile']) === TRUE);

				// Asserts placed in if-statement so they can be read a bit easier
				if ($this->_configurations[$configuration_name]['area_codes_and_local_numbers_general'] === array())
				{
					// area_codes_and_local_numbers_general is an empty array, so area_codes_and_local_numbers_landline and area_codes_and_local_numbers_mobile better be set
					assert($this->_configurations[$configuration_name]['area_codes_and_local_numbers_landline'] !== array());
					assert($this->_configurations[$configuration_name]['area_codes_and_local_numbers_mobile'] !== array());
				}
				else
				{
					// area_codes_and_local_numbers_general is NOT an empty array, so area_codes_and_local_numbers_landline and area_codes_and_local_numbers_mobile better NOT be set
					assert($this->_configurations[$configuration_name]['area_codes_and_local_numbers_landline'] === array());
					assert($this->_configurations[$configuration_name]['area_codes_and_local_numbers_mobile'] === array());
				}

				$international_call_prefixes_regex = implode('|', array_keys($this->_international_call_prefixes));
				$country_code_regex                = $this->_configurations[$configuration_name]['country_code_regex'];
				$trunk_prefix_regex                = $this->_configurations[$configuration_name]['trunk_prefix_regex'];

				// Combine all the area code/local number regexes into 1 array and then loop
				$area_codes_and_local_numbers = array();
				$area_codes_and_local_numbers = $this->_configurations[$configuration_name]['area_codes_and_local_numbers_general'] + $this->_configurations[$configuration_name]['area_codes_and_local_numbers_landline'] + $this->_configurations[$configuration_name]['area_codes_and_local_numbers_mobile'];

				foreach ($area_codes_and_local_numbers as $area_code_regex => $local_number_regexes)
				{
					// There should always be an array (with at least one element) of local numbers regex
					assert(is_array($local_number_regexes) === TRUE);
					assert($local_number_regexes !== array());

					$area_code_and_local_number_regex = '(?P<area_code>' . $area_code_regex . ')(?P<local_number>' . implode('|', $local_number_regexes) . ')';
					$regex                            = '/^(?:(?:(?P<international_call_prefix>' . $international_call_prefixes_regex . ')?(?P<country_code>' . $country_code_regex . '))|(?P<trunk_prefix>' . $trunk_prefix_regex . '))?' . $area_code_and_local_number_regex . '$/';
					$success                          = preg_match($regex, $telephone_number, $matches);
					assert($success !== FALSE);

					if ($matches !== array())
					{
						$formatted_telephone_number_matches = $this->_get_formatted_telephone_number_matches($matches, $configuration_name);
						if ($formatted_telephone_number_matches !== array())
						{
							// $telephone_number is valid and was formatted successfully; add it to the final result
							$telephone_matches[$configuration_name][] = $formatted_telephone_number_matches;
						}
					}
				}
			}

			return $telephone_matches;
		}
	}
