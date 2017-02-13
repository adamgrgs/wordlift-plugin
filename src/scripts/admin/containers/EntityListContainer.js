/**
 * Containers: Entity List.
 *
 * The `EntityListContainer` follows the `react-redux` pattern to bind specific
 * parts of the application state and dispatchers to the contained components.
 *
 * The `EntityListContainer` contains the `EntityList` component.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */
import { connect } from 'react-redux';

/**
 * Internal dependencies
 */
import { toggleEntity } from '../actions';
import EntityList from '../components/EntityList';

/**
 * Map the state to React components' properties.
 *
 * @since 3.11.0
 *
 * @param {object} state A state instance.
 * @returns {{entities}} An object with the list of entities.
 */
const mapStateToProps = ( state ) => {
	return {
		entities: state.entities
	};
};

/**
 * Map dispatchers to React components' properties.
 *
 * @since 3.11.0
 * @param {function} dispatch Redux's dispatch function.
 * @returns {{onClick: (function)}} A list of dispatchers.
 */
const mapDispatchToProps = ( dispatch ) => {
	return {
		/**
		 * The `onClick` dispatchers used by `EntityTile` component.
		 *
		 * @since 3.11.0
		 * @param {object} entity The entity instance being clicked.
		 */
		onClick: ( entity ) => {
			dispatch( toggleEntity( entity ) );
		}
	};
};

/**
 * The `EntityListContainer` instance built by `react-redux` by connecting the
 * store with the state and dispatchers merged to properties passed to the React
 * components.
 *
 * @since 3.11.0
 */
const EntityListContainer = connect(
	mapStateToProps,
	mapDispatchToProps
)( EntityList );

// Finally export the `EntityListContainer`.
export default EntityListContainer;
