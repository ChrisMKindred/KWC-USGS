/**
 * WordPress dependencies
 */

import { TextControl } from '@wordpress/components';

 const Edit = ( props ) => {
	const {
		attributes: { message },
		setAttributes,
	} = props;
	return (
		<div className="usgs-block">
			<TextControl
				label="USGS Location"
				value={ message }
				onChange={ ( value ) => setAttributes( { message: value } ) }
			/>
		</div>
	);
 };
 export default Edit;
