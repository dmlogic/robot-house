# Robot House

Mobile friendly wrapper app to control hand-picked 
devices on a Vera Lite

## todo

* Complete markup for the following:
    - General dashboard
    - Run scene button
    - General "Room"
    - Dimmer control
    - Relay control
    - Thermostat control
    - Battery state
* Routes for Dashboard and room
* Config strategy for what gets displayed on Dash and Rooms
* JS to render Dash
* JS to render a room
* JS to lookup and cache current room / scene states
* Strategy for switching between local and MIOS servers
    - How to store credentials for MIOS?
* JS to handle changing a device
    - Make the call to Vera
    - Get the job details
    - Check the job progress
    - Update the device/cache on completion
* Hosting and domain access
* More I'm sure 