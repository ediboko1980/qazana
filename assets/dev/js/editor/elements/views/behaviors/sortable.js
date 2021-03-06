var SortableBehavior;

SortableBehavior = Marionette.Behavior.extend( {
	defaults: {
		elChildType: 'widget',
	},

	events: {
		sortstart: 'onSortStart',
		sortreceive: 'onSortReceive',
		sortupdate: 'onSortUpdate',
		sortover: 'onSortOver',
		sortout: 'onSortOut',
	},

	initialize: function() {
		this.listenTo( qazana.channels.dataEditMode, 'switch', this.onEditModeSwitched )
			.listenTo( qazana.channels.deviceMode, 'change', this.onDeviceModeChange )
			.listenTo( this.view.options.model, 'request:sort:start', this.startSort )
			.listenTo( this.view.options.model, 'request:sort:update', this.updateSort )
			.listenTo( this.view.options.model, 'request:sort:receive', this.receiveSort );
	},

	onEditModeSwitched: function( activeMode ) {
		if ( 'edit' === activeMode ) {
			this.activate();
		} else {
			this.deactivate();
		}
	},

	onDeviceModeChange: function() {
		var deviceMode = qazana.channels.deviceMode.request( 'currentMode' );

		if ( 'desktop' === deviceMode ) {
			this.activate();
		} else {
			this.deactivate();
		}
	},

	onRender: function() {
		var self = this;

		_.defer( function() {
			self.onEditModeSwitched( qazana.channels.dataEditMode.request( 'activeMode' ) );
		} );
	},

	onDestroy: function() {
		this.deactivate();
	},

	activate: function() {
		if ( ! qazana.userCan( 'design' ) ) {
			return;
		}
		if ( this.getChildViewContainer().sortable( 'instance' ) ) {
			return;
		}

		var $childViewContainer = this.getChildViewContainer(),
			defaultSortableOptions = {
				connectWith: $childViewContainer.selector,
				placeholder: 'qazana-sortable-placeholder qazana-' + this.getOption( 'elChildType' ) + '-placeholder',
				cursorAt: {
					top: 20,
					left: 25,
				},
				helper: this._getSortableHelper.bind( this ),
				cancel: 'input, textarea, button, select, option, .qazana-inline-editing, .qazana-tab-title',

			},
			sortableOptions = _.extend( defaultSortableOptions, this.view.getSortableOptions() );

		$childViewContainer.sortable( sortableOptions );
	},

	_getSortableHelper: function( event, $item ) {
		var model = this.view.collection.get( {
			cid: $item.data( 'model-cid' ),
		} );

		return '<div style="height: 84px; width: 125px;" class="qazana-sortable-helper qazana-sortable-helper-' + model.get( 'elType' ) + '"><div class="icon"><i class="' + model.getIcon() + '"></i></div><div class="qazana-element-title-wrapper"><div class="title">' + model.getTitle() + '</div></div></div>';
	},

	getChildViewContainer: function() {
		return this.view.getChildViewContainer( this.view );
	},

	deactivate: function() {
		var childViewContainer = this.getChildViewContainer();

		if ( childViewContainer.sortable( 'instance' ) ) {
			childViewContainer.sortable( 'destroy' );
		}
	},

	startSort: function( event, ui ) {
		event.stopPropagation();

		var model = this.view.collection.get( {
			cid: ui.item.data( 'model-cid' ),
		} );

		qazana.channels.data
			.reply( 'dragging:model', model )
			.reply( 'dragging:parent:view', this.view )
			.trigger( 'drag:start', model )
			.trigger( model.get( 'elType' ) + ':drag:start' );
	},

	updateSort: function( ui ) {
		var model = qazana.channels.data.request( 'dragging:model' ),
			$childElement = ui.item,
			collection = this.view.collection,
			newIndex = $childElement.parent().children().index( $childElement ),
			child = this.view.children.findByModelCid( model.cid );

		this.view.addChildElement( model.clone(), {
			at: newIndex,
			trigger: {
				beforeAdd: 'drag:before:update',
				afterAdd: 'drag:after:update',
			},
			onBeforeAdd: function() {
				child._isRendering = true;

				collection.remove( model );
			},
		} );

		qazana.saver.setFlagEditorChange( true );
	},

	receiveSort: function( event, ui ) {
		event.stopPropagation();

		if ( this.view.isCollectionFilled() ) {
			jQuery( ui.sender ).sortable( 'cancel' );

			return;
		}

		var model = qazana.channels.data.request( 'dragging:model' ),
			draggedElType = model.get( 'elType' ),
			draggedIsInnerSection = 'section' === draggedElType && model.get( 'isInner' ),
			targetIsInnerColumn = 'column' === this.view.getElementType() && this.view.isInner();

		if ( draggedIsInnerSection && targetIsInnerColumn ) {
			jQuery( ui.sender ).sortable( 'cancel' );

			return;
		}

		var newIndex = ui.item.index(),
			modelData = model.toJSON( { copyHtmlCache: true } );

		this.view.addChildElement( modelData, {
			at: newIndex,
			trigger: {
				beforeAdd: 'drag:before:update',
				afterAdd: 'drag:after:update',
			},
			onAfterAdd: function() {
				var senderSection = qazana.channels.data.request( 'dragging:parent:view' );

				senderSection.isManualRemoving = true;

				model.destroy();

				senderSection.isManualRemoving = false;
			},
		} );
	},

	onSortStart: function( event, ui ) {
		if ( 'column' === this.options.elChildType ) {
			var uiData = ui.item.data( 'sortableItem' ),
				uiItems = uiData.items,
				itemHeight = 0;

			uiItems.forEach( function( item ) {
				if ( item.item[ 0 ] === ui.item[ 0 ] ) {
					itemHeight = item.height;
					return false;
				}
			} );

			ui.placeholder.height( itemHeight );
		}

		this.startSort( event, ui );
	},

	onSortOver: function( event ) {
		event.stopPropagation();

		var model = qazana.channels.data.request( 'dragging:model' );

		jQuery( event.target )
			.addClass( 'qazana-draggable-over' )
			.attr( {
				'data-dragged-element': model.get( 'elType' ),
				'data-dragged-is-inner': model.get( 'isInner' ),
			} );

		this.$el.addClass( 'qazana-dragging-on-child' );
	},

	onSortOut: function( event ) {
		event.stopPropagation();

		jQuery( event.target )
			.removeClass( 'qazana-draggable-over' )
			.removeAttr( 'data-dragged-element data-dragged-is-inner' );

		this.$el.removeClass( 'qazana-dragging-on-child' );
	},

	onSortReceive: function( event, ui ) {
		this.receiveSort( event, ui );
	},

	onSortUpdate: function( event, ui ) {
		event.stopPropagation();

		if ( this.getChildViewContainer()[ 0 ] !== ui.item.parent()[ 0 ] ) {
			return;
		}

		this.updateSort( ui );
	},

	onAddChild: function( view ) {
		view.$el.attr( 'data-model-cid', view.model.cid );
	},
} );

module.exports = SortableBehavior;
