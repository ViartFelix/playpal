import Modal from "../../modal-handler";
import {API_ENTRYPOINT, appendBulmaFormError, clearBulmaErrors, isInRange, isNumber} from "../../helpers";
import axios from "axios";
import EventData from "./EventData";

document.addEventListener("DOMContentLoaded", () => {
	new Main();
})

class Main {
	/** @var HTMLButtonElement Button to submit the fields for the API call */
	submitCalculateDistance;

	/** @var HTMLInputElement Input for the latitude. */
	inputLatitude;

	/** @var HTMLInputElement Input for the longitude. */
	inputLongitude;

	/** @var EventData The event's data */
	eventData;

	/** @var HTMLDivElement Container for the results after the API call */
	containerResults;

	constructor() {
		this.inputLatitude = document.getElementById("latitude-input")
		this.inputLongitude = document.getElementById("longitude-input")
		this.submitCalculateDistance = document.getElementById("submit-calculate-distance")
		this.containerResults = document.getElementById("lat-lon-result")

		const modal = new Modal(
			"#open-coordinates-modal",
			"#target-modal-coordinates"
		)

		this.eventData = new EventData();
		this.eventData.fromWindow();

		this._hydrate()
	}

	/**
	 * Hydrates the page of its events
	 * @private
	 */
	_hydrate()
	{
		this.submitCalculateDistance.addEventListener("click", (event) => {
			clearBulmaErrors(this.getFieldContainerLongitude())
			clearBulmaErrors(this.getFieldContainerLatitude())

			if(this.areFieldsValid()) {
				this._handleSendApi()
			}
		})
	}

	/**
	 * Returns true of false if the fields inside the modal are valid or not.
	 */
	areFieldsValid()
	{
		try {
			const longitude = this.inputLongitude.value;
			const latitude = this.inputLatitude.value;

			const parentLatitudeElement = this.getFieldContainerLatitude()
			const parentLongitudeElement = this.getFieldContainerLongitude()

			if(!isNumber(latitude)) {
				appendBulmaFormError(parentLatitudeElement, "Please enter a valid number.")
				return false;
			}

			if(!isInRange(-90, 90, Number.parseFloat(latitude))) {
				appendBulmaFormError(parentLatitudeElement, "Please enter a valid latitude (between -90 and 90).")
				return false;
			}

			if(!isNumber(longitude)) {
				appendBulmaFormError(parentLongitudeElement, "Please enter a valid number.")
				return false;
			}

			if(!isInRange(-180, 180, Number.parseFloat(longitude))) {
				appendBulmaFormError(parentLongitudeElement, "Please enter a valid longitude (between -180 and 180).")
				return false;
			}

			return true
		} catch (e) {
			return false;
		}
	}

	/**
	 * Handle sending the data to the API
	 * @private
	 */
	_handleSendApi()
	{
		this.enableLoadingButton()

		const eventData = this.eventData.toObject();

		if(eventData.id === undefined || !isNumber(eventData.id)) {
			alert("Unknown error when fetching data for the distance. Please reload the page.")
		}

		const eventId = eventData.id

		axios.get(API_ENTRYPOINT + "/events/" + eventId + "/distance", {
			params: this.getGetParamsForApiCall()
		})
			.then((r) => {
				this._handleApiResponse(r.data)
			})
			.catch((e) => {
				console.log(e)
			}).finally(() => {
				this.disableLoadingButton()
			})
	}

	/**
	 * Handles the response of the API to display infos
	 * @param data r.data of axios
	 * @private
	 */
	_handleApiResponse(data)
	{
		this.clearPreviousApiResults();

		const cleanData = data.bonus.clean;

		const distanceValueElement = this.containerResults.querySelector("#calculated-distance")
		const distanceUnitElement = this.containerResults.querySelector("#distance-unit")
		const cleanUnitElement = this.containerResults.querySelector("#distance-unit-clean")

		this.containerResults.classList.remove("is-hidden")

		//if the 'you are here' is set
		if(!isNumber(cleanData.value)) {
			distanceValueElement.textContent = " the right place "
			cleanUnitElement.textContent = "Here"

			//hide certain fields
			distanceUnitElement.classList.add("is-hidden")
		} else {
			//take the clean value and round to 3 digits
			const cleanValueFloat = Number.parseFloat(cleanData.value);

			//re-parse as float because we don't want zeroes at the end of the number
			distanceValueElement.textContent = Number.parseFloat(cleanValueFloat.toFixed(3));

			//unit's data
			const unitData = cleanData.unit;
			distanceUnitElement.textContent = unitData.short
			cleanUnitElement.textContent = unitData.long
		}
	}

	enableLoadingButton()
	{
		this.submitCalculateDistance.classList.add("is-loading")
	}

	disableLoadingButton()
	{
		this.submitCalculateDistance.classList.remove("is-loading")
	}

	clearPreviousApiResults()
	{
		//hide the container
		this.containerResults.classList.add("is-hidden")

		//and empty the data
		const resetableElements = [
			"calculated-distance", "distance-unit", "distance-unit-clean"
		]
		resetableElements.forEach((value) => {
			const currentElement = this.containerResults.querySelector("#" + value)

			currentElement.textContent = "";
			currentElement.classList.remove("is-hidden")
		})
	}

	getFieldContainerLatitude()
	{
		return this.inputLatitude.parentElement.parentElement
	}

	getFieldContainerLongitude()
	{
		return this.inputLongitude.parentElement.parentElement
	}

	getGetParamsForApiCall()
	{
		return {
			lat: Number.parseFloat(this.inputLatitude.value),
			lon: Number.parseFloat(this.inputLongitude.value),
			digits: 3,
		}
	}
}
