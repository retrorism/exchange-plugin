if ( typeof acf != 'undefined' ) {
	acf.add_action('load', function( $el ){

		// $el will be equivalent to $('body')

		// find a specific field
		var $field = $el.find('#acf-field_579f123511673, #acf-field_57a0a3eff2d3c, #acf-field_57a0a397c1cd6' );

		console.log( $field );
		$field[0].disabled = "disabled";
		// do something to $field

	});
}
