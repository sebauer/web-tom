<?php
# Define parse_ini_string if it doesn't exist.
# Does accept lines starting with ; as comments
# Does not accept comments after values
if( !function_exists('parse_ini_string') ){

	function parse_ini_string( $string ) {
		$array = Array();

		$lines = explode("\n", $string );

		foreach( $lines as $line ) {
			$statement = preg_match(
"/^(?!;)(?P<key>[\w+\.\-]+?)\s*=\s*(?P<value>.+?)\s*$/", $line, $match );

			if( $statement ) {
				$key    = $match[ 'key' ];
				$value    = $match[ 'value' ];

				# Remove quote
				if( preg_match( "/^\".*\"$/", $value ) || preg_match( "/^'.*'$/", $value ) ) {
					$value = mb_substr( $value, 1, mb_strlen( $value ) - 2 );
				}

				$array[ $key ] = $value;
			}
		}
		return $array;
	}
}
?>