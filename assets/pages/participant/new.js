import Modal from "../../modal-handler";

document.addEventListener("DOMContentLoaded", () => {
	new Main();
})

class Main {
	submitCalculateDistance;
	inputLatitude;
	inputLongitude;

	constructor() {
		this.inputLatitude = document.getElementById("latitude-input")
		this.inputLongitude = document.getElementById("longitude-input")
		this.submitCalculateDistance = document.getElementById("submit-calculate-distance")

		const modal = new Modal(
			"#open-coordinates-modal",
			"#target-modal-coordinates"
		)
	}

	_hydrate()
	{
		this.submitCalculateDistance.addEventListener("click", (event) => {
			if(this.areFieldsValid()) {

			}
		})
	}

	/**
	 * Returns true of false if the fields inside the modal are valid or not
	 */
	areFieldsValid()
	{

	}

	/**
	 * Handle sending the data to the API
	 * @private
	 */
	_handleSendApi()
	{

	}
}
