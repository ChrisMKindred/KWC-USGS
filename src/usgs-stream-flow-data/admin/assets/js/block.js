const { __ } = wp.i18n;
const { registerBlockType,} = wp.blocks;
const { CheckboxControl, SelectControl } = wp.components;

registerBlockType( 'usgs-stream-flow-data/usgs-block', {
    title: 'usgs',
    icon: 'carrot',
	category: 'common',
	attributes: {
		state: {
			source: 'attribute',
			type: 'string',
		},
		title: {
			type: 'text',
			default: '',
		},
		graph: {
			type: 'text',
			default: 'false',
		},
		siteID: {
			type: 'text',
			default: '09080400',
		}
	},

    edit: ({attributes,setAttributes,className,sitedata}) => { 
		function onChangeURL(e){
			fetch('https://waterservices.usgs.gov/nwis/iv?stateCd='+ e +'&format=JSON&parameterCd=00060')
			.then(function(response){
				if (response.status >= 400) {
					throw new Error("Bad response from server");
				}
				return response.json();
			})
			.then(function(data){
				sitedata = data.value.timeSeries;	
			});
			setAttributes({state: this.value});
		}
	return <div className={className}>
				<SelectControl
					label={ __('State')}
					value={attributes.url}
					options={[
						{value:'AL', label:'Alabama'},
						{value:'AK', label:'Alaska'},
						{value:'AZ', label:'Arizona'},
						{value:'AR', label:'Arkansas'},
						{value:'CA', label:'California'},
						{value:'CO', label:'Colorado'},
						{value:'CT', label:'Connecticut'},
						{value:'DE', label:'Delaware'},
						{value:'DC', label:'District Of Columbia'},
						{value:'FL', label:'Florida'},
						{value:'GA', label:'Georgia'},
						{value:'HI', label:'Hawaii'},
						{value:'ID', label:'Idaho'},
						{value:'IL', label:'Illinois'},
						{value:'IN', label:'Indiana'},
						{value:'IA', label:'Iowa'},
						{value:'KS', label:'Kansas'},
						{value:'KY', label:'Kentucky'},
						{value:'LA', label:'Louisiana'},
						{value:'ME', label:'Maine'},
						{value:'MD', label:'Maryland'},
						{value:'MA', label:'Massachusetts'},
						{value:'MI', label:'Michigan'},
						{value:'MN', label:'Minnesota'},
						{value:'MS', label:'Mississippi'},
						{value:'MO', label:'Missouri'},
						{value:'MT', label:'Montana'},
						{value:'NE', label:'Nebraska'},
						{value:'NV', label:'Nevada'},
						{value:'NH', label:'New Hampshire'},
						{value:'NJ', label:'New Jersey'},
						{value:'NM', label:'New Mexico'},
						{value:'NY', label:'New York'},
						{value:'NC', label:'North Carolina'},
						{value:'ND', label:'North Dakota'},
						{value:'OH', label:'Ohio'},
						{value:'OK', label:'Oklahoma'},
						{value:'OR', label:'Oregon'},
						{value:'PA', label:'Pennsylvania'},
						{value:'RI', label:'Rhode Island'},
						{value:'SC', label:'South Carolina'},
						{value:'SD', label:'South Dakota'},
						{value:'TN', label:'Tennessee'},
						{value:'TX', label:'Texas'},
						{value:'UT', label:'Utah'},
						{value:'VT', label:'Vermont'},
						{value:'VA', label:'Virginia'},
						{value:'WA', label:'Washington'},
						{value:'WV', label:'West Virginia'},
						{value:'WI', label:'Wisconsin'},
						{value:'WY', label:'Wyoming'},
					]}
					onChange={onChangeURL}
				/>
				Total Sites: {(sitedata) ? ( sitedata.length ) : '0' }
				<ul class='sitename'>
					{(sitedata) ? (
						sitedata.map( (value) => {
							return <li> { value.sourceInfo.siteName } </li>;
						}) ) : ''}
				</ul>
			</div>;
	},

	save: () => {return null;}
} );
