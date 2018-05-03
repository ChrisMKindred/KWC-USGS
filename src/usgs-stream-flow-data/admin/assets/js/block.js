const { __ } = wp.i18n;
const {
	registerBlockType,
	RichText,
} = wp.blocks;

const {
	CheckboxControl
} = wp.components;


registerBlockType( 'usgs-stream-flow-data/usgs-block', {
    title: 'usgs',
    icon: 'carrot',
	category: 'common',
	attributes: {
		title: {
			type: 'array',
			source: 'children',
			selector: 'h2',
		},

	},
    edit: props => {
		const focusedEditable = props.focus ? props.focus.editable || 'title' : null;
		const attributes = props.attributes;
		const onChangeTitle = value => {
			props.setAttributes( { title: value } );
		};
		const onFocusTitle = focus => {
			props.setFocus( _.extend( {}, focus, { editable: 'title' } ) );
		};



		return (
			<div className={ props.className }>
				<RichText
					tagName="h2"
					placeholder={ __( 'a title goes here' ) }
					value={ attributes.title }
					onChange={ onChangeTitle }
					focus={ focusedEditable === 'title' }
					onFocus={ onFocusTitle }
				/>
				<CheckboxControl
					heading="User"
					label="Is author"
					help="Is the user a author or not?"
				/>
			</div>
		);

    },
    save: props => {
        return (
			<p> { props.attributes.title } </p>
		);
    },
} );
