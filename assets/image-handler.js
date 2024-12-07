
export default class ImageHandler {
	constructor() {
	}

	hydrate()
	{
		this.getFallbackElements().forEach((element, index, self) => {
			this.handlePutSvgElementInImage(element)
			element.addEventListener("error", this.handleOnErrorEvent)
		})

		return this;
	}

	getFallbackElements()
	{
		return document.querySelectorAll("[data-icon-on-error]")
	}

	/**
	 * Puts an empty svg bellow an image.
	 */
	handlePutSvgElementInImage(element)
	{
		const elementIconName = element.getAttribute("data-icon-on-error");

		if(elementIconName.length > 0) {
			//create new element and add necessary attributes and classes to hide it
			const iconifyIconTag = document.createElement("iconify-icon")
			iconifyIconTag.classList.add("icon")
			iconifyIconTag.classList.add("has-danger")
			iconifyIconTag.classList.add("is-hidden")
			iconifyIconTag.setAttribute("icon", elementIconName)

			//append to figure
			element.parentElement.appendChild(iconifyIconTag);
		}
	}

	handleOnErrorEvent(event)
	{
		const targetImage = event.target;
		targetImage.classList.add("is-hidden")

		//get the iconify icon and display it
		const iconifyNode = targetImage.parentElement.querySelector("iconify-icon");
		iconifyNode.classList.remove("is-hidden")
	}
}