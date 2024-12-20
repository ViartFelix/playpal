/**
 *
 */
export default class EventData
{
	_latitude;
	_longitude;
	_id;

	constructor(latitude = undefined, longitude = undefined, id = undefined) {
		this._latitude = latitude;
		this._longitude = longitude;
		this._id = id;
	}

	/**
	 * Assign values from the window.eventData object (if exists)
	 */
	fromWindow()
	{
		if(window.eventData !== undefined && typeof window.eventData === "object") {
			const windowData = window.eventData;

			this.id = windowData.id ?? undefined;
			this.latitude = windowData.latitude ?? undefined;
			this.longitude = windowData.longitude ?? undefined;
		} else {
			throw new Error("No valid window.eventData object found.")
		}
	}

	toObject()
	{
		return {
			id: this._id,
			latitude: this._latitude,
			longitude: this._longitude
		}
	}

	get latitude() {
		return this._latitude;
	}

	set latitude(value) {
		this._latitude = value;
	}

	get longitude() {
		return this._longitude;
	}

	set longitude(value) {
		this._longitude = value;
	}

	get id() {
		return this._id;
	}

	set id(value) {
		this._id = value;
	}

}