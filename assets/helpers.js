/**
 * Returns true if number is in range (inclusive)
 * @param min
 * @param max
 * @param value
 * @returns {boolean}
 */
function isInRange(min, max, value) {
	return min <= value && value <= max;
}

/**
 * Returns true if the value provided can be a number.
 * @param value
 * @returns {boolean}
 */
function isNumber(value) {
	return !isNaN(value) && !isNaN(parseFloat(value));
}

/**
 * Appends a bulma-styled error to the provided form field element.
 * @param element
 * @param message
 */
function appendBulmaFormError(element, message)
{
	const errorContainer = document.createElement("div")
	errorContainer.classList.add("help", "message", "is-danger")

	const messageContainer = document.createElement("p")
	messageContainer.classList.add("message-body")
	messageContainer.textContent = message

	errorContainer.appendChild(messageContainer)
	element.append(errorContainer)
}

/**
 * Deletes all bulma elements
 * @param element
 */
function clearBulmaErrors(element)
{
	const allErrorNodes = element.querySelectorAll(".help.message") ?? [];

	allErrorNodes.forEach((value, index, self) => {
		value.remove()
	})
}

/**
 * Base URL of the API of the app
 * @type {string}
 */
const API_ENTRYPOINT = "http://localhost:8000"

export {isInRange, isNumber, appendBulmaFormError, clearBulmaErrors, API_ENTRYPOINT}