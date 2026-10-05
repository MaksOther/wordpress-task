( function () {
	'use strict';

	const config = window.afeData || {};
	const i18n = config.i18n || {};
	const REDIRECT_DELAY = 700;

	function closest( target, selector ) {
		return target && 'function' === typeof target.closest ? target.closest( selector ) : null;
	}

	function showPanel( root, name, moveFocus ) {
		root.querySelectorAll( '[data-afe-panel]' ).forEach( ( panel ) => {
			panel.hidden = panel.dataset.afePanel !== name;
		} );

		const activeTab = 'lost' === name ? 'login' : name;

		root.querySelectorAll( '[data-afe-tab]' ).forEach( ( tab ) => {
			const isActive = tab.dataset.afeTab === activeTab;

			tab.classList.toggle( 'is-active', isActive );
			tab.setAttribute( 'aria-selected', isActive ? 'true' : 'false' );
			tab.tabIndex = isActive ? 0 : -1;
		} );

		if ( moveFocus ) {
			const input = root.querySelector( '[data-afe-panel="' + name + '"] .afe__input' );

			if ( input ) {
				input.focus();
			}
		}
	}

	function setMessage( form, text, type ) {
		const box = form.querySelector( '.afe__message' );

		if ( ! box ) {
			return;
		}

		box.textContent = text || '';
		box.className = 'afe__message' + ( type ? ' afe__message--' + type : '' );
		box.hidden = ! text;
	}

	function setFieldError( field, text ) {
		const input = field.querySelector( '.afe__input' );
		const error = field.querySelector( '.afe__error' );

		if ( input ) {
			if ( text ) {
				input.setAttribute( 'aria-invalid', 'true' );
			} else {
				input.removeAttribute( 'aria-invalid' );
			}
		}

		if ( error ) {
			error.textContent = text || '';
			error.hidden = ! text;
		}
	}

	function clearErrors( form ) {
		setMessage( form, '' );
		form.querySelectorAll( '[data-afe-field]' ).forEach( ( field ) => setFieldError( field, '' ) );
	}

	function showErrors( form, data ) {
		const errors = data.errors && 'object' === typeof data.errors ? data.errors : {};
		let firstInvalid = null;

		Object.keys( errors ).forEach( ( name ) => {
			const field = form.querySelector( '[data-afe-field="' + name + '"]' );

			if ( field ) {
				setFieldError( field, String( errors[ name ] ) );
				firstInvalid = firstInvalid || field.querySelector( '.afe__input' );
			}
		} );

		setMessage( form, data.message || i18n.error, 'error' );

		if ( firstInvalid ) {
			firstInvalid.focus();
		}
	}

	function setBusy( form, isBusy ) {
		const button = form.querySelector( '.afe__button' );

		form.classList.toggle( 'is-loading', isBusy );
		form.setAttribute( 'aria-busy', isBusy ? 'true' : 'false' );

		if ( button ) {
			button.disabled = isBusy;
		}
	}

	function getRecaptchaToken( action ) {
		const siteKey = config.recaptchaSiteKey;

		if ( ! siteKey || ! window.grecaptcha ) {
			return Promise.resolve( '' );
		}

		return new Promise( ( resolve ) => {
			window.grecaptcha.ready( () => {
				window.grecaptcha.execute( siteKey, { action } ).then( resolve, () => resolve( '' ) );
			} );
		} );
	}

	function leave( url ) {
		window.setTimeout( () => {
			if ( url ) {
				window.location.assign( url );
			} else {
				window.location.reload();
			}
		}, REDIRECT_DELAY );
	}

	async function submitForm( form ) {
		const root = form.closest( '[data-afe]' );

		if ( form.classList.contains( 'is-loading' ) ) {
			return;
		}

		clearErrors( form );

		if ( root && root.hasAttribute( 'data-afe-preview' ) ) {
			setMessage( form, i18n.preview, 'info' );
			return;
		}

		setBusy( form, true );

		let leaving = false;

		try {
			const body = new FormData( form );
			const token = await getRecaptchaToken( String( body.get( 'action' ) ) );

			if ( token ) {
				body.append( 'afe_recaptcha', token );
			}

			const response = await fetch( config.ajaxUrl || form.getAttribute( 'action' ), {
				method: 'POST',
				body,
				credentials: 'same-origin',
				headers: { 'X-Requested-With': 'XMLHttpRequest' },
			} );
			const json = await response.json().catch( () => null );

			if ( ! json || 'object' !== typeof json ) {
				throw new Error( 'Unexpected response' );
			}

			const data = json.data && 'object' === typeof json.data ? json.data : {};

			if ( json.success ) {
				setMessage( form, form.dataset.afeSuccess || data.message, 'success' );

				if ( 'lost' === form.dataset.afeForm ) {
					form.reset();
				} else {
					leaving = true;
					leave( data.redirect );
				}
			} else {
				showErrors( form, data );

				if ( data.reload ) {
					leaving = true;
					leave( '' );
				}
			}
		} catch ( error ) {
			setMessage( form, i18n.error, 'error' );
		} finally {
			if ( ! leaving ) {
				setBusy( form, false );
			}
		}
	}

	function togglePassword( button ) {
		const control = button.closest( '.afe__control' );
		const input = control ? control.querySelector( '.afe__input' ) : null;

		if ( ! input ) {
			return;
		}

		const reveal = 'password' === input.type;

		input.type = reveal ? 'text' : 'password';
		button.setAttribute( 'aria-pressed', reveal ? 'true' : 'false' );
		button.setAttribute( 'aria-label', reveal ? button.dataset.afeHideLabel : button.dataset.afeShowLabel );
	}

	document.addEventListener( 'click', ( event ) => {
		const root = closest( event.target, '[data-afe]' );
		const tab = closest( event.target, '[data-afe-tab]' );
		const link = closest( event.target, '[data-afe-show]' );
		const toggle = closest( event.target, '[data-afe-toggle]' );

		if ( ! root ) {
			return;
		}

		if ( tab ) {
			showPanel( root, tab.dataset.afeTab, false );
		} else if ( link ) {
			event.preventDefault();
			showPanel( root, link.dataset.afeShow, true );
		} else if ( toggle ) {
			togglePassword( toggle );
		}
	} );

	document.addEventListener( 'keydown', ( event ) => {
		const tab = closest( event.target, '[data-afe-tab]' );

		if ( ! tab || ( 'ArrowLeft' !== event.key && 'ArrowRight' !== event.key ) ) {
			return;
		}

		const tabs = Array.from( tab.parentElement.querySelectorAll( '[data-afe-tab]' ) );
		const step = 'ArrowRight' === event.key ? 1 : -1;
		const next = tabs[ ( tabs.indexOf( tab ) + step + tabs.length ) % tabs.length ];

		event.preventDefault();
		showPanel( tab.closest( '[data-afe]' ), next.dataset.afeTab, false );
		next.focus();
	} );

	document.addEventListener( 'submit', ( event ) => {
		const form = closest( event.target, '[data-afe-form]' );

		if ( form ) {
			event.preventDefault();
			submitForm( form );
		}
	} );

	document.addEventListener( 'input', ( event ) => {
		const field = closest( event.target, '.afe [data-afe-field]' );

		if ( field && field.querySelector( '[aria-invalid="true"]' ) ) {
			setFieldError( field, '' );
		}
	} );
}() );
