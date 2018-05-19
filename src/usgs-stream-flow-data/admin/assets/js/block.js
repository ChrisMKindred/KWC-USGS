const { __ } = wp.i18n;
const { registerBlockType, RichText,} = wp.blocks;
const { CheckboxControl } = wp.components;

registerBlockType( 'usgs-stream-flow-data/usgs-block', {
    title: 'usgs',
    icon: 'carrot',
	category: 'common',
	attributes: {
		url: {
			source: 'attribute',
			type: 'string',
			selector: '.o_microlink',
			attribute: 'href',
		},
		title: {
			type: 'string',
			source: 'text',
			selector: '.o_microlink',
		}
	},

    edit: ( { attributes, setAttributes, className } ) => { 
		const onChangeURL = ( value ) => {
			
			console.log( 'here1' );	
			const response = fetch( `https://waterservices.usgs.gov/nwis/dv/?site=09080400&format=JSON`,
				{
				cache: 'no-cache',
				headers: {
					'user-agent': 'WP Block',
					'content-type': 'application/json'
				  },
				method: 'GET',
				redirect: 'follow', 
				referrer: 'no-referrer', 
			})
			.then(
				returned => {
					if (returned.ok) return returned;
					throw new Error('Network response was not ok.');
				}
			);
			console.log( response );
			let data = response;
			setAttributes( { url: value[0] } );
			setAttributes( { title: data.value } );
		};
	return <div className={className}>
					<RichText
						tagName="div"
						placeholder={__('Add URL here.')}
						value={attributes.url}
						onChange={onChangeURL}
					/>
					{!attributes.title ? __('Add URL') : <div> {attributes.title} </div>}
				</div>;
	},

	save: ({ attributes, className }) => {
		return <div className={ className }>
				<a className="o_microlink" href={ attributes.url }> { attributes.title } </a>
			</div>;
	}
} );
