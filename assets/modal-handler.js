export default class Modal
{
	/** @var {HTMLElement} */
	openElement;

	/** @var {NodeListOf<HTMLElement>} */
	closeElements;

	/** @var {HTMLElement} */
	targetModal;

	/**
	 * Instantiate a new modal to be opened and closed
	 * @param {string} openElement
	 * @param {string|undefined} closeElements
	 * @param {string} targetModal
	 */
	constructor(openElement, targetModal, closeElements = undefined) {
		this.openElement = document.querySelector(openElement);
		this.targetModal = document.querySelector(targetModal);

		//bulma's recommended classes by default
		//https://bulma.io/documentation/components/modal/#javascript-implementation-example
		closeElements ??= ".modal-background, .modal-close, .modal-card-head .delete, .modal-card-foot .button"
		this.closeElements = document.querySelectorAll(closeElements);

		this._hydrate()
	}

	/**
	 * Hydrates the modal's events.
	 */
	_hydrate() {
		//hydrate open the modal
		this.openElement.addEventListener("click", () => {
			this.openModal()
		})

		//hydrate each elements to close the modal
		this.closeElements.forEach((element) => {
			element.addEventListener("click", () => {
				this.closeModal()
			})
		})
	}

	/**
	 * Opens the modal
	 */
	openModal() {
		this.targetModal.classList.add("is-active")
	}

	/**
	 * Closes the modal
	 */
	closeModal() {
		this.targetModal.classList.remove("is-active")
	}


}