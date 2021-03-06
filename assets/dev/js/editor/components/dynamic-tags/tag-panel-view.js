var TagControlsStack = require( 'qazana-dynamic-tags/tag-controls-stack' ),
	SettingsModel = require( 'qazana-elements/models/base-settings' );

module.exports = Marionette.ItemView.extend( {

	className: 'qazana-dynamic-cover qazana-input-style',

	tagControlsStack: null,

	templateHelpers: function() {
		var helpers = {};
		if ( this.model ) {
			helpers.controls = this.model.options.controls;
		}

		return helpers;
	},

	ui: {
		remove: '.qazana-dynamic-cover__remove',
	},

	events: function() {
		var events = {
			'click @ui.remove': 'onRemoveClick',
		};

		if ( this.hasSettings() ) {
			events.click = 'onClick';
		}

		return events;
	},

	getTemplate: function() {
		var config = this.getTagConfig(),
			templateFunction = Marionette.TemplateCache.get( '#tmpl-qazana-control-dynamic-cover' ),
			renderedTemplate = Marionette.Renderer.render( templateFunction, {
				hasSettings: this.hasSettings(),
				isRemovable: ! this.getOption( 'dynamicSettings' ).default,
				title: config.title,
				content: config.panel_template,
			} );

		return Marionette.TemplateCache.prototype.compileTemplate( renderedTemplate.trim() );
	},

	getTagConfig: function() {
		return qazana.dynamicTags.getConfig( 'tags.' + this.getOption( 'name' ) );
	},

	initSettingsPopup: function() {
		var settingsPopupOptions = {
			className: 'qazana-tag-settings-popup',
			position: {
				my: 'left top+5',
				at: 'left bottom',
				of: this.$el,
				autoRefresh: true,
			},
		};

		var settingsPopup = qazana.dialogsManager.createWidget( 'buttons', settingsPopupOptions );

		this.getSettingsPopup = function() {
			return settingsPopup;
		};
	},

	hasSettings: function() {
		return !! Object.values( this.getTagConfig().controls ).length;
	},

	showSettingsPopup: function() {
		if ( ! this.tagControlsStack ) {
			this.initTagControlsStack();
		}

		var settingsPopup = this.getSettingsPopup();

		if ( settingsPopup.isVisible() ) {
			return;
		}

		settingsPopup.show();
	},

	initTagControlsStack: function() {
		this.tagControlsStack = new TagControlsStack( {
			model: this.model,
			controls: this.model.controls,
			el: this.getSettingsPopup().getElements( 'message' )[ 0 ],
		} );

		this.tagControlsStack.render();
	},

	initModel: function() {
		this.model = new SettingsModel( this.getOption( 'settings' ), {
			controls: this.getTagConfig().controls,
		} );
	},

	initialize: function() {
		if ( ! this.hasSettings() ) {
			return;
		}

		this.initModel();

		this.initSettingsPopup();

		this.listenTo( this.model, 'change', this.render );
	},

	onClick: function() {
		this.showSettingsPopup();
	},

	onRemoveClick: function( event ) {
		event.stopPropagation();

		this.destroy();

		this.trigger( 'remove' );
	},

	onDestroy: function() {
		if ( this.hasSettings() ) {
			this.getSettingsPopup().destroy();
		}
	},
} );
